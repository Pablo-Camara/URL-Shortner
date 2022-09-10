<?php

namespace App\Helpers\Auth\Traits;

use App\Helpers\Auth\AuthValidator;
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

}
