<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Auth\AuthValidator;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\InteractsWithTime;
use App\Helpers\Responses\AuthResponses;
use App\Mail\Auth\EmailConfirmation;
use App\Mail\Auth\PasswordRecovery;
use App\Models\FacebookAccount;
use App\Models\GithubAccount;
use App\Models\GoogleAccount;
use App\Models\LinkedinAccount;
use App\Models\Shortlink;
use App\Models\TwitterAccount;
use App\Models\UserAction;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    use InteractsWithTime, InteractsWithAuthCookie;

    public function __construct() {
        $this->getUserDataFromCookie();
    }

    public function setAuthCookie(User $user)
    {
        $config = config('session');

        // creates new guest token
        $tokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['auth_token_cookie_lifetime']
        );

        $tokenName = 'guest_token';
        $userAbilities = ['guest'];

        if ($user->guest === 0) {

            $tokenName = 'stay_logged_in_token';
            $userAbilities = $user->abilities->all();
            $userAbilities = array_map(function($ability){
                return $ability['name'];
            }, $userAbilities);
            $userAbilities = array_merge(['logged_in'], $userAbilities);

        }

        $userToken = $user->createToken(
            $tokenName,
            $userAbilities,
            $tokenExpirationDatetime
        )->plainTextToken;

        // creates new authCookie
        $authCookie = Cookie::make(
            $config['auth_token_cookie_name'],
            encrypt([
                'auth_token' => $userToken,
                'guest' => $user->guest,
                'user_id' => $user->id
            ]),
            $config['auth_token_cookie_lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null
        );

        Cookie::queue($authCookie);
        return $userToken;
    }

    /**
     * Authenticates the user
     * if no auth token cookie, creates guest user and generates guest token
     * if auth token cookie, decrypt it and return the current user token
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function authenticationAttempt(Request $request, Response $response)
    {
        if (
            !is_null($this->userId)
        ) {
            // if auth cookie is valid
            // if auth token is valid / not expired
            // send the auth token from the cookie
            $response->setContent([
                'at' => $this->authToken,
                'guest' => $this->guest,
            ]);

            $actionName = $this->guest == 0 ?
                AuthActions::AUTHENTICATED_AS_USER : AuthActions::AUTHENTICATED_AS_GUEST;
            UserAction::logAction($this->userId, $actionName);

            return $response;
        }

        // creates new guest user
        $user = new User();
        $user->guest = 1;
        $user->save();

        $userToken = $this->setAuthCookie($user);

        $response->setContent([
            'at' => $userToken,
            'guest' => 1
        ]);

        UserAction::logAction($user->id, AuthActions::AUTHENTICATED_AS_GUEST);

        return $response;
    }

    /**
     * Logs an user out
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function logoutAttempt(Request $request, Response $response)
    {
        $config = config('session');

        if (
            !is_null($this->userId)
        ) {
            UserAction::logAction($this->userId, AuthActions::LOGGED_OUT);
        }

        $response->withoutCookie($config['auth_token_cookie_name'])->setStatusCode(200);

        return $response;
    }

     /**
     * Validates user credentials and generates a logged in authentication token
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function loginAttempt(Request $request, Response $response)
    {
        $config = config('session');

        // when logging in, users must always have an Auth Cookie (with guest token)
        if (is_null($this->userId)) {
            return AuthResponses::notAuthenticated();
        }

        if ($this->guest === 0) {

            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WHILE_LOGGED_IN);

            return $response
                    ->setContent([
                        'at' => $this->authToken,
                        'guest' => 0
                    ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_UNEXISTING_EMAIL);
            return AuthResponses::incorrectCredentials();
        }

        if ( !Hash::check($request->password, $user->password) ) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_WRONG_PASSWORD);
            UserAction::logAction($user->id, AuthActions::ATTEMPTED_TO_LOGIN_WITH_WRONG_PASSWORD);
            return AuthResponses::incorrectCredentials();
        }

        if (!$user->hasVerifiedEmail()) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_UNVERIFIED_EMAIL);
            UserAction::logAction($user->id, AuthActions::ATTEMPTED_TO_LOGIN_WITH_UNVERIFIED_EMAIL);
            return AuthResponses::unverifiedAccount();
        }

        $userToken = $this->setAuthCookie($user);

        // move shortlinks generated as guest
        // to the now logged in user account.
        $totalGeneratedLinksAsGuest = Shortlink::where(
            'user_id', '=', $this->userId
        )->update(['user_id' => $user->id]);

        if ($totalGeneratedLinksAsGuest > 0) {
            UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
            UserAction::logAction($user->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
        }

        UserAction::logAction($this->userId, AuthActions::LOGGED_IN);
        UserAction::logAction($user->id, AuthActions::LOGGED_IN);

        return $response
            ->setContent([
                'at' => $userToken,
                'guest' => 0
            ]);
    }

    /**
     * Registers a new user, after validating user data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function registerAttempt(Request $request, Response $response) {

        // when registering in, users must always have an Auth Cookie (with guest token)
        if (is_null($this->userId)) {
            return AuthResponses::notAuthenticated();
        }

        if ($this->guest === 0) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_REGISTER_WHILE_LOGGED_IN);
            return $response
                    ->setContent([
                        'at' => $this->authToken,
                        'guest' => 0
                    ]);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|confirmed|unique:users',
            'password' => 'required|confirmed',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = new User();
        $user->guest = 0;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));

        try {
            $user->save();
            UserAction::logAction($this->userId, AuthActions::REGISTERED);
        } catch (\Throwable $th) {
            //TODO: log event
            return AuthResponses::registerFailed();
        }

        $this->sendVerificationEmail($user);

        // move shortlinks generated as guest
        // to the now newly created (and now logged in) user account
        $totalGeneratedLinksAsGuest = Shortlink::where(
            'user_id', '=', $this->userId
        )->update(['user_id' => $user->id]);

        if ($totalGeneratedLinksAsGuest > 0) {
            UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
            UserAction::logAction($user->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
        }


        return $response
            ->setContent([
                'success' => 1,
            ])
            ->setStatusCode(Response::HTTP_CREATED);

    }

    /**
     * Resends a verification email
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function resendVerificationEmail(Request $request, Response $response)
    {
        $request->validate([
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // we will not let people exploit this endpoint
            // to find out which emails are registered
            // so we just return 200 and fool the users that are attempting
            // to spam this endpoint
            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, AuthActions::REQUESTED_RESENDING_CONFIRMATION_EMAIL_FOR_UNEXISTING_EMAIL);
            }

            return new Response('', 200);
        }

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, AuthActions::REQUESTED_RESENDING_CONFIRMATION_EMAIL);
        }
        UserAction::logAction($user->id, AuthActions::REQUESTED_RESENDING_CONFIRMATION_EMAIL);
        $this->sendVerificationEmail($user, true);

        return new Response('', 200);

    }

    private function sendVerificationEmail(User $user, $resending = false) {

        $config = config('session');

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $confirmEmailTokens = $user->tokens()
                ->where('abilities', 'like' , '%confirm_email%')
                ->orderBy('id', 'ASC') // older tokens first
                ->get();

        $emailConfirmationToken = null;

        // if this user already has an email confirmation token
        // we will not create a new token
        // and in the process we will delete the expired tokens for email confirmation
        foreach ($confirmEmailTokens as $token) {
            $hasTokenExpired = Carbon::now() >= $token->expires_at;
            if ($hasTokenExpired) {
                $token->delete();
                continue;
            }
            $emailConfirmationToken = $token;
            break;
        }

        if (is_null($emailConfirmationToken)) {
            $emailConfirmationTokenExpirationDatetime = Carbon::now()->addRealMinutes(
                $resending ? $config['resent_email_confirmation_token_lifetime'] : $config['email_confirmation_token_lifetime']
            );
            $emailConfirmationToken = $user->createToken(
                'email_confirmation_token',
                ['confirm_email'],
                $emailConfirmationTokenExpirationDatetime
            );
        }

        // lets only allow Resending a verification email every 15min
        // to avoid mass spam
        if ($resending)
        {
            $totalMinutesSinceLastTokenWasUpdated = $emailConfirmationToken->updated_at->diffInMinutes(Carbon::now());
            if ( $totalMinutesSinceLastTokenWasUpdated <= 15 ) {
                return;
            }
        }

        Mail::to(
            $user->email
        )->queue(new EmailConfirmation($user, $emailConfirmationToken->plainTextToken));

        if ($resending) {
            $emailConfirmationToken->updated_at = Carbon::now();
            $emailConfirmationToken->save();
        }

    }

    /**
     * Validates email/captcha and sends password recovery email
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function recoverPassword(Request $request, Response $response)
    {
        //REQUESTED_PASSWORD_RECOVERY_EMAIL
        $request->validate([
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        if (
            !is_null($this->userId)
        ) {
            UserAction::logAction($this->userId, AuthActions::REQUESTED_PASSWORD_RECOVERY_EMAIL);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // we will not let people exploit this endpoint
            // to find out which emails are registered
            // so we just return 200 and fool the users that are attempting
            // to spam this endpoint
            if (
                !is_null($this->userId)
            ) {
                UserAction::logAction($this->userId, AuthActions::REQUESTED_PASSWORD_RECOVERY_EMAIL_FOR_UNEXISTING_EMAIL);
            }
            return new Response('', 200);
        }

        UserAction::logAction($user->id, AuthActions::REQUESTED_PASSWORD_RECOVERY_EMAIL);


        // delete expired pwd token
        $user->tokens()
            ->where('abilities', 'like' , '%change_password%')
            ->where('expires_at', '<=', Carbon::now())
            ->delete();

        // before counting the user tokens for changing pwd
        $totalPwdRecoveryTokens = $user->tokens()
                ->where('abilities', 'like' , '%change_password%')
                ->orderBy('id', 'ASC') // older tokens first
                ->count();

        if ( $totalPwdRecoveryTokens > 0 ) {
            // if the user already has a pwd recovery token
            // we will not create another nor send another email
            // these tokens already have an expires_at date/time
            // and the token is deleted after consumption.

            // avoiding spammers..
            UserAction::logAction($user->id, AuthActions::USER_ALREADY_HAS_PASSWORD_RECOVERY_TOKEN);
            return new Response('', 200);
        }

        $config = config('session');

        $pwdRecoveryTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['password_recovery_token_lifetime']
        );
        $pwdRecoveryToken = $user->createToken(
            'password_recovery_token',
            ['change_password'],
            $pwdRecoveryTokenExpirationDatetime
        );

        Mail::to(
            $user->email
        )->queue(new PasswordRecovery($user, $pwdRecoveryToken->plainTextToken));

        return new Response('', 200);

    }

    /**
     * Validates email/captcha and sends password recovery email
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request) {

        $request->validate([
            'new_password' => 'required|confirmed',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        UserAction::logAction($user->id, AuthActions::CHANGED_PASSWORD);

        $user->currentAccessToken()->delete();

        return new Response('', 200);
    }


    public function githubRedirect(){
        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('github')->redirect();
    }

     /**
     * Registers and/or login user with Github
     *
     */
    public function githubCallback() {

        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_GITHUB);

        try {
            $user = Socialite::driver('github')->user();

            // store or update github data
            GithubAccount::updateOrCreate(
                [
                    'github_user_id' => $user->id
                ],
                [
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'user_token' => $user->token,
                    'user_refresh_token' => $user->refreshToken,
                    'expires_in' => $user->expiresIn,
                    'approved_scopes' => json_encode($user->approvedScopes),
                    'user_url' => $user->user['html_url'],
                    'user_type' => $user->user['type'],
                    'user_is_site_admin' => $user->user['site_admin'],
                    'user_company' => $user->user['company'],
                    'user_blog_link' => $user->user['blog'],
                    'user_location' => $user->user['location'],
                    'user_hireable' => $user->user['hireable'],
                    'user_bio' => $user->user['bio'],
                    'user_twitter_username' => $user->user['twitter_username'],
                    'user_total_public_repos' => $user->user['public_repos'],
                    'user_total_followers' => $user->user['followers'],
                    'user_acc_created_at' => $user->user['created_at'],
                ]
            );

            // login user using github email
            $existingUser = User::where('email', '=', $user->email)->first();

            $usedGuestAcc = false;
            if (!$existingUser) {
                $existingUser = User::find($this->userId);
                $existingUser->guest = 0;
                $existingUser->name = $user->name;
                $existingUser->email = $user->email;
                $existingUser->save();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_GITHUB);
            }

            if (!$usedGuestAcc) {
                // move shortlinks generated as guest to acc
                $totalGeneratedLinksAsGuest = Shortlink::where(
                    'user_id', '=', $this->userId
                )->update(['user_id' => $existingUser->id]);

                if ($totalGeneratedLinksAsGuest > 0) {
                    UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
                    UserAction::logAction($existingUser->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
                }
            }

            $this->setAuthCookie($existingUser);

            UserAction::logAction($this->userId, AuthActions::LOGGED_IN_WITH_GITHUB);
            if (!$usedGuestAcc) {
                UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_GITHUB);
            }

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            // TODO: log github login attempt failed
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_GITHUB);
            return redirect()->route('login-page');
        }

    }


    public function facebookRedirect(){
        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Registers and/or login user with Facebook
     *
     */
    public function facebookCallback() {

        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_FACEBOOK);

        try {
            $user = Socialite::driver('facebook')->user();

            // store or update facebook data
            FacebookAccount::updateOrCreate(
                [
                    'facebook_user_id' => $user->id
                ],
                [
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'user_token' => $user->token,
                    'user_refresh_token' => $user->refreshToken,
                    'expires_in' => $user->expiresIn,
                    'approved_scopes' => json_encode($user->approvedScopes),
                    'avatar_original' => $user->avatar_original,
                    'profile_url' => $user->profileUrl
                ]
            );


            // login user using facebook email
            $existingUser = User::where('email', '=', $user->email)->first();

            $usedGuestAcc = false;
            if (!$existingUser) {
                $existingUser = User::find($this->userId);
                $existingUser->guest = 0;
                $existingUser->name = $user->name;
                $existingUser->email = $user->email;
                $existingUser->save();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_FACEBOOK);
            }

            if (!$usedGuestAcc) {
                // move shortlinks generated as guest to acc
                $totalGeneratedLinksAsGuest = Shortlink::where(
                    'user_id', '=', $this->userId
                )->update(['user_id' => $existingUser->id]);

                if ($totalGeneratedLinksAsGuest > 0) {
                    UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
                    UserAction::logAction($existingUser->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
                }
            }

            $this->setAuthCookie($existingUser);

            UserAction::logAction($this->userId, AuthActions::LOGGED_IN_WITH_FACEBOOK);
            if (!$usedGuestAcc) {
                UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_FACEBOOK);
            }

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_FACEBOOK);
            return redirect()->route('login-page');
        }

    }

    public function googleRedirect(){
        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Registers and/or login user with google
     *
     */
    public function googleCallback() {

        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_GOOGLE);

        try {
            $user = Socialite::driver('google')->user();

            // store or update facebook data
            GoogleAccount::updateOrCreate(
                [
                    'google_user_id' => $user->id
                ],
                [
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => $user->avatar_original,
                    'user_token' => $user->token,
                    'user_refresh_token' => $user->refreshToken,
                    'expires_in' => $user->expiresIn,
                    'approved_scopes' => json_encode($user->approvedScopes),
                    'user_link' => $user->user['link'],
                    'user_picture' => $user->user['picture'],
                    'user_email_verified' => $user->user['email_verified'],
                    'user_locale' => $user->user['locale'],
                    'user_verified_email' => $user->user['verified_email'],
                ]
            );

            // login user using google email
            $existingUser = User::where('email', '=', $user->email)->first();

            $usedGuestAcc = false;
            if (!$existingUser) {
                $existingUser = User::find($this->userId);
                $existingUser->guest = 0;
                $existingUser->name = $user->name;
                $existingUser->email = $user->email;
                $existingUser->save();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_GOOGLE);
            }

            if (!$usedGuestAcc) {
                // move shortlinks generated as guest to acc
                $totalGeneratedLinksAsGuest = Shortlink::where(
                    'user_id', '=', $this->userId
                )->update(['user_id' => $existingUser->id]);

                if ($totalGeneratedLinksAsGuest > 0) {
                    UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
                    UserAction::logAction($existingUser->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
                }
            }

            $this->setAuthCookie($existingUser);

            UserAction::logAction($this->userId, AuthActions::LOGGED_IN_WITH_GOOGLE);
            if (!$usedGuestAcc) {
                UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_GOOGLE);
            }

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_GOOGLE);
            return redirect()->route('login-page');
        }

    }


    public function linkedInRedirect(){
        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('linkedin')->redirect();
    }

    /**
     * Registers and/or login user with linkedin
     *
     */
    public function linkedInCallback() {

        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_LINKEDIN);

        try {
            $user = Socialite::driver('linkedin')->user();

            // store or update linkedin data
            LinkedinAccount::updateOrCreate(
                [
                    'linkedin_user_id' => $user->id
                ],
                [
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => $user->avatar_original,
                    'user_token' => $user->token,
                    'user_refresh_token' => $user->refreshToken,
                    'expires_in' => $user->expiresIn,
                    'approved_scopes' => json_encode($user->approvedScopes),
                ]
            );

            // login user using linkedin email
            $existingUser = User::where('email', '=', $user->email)->first();

            $usedGuestAcc = false;
            if (!$existingUser) {
                $existingUser = User::find($this->userId);
                $existingUser->guest = 0;
                $existingUser->name = $user->name;
                $existingUser->email = $user->email;
                $existingUser->save();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_LINKEDIN);
            }

            if (!$usedGuestAcc) {
                // move shortlinks generated as guest to acc
                $totalGeneratedLinksAsGuest = Shortlink::where(
                    'user_id', '=', $this->userId
                )->update(['user_id' => $existingUser->id]);

                if ($totalGeneratedLinksAsGuest > 0) {
                    UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
                    UserAction::logAction($existingUser->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
                }
            }

            $this->setAuthCookie($existingUser);

            UserAction::logAction($this->userId, AuthActions::LOGGED_IN_WITH_LINKEDIN);
            if (!$usedGuestAcc) {
                UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_LINKEDIN);
            }

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_LINKEDIN);
            return redirect()->route('login-page');
        }

    }


    public function twitterRedirect(){
        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Registers and/or login user with twitter
     *
     */
    public function twitterCallback() {

        if (is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_TWITTER);

        try {
            $user = Socialite::driver('twitter')->user();

            // store or update twitter data
            TwitterAccount::updateOrCreate(
                [
                    'twitter_user_id' => $user->id,
                ],
                [
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => $user->avatar_original,
                    'user_protected' => $user->user['protected'],
                    'user_followers_count' => $user->user['followers_count'],
                    'user_friends_count' => $user->user['friends_count'],
                    'user_listed_count' => $user->user['listed_count'],
                    'user_created_at' => $user->user['created_at'],
                    'user_favourites_count' => $user->user['favourites_count'],
                    'user_utc_offset' => $user->user['utc_offset'],
                    'user_time_zone' => $user->user['time_zone'],
                    'user_geo_enabled' => $user->user['geo_enabled'],
                    'user_verified' => $user->user['verified'],
                    'user_statuses_count' => $user->user['statuses_count'],
                    'user_lang' => $user->user['lang'],
                    'user_contributors_enabled' => $user->user['contributors_enabled'],
                    'user_is_translator' => $user->user['is_translator'],
                    'user_is_translation_enabled' => $user->user['is_translation_enabled'],
                    'user_profile_use_background_image' => $user->user['profile_use_background_image'],
                    'user_has_extended_profile' => $user->user['has_extended_profile'],
                    'user_default_profile' => $user->user['default_profile'],
                    'user_default_profile_image' => $user->user['default_profile_image'],
                    'user_suspended' => $user->user['suspended'],
                    'user_needs_phone_verification' => $user->user['needs_phone_verification'],
                    'user_url' => $user->user['url'],
                    'user_location' => $user->user['location'],
                    'user_description' => $user->user['description'],
                    'user_token' => $user->token,
                    'user_token_secret' => $user->tokenSecret,
                ]
            );

            // login user using twitter email
            $existingUser = User::where('email', '=', $user->email)->first();

            $usedGuestAcc = false;
            if (!$existingUser) {
                $existingUser = User::find($this->userId);
                $existingUser->guest = 0;
                $existingUser->name = $user->name;
                $existingUser->email = $user->email;
                $existingUser->save();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_TWITTER);
            }

            if (!$usedGuestAcc) {
                // move shortlinks generated as guest to acc
                $totalGeneratedLinksAsGuest = Shortlink::where(
                    'user_id', '=', $this->userId
                )->update(['user_id' => $existingUser->id]);

                if ($totalGeneratedLinksAsGuest > 0) {
                    UserAction::logAction($this->userId, AuthActions::SAVED_SHORTLINKS_GENERATED_AS_GUEST_TO_ACCOUNT);
                    UserAction::logAction($existingUser->id, AuthActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
                }
            }

            $this->setAuthCookie($existingUser);

            UserAction::logAction($this->userId, AuthActions::LOGGED_IN_WITH_TWITTER);
            if (!$usedGuestAcc) {
                UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_TWITTER);
            }

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_TWITTER);
            return redirect()->route('login-page');
        }

    }
}
