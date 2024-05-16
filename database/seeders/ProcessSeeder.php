<?php

namespace Database\Seeders;

use App\Models\Processes\Process;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Process::create([
            'office_id' => 2,
            'name' => 'RMO Ticket Transfer',
            'description' => 'Transferring ticket'
        ]);
        
        Process::create([
            'office_id' => 2,
            'name' => 'Request for Realignment',
            'description' => 'Requesting for realignment meeting'
        ]);

        Process::create([
            'office_id' => 3,
            'name' => 'Admin Ticket Transfer',
            'description' => 'Transferring ticket'
        ]);
        
        Process::create([
            'office_id' => 3,
            'name' => 'Request for System Update',
            'description' => 'Requesting for system update'
        ]);
    }
}
