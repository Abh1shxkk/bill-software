<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);

        // Get unique actions for filter
        $actions = AuditLog::where('organization_id', auth()->user()->organization_id)
            ->select('action')
            ->distinct()
            ->pluck('action');

        // Get users for filter
        $users = auth()->user()->organization->users()->get(['user_id', 'full_name']);

        return view('admin.audit-logs.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Show audit log details
     */
    public function show(AuditLog $auditLog)
    {
        // Ensure user can only view their organization's logs
        if ($auditLog->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::where('organization_id', auth()->user()->organization_id)
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        // Log the export action
        AuditLog::log('exported', null, null, null, 'Exported ' . $logs->count() . ' audit log entries');

        $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Date/Time', 'User', 'Action', 'Model', 'Details', 'IP Address']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name,
                    $log->action,
                    $log->model_name ?? class_basename($log->model_type),
                    $log->notes ?? '-',
                    $log->ip_address,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
