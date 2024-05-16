<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use App\Models\ResearchIds\ResearchId;
use App\Models\ResearchIds\ResearchIdUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResearchIdRequest;

class ResearchIdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::where('id', '!=', 1)->get(); // Get all sections except client
        $clients = User::where('role', User::ROLE_CLIENT)->where('status', User::STATUS_ACTIVE)->get();

        return Inertia::render('Admin/Research', [
            'offices' => $offices,
            'clients' => $clients
        ]);
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $options = $request->options;
        $research = ResearchId::query();
        
        if ($request->office_id != "") {
            $research = $research->where('office_id', $request->office_id);
        }

        if ($request->keyword != "") {
            $research = $research->where('research_code', 'LIKE', '%' . $request->keyword . '%');
        }

        $research = $research->paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json(['items' => $research]);
    }

    /**
     * Fetch users of the specified resource in storage.
     */
    public function fetchStaff($id)
    {
        $staffs = ResearchIdUser::with('staff')->where('research_id', $id)->get();

        return response()->json([
            'staffs' => $staffs
        ]);
    }

    /**
     * Get users not included in specified resource
     */
    public function fetchAvailable($id)
    {
        $researchIdUsers = ResearchIdUser::where('research_id', $id)->get();

        $admins = User::whereIn('role', [User::ROLE_STAFF, User::ROLE_DIRECTOR])->whereNotIn('id', $researchIdUsers->pluck('admin_id')->toArray())->get();

        return response()->json(['staffs' => $admins]);
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
    public function store(ResearchIdRequest $request)
    {
        $researchId = ResearchId::create([
            'research_code' => $request->code,
            'office_id' => $request->office_id,
            'client_id' => $request->client_id,
            'status' => ResearchId::STATUS_ACTIVE // default
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have created a Research Id. (#'.$researchId->research_code.')'
        ]);

        return response()->json([
            'message' => 'Research Id successfully created.'
        ]);
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
    public function update(Request $request, $id)
    {
        $researchId = ResearchId::findOrFail($id);

        if ($request->function == 'add') {
            $researchId->staffs()->create([
                'admin_id' => $request->add_id
            ]);
        } else {
            ResearchIdUser::findOrFail($request->remove_id)->delete();
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have updated a Research Id. (#'.$researchId->research_code.')'
        ]);

        return response()->json([
            'message' => 'Research Id successfully updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Toggle status of the specified resource from storage.
     */
    public function toggle($id)
    {
        $research = ResearchId::findOrFail($id);
        $research->status = !$research->status;
        $research->save();

        return response()->json(['message' => 'Status updated successfully.']);
    }

}
