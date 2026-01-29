<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display components for a subject.
     */
    public function index(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $components = $subject->components()
            ->orderBy('sort_order')
            ->get();

        $totalWeight = $components->sum('weight');

        return view('admin.components.index', compact('subject', 'components', 'totalWeight'));
    }

    /**
     * Show the form for creating a new component.
     */
    public function create(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        return view('admin.components.create', compact('subject'));
    }

    /**
     * Store a newly created component.
     */
    public function store(Request $request, Subject $subject)
    {
        $this->ensureSubjectInScope($subject, $request->user());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['subject_id'] = $subject->id;
        $validated['sort_order'] = $validated['sort_order'] ?? $subject->components()->count();

        $component = Component::create($validated);
        $this->auditService->logCreated($component);

        return redirect()
            ->route('admin.subjects.components.index', $subject)
            ->with('success', 'Komponen berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified component.
     */
    public function edit(Subject $subject, Component $component)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $this->ensureComponentBelongsToSubject($component, $subject);
        return view('admin.components.edit', compact('subject', 'component'));
    }

    /**
     * Update the specified component.
     */
    public function update(Request $request, Subject $subject, Component $component)
    {
        $this->ensureSubjectInScope($subject, $request->user());
        $this->ensureComponentBelongsToSubject($component, $subject);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldValues = $component->toArray();
        $component->update($validated);
        $this->auditService->logUpdated($component, $oldValues);

        return redirect()
            ->route('admin.subjects.components.index', $subject)
            ->with('success', 'Komponen berhasil diperbarui.');
    }

    /**
     * Toggle component active status.
     */
    public function toggleStatus(Subject $subject, Component $component)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $this->ensureComponentBelongsToSubject($component, $subject);
        $oldValues = $component->toArray();
        $component->update(['is_active' => !$component->is_active]);
        $this->auditService->logUpdated($component, $oldValues);

        return redirect()
            ->route('admin.subjects.components.index', $subject)
            ->with('success', 'Status komponen berhasil diubah.');
    }

    /**
     * Update component sort order.
     */
    public function reorder(Request $request, Subject $subject)
    {
        $this->ensureSubjectInScope($subject, $request->user());
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:components,id',
        ]);

        $order = $validated['order'];
        $countInSubject = Component::where('subject_id', $subject->id)
            ->whereIn('id', $order)
            ->count();
        if ($countInSubject !== count($order)) {
            abort(403, 'Beberapa komponen tidak berada dalam mata pelajaran ini.');
        }

        foreach ($validated['order'] as $index => $componentId) {
            Component::where('id', $componentId)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified component.
     */
    public function destroy(Subject $subject, Component $component)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $this->ensureComponentBelongsToSubject($component, $subject);
        if ($component->hasHistoricalData()) {
            return redirect()
                ->route('admin.subjects.components.index', $subject)
                ->with('error', 'Komponen dengan data historis tidak dapat dihapus.');
        }

        $this->auditService->logDeleted($component);
        $component->delete();

        return redirect()
            ->route('admin.subjects.components.index', $subject)
            ->with('success', 'Komponen berhasil dihapus.');
    }

    /**
     * Ensure the subject is within the user's scope.
     */
    private function ensureSubjectInScope(Subject $subject, User $user): void
    {
        if ($user->isPembina() && !$user->isAdmin() && !$user->isSuperadmin()) {
            $isAssigned = $user->pembinaSubjects()
                ->where('subjects.id', $subject->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
            }
        }
    }

    /**
     * Ensure the component belongs to the given subject.
     */
    private function ensureComponentBelongsToSubject(Component $component, Subject $subject): void
    {
        if ((int) $component->subject_id !== (int) $subject->id) {
            abort(404);
        }
    }
}
