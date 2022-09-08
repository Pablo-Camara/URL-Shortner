<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display homepage
     */
    public function index() {
        return view('home', [
            'page' => 'ShortenUrl'
        ]);
    }

    /**
     * Display login page
     */
    public function login() {
        return view('home', [
            'page' => 'Login'
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

        // and delete current email confirmation token
        $request->user()->currentAccessToken()->delete();

        // TODO: Send welcome email?
        return view('home', [
            'page' => 'EmailConfirmed'
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
                // TODO: log this event in the future?
            } catch (\Throwable $th) {
                // TODO: log this event in the future?
            }
        }

        return view('home', [
            'page' => 'ChangePassword',
            'passwordRecoveryToken' => $request->bearerToken()
        ]);
    }
}
