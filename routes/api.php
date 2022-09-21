<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ShortlinkController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/authenticate', [AuthenticationController::class, 'authenticationAttempt']);

Route::post('/login', [AuthenticationController::class, 'loginAttempt'])
        ->middleware(['auth:sanctum'])
        ->name('login-attempt');

Route::post('/logout', [AuthenticationController::class, 'logoutAttempt'])
        ->middleware(['auth:sanctum', 'abilities:logged_in'])
        ->name('logout-attempt');

Route::post('/register', [AuthenticationController::class, 'registerAttempt'])
        ->middleware(['auth:sanctum'])
        ->name('register-attempt');

Route::post('/resend-verification-email', [AuthenticationController::class, 'resendVerificationEmail'])
        ->middleware(['auth:sanctum'])
        ->name('resend-verification-email');

 Route::post('/recover-password', [AuthenticationController::class, 'recoverPassword'])
        ->middleware(['auth:sanctum'])
        ->name('recover-password');

Route::post('/change-password', [AuthenticationController::class, 'changePassword'])
        ->middleware(['auth:sanctum', 'abilities:change_password'])
        ->name('recover-password');

Route::post('/links', [ShortlinkController::class, 'myLinks'])
        ->middleware(['auth:sanctum'])
        ->name('get-my-links');

Route::middleware('auth:sanctum')->post('/shorten', [ShortlinkController::class, 'shorten']);
Route::middleware('auth:sanctum')->post('/register-available', [ShortlinkController::class, 'registerAvailable']);


Route::post('/shortlinks/edit', [ShortlinkController::class, 'editShortlinkUrl'])
        ->middleware(['auth:sanctum']);

Route::post('/shortlinks/delete', [ShortlinkController::class, 'deleteShortlinkUrl'])
        ->middleware(['auth:sanctum']);

Route::post('/stats', [StatisticsController::class, 'generic'])
        ->middleware(['auth:sanctum', 'abilities:admin']);;
