<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $students = User::whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ]);

        $password = $validated['password'] ?? Str::random(12);

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $password,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $role = Role::where('name', 'student')->first();
        if ($role) {
            $student->roles()->sync([$role->id]);
        }

        $this->auditService->logCreated($student);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Siswa berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ]);

        $oldValues = $student->toArray();

        $updates = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'] ?? $student->is_active,
        ];

        if (!empty($validated['password'])) {
            $updates['password'] = $validated['password'];
        }

        $student->update($updates);

        $this->auditService->logUpdated($student, $oldValues);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }
}
