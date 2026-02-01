<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Permission;
use App\Models\Role;
use App\Support\DefaultRolePermissions;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('permissions:sync-defaults-new-roles', function () {
    $this->info('Ensuring permissions list is up to date...');
    foreach (DefaultRolePermissions::permissions() as $permission) {
        Permission::updateOrCreate(['name' => $permission['name']], $permission);
    }

    $roleDefaults = DefaultRolePermissions::roleDefaults();
    $roles = array_merge(['superadmin'], array_keys($roleDefaults));

    foreach ($roles as $roleName) {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->warn("Role '{$roleName}' tidak ditemukan, dilewati.");
            continue;
        }

        if ($role->permissions()->count() > 0) {
            $this->line("Role '{$roleName}' sudah punya permission, dilewati.");
            continue;
        }

        if ($roleName === 'superadmin') {
            $permissionIds = Permission::pluck('id');
        } else {
            $permissionIds = Permission::whereIn('name', $roleDefaults[$roleName])->pluck('id');
        }

        $role->permissions()->sync($permissionIds);
        $this->info("Role '{$roleName}' diset dengan permission default.");
    }

    $this->info('Selesai.');
})->purpose('Sync default permissions only for roles without existing permissions.');
