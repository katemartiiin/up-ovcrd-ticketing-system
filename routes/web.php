<?php

use App\Http\Controllers\Client\GeneralController;
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

/* Client Controllers */
use App\Http\Controllers\Client\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Client\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Client\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Client\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Client\Auth\NewPasswordController;
use App\Http\Controllers\Client\Auth\PasswordController;
use App\Http\Controllers\Client\Auth\PasswordResetLinkController;
use App\Http\Controllers\Client\Auth\RegisteredUserController;
use App\Http\Controllers\Client\Auth\VerifyEmailController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\TicketController;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
|
| Here is where you can register client routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    /* Client Guest */
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('/', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');

    Route::get('auth/google', [GoogleController::class, 'signInwithGoogle'])->name('google.auth');
    Route::get('callback/google', [GoogleController::class, 'callbackToGoogle'])->name('google.callback');
});

Route::middleware(['auth', 'verified'])->group(function() {

    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    /* Profile Routes */
    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'edit')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    /* Dashboard Routes */
    Route::get('/dashboard', [GeneralController::class, 'dashboardPage'])->name('dashboard');

    /* Notification Management Routes */
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/fetch', 'fetch')->name('fetch');
    });

    /* Ticket Management Routes */
    Route::controller(TicketController::class)->prefix('tickets')->name('tickets.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/{id}/notes', 'fetchTicketNotes')->name('fetch.notes');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/store', 'store')->name('store');
        Route::post('/note', 'note')->name('note');
        Route::patch('/{id}/follow', 'follow')->name('follow');
        Route::patch('/{id}/resolve', 'resolve')->name('resolve');
        Route::patch('/{id}/restore', 'restore')->name('restore');
        Route::delete('/{id}/delete', 'destroy')->name('destroy');
    });

});

// require __DIR__.'/auth.php';
