@extends('layouts.admin')

@section('title', 'Access Denied')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger mt-5">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-x text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="text-danger mb-3">Access Denied</h2>
                    <p class="text-muted mb-4">
                        You do not have permission to access this module.<br>
                        Please contact your administrator if you believe this is an error.
                    </p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
