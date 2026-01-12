@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-text me-2"></i>Audit Log Details
                    </h5>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small">Date/Time</label>
                            <div>{{ $auditLog->created_at->format('d M Y, H:i:s') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Action</label>
                            <div>
                                <span class="badge {{ $auditLog->action_badge_class }} fs-6">
                                    {{ ucfirst($auditLog->action) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">User</label>
                            <div>{{ $auditLog->user_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">IP Address</label>
                            <div>{{ $auditLog->ip_address }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Model Type</label>
                            <div>{{ class_basename($auditLog->model_type) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Model Name</label>
                            <div>{{ $auditLog->model_name }}</div>
                        </div>
                        @if($auditLog->url)
                        <div class="col-12">
                            <label class="text-muted small">URL</label>
                            <div class="text-break small">{{ $auditLog->url }}</div>
                        </div>
                        @endif
                        @if($auditLog->notes)
                        <div class="col-12">
                            <label class="text-muted small">Notes</label>
                            <div>{{ $auditLog->notes }}</div>
                        </div>
                        @endif
                    </div>

                    @if($auditLog->changed_fields)
                    <h6 class="mb-3">Changed Fields</h6>
                    <div class="alert alert-info">
                        @foreach($auditLog->changed_fields as $field)
                            <span class="badge bg-secondary me-1">{{ $field }}</span>
                        @endforeach
                    </div>
                    @endif

                    @if($auditLog->old_values || $auditLog->new_values)
                    <div class="row">
                        @if($auditLog->old_values)
                        <div class="col-md-6">
                            <h6 class="mb-3">
                                <i class="bi bi-dash-circle text-danger me-1"></i>Old Values
                            </h6>
                            <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                <pre class="mb-0" style="font-size: 0.75rem;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                        @endif
                        @if($auditLog->new_values)
                        <div class="col-md-6">
                            <h6 class="mb-3">
                                <i class="bi bi-plus-circle text-success me-1"></i>New Values
                            </h6>
                            <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                <pre class="mb-0" style="font-size: 0.75rem;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($auditLog->user_agent)
                    <hr class="my-4">
                    <h6 class="mb-3">Technical Details</h6>
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted">
                            <strong>User Agent:</strong><br>
                            {{ $auditLog->user_agent }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
