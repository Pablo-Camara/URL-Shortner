<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\ShortlinkActions;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
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
use App\Models\UserDevice;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    use InteractsWithTime, InteractsWithAuthCookie;

    public function __construct() {
        $this->getUserDataFromCookie();
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
        $authenticatedResponseData = $this->getAuthenticatedAuthResponseData();
        if ( !is_null($authenticatedResponseData) ) {
            $response->setContent($authenticatedResponseData);

            UserAction::logAction(
                $this->userId,
                $this->guest == 0 ?
                    AuthActions::AUTHENTICATED_AS_USER : AuthActions::AUTHENTICATED_AS_GUEST
            );

            return $response;
        }

        // creates new guest user
        $user = User::createNewGuestUser();
        if (is_null($user)) {
            // failed to create the guest user
            UserAction::logAction(null, AuthActions::FAILED_TO_CREATE_GUEST_ACCOUNT);
            return AuthResponses::failedToCreateGuestAccount();
        }

        try {
            UserDevice::create(
                $request->input('dw'),
                $request->input('dh'),
                $request->input('ua'),
                $user->id
            );
        } catch (\Throwable $th) {
            UserAction::logAction($user->id, AuthActions::FAILED_TO_STORE_USER_DEVICE);
        }

        $newAccessToken = $this->setAuthCookie($user);
        $response->setContent([
            'at' => $newAccessToken->plainTextToken,
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
        $authTokenCookieName = config('session.auth_token_cookie_name');

        if ( $this->isLoggedIn() ) {
            UserAction::logAction($this->userId, AuthActions::LOGGED_OUT);
        } else {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOG_OUT_WITHOUT_BEING_LOGGED_IN);
        }

        $response->withoutCookie($authTokenCookieName)->setStatusCode(200);

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
        if (false == $this->isAuthenticated()) {
            return AuthResponses::notAuthenticated();
        }

        if ($this->isLoggedIn()) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WHILE_LOGGED_IN);
            return $response->setContent($this->getAuthenticatedAuthResponseData());
        }

        $validations = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($validations);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_LOGIN_WITH_UNEXISTING_EMAIL);
            return AuthResponses::incorrectCredentials();
        }

        if ( !Hash::check($request->password, $user->password) ) {
            // todo: store guest acc id if any, associated to:
            UserAction::logAction($user->id, AuthActions::ATTEMPTED_TO_LOGIN_WITH_WRONG_PASSWORD);
            return AuthResponses::incorrectCredentials();
        }

        if (!$user->hasVerifiedEmail()) {
            // todo: store guest acc id if any, associated to:
            UserAction::logAction($user->id, AuthActions::ATTEMPTED_TO_LOGIN_WITH_UNVERIFIED_EMAIL);
            return AuthResponses::unverifiedAccount();
        }

        $newAccessToken = $this->setAuthCookie($user);

        $this->moveGuestDataToRegisteredUser(
            $this->userId,
            $user->id
        );

        // todo: store guest acc id if any, associated to:
        UserAction::logAction($user->id, AuthActions::LOGGED_IN);

        $authResponse = $this->getAuthResponseDataForUserToken(
            $newAccessToken->plainTextToken,
            $newAccessToken->accessToken
        );
        $response->setContent($authResponse);

        return $response;
    }

    /**
     * Registers a new user, after validating user data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function registerAttempt(Request $request, Response $response) {
        if (false == $this->isAuthenticated()) {
            return AuthResponses::notAuthenticated();
        }

        if ($this->isLoggedIn()) {
            UserAction::logAction($this->userId, AuthActions::ATTEMPTED_TO_REGISTER_WHILE_LOGGED_IN);
            return $response->setContent($this->getAuthenticatedAuthResponseData());
        }

        $validations = [
            'name' => 'required',
            'email' => 'required|email|confirmed|unique:users',
            'password' => 'required|confirmed',
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($validations);

        $user = new User();
        $user->guest = 0;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));

        DB::beginTransaction();
        try {
            $user->save();

            $userPermissions = new UserPermission();
            $userPermissions->user_id = $user->id;
            $userPermissions->save();

            UserAction::logAction($this->userId, AuthActions::REGISTERED);
        } catch (\Throwable $th) {
            //TODO: log event
            return AuthResponses::registerFailed();
        }
        DB::commit();

        $this->sendVerificationEmail($user);
        $this->moveGuestDataToRegisteredUser($this->userId, $user->id);

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

        $validations = [
            'email' => 'required|email',
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($validations);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // we will not let people exploit this endpoint
            // to find out which emails are registered
            // so we just return 200 and fool the users that are attempting
            // to spam this endpoint
            UserAction::logAction($this->userId, AuthActions::REQUESTED_RESENDING_CONFIRMATION_EMAIL_FOR_UNEXISTING_EMAIL);

            return new Response('', 200);
        }

        // todo: store guest acc id if any, associated to ( from which guest attempted this ? )
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
        $validations = [
            'email' => 'required|email',
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        //REQUESTED_PASSWORD_RECOVERY_EMAIL
        $request->validate($validations);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // we will not let people exploit this endpoint
            // to find out which emails are registered
            // so we just return 200 and fool the users that are attempting
            // to spam this endpoint
            UserAction::logAction($this->userId, AuthActions::REQUESTED_PASSWORD_RECOVERY_EMAIL_FOR_UNEXISTING_EMAIL);
            return new Response('', 200);
        }

        //todo: from which guest acc id ?
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

        $validations = [
            'new_password' => 'required|confirmed',
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        Validator::make(
            $request->all(),
            $validations,
            [],
            [
                'new_password' => 'nova palavra-passe',
            ]
        )->validate();


        $user = $request->user();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        UserAction::logAction($user->id, AuthActions::CHANGED_PASSWORD);

        $user->currentAccessToken()->delete();

        return new Response('', 200);
    }


    public function githubRedirect(){
        $enableLoginBtn = config('services.github.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('github')->redirect();
    }

     /**
     * Registers and/or login user with Github
     *
     */
    public function githubCallback() {
        $enableLoginBtn = config('services.github.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

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
                $existingUser->avatar = $user->avatar;

                DB::beginTransaction();
                try {
                    $existingUser->save();

                    $userPermissions = new UserPermission();
                    $userPermissions->user_id = $existingUser->id;
                    $userPermissions->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                DB::commit();


                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_GITHUB);
            }

            if (!$usedGuestAcc) {
                $this->moveGuestDataToRegisteredUser($this->userId, $existingUser->id);
                User::updateAvatar($existingUser->id, $user->avatar);
            }

            $this->setAuthCookie($existingUser);

            // todo: from which guest id ( if not $usedGuestAcc )
            UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_GITHUB);
            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            // TODO: log github login attempt failed
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_GITHUB);
            return redirect()->route('login-page');
        }

    }


    public function facebookRedirect(){
        $enableLoginBtn = config('services.facebook.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Registers and/or login user with Facebook
     *
     */
    public function facebookCallback() {

        $enableLoginBtn = config('services.facebook.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

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
                $existingUser->avatar = $user->avatar;

                DB::beginTransaction();
                try {
                    $existingUser->save();

                    $userPermissions = new UserPermission();
                    $userPermissions->user_id = $existingUser->id;
                    $userPermissions->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                DB::commit();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_FACEBOOK);
            }

            if (!$usedGuestAcc) {
                $this->moveGuestDataToRegisteredUser($this->userId, $existingUser->id);
                User::updateAvatar($existingUser->id, $user->avatar);
            }

            $this->setAuthCookie($existingUser);

            //TODO: from which guest?
            UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_FACEBOOK);

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_FACEBOOK);
            return redirect()->route('login-page');
        }

    }

    public function googleRedirect(){
        $enableLoginBtn = config('services.google.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Registers and/or login user with google
     *
     */
    public function googleCallback() {

        $enableLoginBtn = config('services.google.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

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
                $existingUser->avatar = $user->avatar;

                DB::beginTransaction();
                try {
                    $existingUser->save();

                    $userPermissions = new UserPermission();
                    $userPermissions->user_id = $existingUser->id;
                    $userPermissions->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                DB::commit();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_GOOGLE);
            }

            if (!$usedGuestAcc) {
                $this->moveGuestDataToRegisteredUser($this->userId, $existingUser->id);
                User::updateAvatar($existingUser->id, $user->avatar);
            }

            $this->setAuthCookie($existingUser);

            //TODO: from which guest?
            UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_GOOGLE);

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_GOOGLE);
            return redirect()->route('login-page');
        }

    }


    public function linkedInRedirect(){
        $enableLoginBtn = config('services.linkedin.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('linkedin')->redirect();
    }

    /**
     * Registers and/or login user with linkedin
     *
     */
    public function linkedInCallback() {

        $enableLoginBtn = config('services.linkedin.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

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
                $existingUser->avatar = $user->avatar;

                DB::beginTransaction();
                try {
                    $existingUser->save();

                    $userPermissions = new UserPermission();
                    $userPermissions->user_id = $existingUser->id;
                    $userPermissions->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                DB::commit();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_LINKEDIN);
            }

            if (!$usedGuestAcc) {
                $this->moveGuestDataToRegisteredUser($this->userId, $existingUser->id);
                User::updateAvatar($existingUser->id, $user->avatar);
            }

            $this->setAuthCookie($existingUser);

            //TODO: from which guest?
            UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_LINKEDIN);

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_LINKEDIN);
            return redirect()->route('login-page');
        }

    }


    public function twitterRedirect(){
        $enableLoginBtn = config('services.twitter.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Registers and/or login user with twitter
     *
     */
    public function twitterCallback() {

        $enableLoginBtn = config('services.twitter.enable_login_btn');
        if (!$enableLoginBtn || is_null($this->userId) || $this->guest == 0) {
            return redirect()->route('login-page');
        }

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
                $existingUser->avatar = $user->avatar;

                DB::beginTransaction();
                try {
                    $existingUser->save();

                    $userPermissions = new UserPermission();
                    $userPermissions->user_id = $existingUser->id;
                    $userPermissions->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
                DB::commit();

                $usedGuestAcc = true;
                UserAction::logAction($existingUser->id, AuthActions::REGISTERED_WITH_TWITTER);
            }

            if (!$usedGuestAcc) {
                $this->moveGuestDataToRegisteredUser($this->userId, $existingUser->id);
                User::updateAvatar($existingUser->id, $user->avatar);
            }

            $this->setAuthCookie($existingUser);

            //TODO: from which guest?
            UserAction::logAction($existingUser->id, AuthActions::LOGGED_IN_WITH_TWITTER);

            return redirect()->route('my-links-page');

        } catch (\Throwable $th) {
            UserAction::logAction($this->userId, AuthActions::FAILED_TO_LOGIN_WITH_TWITTER);
            return redirect()->route('login-page');
        }

    }


    public function moveGuestDataToRegisteredUser (
        $guestUserId,
        $registeredUserId
    ) {
        // lets move Guest actions to the registered user
        $totalActionsAsGuest = UserAction::moveActionsFromUserToUser($guestUserId, $registeredUserId);
        if (is_null($totalActionsAsGuest)) {
            UserAction::logAction($registeredUserId, AuthActions::FAILED_TO_IMPORT_ACTIONS_FROM_GUEST_ACCOUNT);
        }

        if ($totalActionsAsGuest > 0) {
            UserAction::logAction($registeredUserId, AuthActions::IMPORTED_ACTIONS_FROM_GUEST_ACCOUNT);
        }


        // move shortlinks generated as guest
        // to the registered user account.
        $totalGeneratedShortlinksAsGuest = Shortlink::moveShortlinksFromUserToUser($guestUserId, $registeredUserId);
        if (is_null($totalGeneratedShortlinksAsGuest)) {
            UserAction::logAction($registeredUserId, ShortlinkActions::FAILED_TO_IMPORT_SHORTLINKS_FROM_GUEST_ACCOUNT);
        }

        if ($totalGeneratedShortlinksAsGuest > 0) {
            UserAction::logAction($registeredUserId, ShortlinkActions::IMPORTED_SHORTLINKS_FROM_GUEST_ACCOUNT);
        }


        // move guest device
        // to the now newly created user account
        $totalUserDevicesAsGuest = UserDevice::moveDevicesFromUserToUser($guestUserId, $registeredUserId);

        if (is_null($totalUserDevicesAsGuest)) {
            UserAction::logAction($registeredUserId, AuthActions::FAILED_TO_IMPORT_DETECTED_DEVICES_FROM_GUEST_ACCOUNT);
        }

        if ($totalUserDevicesAsGuest > 0) {
            UserAction::logAction($registeredUserId, AuthActions::IMPORTED_DETECTED_DEVICES_FROM_GUEST_ACCOUNT);
        }

    }
}
