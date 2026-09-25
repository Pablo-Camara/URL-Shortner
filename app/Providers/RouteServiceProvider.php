<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/app';

    public function boot(): void
    {
        RateLimiter::for('account', fn (Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('manage', fn (Request $r) => Limit::perMinute(120)->by($r->user()?->id ?? $r->ip()));
        $this->routes(fn () => Route::middleware('web')->group(base_path('routes/web.php')));
    }
}
