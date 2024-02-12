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
            'name' => 'No Office',
            'short_code' => 'Assign Office'
        ]);

        Office::create([
            'name' => 'Research Management Office',
            'short_code' => 'RMO'
        ]);

        Office::create([
            'name' => 'Administrative Unit',
            'short_code' => 'Admin'
        ]);
    }
}
