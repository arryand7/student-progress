<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role and permission seeders first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            ProgramSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }

        // Create default users only for non-production environments.
        if (!app()->environment('production')) {
            $superadmin = User::updateOrCreate(
                ['email' => 'superadmin@sabira.sch.id'],
                [
                    'name' => 'Super Administrator',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $superadmin->roles()->sync([Role::where('name', 'superadmin')->first()->id]);

            $admin = User::updateOrCreate(
                ['email' => 'admin@sabira.sch.id'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $admin->roles()->sync([Role::where('name', 'admin')->first()->id]);

            $pembina = User::updateOrCreate(
                ['email' => 'pembina@sabira.sch.id'],
                [
                    'name' => 'Pembina OSN',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $pembina->roles()->sync([Role::where('name', 'pembina')->first()->id]);

            $student = User::updateOrCreate(
                ['email' => 'siswa@sabira.sch.id'],
                [
                    'name' => 'Siswa Test',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $student->roles()->sync([Role::where('name', 'student')->first()->id]);
        }
    }
}
