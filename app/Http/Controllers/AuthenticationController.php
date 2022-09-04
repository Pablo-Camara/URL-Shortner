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
use App\Mail\EmailConfirmation;
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

        $emailConfirmationTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['email_confirmation_token_lifetime']
        );
        $emailConfirmationToken = $user->createToken(
            'email_confirmation_token',
            ['confirm_email'],
            $emailConfirmationTokenExpirationDatetime
        )->plainTextToken;

        Mail::to(
            $user->email
        )->queue(new EmailConfirmation($user, $emailConfirmationToken));

        $userAbilities = ['logged_in'];

        $userTokenExpirationDatetime = Carbon::now()->addRealMinutes(
            $config['auth_token_cookie_lifetime']
        );

        $userToken = $user->createToken(
            'stay_logged_in_token',
            $userAbilities,
            $userTokenExpirationDatetime
        )->plainTextToken;

        // move shortlinks generated as guest
        // to the now newly created (and now logged in) user account
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
            ->setStatusCode(Response::HTTP_CREATED)
            ->withCookie($authCookie);

    }
}
