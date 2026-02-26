<?php $__env->startSection('title', 'Stock Adjustment Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-sliders me-2"></i> Stock Adjustment Invoices</h4>
        <div class="text-muted small">View and manage stock adjustments</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.stock-adjustment.transaction')); ?>" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Adjustment
        </a>
        <a href="<?php echo e(route('admin.stock-adjustment.modification')); ?>" class="btn btn-warning">
            <i class="bi bi-pencil-square me-1"></i> Modification
        </a>
    </div>
</div>

<!-- Filters Card - Matching Items Index -->
<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label for="filterBy" class="form-label">Search By</label>
                    <select class="form-select" id="filterBy" name="filter_by">
                        <option value="trn_no">Trn No</option>
                        <option value="remarks">Remarks</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchInput" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Type to search..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()" title="Clear search">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="dateFrom" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="dateFrom" name="date_from">
                </div>
                <div class="col-md-2">
                    <label for="dateTo" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="dateTo" name="date_to">
                </div>
            </form>
        </div>
    </div>

    <!-- Table - Matching Items Index -->
    <div class="table-responsive" id="adjustment-table-wrapper" style="position: relative; min-height: 400px;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Trn No</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th class="text-center">Items</th>
                    <th class="text-center">Shortage</th>
                    <th class="text-center">Excess</th>
                    <th class="text-end">Total Amount</th>
                    <th>Remarks</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="adjustmentsTableBody">
                <?php $__empty_1 = true; $__currentLoopData = $adjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $adjustment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($adjustments->firstItem() + $index); ?></td>
                    <td><strong><?php echo e($adjustment->trn_no); ?></strong></td>
                    <td><?php echo e($adjustment->adjustment_date->format('d-m-Y')); ?></td>
                    <td><?php echo e($adjustment->day_name ?? '-'); ?></td>
                    <td class="text-center">
                        <span class="badge bg-info text-white"><?php echo e($adjustment->total_items); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger"><?php echo e($adjustment->shortage_items); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success"><?php echo e($adjustment->excess_items); ?></span>
                    </td>
                    <td class="text-end fw-bold <?php echo e($adjustment->total_amount < 0 ? 'text-danger' : 'text-success'); ?>">
                        ₹<?php echo e(number_format(abs($adjustment->total_amount), 2)); ?>

                    </td>
                    <td><?php echo e(Str::limit($adjustment->remarks, 30) ?? '-'); ?></td>
                    <td class="text-end">
                        <a href="<?php echo e(route('admin.stock-adjustment.modification', ['trn_no' => $adjustment->trn_no])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAdjustment(<?php echo e($adjustment->id); ?>, '<?php echo e($adjustment->trn_no); ?>')" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">No stock adjustments found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer - Matching Items Index -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing <?php echo e($adjustments->firstItem() ?? 0); ?>-<?php echo e($adjustments->lastItem() ?? 0); ?> of <?php echo e($adjustments->total()); ?></div>
            <div class="text-muted">Page <?php echo e($adjustments->currentPage()); ?> of <?php echo e($adjustments->lastPage()); ?></div>
        </div>
        <?php if($adjustments->hasMorePages()): ?>
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div id="adjustment-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span id="adjustment-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
        </div>
        <div id="adjustment-sentinel" data-next-url="<?php echo e($adjustments->appends(request()->query())->nextPageUrl()); ?>" style="height: 1px;"></div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete stock adjustment <strong id="deleteTrnNo"></strong>?</p>
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-circle me-1"></i>This will reverse all stock changes made by this adjustment.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let deleteId = null;
let isLoading = false;
let isSearching = false;
let searchTimeout;
let observer = null;

// Initialize infinite scroll - matching Items index pattern
window.initAdjustmentInfiniteScroll = function() {
    const sentinel = document.getElementById('adjustment-sentinel');
    if (!sentinel) return;
    
    // Disconnect previous observer if exists
    if (observer) {
        observer.disconnect();
    }
    
    async function loadMore() {
        const spinner = document.getElementById('adjustment-spinner');
        const loadText = document.getElementById('adjustment-load-text');
        const nextUrl = sentinel.getAttribute('data-next-url');
        
        if (!nextUrl || isLoading) return;
        
        isLoading = true;
        spinner && spinner.classList.remove('d-none');
        loadText && (loadText.textContent = 'Loading...');
        
        try {
            // Add current filters to URL
            const url = new URL(nextUrl, window.location.origin);
            url.searchParams.set('filter_by', document.getElementById('filterBy').value);
            url.searchParams.set('search', document.getElementById('searchInput').value);
            url.searchParams.set('date_from', document.getElementById('dateFrom').value);
            url.searchParams.set('date_to', document.getElementById('dateTo').value);
            
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Get new rows
            const newRows = doc.querySelectorAll('#adjustmentsTableBody tr');
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            
            // Append new rows
            const tbody = document.getElementById('adjustmentsTableBody');
            realRows.forEach(tr => tbody.appendChild(tr));
            
            // Update footer
            const newFooter = doc.querySelector('.card-footer');
            const currentFooter = document.querySelector('.card-footer');
            if (newFooter && currentFooter) {
                currentFooter.innerHTML = newFooter.innerHTML;
                // Reinitialize infinite scroll
                window.initAdjustmentInfiniteScroll();
            }
            
            isLoading = false;
        } catch(e) {
            console.error('Error loading more:', e);
            spinner && spinner.classList.add('d-none');
            loadText && (loadText.textContent = 'Error loading');
            isLoading = false;
        }
    }
    
    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading) {
                loadMore();
            }
        });
    }, { rootMargin: '100px' });
    
    observer.observe(sentinel);
};

