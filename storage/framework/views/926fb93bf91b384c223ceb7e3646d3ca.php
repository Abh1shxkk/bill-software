<?php $__env->startSection('title', 'Debit Note Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-plus me-2"></i> Debit Note List</h4>
        <div class="text-muted small">View and manage all debit notes</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.debit-note.transaction')); ?>" class="btn btn-danger">
            <i class="bi bi-plus-circle me-1"></i> New Debit Note
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.debit-note.invoices')); ?>" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label for="filter_by" class="form-label">Filter By</label>
                    <select class="form-select" id="filter_by" name="filter_by">
                        <option value="party_name" <?php echo e(request('filter_by', 'party_name') == 'party_name' ? 'selected' : ''); ?>>Party Name</option>
                        <option value="debit_note_no" <?php echo e(request('filter_by') == 'debit_note_no' ? 'selected' : ''); ?>>DN No.</option>
                        <option value="amount" <?php echo e(request('filter_by') == 'amount' ? 'selected' : ''); ?>>Amount</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo e(request('search')); ?>" placeholder="Enter search term..." autocomplete="off">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-search"></i>
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
                <div class="col-md-2">
                    <label for="party_type" class="form-label">Party Type</label>
                    <select class="form-select" id="party_type" name="party_type">
                        <option value="">All</option>
                        <option value="S" <?php echo e(request('party_type') == 'S' ? 'selected' : ''); ?>>Supplier</option>
                        <option value="C" <?php echo e(request('party_type') == 'C' ? 'selected' : ''); ?>>Customer</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100" title="Clear All Filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive" id="dn-table-wrapper" style="position: relative; min-height: 400px; max-height: 600px; overflow-y: auto;">
        <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>DN No.</th>
                    <th>Party Type</th>
                    <th>Party Name</th>
                    <th class="text-end">DN Amount</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="dn-table-body">
                <?php $__empty_1 = true; $__currentLoopData = $debitNotes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(($debitNotes->currentPage() - 1) * $debitNotes->perPage() + $loop->iteration); ?></td>
                        <td><?php echo e($dn->debit_note_date ? $dn->debit_note_date->format('d/m/Y') : '-'); ?></td>
                        <td><strong><?php echo e($dn->debit_note_no ?? '-'); ?></strong></td>
                        <td>
                            <?php if($dn->debit_party_type == 'S'): ?>
                                <span class="badge bg-info">Supplier</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Customer</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($dn->debit_party_name ?? '-'); ?></td>
                        <td class="text-end">
                            <span class="badge bg-danger">₹<?php echo e(number_format($dn->dn_amount ?? 0, 2)); ?></span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="<?php echo e(route('admin.debit-note.show', $dn->id)); ?>" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.debit-note.modification')); ?>?debit_note_no=<?php echo e($dn->debit_note_no); ?>" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-dn" 
                                    data-dn-id="<?php echo e($dn->id); ?>" 
                                    data-dn-no="<?php echo e($dn->debit_note_no); ?>" 
                                    data-party="<?php echo e($dn->debit_party_name ?? 'Unknown'); ?>"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">No debit notes found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Infinite Scroll Footer -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing <?php echo e($debitNotes->firstItem() ?? 0); ?>-<?php echo e($debitNotes->lastItem() ?? 0); ?> of <?php echo e($debitNotes->total() ?? 0); ?></div>
            <div class="text-muted">Page <?php echo e($debitNotes->currentPage()); ?> of <?php echo e($debitNotes->lastPage()); ?></div>
        </div>
        <?php if($debitNotes->hasMorePages()): ?>
            <div class="d-flex align-items-center justify-content-center gap-2">
                <div id="dn-spinner" class="spinner-border text-danger d-none" style="width: 2rem; height: 2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span id="dn-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
            </div>
            <div id="dn-sentinel" data-next-url="<?php echo e($debitNotes->appends(request()->query())->nextPageUrl()); ?>" style="height: 1px;"></div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDnModal" tabindex="-1" aria-labelledby="deleteDnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteDnModalLabel"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this debit note?</p>
                <div class="alert alert-warning">
                    <strong>DN No:</strong> <span id="delete-dn-no"></span><br>
                    <strong>Party:</strong> <span id="delete-party"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const filterSelect = document.getElementById('filter_by');
    const searchInput = document.getElementById('search');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const partyTypeSelect = document.getElementById('party_type');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('dn-table-body');
    const tableWrapper = document.getElementById('dn-table-wrapper');
    const overlay = document.getElementById('search-loading');
    
    let observer = null;
    let isLoading = false;
    
    function updatePlaceholder() {
        const filterValue = filterSelect.value;
        const placeholders = {
            'party_name': 'Enter party name...',
            'debit_note_no': 'Enter DN number...',
            'inv_ref_no': 'Enter invoice ref no...',
            'amount': 'Enter minimum amount...'
        };
        
        searchInput.placeholder = placeholders[filterValue] || 'Enter search term...';
        
        if (filterValue === 'amount') {
            searchInput.type = 'number';
            searchInput.step = '0.01';
        } else {
            searchInput.type = 'text';
            searchInput.removeAttribute('step');
        }
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

    async function fetchDebitNotes(urlOrParams, pushState = true) {
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
            const newTableBody = doc.querySelector('#dn-table-body');
            const newFooter = doc.querySelector('.card-footer');
            
            if (newTableBody) tableBody.innerHTML = newTableBody.innerHTML;
            if (newFooter) {
                const currentFooter = document.querySelector('.card-footer');
                if (currentFooter) currentFooter.innerHTML = newFooter.innerHTML;
            }
            
            if (tableWrapper) tableWrapper.scrollTo({ top: 0, behavior: 'smooth' });
            initInfiniteScroll();
            
            if (pushState) window.history.pushState({}, '', url.toString());
        } catch (e) {
            console.error(e);
            alert('Failed to load debit notes.');
        } finally {
            setLoading(false);
        }
    }

    const debouncedSearch = debounce(() => fetchDebitNotes(getFormParams()), 300);
    searchInput.addEventListener('input', debouncedSearch);
    filterSelect.addEventListener('change', () => { updatePlaceholder(); fetchDebitNotes(getFormParams()); });
    dateFromInput && dateFromInput.addEventListener('change', () => fetchDebitNotes(getFormParams()));
    dateToInput && dateToInput.addEventListener('change', () => fetchDebitNotes(getFormParams()));
    partyTypeSelect && partyTypeSelect.addEventListener('change', () => fetchDebitNotes(getFormParams()));
    filterForm.addEventListener('submit', (e) => { e.preventDefault(); fetchDebitNotes(getFormParams()); });

    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(){
            if (filterSelect) filterSelect.value = 'party_name';
            if (searchInput) searchInput.value = '';
            if (dateFromInput) dateFromInput.value = '';
            if (dateToInput) dateToInput.value = '';
            if (partyTypeSelect) partyTypeSelect.value = '';
            updatePlaceholder();
            fetchDebitNotes(new URLSearchParams());
        });
    }

    // Delete functionality
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.delete-dn');
        if (!button) return;

        const dnId = button.getAttribute('data-dn-id');
        const dnNo = button.getAttribute('data-dn-no');
        const party = button.getAttribute('data-party');
        
        document.getElementById('delete-dn-no').textContent = dnNo;
        document.getElementById('delete-party').textContent = party;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteDnModal'));
        modal.show();
        
        document.getElementById('confirm-delete').onclick = function() {
            deleteDebitNote(dnId, modal);
        };
    });

    function deleteDebitNote(dnId, modal) {
        const confirmBtn = document.getElementById('confirm-delete');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(`<?php echo e(url('admin/debit-note')); ?>/${dnId}`, {
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
                fetchDebitNotes(window.location.href, false);
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => alert('Error deleting debit note'))
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Delete';
        });
    }

    // Infinite scroll
    function initInfiniteScroll() {
        if (observer) observer.disconnect();

        const sentinel = document.getElementById('dn-sentinel');
        const spinner = document.getElementById('dn-spinner');
        const loadText = document.getElementById('dn-load-text');

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
                params.forEach((value, key) => { if (value) url.searchParams.set(key, value); });

                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#dn-table-body tr');

                const realRows = Array.from(newRows).filter(tr => {
                    const tds = tr.querySelectorAll('td');
                    return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                });

                realRows.forEach(tr => tableBody.appendChild(tr));

                const newFooter = doc.querySelector('.card-footer');
                if (newFooter) {
                    const currentFooter = document.querySelector('.card-footer');
                    if (currentFooter) currentFooter.innerHTML = newFooter.innerHTML;
                }

                const newSentinel = doc.querySelector('#dn-sentinel');
                if (newSentinel) {
                    sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                    spinner && spinner.classList.add('d-none');
                    loadText && (loadText.textContent = 'Scroll for more');
                    isLoading = false;
                } else {
                    observer.disconnect();
                    sentinel.remove();
                    spinner && spinner.remove();
                    loadText && (loadText.textContent = 'All records loaded');
                }
            } catch (e) {
                console.error(e);
                spinner && spinner.classList.add('d-none');
                loadText && (loadText.textContent = 'Error loading');
                isLoading = false;
            }
        }

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting && !isLoading) loadMore(); });
        }, { rootMargin: '100px' });

        observer.observe(sentinel);
    }

    window.addEventListener('popstate', function() { fetchDebitNotes(window.location.href, false); });
    initInfiniteScroll();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/debit-note/invoices.blade.php ENDPATH**/ ?>