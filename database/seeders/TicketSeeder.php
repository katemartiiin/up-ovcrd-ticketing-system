<?php

namespace Database\Seeders;

use App\Models\Tickets\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticket = Ticket::create([
            'control_number' => '202401-0001',
            'office_id' => 2,
            'user_id' => 1,
            'process_id' => 2,
            'title' => 'DOST Realignment Meeting',
            'description' => 'Requesting realignment with DOST for sample agenda.'
        ]);

        $ticket->logs()->create([
            'title' => 'Ticket Created',
            'description' => 'Client One has created Ticket #' . $ticket->control_number . '.'
        ]);
    }
}
