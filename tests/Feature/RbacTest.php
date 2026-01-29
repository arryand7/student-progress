<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_routes(): void
    {
        $studentRole = Role::create([
            'name' => 'student',
            'display_name' => 'Student',
            'description' => 'Student role',
        ]);

        $student = User::factory()->create(['is_active' => true]);
        $student->roles()->sync([$studentRole->id]);

        $response = $this->actingAs($student)->get('/admin/programs');
        $response->assertStatus(403);
    }

    public function test_admin_without_permission_is_denied(): void
    {
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Admin role',
        ]);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->sync([$adminRole->id]);

        $response = $this->actingAs($admin)->get('/admin/programs');
        $response->assertStatus(403);
    }

    public function test_admin_with_permission_can_access_programs(): void
    {
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Admin role',
        ]);

        $permission = Permission::create([
            'name' => 'program.edit',
            'category' => 'program',
            'description' => 'Edit programs',
            'is_configurable' => true,
        ]);

        $adminRole->permissions()->sync([$permission->id]);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->sync([$adminRole->id]);

        $response = $this->actingAs($admin)->get('/admin/programs');
        $response->assertStatus(200);
    }
}
