<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $route = null;
        if ($request->expectsJson()) {
            $route = null;
        } else {
            switch ($request->route()->getName()) {
                case 'staff.dashboard':
                case 'staff.tickets.index':
                case 'staff.tickets.show':
                case 'staff.profile.edit':
                case 'staff.notifications.index':
                    $route = route('staff.login');
                    break;
                case 'admin.dashboard':
                case 'admin.research.index':
                case 'admin.offices.index':
                case 'admin.offices.create':
                case 'admin.offices.edit':
                case 'admin.users.index':
                case 'admin.users.create':
                case 'admin.users.edit':
                case 'admin.users.show':
                case 'admin.profile.edit':
                case 'admin.notifications.index':
                    $route = route('admin.login');
                    break;
                case 'vcd.dashboard':
                case 'vcd.profile.edit':
                case 'vcd.reports.index':
                case 'vcd.tickets.index':
                case 'vcd.notifications.index':
                    $route = route('vcd.login');
                    break;
                default:
                    $route = route('login');
                    break;
            }
        }
        return $route;
        // return $request->expectsJson() ? null : route('login');
    }
}
