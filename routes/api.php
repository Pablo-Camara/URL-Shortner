<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ShortlinkController;
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
/*
Route::post('/login', [AuthenticationController::class, 'loginAttempt'])
        ->middleware(['auth:sanctum'])
        ->name('login-attempt');*/

Route::middleware('auth:sanctum')->post('/shorten', [ShortlinkController::class, 'store']);
