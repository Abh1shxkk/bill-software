@extends('layouts.admin')

@section('title', 'Hotkey Management')

@section('content')
<style>
    .hotkey-badge {
        font-family: 'Consolas', 'Monaco', monospace;
        background: #4f46e5;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        display: inline-block;
    }
    
    .status-toggle {
        cursor: pointer;
    }
</style>

<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-start gap-3">
        <div>
            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-keyboard me-2"></i> Hotkey Management
            </h4>
            <div class="text-muted small">Manage keyboard shortcuts for the application</div>
        </div>
    </div>
    <div>
        <form action="{{ route('admin.administration.hotkeys.reset-to-default') }}" method="POST" class="d-inline" 
              onsubmit="return confirm('Reset all hotkeys to default?')">
            @csrf
            <button type="submit" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Default
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded">
    <!-- Filters -->
    <div class="card mb-0">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small mb-1">Category</label>
                    <select id="filterCategory" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Scope</label>
                    <select id="filterScope" class="form-select form-select-sm">
                        <option value="">All Scopes</option>
                        @foreach($scopes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="filterSearch" class="form-control" placeholder="Search module, key...">
                        <button class="btn btn-outline-secondary" type="button" id="clearFilters" title="Clear">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="table-responsive" id="hotkey-table-wrapper" style="position: relative; min-height: 300px;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table align-middle mb-0 table-hover" style="table-layout: fixed;">
            <thead class="table-light">
                <tr>
                    <th style="width: 30%;">Module Name</th>
                    <th style="width: 15%;">Key</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 12%;">Scope</th>
                    <th style="width: 12%;" class="text-center">Status</th>
                    <th style="width: 13%;" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody id="hotkey-table-body">
                <!-- Data loads via AJAX -->
            </tbody>
        </table>
    </div>
    
    <!-- Footer with pagination info -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div id="showing-info">Showing 0-0 of 0</div>
            <div class="text-muted" id="page-info">Page 1 of 1</div>
        </div>
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div id="hotkey-spinner" class="spinner-border text-primary d-none" style="width: 1.5rem; height: 1.5rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span id="hotkey-load-text" class="text-muted small"></span>
        </div>
        <div id="hotkey-sentinel" style="height: 1px;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('hotkey-table-body');
    const loadingOverlay = document.getElementById('search-loading');
    const spinner = document.getElementById('hotkey-spinner');
    const loadText = document.getElementById('hotkey-load-text');
    const showingInfo = document.getElementById('showing-info');
    const pageInfo = document.getElementById('page-info');
    const sentinel = document.getElementById('hotkey-sentinel');
    
    let currentPage = 1;
    let lastPage = 1;
    let isLoading = false;
    let hasMore = true;
    let totalLoaded = 0;
    let totalItems = 0;
    const perPage = 30;
    
    const categories = @json($categories);
    const baseUrl = "{{ url('admin/administration/hotkeys') }}";
    const csrfToken = "{{ csrf_token() }}";
    
    // Intersection Observer for infinite scroll
    let observer = null;
    
    function setupObserver() {
        if (observer) observer.disconnect();
        
        observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                loadHotkeys(false);
            }
        }, { threshold: 0.1 });
        
        if (sentinel) observer.observe(sentinel);
    }
    
    // Load hotkeys via AJAX
    function loadHotkeys(reset = false) {
        if (isLoading) return;
        if (!hasMore && !reset) return;
        
        if (reset) {
            currentPage = 1;
            hasMore = true;
            tbody.innerHTML = '';
            totalLoaded = 0;
        }
        
        isLoading = true;
        
        if (reset) {
            loadingOverlay.style.display = 'flex';
        } else {
            spinner.classList.remove('d-none');
            loadText.textContent = 'Loading more...';
        }
        
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage,
            category: document.getElementById('filterCategory').value,
            scope: document.getElementById('filterScope').value,
            status: document.getElementById('filterStatus').value,
            search: document.getElementById('filterSearch').value
        });
        
        fetch(`${baseUrl}/data?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            totalItems = data.total;
            lastPage = data.last_page;
            
            if (data.hotkeys && data.hotkeys.length > 0) {
                data.hotkeys.forEach(hotkey => {
                    tbody.insertAdjacentHTML('beforeend', renderHotkeyRow(hotkey));
                });
                
                totalLoaded += data.hotkeys.length;
                currentPage++;
                hasMore = data.has_more;
                
                // Update info
                showingInfo.textContent = `Showing 1-${totalLoaded} of ${totalItems}`;
                pageInfo.textContent = `Page ${data.current_page} of ${lastPage}`;
                loadText.textContent = hasMore ? 'Scroll for more' : 'All loaded';
                
                attachToggleHandlers();
            } else if (reset) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No hotkeys found
                        </td>
                    </tr>
                `;
                showingInfo.textContent = 'Showing 0-0 of 0';
                pageInfo.textContent = 'Page 0 of 0';
                loadText.textContent = '';
            }
            
            loadingOverlay.style.display = 'none';
            spinner.classList.add('d-none');
            isLoading = false;
        })
        .catch(error => {
            console.error('Error loading hotkeys:', error);
            loadingOverlay.style.display = 'none';
            spinner.classList.add('d-none');
            isLoading = false;
        });
    }
    
    // Render a single hotkey row
    function renderHotkeyRow(hotkey) {
        const keyDisplay = hotkey.key_combination.toUpperCase().replace(/\+/g, ' + ');
        const categoryLabel = categories[hotkey.category] || hotkey.category;
        const scopeClass = hotkey.scope === 'global' ? 'bg-primary' : 'bg-info';
        const rowClass = hotkey.is_active ? '' : 'table-secondary';
        
        return `
            <tr class="${rowClass}" data-id="${hotkey.id}">
                <td>
                    ${escapeHtml(hotkey.module_name)}
                    ${hotkey.description ? `<br><small class="text-muted">${escapeHtml(hotkey.description)}</small>` : ''}
                </td>
                <td>
                    <span class="hotkey-badge">${keyDisplay}</span>
                </td>
                <td>
                    <span class="badge bg-secondary">${categoryLabel}</span>
                </td>
                <td>
                    <span class="badge ${scopeClass}">${hotkey.scope.charAt(0).toUpperCase() + hotkey.scope.slice(1)}</span>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block mb-0">
                        <input class="form-check-input status-toggle" type="checkbox" 
                               data-id="${hotkey.id}"
                               ${hotkey.is_active ? 'checked' : ''}>
                    </div>
                </td>
                <td class="text-center">
                    <a href="${baseUrl}/${hotkey.id}/edit" class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
            </tr>
        `;
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Attach toggle handlers
    function attachToggleHandlers() {
        document.querySelectorAll('.status-toggle:not(.attached)').forEach(toggle => {
            toggle.classList.add('attached');
            toggle.addEventListener('change', function() {
                const hotkeyId = this.dataset.id;
                const row = this.closest('tr');
                const checkbox = this;
                
                fetch(`${baseUrl}/${hotkeyId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.style.transition = 'background-color 0.3s';
                        row.style.backgroundColor = data.is_active ? '#d1fae5' : '#fee2e2';
                        
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                            row.classList.toggle('table-secondary', !data.is_active);
                        }, 400);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    checkbox.checked = !checkbox.checked;
                });
            });
        });
    }
    
    // Filter handlers with debounce
    let filterTimeout;
    function handleFilterChange() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => loadHotkeys(true), 300);
    }
    
    document.getElementById('filterCategory').addEventListener('change', handleFilterChange);
    document.getElementById('filterScope').addEventListener('change', handleFilterChange);
    document.getElementById('filterStatus').addEventListener('change', handleFilterChange);
    document.getElementById('filterSearch').addEventListener('input', handleFilterChange);
    
    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('filterCategory').value = '';
        document.getElementById('filterScope').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterSearch').value = '';
        loadHotkeys(true);
    });
    
    // Setup observer and initial load
    setupObserver();
    loadHotkeys(true);
});
</script>
@endpush
