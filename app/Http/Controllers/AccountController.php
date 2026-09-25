<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function session(Request $request)
    {
        return response()->json(['user' => $request->user()?->only('id', 'name', 'email'), 'csrf_token' => csrf_token(), 'registration_enabled' => config('links.registration_enabled')]);
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string', 'max:255']]);
        if (! Auth::attempt([...$data, 'guest' => false])) {
            throw ValidationException::withMessages(['email' => 'The email or password is incorrect.']);
        }
        $request->session()->regenerate();

        return $this->session($request);
    }

    public function register(Request $request)
    {
        abort_unless(config('links.registration_enabled'), 403, 'Registration is closed.');
        $data = $request->validate(['name' => ['required', 'string', 'max:80'], 'email' => ['required', 'email', 'max:255', 'unique:users'], 'password' => ['required', 'confirmed', 'max:255', Password::min(12)]]);
        $user = User::create([...$data, 'guest' => false]);
        Auth::login($user);
        $request->session()->regenerate();

        return $this->session($request)->setStatusCode(201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->session($request);
    }
}
