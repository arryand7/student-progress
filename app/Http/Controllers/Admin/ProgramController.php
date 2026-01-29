<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display a listing of programs.
     */
    public function index()
    {
        $programs = Program::withCount(['subjects', 'enrollments'])
            ->orderBy('name')
            ->paginate(10);

        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $program = Program::create($validated);
        $this->auditService->logCreated($program);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil dibuat.');
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program)
    {
        $program->load(['subjects.components', 'enrollments.user']);

        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code,' . $program->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldValues = $program->toArray();
        $program->update($validated);
        $this->auditService->logUpdated($program, $oldValues);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    /**
     * Toggle program active status.
     */
    public function toggleStatus(Program $program)
    {
        $oldValues = $program->toArray();
        
        if ($program->is_active) {
            $program->deactivate();
        } else {
            $program->activate();
        }

        $this->auditService->logUpdated($program, $oldValues);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Status program berhasil diubah.');
    }

    /**
     * Remove the specified program (soft delete).
     */
    public function destroy(Program $program)
    {
        // Prevent deletion if has historical data
        if ($program->hasHistoricalData()) {
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Program dengan data historis tidak dapat dihapus. Nonaktifkan saja.');
        }

        $this->auditService->logDeleted($program);
        $program->delete();

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }
}
