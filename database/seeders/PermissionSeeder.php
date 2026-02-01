<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\DefaultRolePermissions;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all configurable permissions as per RBAC.md
        $permissions = DefaultRolePermissions::permissions();

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign default permissions to roles based on RBAC.md matrix
        $this->assignDefaultPermissions();
    }

    /**
     * Assign default permissions to roles.
     */
    private function assignDefaultPermissions(): void
    {
        $superadmin = Role::where('name', 'superadmin')->first();
        $admin = Role::where('name', 'admin')->first();
        $pembina = Role::where('name', 'pembina')->first();
        $student = Role::where('name', 'student')->first();

        // Superadmin gets all permissions
        if ($superadmin) {
            $allPermissions = Permission::pluck('id');
            $superadmin->permissions()->sync($allPermissions);
        }

        // Admin permissions
        if ($admin) {
            $adminPermissions = Permission::whereIn('name', DefaultRolePermissions::roleDefaults()['admin'])->pluck('id');
            $admin->permissions()->syncWithoutDetaching($adminPermissions);
        }

        // Pembina permissions
        if ($pembina) {
            $pembinaPermissions = Permission::whereIn('name', DefaultRolePermissions::roleDefaults()['pembina'])->pluck('id');
            $pembina->permissions()->syncWithoutDetaching($pembinaPermissions);
        }

        // Student permissions (limited - only own data)
        if ($student) {
            $studentPermissions = Permission::whereIn('name', DefaultRolePermissions::roleDefaults()['student'])->pluck('id');
            $student->permissions()->syncWithoutDetaching($studentPermissions);
        }
    }
}
