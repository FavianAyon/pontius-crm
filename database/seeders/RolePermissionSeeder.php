<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_any_lead',
            'view_lead',
            'create_lead',
            'update_lead',
            'delete_lead',
            'assign_lead',
            'view_all_leads',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $agent = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);

        $admin->syncPermissions($permissions);

        $supervisor->syncPermissions([
            'view_any_lead',
            'view_lead',
            'create_lead',
            'update_lead',
            'assign_lead',
            'view_all_leads',
        ]);

        $agent->syncPermissions([
            'view_any_lead',
            'view_lead',
            'create_lead',
            'update_lead',
        ]);

        $firstUser = User::query()->first();

        if ($firstUser && ! $firstUser->hasRole('admin')) {
            $firstUser->assignRole('admin');
        }
    }
}
