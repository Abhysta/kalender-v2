<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Run after installing spatie/laravel-permission:
 *   composer require spatie/laravel-permission
 *   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
 *   php artisan migrate
 *   php artisan db:seed --class=RolePermissionSeeder
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (! class_exists(\Spatie\Permission\Models\Permission::class)) {
            $this->command->warn('spatie/laravel-permission not installed. Skipping.');
            return;
        }

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

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        $admin      = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $superAdmin->syncPermissions(\Spatie\Permission\Models\Permission::all());

        $admin->syncPermissions([
            'manage_templates',
            'manage_batches',
            'generate_schedule',
            'assign_wi',
            'manage_holidays',
            'export_pdf',
            'monitor_conflicts',
        ]);
    }
}
