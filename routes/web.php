<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::view('/', 'app');
Route::view('/app', 'app')->name('login');
Route::prefix('api')->group(function () {
    Route::get('/session', [AccountController::class, 'session']);
    Route::post('/login', [AccountController::class, 'login'])->middleware('throttle:account');
    Route::post('/register', [AccountController::class, 'register'])->middleware('throttle:account');
    Route::post('/logout', [AccountController::class, 'logout'])->middleware('auth');
    Route::middleware(['auth', 'throttle:manage'])->group(function () {
        Route::get('/stats', [LinkController::class, 'stats']);
        Route::get('/links', [LinkController::class, 'index']);
        Route::post('/links', [LinkController::class, 'store']);
        Route::get('/links/{link}', [LinkController::class, 'show']);
        Route::patch('/links/{link}', [LinkController::class, 'update']);
        Route::patch('/links/{link}/status', [LinkController::class, 'status']);
    });
});
// Keep redirect reads stateless: analytics never creates a visitor session or cookie.
Route::get('/{alias}', RedirectController::class)->where('alias', '[a-zA-Z0-9-]+')->withoutMiddleware([
    VerifyCsrfToken::class,
    StartSession::class,
    AddQueuedCookiesToResponse::class,
    ShareErrorsFromSession::class,
]);
