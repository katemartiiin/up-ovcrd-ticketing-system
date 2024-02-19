<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\Office;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Processes\Process;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeRequest;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Offices/Index');
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $options = $request->options;

        $offices = Office::query();

        if ($request->keyword != "") {
            $offices = $offices->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->status != "") {
            $offices = $offices->where('status', $request->status);
        }

        $offices = $offices->paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json(['items' => $offices]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admins = User::where('role', '!=', User::ROLE_CLIENT)->where('status', User::STATUS_ACTIVE)->get();

        return Inertia::render('Admin/Offices/Create', [
            'users' => $admins
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfficeRequest $request)
    {
        $office = Office::create($request->except('users'));
        $addedUsers = $request->users;
        $addedProcess = $request->processes;
        $admins = User::where('role', '!=', User::ROLE_CLIENT)->where('status', User::STATUS_ACTIVE)->get();

        if (count($addedUsers)) {
            foreach ($addedUsers as $user) {
                $changeUser = $admins->where('id', $user['id'])->first();
                $changeUser->update(['office_id' => $office->id]);
            }
        }

        // First process - always for ticket transfer
        Process::create([
            'office_id' => $office->id,
            'name' => $request->short_code . ' Ticket Transfer',
            'description' => 'Transferring ticket'
        ]);

        if (count($addedProcess)) {
            foreach ($addedProcess as $process) {
                Process::create([
                    'name' => $process,
                    'office_id' => $office->id
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have created a section (#' . $office->id . ').'
        ]);

        return response()->json([
            'message' => 'Office successfully created.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $admins = User::where('role', '!=', User::ROLE_CLIENT)->where('status', User::STATUS_ACTIVE)->get();
        $office = Office::with('users', 'processes')->findOrFail($id);

        return Inertia::render('Admin/Offices/Edit', [
            'item' => $office,
            'users' => $admins
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);
        $office->update($request->except('users'));

        $addedUsers = $request->users;
        $addedProcess = $request->added_process;
        $admins = User::where('role', '!=', User::ROLE_CLIENT)->get();
        $processes = Process::where('office_id', $id)->get();

        $removedUsers = $request->removed_users;
        $removedProcess = $request->removed_process;

        if (count($addedUsers)) {
            foreach ($addedUsers as $user) {
                $changeUser = $admins->where('id', $user['id'])->first();
                $changeUser->update(['office_id' => $office->id]);
            }
        }

        if (count($removedUsers)) {
            foreach ($removedUsers as $user) {
                $changeUser = $admins->where('id', $user['id'])->first();
                $changeUser->update(['office_id' => 1]); // default office
            }
        }

        if (count($removedProcess)) {
            foreach ($removedProcess as $process) {
                $remove = $processes->where('id', $process['id'])->first();
                $remove->delete();
            }
        }

        if (count($addedProcess)) {
            foreach ($addedProcess as $process) {
                Process::create([
                    'name' => $process,
                    'office_id' => $id
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have updated a section (#' . $id . ').'
        ]);

        return response()->json([
            'message' => 'Section was successfully updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Activate / Deactivate the specified resource from storage.
     * 
     */
    public function toggleActivate($id)
    {
        $office = Office::findOrFail($id);
        $office->status = !$office->status;
        $office->save();

        $status = $office->status ? 'activated' : 'deactivated';

        User::where('office_id', $id)->update(['status' => $office->status]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have ' . $status . ' section #' . $id . '.'
        ]);

        return response()->json([
            'message' => 'Section #' . $id . ' was successfully '. $status .'.'
        ]);
    }
}
