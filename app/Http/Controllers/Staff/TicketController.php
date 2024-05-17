<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Inertia\Inertia;
use Exception;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Tickets\Ticket;
use App\Models\Tickets\TicketLog;
use App\Models\ResearchIds\ResearchIdUser;

use App\Http\Controllers\Controller;
use App\Actions\SendEmailNotificationAction;

use App\Http\Requests\TicketNoteRequest;
use App\Http\Requests\TicketTransferRequest;
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
        $offices = Office::where('id', '!=', 1)->where('id', '!=', $officeId)->get();

        return Inertia::render('Staff/Tickets/Index', [
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
        // Get user office id
        $officeId = auth()->user()->office_id;

        $tickets = Ticket::query();
        // Get tickets only from user's office
        $tickets = $tickets->where('office_id', $officeId);
        $tickets = $tickets->with('notes');

        switch ($request->tab) {
            case '0':
                $tickets = $tickets->where('staff_id', auth()->user()->id)->where('status', Ticket::STATUS_ACTIVE);
                break;
            case '1':
                $tickets = $tickets->where('status', Ticket::STATUS_ACTIVE);
                break;
            case '2':
                $tickets = $tickets->where('status', '!=', Ticket::STATUS_ACTIVE);
                break;
            default:
                break;
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

        $offices = Office::where('id', '!=', 1)->where('id', '!=', $officeId)->get();
        
        $ticket = Ticket::with('process', 'office', 'files', 'notes')->findOrFail($id);
        $logs = TicketLog::where('ticket_id', $id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('Staff/Tickets/Id', [
            'item' => $ticket,
            'logs' => $logs,
            'offices' => $offices
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
     * Transfer specified resource.
     */
    public function transfer(TicketTransferRequest $request)
    {
        try {
            DB::beginTransaction();
            $ticket = Ticket::with('office', 'client')->findOrFail($request->ticket_id);
            $office = Office::findOrFail($request->office_id);
    
            if ($office) {
                $ticket->logs()->create([
                    'title' => 'Ticket Transfer',
                    'description' => auth()->user()->name . ' has transferred ticket from '. $ticket->office->name .' to ' . $office->name
                ]);

                $ticket->update([
                    'staff_id' => null,
                    'office_id' => $office->id,
                    'process_id' => 1 // default process for transfer
                ]);

                ActivityLog::create([
                    'user_id' => auth()->user()->id,
                    'type' => User::class,
                    'action' => 'You have transferred Ticket #' . $ticket->control_number . '.'
                ]);

                Notification::create([
                    'resource_id' => $ticket->user_id,
                    'type' => User::class,
                    'description' => auth()->user()->name . ' has transferred ticket from '. $ticket->office->name .' to ' . $office->name
                ]);

                $data = [
                    'user' => ['name' => $ticket->client->name, 'email' => $ticket->client->email],
                    'notification' => 'Ticket #' . $ticket->control_number . ' has been transferred from '. $ticket->office->name .' to ' . $office->name,
                    'link' => route('login')
                ];
        
                // Send email notification
                $action = new SendEmailNotificationAction;
                $action->execute($data);

                $ticket->updated_at = now();
                $ticket->save();
            }
    
            DB::commit();

            return response()->json([
                'message' => 'Ticket successfully transferred.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    /**
     * Completed the specified resource from storage.
     */
    public function completed($id)
    {
        $ticket = Ticket::with('client')->findOrFail($id);
        $ticket->update(['status' => Ticket::STATUS_COMPLETED]);

        $ticket->logs()->create([
            'title' => 'Ticket Completed',
            'description' => 'Ticket #' . $ticket->control_number . ' was marked as completed by ' . auth()->user()->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'type' => User::class,
            'action' => 'You have completed Ticket #' . $ticket->control_number . '.'
        ]);

        Notification::create([
            'resource_id' => $ticket->user_id,
            'type' => User::class,
            'description' => auth()->user()->name . ' has completed your Ticket #' . $ticket->control_number
        ]);

        $data = [
            'user' => ['name' => $ticket->client->name, 'email' => $ticket->client->email],
            'notification' => 'Ticket #' . $ticket->control_number . ' has been marked as completed.',
            'link' => route('login')
        ];

        // Send email notification
        $action = new SendEmailNotificationAction;
        $action->execute($data);

        return response()->json([
            'message' => 'Ticket #' . $id . ' was successfully marked as completed.'
        ]);
    }

    /**
     * Claim a specified resource in storage
     */
    public function claim(Request $request)
    {
        $ticket = Ticket::findOrFail($request->id);
        
        if ($ticket->research_id != "") {
            $canClaim = ResearchIdUser::where('research_id', $ticket->research_id)->where('admin_id', auth()->user()->id)->first();

            if ($canClaim) {
                $this->addTicket(auth()->user(), $ticket);

                return response()->json(['message' => 'You have added #' . $ticket->control_number . ' to your tickets.']);
            } else {
                return response()->json([
                    'message' => 'You are not allowed to claim this ticket.'
                ], 403);
            }

        } else {
            $this->addTicket(auth()->user(), $ticket);
            
            return response()->json(['message' => 'You have added #' . $ticket->control_number . ' to your tickets.']);
        }
    }

    public function addTicket($user, $ticket)
    {
        $ticket->update(['staff_id' => $user->id]);

        ActivityLog::create([
            'user_id' => $user->id,
            'type' => User::class,
            'action' => 'You have added #' . $ticket->control_number . ' to your tickets.'
        ]);

        $ticket->logs()->create([
            'title' => 'Ticket Added',
            'description' => $user->name . ' has added #' . $ticket->control_number . ' to his/her tickets.'
        ]);
    }
}
