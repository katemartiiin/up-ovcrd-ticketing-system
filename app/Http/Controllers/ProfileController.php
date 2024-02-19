<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\User;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = User::findOrFail(auth()->user()->id);

        return Inertia::render('Profile', [
            'user' => $user,
            'status' => session('status')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name
        ]);

        if ($request->has('avatar')) {
            $file = $request->avatar;
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('public/avatars', $fileName);

            $user->update([
                'image_path' => $path,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have updated your profile.'
        ]);

        return response()->json(['message' => 'Your profile was successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
