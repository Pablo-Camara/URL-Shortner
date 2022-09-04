<?php

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
Route::get('/login', [HomeController::class, 'login'])->name('login');

Route::get('/confirm-email/{token}', [HomeController::class, 'confirmEmail'])
        ->middleware(['email.confirm', 'auth:sanctum', 'abilities:confirm_email'])
        ->name('emailConfirmationLink');

Route::get('/{shortstring}', [ShortlinkController::class, 'visit']);
