<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\HomeControllerActions;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use App\Models\User;
use App\Models\UserAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{

    use InteractsWithAuthCookie;

    public function __construct() {
        $this->getUserDataFromCookie();

        View::share('isAdmin', $this->isAdmin());
        View::share('authToken', $this->authToken);
        View::share('isLoggedIn', $this->isLoggedIn() ? 'true' : 'false');
        View::share('userPermissions', json_encode($this->userPermissions));
        View::share('userData', json_encode($this->userData));
    }

    /**
     * Display homepage
     */
    public function index() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_HOME_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'HomePage'
        ]);
    }

    /**
     * Display link personalization page
     */
    public function linkPersonalization() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_LINK_PERSONALIZATION_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'RegisterCustomShortlink'
        ]);
    }

    /**
     * Display profile page
     */
    public function myProfile() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_PROFILE_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'Profile'
        ]);
    }

    /**
     * Display login page
     */
    public function login() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_LOGIN_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'Login'
        ]);
    }

    /**
     * Display register page
     */
    public function register() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_REGISTER_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'Register'
        ]);
    }

    /**
     * Display home page with my links open
     */
    public function myLinks() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_MY_LINKS_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'MyLinks'
        ]);
    }

    /**
     * Display contact page directly
     */
    public function contactUs() {
        UserAction::logAction($this->userId, HomeControllerActions::OPENED_CONTACT_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'ContactUs'
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

        UserAction::logAction($user->id, HomeControllerActions::OPENED_CHANGE_PASSWORD_PAGE_DIRECTLY);

        if ( !$user->hasVerifiedEmail() ) {
            try {
                $user->email_verified_at = Carbon::now();
                $user->save();
                UserAction::logAction($user->id, AuthActions::CONFIRMED_EMAIL_THROUGH_PASSWORD_RECOVERY);
            } catch (\Throwable $th) {
                UserAction::logAction($user->id, AuthActions::FAILED_TO_CONFIRM_EMAIL_THROUGH_PASSWORD_RECOVERY);
            }
        }

        return view('home', [
            'view' => 'ChangePassword',
            'passwordRecoveryToken' => $request->bearerToken()
        ]);
    }


    /**
     * Display the admin panel
     */
    public function adminPanel() {
        if (false == $this->isAdmin()) {
            return redirect('/');
        }

        UserAction::logAction($this->userId, HomeControllerActions::OPENED_ADMIN_PANEL_PAGE_DIRECTLY);
        return view('home', [
            'view' => 'PA'
        ]);
    }
}
