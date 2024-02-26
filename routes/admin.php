<?php

use Illuminate\Support\Facades\Route;

/* Admin Controllers */
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ResearchIdController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

/**
 * Admin System Routes
 */
Route::middleware(['auth:admin', 'verified'])->group(function() {

    /* Dashboard Routes */
    Route::get('/dashboard', [GeneralController::class, 'dashboardPage'])->name('dashboard');

    /* Research Id Management Routes */
    Route::controller(ResearchIdController::class)->prefix('research')->name('research.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/staffs', 'fetchStaff')->name('staffs');
        Route::get('/{id}/available', 'fetchAvailable')->name('available');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/store', 'store')->name('store');
        Route::post('/{id}/update', 'update')->name('update');
        Route::patch('/{id}/toggle', 'toggle')->name('toggle');
    });

    /* Office Management Routes */
    Route::controller(OfficeController::class)->prefix('offices')->name('offices.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::get('/create', 'create')->name('create');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/store', 'store')->name('store');
        Route::post('/{id}/update', 'update')->name('update');
        Route::patch('/{id}/activate', 'toggleActivate')->name('activate');
    });

    /* User Management Routes */
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::get('/create', 'create')->name('create');
        Route::post('/fetch', 'fetch')->name('fetch');
        Route::post('/store', 'store')->name('store');
        Route::post('/{id}/update', 'update')->name('update');
        Route::patch('/{id}/activate', 'toggleActivate')->name('activate');
    });

    /* Notification Management Routes */
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/fetch', 'fetch')->name('fetch');
    });

});