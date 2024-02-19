<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use Inertia\Inertia;

use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    // Go to Default page
    public function defaultPage()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    // Go to Dashboard
    public function dashboardPage()
    {
        return Inertia::render('Client/Dashboard');
    }
    
}
