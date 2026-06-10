<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_users',
            'manage_units',
            'manage_templates',
            'manage_batches',
            'generate_schedule',
            'assign_wi',
            'manage_holidays',
            'export_pdf',
            'monitor_conflicts',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin  = Role::firstOrCreate(['name' => 'super_admin',  'guard_name' => 'web']);
        $unitManager = Role::firstOrCreate(['name' => 'unit_manager', 'guard_name' => 'web']);
        $admin       = Role::firstOrCreate(['name' => 'admin',        'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());

        $unitManager->syncPermissions([
            'manage_templates',
            'manage_batches',
            'generate_schedule',
            'assign_wi',
            'manage_holidays',
            'export_pdf',
            'monitor_conflicts',
        ]);

        $admin->syncPermissions([
            'manage_templates',
            'manage_batches',
            'generate_schedule',
            'assign_wi',
            'export_pdf',
        ]);
    }
}
