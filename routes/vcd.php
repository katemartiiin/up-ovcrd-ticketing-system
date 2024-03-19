<?php

use Illuminate\Support\Facades\Route;

/* Vcd Controllers */
use App\Http\Controllers\Vcd\NotificationController;
use App\Http\Controllers\Vcd\GeneralController;
use App\Http\Controllers\Vcd\ReportsController;
use App\Http\Controllers\Vcd\TicketController;

/*
|--------------------------------------------------------------------------
| VcD Routes
|--------------------------------------------------------------------------
|
| Here is where you can register vice-chancellor and director routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

/**
 * VCD System routes
 */
Route::middleware(['auth:vcd', 'verified'])->prefix('vcd')->group(function () {

    /* Dashboard Routes */
    Route::controller(GeneralController::class)->group(function() {
        Route::get('/dashboard', 'dashboardPage')->name('dashboard');
        Route::get('/about', 'aboutPage')->name('about');
        Route::post('/charts', 'fetchCharts')->name('charts');
        Route::post('/charts/filter', 'filter')->name('charts.filter');
    });

    /* Ticket Management Routes */
    Route::controller(TicketController::class)->prefix('tickets')->name('tickets.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/notes', 'fetchTicketNotes')->name('fetch.notes');
        Route::post('/note', 'note')->name('note');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::put('/{id}/assign', 'assign')->name('assign');
    });

    /* Notification Management Routes */
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/fetch', 'fetch')->name('fetch');
    });

    /* Report Routes */
    Route::controller(ReportsController::class)->prefix('reports')->name('reports.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/generate', 'generate')->name('generate');
    });
});