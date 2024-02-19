<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tickets
        $tickets = ['create', 'edit', 'delete', 'view'];
        // Users
        $users = ['create', 'edit', 'delete', 'restore', 'view'];
        // Offices
        $offices = ['create', 'edit', 'delete', 'restore', 'view'];
        // Reports
        $reports = ['create', 'delete', 'view'];

        // Roles
        $admin = Role::where('name', 'Admin')->first();
        $staff = Role::where('name', 'Staff')->first();
        $director = Role::where('name', 'Director')->first();
        $vc = Role::where('name', 'Vice-Chancellor')->first();

        foreach ($tickets as $ticket) {
            $permission = Permission::create([
                'name' => $ticket . ' ticket',
                'guard_name' => 'admin'
            ]);

            $staff->givePermissionTo($permission);
            $permission->assignRole($staff);

            $director->givePermissionTo($permission);
            $permission->assignRole($director);
        }

        foreach ($users as $user) {
            $permission = Permission::create([
                'name' => $user . ' user',
                'guard_name' => 'admin'
            ]);

            $admin->givePermissionTo($permission);
            $permission->assignRole($admin);
        }

        foreach ($offices as $office) {
            $permission = Permission::create([
                'name' => $office . ' office',
                'guard_name' => 'admin'
            ]);

            $admin->givePermissionTo($permission);
            $permission->assignRole($admin);
        }

        foreach ($reports as $report) {
            $permission = Permission::create([
                'name' => $report . ' report',
                'guard_name' => 'admin'
            ]);

            $director->givePermissionTo($permission);
            $permission->assignRole($director);

            $vc->givePermissionTo($permission);
            $permission->assignRole($vc);
        }

        

    }
}
