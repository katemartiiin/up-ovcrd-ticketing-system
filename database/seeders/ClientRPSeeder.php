<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClientRPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create([
            'name' => 'Client',
            'guard_name' => 'web'
        ]);

        $tickets = ['create', 'edit', 'delete', 'view'];

        foreach ($tickets as $ticket) {
            $permission = Permission::create([
                'name' => $ticket . ' ticket',
                'guard_name' => 'web'
            ]);

            $role->givePermissionTo($permission);
            $permission->assignRole($role);
        }
    }
}
