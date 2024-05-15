<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;

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
        $notifications = Notification::where('resource_id', auth()->user()->id)->orderBy('created_at', 'desc')->limit(10)->get();
        $activities = ActivityLog::where('resource_id', auth()->user()->id)->orderBy('created_at', 'desc')->limit(10)->get();

        return Inertia::render('Client/Dashboard', [
            'notifications' => $notifications,
            'activities' => $activities
        ]);
    }
    
}
