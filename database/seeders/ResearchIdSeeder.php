<?php

namespace Database\Seeders;

use App\Models\ResearchIds\ResearchId;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearchIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $researchId = ResearchId::create([
            'research_code' => 'RS0001',
            'office_id' => 2
        ]);

        $researchId->staffs()->create([
            'admin_id' => 4
        ]);

        $researchId->staffs()->create([
            'admin_id' => 2
        ]);

        $researchId = ResearchId::create([
            'research_code' => 'RS0002',
            'office_id' => 2
        ]);

        $researchId->staffs()->create([
            'admin_id' => 3
        ]);
    }
}
