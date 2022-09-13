<?php

namespace App\Helpers\Auth\Traits;

use App\Helpers\Auth\AuthValidator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

trait InteractsWithAuthCookie
{

    private $userId = null;
    private $authToken = null;
    private $guest = 1;


    private function getUserDataFromCookie() {
        $config = config('session');

        $authCookie = Cookie::get($config['auth_token_cookie_name']);

        $isAuthCookieValid = false;
        $isAuthTokenValid = false;

        if (
            !is_null($authCookie)
        ) {
            try {
                $authCookie = decrypt($authCookie);

                $isAuthCookieValid = AuthValidator::validateAuthCookieDecryptedContent($authCookie);

                if($isAuthCookieValid) {
                    $isAuthTokenValid = AuthValidator::validateAuthToken($authCookie['auth_token']);
                }

                if ($isAuthCookieValid && $isAuthTokenValid) {
                    $this->userId = $authCookie['user_id'];
                    $this->authToken = $authCookie['auth_token'];
                    $this->guest = $authCookie['guest'];
                }
            } catch (\Throwable $th) {
                //TODO: log if something weent wrong here: FAILED_RETRIEVING_DATA_FROM_EXISTING_COOKIE
                throw $th;
            }

        }
    }

    private function setAuthCookie(User $user)
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

}
