@extends('layouts.admin')

@section('title', 'Access Denied')

@section('content')
{{-- Empty content - will redirect after showing toast --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show toast notification
        if (typeof crudNotification !== 'undefined') {
            crudNotification.showToast('error', 'Access Denied', 'You do not have permission to access "{{ $module }}" module. Please contact your administrator.');
        } else {
            // Fallback toast using Bootstrap
            showAccessDeniedToast();
        }
        
        // Go back after showing toast
        setTimeout(function() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ route("admin.dashboard") }}';
            }
        }, 100);
    });
    
    function showAccessDeniedToast() {
        // Create toast container if not exists
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        // Create toast
        const toastId = 'access-denied-toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-shield-x me-2"></i>
                        <strong>Access Denied:</strong> You do not have permission to access "{{ $module }}" module.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }
</script>
@endpush
