<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\HomeControllerActions;
use App\Helpers\Auth\AuthValidator;
use App\Models\User;
use App\Models\UserAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{

    public $userId = null;

    public function __construct() {

        $config = config('session');

        $authCookie = Cookie::get($config['auth_token_cookie_name']);

        $isAuthCookieValid = false;
        $isAuthTokenValid = false;

        if (
            !is_null($authCookie)
        ) {
            $authCookie = decrypt($authCookie);

            $isAuthCookieValid = AuthValidator::validateAuthCookieDecryptedContent($authCookie);

            if($isAuthCookieValid) {
                $isAuthTokenValid = AuthValidator::validateAuthToken($authCookie['auth_token']);
            }

            if ($isAuthCookieValid && $isAuthTokenValid) {
                $this->userId = $authCookie['user_id'];
            }
        }
    }
    /**
     * Display homepage
     */
    public function index() {
        return view('home', [
            'view' => 'HomePage'
        ]);
    }

    /**
     * Display login page
     */
    public function login() {

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, HomeControllerActions::OPENED_LOGIN_PAGE_DIRECTLY);
        }

        return view('home', [
            'view' => 'Login'
        ]);
    }

    /**
     * Display register page
     */
    public function register() {

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, HomeControllerActions::OPENED_REGISTER_PAGE_DIRECTLY);
        }

        return view('home', [
            'view' => 'Register'
        ]);
    }

    /**
     * Display home page with my links open
     */
    public function myLinks() {

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, HomeControllerActions::OPENED_MY_LINKS_PAGE_DIRECTLY);
        }

        return view('home', [
            'view' => 'MyLinks'
        ]);
    }

    /**
     * Email confirmation route
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmEmail(Request $request) {
        // if the user got here..
        // then his token was already checked by sanctum

        // lets set the email_verified_at
        $user = User::findOrFail($request->user()->id);
        $user->email_verified_at = Carbon::now();
        $user->save();

        UserAction::logAction($user->id, AuthActions::CONFIRMED_EMAIL);

        // and delete current email confirmation token
        $request->user()->currentAccessToken()->delete();

        // TODO: Send welcome email?
        return view('home', [
            'view' => 'EmailConfirmed'
        ]);
    }

    /**
     * Route to change password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request) {
        // if the user got here..
        // then his token was already checked by sanctum

        // if the user got here
        // it is because he clicked in a link in his email
        // meaning, we can set his email_verified_at
        // in case the account is not yet verified

        // this is a cool feature I thought would be nice to have :D
        $user = User::findOrFail($request->user()->id);

        if ( !$user->hasVerifiedEmail() ) {
            try {
                $user->email_verified_at = Carbon::now();
                $user->save();
                UserAction::logAction($user->id, AuthActions::CONFIRMED_EMAIL_THROUGH_PASSWORD_RECOVERY);
            } catch (\Throwable $th) {
                // TODO: log this event in the future?
            }
        }

        UserAction::logAction($user->id, HomeControllerActions::OPENED_CHANGE_PASSWORD_PAGE_DIRECTLY);

        return view('home', [
            'view' => 'ChangePassword',
            'passwordRecoveryToken' => $request->bearerToken()
        ]);
    }
}
