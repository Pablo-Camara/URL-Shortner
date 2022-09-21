<?php

namespace App\Helpers\Auth\Traits;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Auth\AuthValidator;
use App\Models\User;
use App\Models\UserAction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;

trait InteractsWithAuthCookie
{

    private $authToken = null;
    private $userId = null;
    private $guest = 1;
    private $isAdmin = 0;
    private $userPermissions = null;
    private $userData = null;

    private function getUserDataFromCookie() {
        $authTokenCookieName = config('session.auth_token_cookie_name');
        $authCookie = Cookie::get($authTokenCookieName);

        $personalAccessToken = false;

        if (
            !is_null($authCookie)
        ) {
            try {
                $authCookie = decrypt($authCookie);

                if(
                    false == AuthValidator::isAuthCookieDecryptedContentValid($authCookie)
                ) {
                    throw new Exception('Invalid Auth Cookie data');
                }

                $personalAccessToken = AuthValidator::isAuthTokenValid($authCookie['auth_token']);

                if (
                    false == $personalAccessToken
                ) {
                    throw new Exception('Invalid personal access token');
                }


                $this->authToken = $authCookie['auth_token'];
                $authResponseData = $this->getAuthResponseDataForUserToken(
                    $this->authToken,
                    $personalAccessToken
                );

                if ($personalAccessToken->tokenable instanceof User) {
                    $this->userId = $personalAccessToken->tokenable->id;
                    $this->guest = $authResponseData['guest'];
                    if ($this->guest === 0) {
                        $this->isAdmin = $authResponseData['permissions']['is_admin'];
                        $this->userPermissions = $authResponseData['permissions'];
                        $this->userData = $authResponseData['data'];
                    }
                }

            } catch (\Throwable $th) {
                UserAction::logAction(null, AuthActions::FAILED_RETRIEVING_DATA_FROM_EXISTING_AUTH_COOKIE);
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

        $newAccessToken = $user->createToken(
            $tokenName,
            $userAbilities,
            $tokenExpirationDatetime
        );

        // creates new authCookie
        $authCookie = Cookie::make(
            $config['auth_token_cookie_name'],
            encrypt([
                'auth_token' => $newAccessToken->plainTextToken
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
        return $newAccessToken;
    }

    private function isAuthenticated () {
        return !is_null($this->userId);
    }

    private function isLoggedIn () {
        return $this->isAuthenticated() && $this->guest === 0;
    }

    private function isAdmin() {
        return $this->isAdmin == true;
    }

    private function getAuthenticatedAuthResponseData() {
        if ($this->isAuthenticated()) {
            $authResponse = [
                'at' => $this->authToken,
                'guest' => $this->guest,
            ];

            if ($this->isLoggedIn()) {
                $authResponse['permissions'] = $this->userPermissions;
                $authResponse['data'] = $this->userData;
            }

            return $authResponse;
        }

        return null;
    }

    private function getAuthResponseDataForUserToken (
        $plainTextToken,
        PersonalAccessToken $personalAccessToken
    ) {
        $user = $personalAccessToken->tokenable;

        if (
            false == ($user instanceof User)
        ) {
            return null;
        }

        $authResponse = [
            'at' => $plainTextToken,
            'guest' => $user->guest
        ];

        if (false === $user->isGuest()) {
            $authResponse['permissions'] = [
                'is_admin' => $user->isAdmin()
            ];

            $userPermissions = $user->userPermissions()->first();
            if ($userPermissions) {
                $authResponse['permissions'] = array_merge(
                    $authResponse['permissions'],
                    $userPermissions->toArray()
                );
            }

            $authResponse['data'] = [
                'avatar' => $user->avatar,
                'name' => $user->name
            ];

            if ( $user->isAdmin() ) {
                $authResponse['data']['adminPanel'] = '/painel-admin'; // hard code for now
            }
        }

        return $authResponse;
    }

}
