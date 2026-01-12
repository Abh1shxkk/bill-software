@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-journal-text me-2"></i>Audit Logs
            </h5>
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" 
                           placeholder="From" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" 
                           placeholder="To" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                </div>
            </form>

            <!-- Logs Table -->
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Details</th>
                            <th>IP</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <small>{{ $log->created_at->format('d M Y') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <strong>{{ $log->user_name }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $log->action_badge_class }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ class_basename($log->model_type) }}</small><br>
                                <span title="{{ $log->model_name }}">
                                    {{ Str::limit($log->model_name, 20) }}
                                </span>
                            </td>
                            <td>
                                @if($log->changed_fields)
                                    <small class="text-muted">
                                        Changed: {{ implode(', ', array_slice($log->changed_fields, 0, 3)) }}
                                        @if(count($log->changed_fields) > 3)
                                            +{{ count($log->changed_fields) - 3 }} more
                                        @endif
                                    </small>
                                @elseif($log->notes)
                                    <small>{{ Str::limit($log->notes, 30) }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.audit-logs.show', $log) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No audit logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
