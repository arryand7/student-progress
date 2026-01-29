<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class PembinaAssignmentController extends Controller
{
    /**
     * Show assignment matrix for pembina -> subject.
     */
    public function index()
    {
        $subjects = Subject::with(['program', 'pembinas'])->orderBy('name')->get();
        $pembinas = User::whereHas('roles', fn($q) => $q->where('name', 'pembina'))
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.pembina-assignments.index', compact('subjects', 'pembinas'));
    }

    /**
     * Update assignments for a subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'pembina_ids' => 'array',
            'pembina_ids.*' => 'exists:users,id',
        ]);

        $subject->pembinas()->sync($validated['pembina_ids'] ?? []);

        return redirect()
            ->route('admin.pembina-assignments.index')
            ->with('success', 'Penugasan pembina berhasil diperbarui.');
    }
}
