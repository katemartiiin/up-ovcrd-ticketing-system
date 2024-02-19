<?php

namespace App\Http\Controllers\Vcd;

use App\Actions\SendEmailNotificationAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Exception;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Tickets\Ticket;
use App\Models\Tickets\TicketLog;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketNoteRequest;
use App\Models\Tickets\TicketNote;

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

        $staffs = User::where('office_id', $officeId)->where('role', User::ROLE_STAFF)->get();

        return Inertia::render('Vcd/Tickets/Index', [
            'office' => $office,
            'offices' => $offices,
            'staffs' => $staffs
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

    public function fetchTicketNotes($id)
    {
        $notes = TicketNote::where('ticket_id', $id)->orderBy('created_at', 'desc')->get();

        return response()->json(['notes' => $notes]);
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
        // Get user office id
        $officeId = auth()->user()->office_id;

        $offices = Office::where('id', '!=', $officeId)->get();
        
        $ticket = Ticket::with('process', 'office', 'files', 'notes')->findOrFail($id);
        $logs = TicketLog::where('ticket_id', $id)->orderBy('created_at', 'desc')->get();

        $staffs = User::where('office_id', $officeId)->where('role', User::ROLE_STAFF)->get();

        return Inertia::render('Vcd/Tickets/Id', [
            'item' => $ticket,
            'logs' => $logs,
            'offices' => $offices,
            'staffs' => $staffs
        ]);
    }

    /**
     * Add note to ticket
     */
    public function note(TicketNoteRequest $request)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::with('client')->findOrFail($request->ticket_id);
            $ticket->notes()->create([
                'note' => $request->note,
                'author_id' => auth()->user()->id,
                'author_model' => User::class
            ]);

            if ($request->has('files')) {

                foreach ($request->file('files') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileNameToStore = time() . '_' . $fileName;
                    $path = $file->storeAs('public/tickets', $fileNameToStore);

                    $ticket->files()->create([
                        'name' => $fileName,
                        'path' => $path
                    ]);
                }
            }

            $ticket->logs()->create([
                'title' => 'Ticket Note',
                'description' => auth()->user()->name . ' has added a note for Ticket #' . $ticket->control_number
            ]);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => User::class,
                'action' => 'You added a note for Ticket #' . $ticket->control_number . '.'
            ]);

            Notification::create([
                'resource_id' => $ticket->user_id,
                'type' => User::class,
                'description' => 'A note was added for Ticket #' . $ticket->control_number . '.'
            ]);

            $data = [
                'user' => ['name' => $ticket->client->name, 'email' => $ticket->client->email],
                'notification' => 'A note was added for Ticket #' . $ticket->control_number . '.',
                'link' => route('login')
            ];

            $ticket->update(['updated_at' => now()]);
    
            // Send email notification
            $action = new SendEmailNotificationAction;
            $action->execute($data);

            $ticket->updated_at = now();
            $ticket->save();

            DB::commit();

            return response()->json([
                'message' => 'Your note was successfully added to the ticket.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
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

            $staff = User::findOrFail($request->staff_id);

            $ticket->logs()->create([
                'title' => 'Ticket Assigned',
                'description' => auth()->user()->name . ' has assigned Ticket #' . $ticket->control_number . ' to ' . $staff->name
            ]);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => User::class,
                'action' => 'You assigned Ticket #' . $ticket->control_number . ' to ' . $staff->name . '.'
            ]);

            Notification::create([
                'resource_id' => $staff->id,
                'type' => User::class,
                'description' => 'Ticket #' . $ticket->control_number . ' was assigned to you.'
            ]);

            $data = [
                'user' => ['name' => $staff->name, 'email' => $staff->email],
                'notification' => 'Ticket #' . $ticket->control_number . ' was assigned to you.',
                'link' => route('login')
            ];

            // Send email notification
            $action = new SendEmailNotificationAction;
            $action->execute($data);

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
