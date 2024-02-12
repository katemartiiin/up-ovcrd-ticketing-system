<?php

use Illuminate\Support\Facades\Route;

/* Staff Controllers */
use App\Http\Controllers\Staff\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Staff\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Staff\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Staff\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Staff\Auth\PasswordResetLinkController;
use App\Http\Controllers\Staff\Auth\RegisteredUserController;
use App\Http\Controllers\Staff\Auth\NewPasswordController;
use App\Http\Controllers\Staff\Auth\VerifyEmailController;
use App\Http\Controllers\Staff\Auth\PasswordController;

use App\Http\Controllers\Staff\NotificationController;
use App\Http\Controllers\Staff\GeneralController;
use App\Http\Controllers\Staff\ProfileController;
use App\Http\Controllers\Staff\TicketController;

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
|
| Here is where you can register staff routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

/**
 * Staff Routes
 */
Route::middleware('guest')->prefix('staff')->group(function () {
    /* Staff */
    Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
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
});

/**
 * Staff Auth routes
 */
Route::middleware('auth:admin')->prefix('staff')->group(function () {
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

    /* Dashboard Routes */
    Route::get('/dashboard', [GeneralController::class, 'dashboardPage'])->name('dashboard');

    /* Profile Routes */
    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'edit')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    /* Ticket Management Routes */
    Route::controller(TicketController::class)->prefix('tickets')->name('tickets.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/notes', 'fetchTicketNotes')->name('fetch.notes');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/note', 'note')->name('note');
        Route::post('/transfer', 'transfer')->name('transfer');
        Route::patch('/{id}/claim', 'claim')->name('claim');
        Route::patch('/{id}/completed', 'completed')->name('completed');
    });

    /* Notification Management Routes */
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/fetch', 'fetch')->name('fetch');
    });
});