<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::create([
            'name' => 'Clients',
            'short_code' => 'Clients',
            'status' => 1
        ]);

        Office::create([
            'name' => 'Research Management Office',
            'short_code' => 'RMO',
            'status' => 1
        ]);

        Office::create([
            'name' => 'Administrative Unit',
            'short_code' => 'Admin',
            'status' => 1
        ]);
    }
}
