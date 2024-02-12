<?php

namespace App\Http\Controllers\Vcd;

use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\Office;
use App\Models\Tickets\Ticket;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Users\Admin;
use Exception;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get user office id
        $officeId = auth()->user()->office_id;

        $office = Office::with('processes')->findOrFail($officeId);
        $offices = Office::where('id', '!=', $officeId)->get();

        return Inertia::render('Vcd/Tickets/Index', [
            'office' => $office,
            'offices' => $offices
        ]);
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $options = $request->options;
        $tickets = Ticket::query();

        $user = auth()->user();

        if ($user->role == 3) {
            $tickets = $tickets->where('office_id', $user->office_id);
        }

        if ($request->trashed) {
            $tickets = $tickets->whereIn('status', [Ticket::STATUS_COMPLETED, Ticket::STATUS_RESOLVED]);
        } else {
            $tickets = $tickets->where('status', Ticket::STATUS_ACTIVE);
        }

        if ($request->keyword != "") {
            $tickets = $tickets->where('control_number', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->process_id != "") {
            $tickets = $tickets->where('process_id', $request->process_id);
        }

        $tickets = $tickets->paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json([
            'items' => $tickets
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
    public function show($id)
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

    /**
     * Assign the specified resource from storage to the specified staff.
     */
    public function assign(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($id);
            $ticket->update([
                'staff_id' => $request->staff_id
            ]);

            $staff = Admin::findOrFail($request->staff_id);

            $ticket->logs()->create([
                'title' => 'Ticket Assigned',
                'description' => auth()->user()->name . ' has assigned Ticket #' . $ticket->control_number . ' to ' . $staff->name
            ]);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => Admin::class,
                'action' => 'You assigned Ticket #' . $ticket->control_number . ' to ' . $staff->name . '.'
            ]);

            Notification::create([
                'resource_id' => $staff->id,
                'type' => Admin::class,
                'description' => 'Ticket #' . $ticket->control_number . ' was assigned to you.'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'You have successfully assigned this ticket.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

}
