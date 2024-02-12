<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use Inertia\Inertia;

use App\Models\Users\Admin;
use App\Models\ActivityLog;
use App\Models\Notification;

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
        $activities = ActivityLog::where('user_id', auth()->user()->id)->where('type', Admin::class)->orderBy('created_at', 'desc')->limit(10)->get();
        $notifications = Notification::where('resource_id', auth()->user()->id)->where('type', Admin::class)->orderBy('created_at', 'desc')->limit(10)->get();

        return Inertia::render('Staff/Dashboard', [
            'activities' => $activities,
            'notifications' => $notifications
        ]);
    }
    
}
