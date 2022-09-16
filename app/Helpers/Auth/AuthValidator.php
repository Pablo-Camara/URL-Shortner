<?php

namespace App\Helpers\Auth;

use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class AuthValidator {

    /**
     * Validate Auth Cookie decrypted content
     * make sure content is an array
     * make sure array has an auth_token
     *
     * @param mixed $authCookie
     * @return bool
     */
    public static function isAuthCookieDecryptedContentValid($authCookieDecryptedContent) : bool
    {
        // is the auth cookie decrypted content valid ?
        if (
            !is_array($authCookieDecryptedContent)
            ||
            empty($authCookieDecryptedContent['auth_token'])
        ) {
            //invalid auth cookie data
            //must be an array
            //must contain auth_token
            return false;
        }

        return true;
    }


    /**
     * Validate an Auth token
     * makes sure token exists
     * makes sure token is not expired
     *
     * @param ?string $authToken
     * @return PersonalAccessToken|false
     */
    public static function isAuthTokenValid(?string $authToken) : PersonalAccessToken|bool {

        // an empty or null string is an invalid token..
        if (empty($authToken)) {
            return false;
        }

        // does the auth_token still exist?
        $personalAccessToken = PersonalAccessToken::findToken(
            $authToken
        );

        if ($personalAccessToken) {
            // is the auth_token still valid / not expired?
            $hasTokenExpired = Carbon::now() >= $personalAccessToken->expires_at;

            if ($hasTokenExpired) {
                return false;
            }

            // if auth_token exists and is not expired it is valid
            // lets return the PersonalAccessToken back
            return $personalAccessToken;
        }

        // if it no longer exists it is invalid
        return false;
    }
}
