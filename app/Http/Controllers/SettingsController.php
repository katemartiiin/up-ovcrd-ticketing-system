<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Setting');
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch()
    {
        $settings = Setting::all();

        return response()->json([
            'items' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $settings = Setting::all();

        $newSettings = $request->settings;

        foreach ($newSettings as $setting) {
            $settings->where('name', $setting['name'])->first()->update([
                'value' => $setting['value']
            ]);
        }

        return response()->json(['message' => 'Settings successfully updated. Kindly refresh your browser to ensure your changes will reflect.']);
    }
}
