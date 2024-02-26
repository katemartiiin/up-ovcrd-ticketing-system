<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

use App\Models\ActivityLog;
use App\Models\User;

use App\Providers\RouteServiceProvider;


class AuthProviderController extends Controller
{
    public function login()
    {
        return Inertia::render('Login', [
            'status' => session('status')
        ]);
    }

    /**
     * Log out an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $guard = "web";

        if ($request->route()->getName() == 'admin.logout') {
            $guard = $request->type;
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have logged out.'
        ]);

        Auth::guard($guard)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirect()
    {
        session(['status' => null]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $route = "";
        $guard = "";

        $googleUser = Socialite::driver('google')->user();

        // if (!str_contains($googleUser->email, 'up.edu.ph')) {
        //     session(['status' => 'Please use your UP email to login.']);
        //     return redirect()->route('login');
        // }

        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {

            if ($user->status == User::STATUS_INACTIVE) {
                session(['status' => 'Your account has been deactivated. Kindly contact admin if you need any assistance.']);
                return redirect()->route('login');
            }

            switch($user->role) {
                case User::ROLE_ADMIN:
                    $route = RouteServiceProvider::ADMIN_HOME;
                    $guard = "admin";
                    break;
                case User::ROLE_STAFF:
                    $route = RouteServiceProvider::STAFF_HOME;
                    $guard = "staff";
                    break;
                case User::ROLE_DIRECTOR:
                case User::ROLE_VC:
                    $route = RouteServiceProvider::VCD_HOME;
                    $guard = "vcd";
                    break;
                case User::ROLE_CLIENT:
                    $route = RouteServiceProvider::HOME;
                    $guard = "web";
                    break;
                default:
                    $route = RouteServiceProvider::HOME;
                    $guard = "web";
                    break;
            }

            Auth::guard($guard)->login($user);

        } else {

            $user = User::create([
                'name' => $googleUser->name,
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'google_avatar' => $googleUser->avatar,
                'role' => User::ROLE_CLIENT
            ]);

            Auth::guard('web')->login($user);
            $route = RouteServiceProvider::HOME;
        }
        return redirect()->intended($route);
    }

}
