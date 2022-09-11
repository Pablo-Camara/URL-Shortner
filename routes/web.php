<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShortlinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/entrar', [HomeController::class, 'login'])->name('login-page');
Route::get('/criar-conta', [HomeController::class, 'register'])->name('register-page');
Route::get('/os-meus-links', [HomeController::class, 'myLinks'])->name('my-links-page');

Route::get('/confirmar-email/{token}', [HomeController::class, 'confirmEmail'])
        ->middleware(['email.confirm', 'auth:sanctum', 'abilities:confirm_email'])
        ->name('emailConfirmationLink');

Route::get('/alterar-palavra-passe/{token}', [HomeController::class, 'changePassword'])
        ->middleware(['password.change', 'auth:sanctum', 'abilities:change_password'])
        ->name('changePasswordLink');


/**
 * routes for Login with External services
 */

 /** Google */
Route::get('/auth/google/redirect', [AuthenticationController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthenticationController::class, 'googleCallback']);

 /** Facebook */
Route::get('/auth/facebook/redirect', [AuthenticationController::class, 'facebookRedirect']);
Route::get('/auth/facebook/callback', [AuthenticationController::class, 'facebookCallback']);

 /** Github */
Route::get('/auth/github/redirect', [AuthenticationController::class, 'githubRedirect']);
Route::get('/auth/github/callback', [AuthenticationController::class, 'githubCallback']);

 /** -- */


Route::get('/{shortstring}', [ShortlinkController::class, 'visit']);
