<?php

namespace Database\Seeders;

use App\Models\Users\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = Role::findOrFail(1);
        $sa = Admin::create([
            'name' => 'Super Admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('super123'),
            'office_id' => 1,
            'role' => 1,
            'image_path' => 'https://placehold.co/500x500'
        ]);

        $sa->assignRole($superAdmin);
        $sa->syncPermissions($superAdmin->permissions);

        // Staff - RMO
        $staff = Role::findOrFail(1);
        $rmo = Admin::create([
            'name' => 'RMO Staff',
            'first_name' => 'RMO',
            'last_name' => 'Staff',
            'email' => 'rmo@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('staff123'),
            'office_id' => 2,
            'role' => 2,
            'image_path' => 'https://placehold.co/500x500'
        ]);

        $rmo->assignRole($staff);
        $rmo->syncPermissions($staff->permissions);

        // Staff - Admin
        $admin = Admin::create([
            'name' => 'Admin Staff',
            'first_name' => 'Admin',
            'last_name' => 'Staff',
            'email' => 'admin@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('staff123'),
            'office_id' => 3,
            'role' => 2,
            'image_path' => 'https://placehold.co/500x500'
        ]);

        $admin->assignRole($staff);
        $admin->syncPermissions($staff->permissions);

        // VCD - Director
        $director = Role::findOrFail(3);
        $dir = Admin::create([
            'name' => 'Director Staff',
            'first_name' => 'RMO',
            'last_name' => 'Director',
            'email' => 'dir@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('direc123'),
            'office_id' => 2,
            'role' => 3,
            'image_path' => 'https://placehold.co/500x500'
        ]);

        $dir->assignRole($director);
        $dir->syncPermissions($director->permissions);

        // VCD - Vice-Chancellor
        $vice = Role::findOrFail(4);
        $vc = Admin::create([
            'name' => 'Vice Chancellor',
            'first_name' => 'Vice',
            'last_name' => 'Chancellor',
            'email' => 'vc@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('vice123'),
            'office_id' => 1,
            'role' => 4,
            'image_path' => 'https://placehold.co/500x500'
        ]);

        $vc->assignRole($vice);
        $vc->syncPermissions($vice->permissions);
    }
}
