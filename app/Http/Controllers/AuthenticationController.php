<?php

namespace App\Http\Controllers;

use App\Helpers\Auth\AuthValidator;
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
use App\Models\Shortlink;
use Illuminate\Support\Facades\Mail;

class AuthenticationController extends Controller
{
    use InteractsWithTime;
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
        $config = config('session');

        $authCookie = Cookie::get($config['auth_token_cookie_name']);

        if (
            !is_null($authCookie)
        ) {
            $authCookie = decrypt($authCookie);

            $isAuthCookieValid = AuthValidator::validateAuthCookieDecryptedContent($authCookie);
            $isAuthTokenValid = false;

            if($isAuthCookieValid) {
                $isAuthTokenValid = AuthValidator::validateAuthToken($authCookie['auth_token']);
            }

            if ($isAuthCookieValid && $isAuthTokenValid) {
                // if auth cookie is valid
                // if auth token is valid / not expired
                // send the auth token from the cookie
                $response->setContent([
                    'at' => $authCookie['auth_token'],
                    'guest' => $authCookie['guest']
                ]);

                return $response;
            }
        }

        // creates new guest user
        $user = new User();
        $user->guest = 1;
        $user->save();

        // creates new guest token
        $guestTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['auth_token_cookie_lifetime']
        );

        $userToken = $user->createToken(
            'guest_token',
            ['guest'],
            $guestTokenExpirationDatetime
        )->plainTextToken;

        $response->setContent([
            'at' => $userToken,
            'guest' => 1
        ]);

        // creates new authCookie
        $authCookie = Cookie::make(
            $config['auth_token_cookie_name'],
            encrypt([
                'auth_token' => $userToken,
                'guest' => 1,
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

        // send response with the new cookie
        return $response
            ->withCookie($authCookie);
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

        $authCookie = Cookie::get($config['auth_token_cookie_name']);

        // when logging in, users must always have an Auth Cookie (with guest token)
        if (is_null($authCookie)) {
            return AuthResponses::notAuthenticated();
        }

        $authCookie = decrypt($authCookie);

        $isAuthCookieValid = AuthValidator::validateAuthCookieDecryptedContent($authCookie);
        $isAuthTokenValid = false;

        if($isAuthCookieValid) {
            $isAuthTokenValid = AuthValidator::validateAuthToken($authCookie['auth_token']);
        }

        if (
            $isAuthCookieValid && $isAuthTokenValid
        ) {
            // if auth cookie is valid
            // if auth token is valid / not expired
            // and the auth cookie is for a logged in user (non guest)
            // the user is already logged in
            // lets return the current auth cookie data

            if ($authCookie['guest'] === 0) {
                return $response
                        ->setContent([
                            'at' => $authCookie['auth_token'],
                            'guest' => 0
                        ]);
            }
        } else {
            // auth cookie or token is invalid
            // lets forbid the login. Must authenticate as guest first.

            // and lets forget this invalid auth cookie and/or token
            return AuthResponses::notAuthenticated()->withoutCookie($config['auth_token_cookie_name']);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
            return AuthResponses::incorrectCredentials();
        }

        if (!$user->hasVerifiedEmail()) {
            return AuthResponses::unverifiedAccount();
        }

        $userAbilities = $user->abilities->all();
        $userAbilities = array_map(function($ability){
            return $ability['name'];
        }, $userAbilities);
        $userAbilities = array_merge(['logged_in'], $userAbilities);

        $userTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['auth_token_cookie_lifetime']
        );

        $userToken = $user->createToken(
            'stay_logged_in_token',
            $userAbilities,
            $userTokenExpirationDatetime
        )->plainTextToken;

        // move shortlinks generated as guest
        // to the now logged in user account.
        Shortlink::where(
            'user_id', '=', $authCookie['user_id']
        )->update(['user_id' => $user->id]);

        $authCookie = Cookie::make(
            $config['auth_token_cookie_name'],
            encrypt([
                'auth_token' => $userToken,
                'guest' => 0,
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

        return $response
            ->setContent([
                'at' => $userToken,
                'guest' => 0
            ])
            ->withCookie($authCookie);
    }

    /**
     * Registers a new user, after validating user data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function registerAttempt(Request $request, Response $response) {
        $config = config('session');

        $authCookie = Cookie::get($config['auth_token_cookie_name']);

        // when registering in, users must always have an Auth Cookie (with guest token)
        if (is_null($authCookie)) {
            return AuthResponses::notAuthenticated();
        }

        $authCookie = decrypt($authCookie);

        $isAuthCookieValid = AuthValidator::validateAuthCookieDecryptedContent($authCookie);
        $isAuthTokenValid = false;

        if($isAuthCookieValid) {
            $isAuthTokenValid = AuthValidator::validateAuthToken($authCookie['auth_token']);
        }

        if (
            $isAuthCookieValid && $isAuthTokenValid
        ) {
            // if auth cookie is valid
            // if auth token is valid / not expired
            // and the auth cookie is for a logged in user (non guest)
            // the user is already logged in
            // lets return the current auth cookie data

            if ($authCookie['guest'] === 0) {
                return $response
                        ->setContent([
                            'at' => $authCookie['auth_token'],
                            'guest' => 0
                        ]);
            }
        } else {
            // auth cookie or token is invalid
            // lets forbid this action. Must authenticate as guest first.

            // this should never happen in reality.

            // and lets forget this invalid auth cookie and/or token
            return AuthResponses::notAuthenticated()->withoutCookie($config['auth_token_cookie_name']);
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
        } catch (\Throwable $th) {
            //TODO: log event
            return AuthResponses::registerFailed();
        }

        $this->sendVerificationEmail($user);

        /*$userAbilities = ['logged_in'];

        $userTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['auth_token_cookie_lifetime']
        );

        $userToken = $user->createToken(
            'stay_logged_in_token',
            $userAbilities,
            $userTokenExpirationDatetime
        )->plainTextToken;*/

        // move shortlinks generated as guest
        // to the now newly created (and now logged in) user account
        Shortlink::where(
            'user_id', '=', $authCookie['user_id']
        )->update(['user_id' => $user->id]);

        /*
        $authCookie = Cookie::make(
            $config['auth_token_cookie_name'],
            encrypt([
                'auth_token' => $userToken,
                'guest' => 0,
                'user_id' => $user->id
            ]),
            $config['auth_token_cookie_lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null
        );*/


        return $response
            /* ->setContent([
                'at' => $userToken,
                'guest' => 0
            ]) */
            ->setContent([
                'success' => 1,
            ])
            ->setStatusCode(Response::HTTP_CREATED);
            //->withCookie($authCookie);

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
            return new Response('', 200);
        }

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
            return new Response('', 200);
        }

        $config = config('session');

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
            return new Response('', 200);
        }

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

        $user->currentAccessToken()->delete();

        return new Response('', 200);
    }
}
