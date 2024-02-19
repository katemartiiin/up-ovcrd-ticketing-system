<?php

namespace App\Console\Commands;

use App\Actions\SendEmailNotificationAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Models\Tickets\Ticket;
use App\Models\User;

class ResolveTicketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:resolve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command automatically updates the status of all completed tickets.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resolvedDate = Carbon::now()->subDays(3)->endOfDay();
        $tickets = Ticket::where('status', Ticket::STATUS_COMPLETED)->where('updated_at', '<', $resolvedDate)->get();

        foreach ($tickets as $ticket) {
            $ticket->status = Ticket::STATUS_RESOLVED;
            $ticket->save();

            $staff = User::findOrFail($ticket->staff_id);

            $data = [
                'user' => ['name' => $staff->name, 'email' => $staff->email],
                'notification' => 'Ticket #' . $ticket->control_number . ' has been resolved.',
                'link' => route('login')
            ];

            // Send email notification
            $action = new SendEmailNotificationAction;
            $action->execute($data);
        }

        Log::info(Carbon::now()->format('Y-m-d H:i a') . ' Tickets resolved successfully');

        $this->info(Carbon::now()->format('Y-m-d H:i a') . ' Tickets resolved successfully');
    }
}
