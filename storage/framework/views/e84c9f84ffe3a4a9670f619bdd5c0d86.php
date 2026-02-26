<?php $__env->startSection('title', 'Claim to Supplier'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Claim to Supplier</h4>
        <div class="text-muted small">Manage claim to supplier transactions</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.claim-to-supplier.transaction')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.claim-to-supplier.index')); ?>" class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="filter_by" class="form-label">Filter By</label>
                    <select class="form-select" id="filter_by" name="filter_by">
                        <option value="claim_no" <?php echo e(request('filter_by', 'claim_no') == 'claim_no' ? 'selected' : ''); ?>>Claim No.</option>
                        <option value="supplier_name" <?php echo e(request('filter_by') == 'supplier_name' ? 'selected' : ''); ?>>Supplier Name</option>
                        <option value="invoice_no" <?php echo e(request('filter_by') == 'invoice_no' ? 'selected' : ''); ?>>Invoice No.</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo e(request('search')); ?>" placeholder="Enter search term..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="<?php echo e(request('date_from')); ?>" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="<?php echo e(request('date_to')); ?>" autocomplete="off">
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive" id="cts-table-wrapper" style="position: relative; min-height: 400px;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Claim No.</th>
                    <th>Supplier</th>
                    <th class="text-end">Net Amount</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="cts-table-body">
                <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration); ?></td>
                    <td><?php echo e($transaction->claim_date ? $transaction->claim_date->format('d/m/Y') : '-'); ?></td>
                    <td><strong><?php echo e($transaction->claim_no); ?></strong></td>
                    <td><?php echo e($transaction->supplier_name ?? ($transaction->supplier->name ?? '-')); ?></td>
                    <td class="text-end">
                        <span class="badge bg-success">₹<?php echo e(number_format($transaction->net_amount ?? 0, 2)); ?></span>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo e($transaction->status == 'active' ? 'success' : 'danger'); ?>">
                            <?php echo e(ucfirst($transaction->status ?? 'active')); ?>

                        </span>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="<?php echo e(route('admin.claim-to-supplier.show', $transaction->id)); ?>" title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.claim-to-supplier.modification')); ?>?claim_no=<?php echo e($transaction->claim_no); ?>" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-cts" 
                                data-id="<?php echo e($transaction->id); ?>" 
                                data-claim-no="<?php echo e($transaction->claim_no); ?>" 
                                data-supplier="<?php echo e($transaction->supplier_name ?? 'Unknown'); ?>"
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No claim to supplier transactions found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <?php if(isset($transactions) && $transactions->total() > 0): ?>
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing <?php echo e($transactions->firstItem() ?? 0); ?>-<?php echo e($transactions->lastItem() ?? 0); ?> of <?php echo e($transactions->total() ?? 0); ?></div>
            <div class="text-muted">Page <?php echo e($transactions->currentPage()); ?> of <?php echo e($transactions->lastPage()); ?></div>
        </div>
        <?php if($transactions->hasMorePages()): ?>
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div id="cts-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span id="cts-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
        </div>
        <div id="cts-sentinel" data-next-url="<?php echo e($transactions->appends(request()->query())->nextPageUrl()); ?>" style="height: 1px;"></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCtsModal" tabindex="-1" aria-labelledby="deleteCtsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCtsModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this claim to supplier transaction?</p>
                <div class="alert alert-warning">
                    <strong>Claim No:</strong> <span id="delete-claim-no"></span><br>
                    <strong>Supplier:</strong> <span id="delete-supplier"></span><br>
                    <small class="text-muted">This action cannot be undone. Stock quantities will be restored.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete Transaction</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filter_by');
    const searchInput = document.getElementById('search');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('cts-table-body');
    const tableWrapper = document.getElementById('cts-table-wrapper');
    const overlay = document.getElementById('search-loading');
    
    let observer = null;
    let isLoading = false;
    
    function updatePlaceholder() {
        const filterValue = filterSelect.value;
        const placeholders = {
            'claim_no': 'Enter claim number...',
            'supplier_name': 'Enter supplier name...',
            'invoice_no': 'Enter invoice number...'
        };
        searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
    }
    
    filterSelect.addEventListener('change', updatePlaceholder);
    updatePlaceholder();

    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    function setLoading(loading) {
        if (!overlay) return;
        overlay.style.display = loading ? 'flex' : 'none';
    }

    function getFormParams() {
        return new URLSearchParams(new FormData(filterForm));
    }

    async function fetchData(urlOrParams, pushState = true) {
        try {
            setLoading(true);
            let url;
            if (typeof urlOrParams === 'string') {
                url = new URL(urlOrParams, window.location.origin);
            } else {
                url = new URL(filterForm.getAttribute('action'), window.location.origin);
                url.search = urlOrParams.toString();
            }

            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('#cts-table-body');
            const newFooter = doc.querySelector('.card-footer');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            if (newFooter) {
                const currentFooter = document.querySelector('.card-footer');
                if (currentFooter) {
                    currentFooter.innerHTML = newFooter.innerHTML;
                }
            }
            
            if (tableWrapper) {
                tableWrapper.scrollTo({ top: 0, behavior: 'smooth' });
            }

            initInfiniteScroll();
            
            if (pushState) {
                window.history.pushState({}, '', url.toString());
            }
        } catch (e) {
            console.error(e);
            alert('Failed to load data.');
        } finally {
            setLoading(false);
        }
    }

    const debouncedSearch = debounce(() => fetchData(getFormParams()), 300);
    searchInput.addEventListener('input', debouncedSearch);
    filterSelect.addEventListener('change', () => {
        updatePlaceholder();
        fetchData(getFormParams());
    });
    dateFromInput && dateFromInput.addEventListener('change', () => fetchData(getFormParams()));
    dateToInput && dateToInput.addEventListener('change', () => fetchData(getFormParams()));
    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        fetchData(getFormParams());
    });

    // Delete functionality
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.delete-cts');
        if (!button) return;

        const id = button.getAttribute('data-id');
        const claimNo = button.getAttribute('data-claim-no');
        const supplier = button.getAttribute('data-supplier');
        
        document.getElementById('delete-claim-no').textContent = claimNo;
        document.getElementById('delete-supplier').textContent = supplier;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCtsModal'));
        modal.show();
        
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.onclick = function() {
            deleteTransaction(id, modal);
        };
    });

    function deleteTransaction(id, modal) {
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(`<?php echo e(url('admin/claim-to-supplier')); ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                modal.hide();
                fetchData(window.location.href, false);
            } else {
                alert('Error deleting: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error deleting transaction');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Delete Transaction';
        });
    }

    // Infinite scroll functionality
    function initInfiniteScroll() {
        if (observer) {
            observer.disconnect();
        }

        const sentinel = document.getElementById('cts-sentinel');
        const spinner = document.getElementById('cts-spinner');
        const loadText = document.getElementById('cts-load-text');

        if (!sentinel || !tableBody) return;

        isLoading = false;

        async function loadMore() {
            if (isLoading) return;
            const nextUrl = sentinel.getAttribute('data-next-url');
            if (!nextUrl) return;

            isLoading = true;
            spinner && spinner.classList.remove('d-none');
            loadText && (loadText.textContent = 'Loading...');

            try {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const url = new URL(nextUrl, window.location.origin);
                params.forEach((value, key) => {
                    if (value) url.searchParams.set(key, value);
                });

                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#cts-table-body tr');

                const realRows = Array.from(newRows).filter(tr => {
                    const tds = tr.querySelectorAll('td');
                    return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                });

                realRows.forEach(tr => tableBody.appendChild(tr));

                const newSentinel = doc.querySelector('#cts-sentinel');
                if (newSentinel) {
                    sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                    spinner && spinner.classList.add('d-none');
                    loadText && (loadText.textContent = 'Scroll for more');
                    isLoading = false;
                } else {
                    observer.disconnect();
                    sentinel.remove();
                    spinner && spinner.remove();
                    loadText && loadText.remove();
                }
            } catch (e) {
                console.error(e);
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
    }

    window.addEventListener('popstate', function() {
        fetchData(window.location.href, false);
    });

    initInfiniteScroll();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/claim-to-supplier/index.blade.php ENDPATH**/ ?>