<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Notification;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Client/Notification');
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $options = $request->options;
        $notifications = Notification::where('resource_id', auth()->user()->id)->paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json([
            'items' => $notifications
        ]);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
