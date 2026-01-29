<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display permission management page.
     */
    public function index()
    {
        $roles = Role::with('permissions')
            ->whereNot('name', 'superadmin')
            ->orderBy('name')
            ->get();

        $permissions = Permission::configurable()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('superadmin.permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Update permissions for a role.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing superadmin permissions
        if ($role->name === 'superadmin') {
            return redirect()
                ->route('superadmin.permissions.index')
                ->with('error', 'Permissions Super Administrator tidak dapat diubah.');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $oldPermissions = $role->permissions()->pluck('permissions.id')->toArray();
        $newPermissions = $validated['permissions'] ?? [];

        // Update permissions
        $role->permissions()->sync($newPermissions);

        // Log the change
        $this->auditService->logPermissionChange(
            $role,
            'update',
            [
                'old' => $oldPermissions,
                'new' => $newPermissions,
            ]
        );

        return redirect()
            ->route('superadmin.permissions.index')
            ->with('success', "Permissions untuk role {$role->display_name} berhasil diperbarui.");
    }

    /**
     * Show permission matrix.
     */
    public function matrix()
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('category')
            ->orderBy('name')
            ->get();

        return view('superadmin.permissions.matrix', compact('roles', 'permissions'));
    }
}
