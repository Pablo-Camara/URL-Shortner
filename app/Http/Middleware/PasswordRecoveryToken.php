<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PasswordRecoveryToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = urldecode($request->route('token'));
        $request->headers->set('Authorization', 'Bearer ' . $token);
        return $next($request);
    }
}
