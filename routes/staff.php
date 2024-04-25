<?php

use Illuminate\Support\Facades\Route;

/* Staff Controllers */
use App\Http\Controllers\Staff\NotificationController;
use App\Http\Controllers\Staff\GeneralController;
use App\Http\Controllers\Staff\TicketController;

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
|
| Here is where you can register staff routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "staff" middleware group. Now create something great!
|
*/

/**
 * Staff System routes
 */
Route::middleware(['auth:staff', 'verified'])->prefix('staff')->group(function () {

    /* Dashboard Routes */
    Route::get('/dashboard', [GeneralController::class, 'dashboardPage'])->name('dashboard');

    /* About Page */
    Route::get('/about', [GeneralController::class, 'aboutPage'])->name('about');

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