<?php


use Illuminate\Support\Facades\Route;

/* Client Controllers */
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\GeneralController;

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

Route::middleware(['auth', 'verified'])->group(function() {

    /* Dashboard Routes */
    Route::get('/dashboard', [GeneralController::class, 'dashboardPage'])->name('dashboard');
    /* About Page */
    Route::get('/about', [GeneralController::class, 'aboutPage'])->name('about');

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

require __DIR__.'/auth.php';
