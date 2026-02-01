<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct(private AuditService $auditService)
    {
    }

    /**
     * Display list of users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');

        $users = User::with('roles')
            ->when($search, function ($query, $searchValue) {
                $query->where(function ($sub) use ($searchValue) {
                    $sub->where('name', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%");
                });
            })
            ->when($role, function ($query, $roleValue) {
                $query->whereHas('roles', fn($q) => $q->where('name', $roleValue));
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', (bool) $status);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'roles', 'search', 'role', 'status'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.create', compact('roles'));
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->roles()->sync($validated['roles']);
        $this->auditService->logCreated($user);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        $oldValues = $user->toArray();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $request->boolean('is_active');

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->roles()->sync($validated['roles']);

        $this->auditService->logUpdated($user, $oldValues);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }
}
