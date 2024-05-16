<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

use App\Models\User;

class SuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set first user as super admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $superAdmin = Role::findOrFail(1);
        $sa = User::findOrFail(1);

        $sa->role = User::ROLE_ADMIN;
        $sa->office_id = null;
        $sa->save();
        
        $sa->assignRole($superAdmin);
        $sa->syncPermissions($superAdmin->permissions);

        $this->info('Super Admin successfully set.');
    }
}
