<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthProviderController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register auth routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::middleware('guest')->controller(AuthProviderController::class)->group(function () {
    /* Login */
    Route::get('/', 'login')->name('login');

    Route::get('auth/google/redirect', 'redirect')->name('google.redirect');
    Route::get('auth/google/callback', 'callback')->name('google.callback');
});

Route::controller(AuthProviderController::class)->group(function() {
    /* Profile routes */
    Route::middleware(['auth:admin,staff,vcd,web', 'verified'])->controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function() {
        Route::get('/edit', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
    });

    /* Log out routes */
    Route::middleware(['auth', 'verified'])->post('logout', 'logout')->name('logout');
    Route::middleware(['auth:admin,staff,vcd', 'verified'])->post('{type}/logout', 'logout')->name('admin.logout');
});