// Perform search - matching Items index pattern
window.performAdjustmentSearch = function() {
    if (isSearching) return;
    isSearching = true;
    
    const loadingSpinner = document.getElementById('search-loading');
    const searchInput = document.getElementById('searchInput');
    
    // Show loading
    if (loadingSpinner) loadingSpinner.style.display = 'flex';
    if (searchInput) searchInput.style.opacity = '0.6';
    
    const params = new URLSearchParams({
        filter_by: document.getElementById('filterBy').value,
        search: document.getElementById('searchInput').value,
        date_from: document.getElementById('dateFrom').value,
        date_to: document.getElementById('dateTo').value
    });
    
    fetch(`<?php echo e(route('admin.stock-adjustment.invoices')); ?>?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Get new rows
        const newRows = doc.querySelectorAll('#adjustmentsTableBody tr');
        const realRows = Array.from(newRows).filter(tr => {
            const tds = tr.querySelectorAll('td');
            return !(tds.length === 1 && tr.querySelector('td[colspan]'));
        });
        
        // Update table
        const tbody = document.getElementById('adjustmentsTableBody');
        tbody.innerHTML = '';
        if (realRows.length) {
            realRows.forEach(tr => tbody.appendChild(tr));
        } else {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No stock adjustments found</td></tr>';
        }
        
        // Update footer
        const newFooter = doc.querySelector('.card-footer');
        const currentFooter = document.querySelector('.card-footer');
        if (newFooter && currentFooter) {
            currentFooter.innerHTML = newFooter.innerHTML;
            window.initAdjustmentInfiniteScroll();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('adjustmentsTableBody').innerHTML = '<tr><td colspan="10" class="text-center text-danger py-4">Error loading data</td></tr>';
    })
    .finally(() => {
        isSearching = false;
        if (loadingSpinner) loadingSpinner.style.display = 'none';
        if (searchInput) searchInput.style.opacity = '1';
    });
};

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterBy').value = 'trn_no';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    window.performAdjustmentSearch();
}

document.addEventListener('DOMContentLoaded', function() {
    // Debounced search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(window.performAdjustmentSearch, 300);
    });
    document.getElementById('filterBy').addEventListener('change', window.performAdjustmentSearch);
    document.getElementById('dateFrom').addEventListener('change', window.performAdjustmentSearch);
    document.getElementById('dateTo').addEventListener('change', window.performAdjustmentSearch);
    
    // Initialize infinite scroll
    window.initAdjustmentInfiniteScroll();
});

function deleteAdjustment(id, trnNo) {
    deleteId = id;
    document.getElementById('deleteTrnNo').textContent = trnNo;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmDelete() {
    if (!deleteId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';
    btn.disabled = true;
    
    fetch(`<?php echo e(url('admin/stock-adjustment')); ?>/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        
        if (data.success) {
            if (typeof crudNotification !== 'undefined') {
                crudNotification.showToast('success', 'Success', data.message);
            }
            window.performAdjustmentSearch();
        } else {
            throw new Error(data.message || 'Error deleting adjustment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof crudNotification !== 'undefined') {
            crudNotification.showToast('error', 'Error', error.message);
        } else {
            alert('Error: ' + error.message);
        }
    })
    .finally(() => {
        btn.innerHTML = '<i class="bi bi-trash me-1"></i> Delete';
        btn.disabled = false;
        deleteId = null;
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/stock-adjustment/invoices.blade.php ENDPATH**/ ?>