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
            'view_any_development',
            'view_development',
            'create_development',
            'update_development',
            'delete_development',

            'view_any_listing',
            'view_listing',
            'create_listing',
            'update_listing',
            'delete_listing',

            'view_any_development_unit',
            'view_development_unit',
            'create_development_unit',
            'update_development_unit',
            'delete_development_unit',
            'change_development_unit_status',
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
            'view_any_development',
            'view_development',
            'create_development',
            'update_development',

            'view_any_listing',
            'view_listing',
            'create_listing',
            'update_listing',

            'view_any_development_unit',
            'view_development_unit',
            'create_development_unit',
            'update_development_unit',
            'change_development_unit_status',
        ]);

        $agent->syncPermissions([
            'view_any_lead',
            'view_lead',
            'create_lead',
            'update_lead',
            'view_any_development',
            'view_development',
            'view_any_listing',
            'view_listing',
            'view_any_development_unit',
            'view_development_unit',
        ]);

        $firstUser = User::query()->first();

        if ($firstUser && ! $firstUser->hasRole('admin')) {
            $firstUser->assignRole('admin');
        }
    }
}
