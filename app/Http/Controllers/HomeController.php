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
        $captchaSitekey = config('captcha.sitekey');
        return view('home', [
            'captchaSitekey' => $captchaSitekey,
            'page' => 'ShortenUrl'
        ]);
    }

    /**
     * Display login page
     */
    public function login() {
        $captchaSitekey = config('captcha.sitekey');
        return view('home', [
            'captchaSitekey' => $captchaSitekey,
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

        $captchaSitekey = config('captcha.sitekey');
        return view('home', [
            'captchaSitekey' => $captchaSitekey,
            'page' => 'EmailConfirmed'
        ]);
    }
}
