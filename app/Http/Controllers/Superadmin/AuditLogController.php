<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs.
     */
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn($q, $action) => $q->where('action', $action))
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->model, fn($q, $model) => $q->where('auditable_type', 'like', "%{$model}%"))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderByDesc('created_at')
            ->paginate(25);

        // Get unique actions for filter
        $actions = AuditLog::distinct()->pluck('action');

        return view('superadmin.audit-logs.index', compact('logs', 'actions'));
    }

    /**
     * Show audit log details.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('superadmin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs.
     */
    public function export(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderByDesc('created_at')
            ->get();

        // Simple CSV export
        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'User', 'Action', 'Model', 'Model ID', 'Reason', 'IP Address', 'Created At']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    class_basename($log->auditable_type),
                    $log->auditable_id,
                    $log->reason,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
