<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

use App\Models\Office;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Tickets\Ticket;
use App\Models\Tickets\TicketLog;
use App\Models\Tickets\TicketNote;
use App\Models\ResearchIds\ResearchId;
use App\Models\ResearchIds\ResearchIdUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketNoteRequest;

use App\Actions\SendEmailNotificationAction;
use App\Actions\GenerateControlNumAction;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::with('processes')->get();
        $researchIds = ResearchId::with('office.processes')->where('client_id', auth()->user()->id)->where('status', ResearchId::STATUS_ACTIVE)->get();

        return Inertia::render('Client/Tickets/Index', [
            'ticketOffices' => $offices,
            'researchIds' => $researchIds
        ]);
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $tickets = Ticket::query();
        $tickets = $tickets->with('process', 'office', 'notes')->where('user_id', auth()->user()->id);

        $options = $request->options;

        if ($request->keyword != "") {
            $tickets = $tickets->where('control_number', 'LIKE', '%' . $request->keyword . '%');
        }
        
        if ($request->office_id != "") {
            $tickets = $tickets->where('office_id', $request->office_id);
        }

        if ($request->process_id != "") {
            $tickets = $tickets->where('process_id', $request->process_id);
        }

        if ($request->status != "") {
            $tickets = $tickets->where('status', $request->status);
        } else {
            $tickets = $tickets->where('status', '!=', Ticket::STATUS_RESOLVED);
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
    public function store(TicketRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $action = new GenerateControlNumAction;
            $controlNumber = $action->execute();

            $ticket = Ticket::create([
                'control_number' => $controlNumber,
                'office_id' => $request->office_id,
                'user_id' => auth()->user()->id,
                'process_id' => $request->process_id,
                'research_id' => $request->research_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 0 // Default Active
            ]);

            if ($request->has('files')) {

                foreach ($request->file('files') as $file) {
                    $fileName = $file->getClientOriginalName();
                    // $extension = $file->getClientOriginalExtension();
                    $fileNameToStore = time() . '_' . $fileName;
                    $path = $file->storeAs('public/tickets', $fileNameToStore);

                    $ticket->files()->create([
                        'name' => $fileName,
                        'path' => $path
                    ]);
                }
            }

            $ticket->logs()->create([
                'title' => 'Ticket Created',
                'description' => auth()->user()->name . ' has created Ticket #' . $ticket->control_number . '.'
            ]);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => User::class,
                'action' => 'You have created Ticket #'. $ticket->control_number .'.'
            ]);

            $sectionStaffs = User::where('office_id', $ticket->office_id)->where('role', User::ROLE_STAFF)->get();

            if (count($sectionStaffs)) {
                foreach ($sectionStaffs as $staff) {
                    $this->newTicketNotify($ticket, $staff);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Your ticket was created successfully.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    /**
     * Add note to ticket
     */
    public function note(TicketNoteRequest $request)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($request->ticket_id);
            $ticket->notes()->create([
                'note' => $request->note,
                'author_id' => auth()->user()->id,
                'author_model' => User::class
            ]);

            if ($request->has('files')) {

                foreach ($request->file('files') as $file) {
                    $fileName = $file->getClientOriginalName();
                    // $extension = $file->getClientOriginalExtension();
                    $fileNameToStore = time() . '_' . $fileName;
                    $path = $file->storeAs('public/tickets', $fileNameToStore);

                    $ticket->files()->create([
                        'name' => $fileName,
                        'path' => $path
                    ]);
                }
            }

            if ($ticket->staff_id != "") {
                Notification::create([
                    'type' => User::class,
                    'resource_id' => $ticket->staff_id,
                    'description' => auth()->user()->name . ' has added a note for your Ticket #' . $ticket->control_number . '.'
                ]);
            }

            $ticket->logs()->create([
                'title' => 'Ticket Note',
                'description' => auth()->user()->name . ' has added a note for Ticket #' . $ticket->control_number
            ]);

            ActivityLog::create([
                'user_id' => auth()->user()->id,
                'type' => User::class,
                'action' => 'You have added a note for Ticket #'. $ticket->control_number .'.'
            ]);

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
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::with('process', 'office', 'files', 'notes')->findOrFail($id);
        $logs = TicketLog::where('ticket_id', $id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('Client/Tickets/Id', [
            'item' => $ticket,
            'logs' => $logs
        ]);
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
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $ticket = Ticket::withTrashed()->findOrFail($id);
        $ticket->restore();

        $ticket->logs()->create([
            'title' => 'Ticket restored',
            'description' => 'Ticket #' . $id . ' was restored by ' . auth()->user()->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You restored Ticket #'. $ticket->control_number .'.'
        ]);

        return response()->json([
            'message' => 'Ticket #' . $ticket->control_number . ' was successfully restored.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        $ticket->logs()->create([
            'title' => 'Ticket removed',
            'description' => 'Ticket #' . $id . ' was removed by ' . auth()->user()->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You removed Ticket #'. $ticket->control_number .'.'
        ]);

        return response()->json([
            'message' => 'Ticket #' . $ticket->control_number . ' was successfully removed.'
        ]);
    }

    /**
     * Resolve the specified resource from storage.
     */
    public function resolve($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => Ticket::STATUS_RESOLVED]);

        $ticket->logs()->create([
            'title' => 'Ticket Resolved',
            'description' => 'Ticket #' . $ticket->control_number . ' was marked as resolved by ' . auth()->user()->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have resolved Ticket #'. $ticket->control_number .'.'
        ]);

        $staff = User::findOrFail($ticket->staff_id);

        $data = [
            'user' => ['name' => $staff->name, 'email' => $staff->email],
            'notification' => 'Ticket #' . $ticket->control_number . ' has been resolved.',
            'link' => route('login')
        ];

        // Send email notification
        $action = new SendEmailNotificationAction;
        $action->execute($data);

        return response()->json([
            'message' => 'Ticket #' . $ticket->control_number . ' was successfully marked as resolved.'
        ]);
    }

    /**
     * Follow-up the specified resource from storage
     */
    public function follow($id)
    {
        $ticket = Ticket::findOrFail($id);
        
        if ($ticket->staff_id != "") {

            $staff = User::findOrFail($ticket->staff_id);
            $this->followUpNotify($ticket, $staff);

        } else {
            if ($ticket->research_id != "") {
                $researchStaffs = ResearchIdUser::where('research_id', $ticket->research_id)->pluck('admin_id');
                $staffs = User::whereIn('id', $researchStaffs)->get();

                foreach ($staffs as $staff) {
                    $this->followUpNotify($ticket, $staff);
                }
            }
        }

        $ticket->logs()->create([
            'title' => 'Ticket Follow-up',
            'description' => 'Ticket #' . $ticket->control_number . ' has requested follow-up.'
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You requested a follow-up for Ticket #'. $ticket->control_number .'.'
        ]);

        return response()->json(['message' => 'Follow-up for your ticket has been sent.']);
    }

    public function followUpNotify($ticket, $staff)
    {
        Notification::create([
            'type' => User::class,
            'resource_id' => $staff->id,
            'description' => 'Ticket #' . $ticket->control_number . ' has requested a follow-up.'
        ]);

        $data = [
            'user' => ['name' => $staff->name, 'email' => $staff->email],
            'notification' => 'Ticket #' . $ticket->control_number . ' has requested a follow-up.',
            'link' => route('login')
        ];

        // Send email notification
        $action = new SendEmailNotificationAction;
        $action->execute($data);
    }

    public function newTicketNotify($ticket, $staff)
    {
        $data = [
            'user' => ['name' => $staff->name, 'email' => $staff->email],
            'notification' => 'Ticket #' . $ticket->control_number . ' has been created.',
            'link' => route('login')
        ];

        // Send email notification
        $action = new SendEmailNotificationAction;
        $action->execute($data);
    }
}
