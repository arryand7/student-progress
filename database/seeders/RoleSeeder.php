<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'System owner with full access, including configuration, permission management, audit, and override authority.',
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Manages academic structure and student enrollment without evaluation input rights.',
            ],
            [
                'name' => 'pembina',
                'display_name' => 'Pembina (Teacher)',
                'description' => 'Inputs weekly evaluations and monitors student progress.',
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Read-only access to personal academic progress.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
