<?php $__env->startSection('title', 'Sales Men'); ?>

<?php $__env->startSection('content'); ?>
<style>
  /* Scroll to Top Button Styles */
  #scrollToTop {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    background: #0d6efd;
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
  }
  
  #scrollToTop.show {
    opacity: 1;
    visibility: visible;
  }
  
  #scrollToTop:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    background: #0b5ed7;
  }
  
  #scrollToTop i {
    font-size: 22px;
  }
  
  /* Smooth scroll */
  .content {
    scroll-behavior: smooth !important;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div>
      <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-person-badge me-2"></i> Sales Men</h4>
      <div class="text-muted small">Manage your sales team</div>
    </div>
    <?php echo $__env->make('layouts.partials.module-shortcuts', [
        'createRoute' => route('admin.sales-men.create'),
        'tableBodyId' => 'salesmen-table-body',
        'checkboxClass' => 'sales-men-checkbox'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
  <div class="d-flex gap-2">
    <button type="button" id="delete-selected-sales-men-btn" class="btn btn-danger d-none" onclick="confirmMultipleDeleteSalesMen()">
      <i class="bi bi-trash me-1"></i> Delete Selected (<span id="selected-sales-men-count">0</span>)
    </button>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="<?php echo e(route('admin.sales-men.index')); ?>" class="row g-3" id="salesmen-filter-form">
        <div class="col-md-3">
          <label for="search_field" class="form-label">Search By</label>
          <select class="form-select" id="search_field" name="search_field">
            <option value="all" <?php echo e(request('search_field', 'all') == 'all' ? 'selected' : ''); ?>>All Fields</option>
            <option value="name" <?php echo e(request('search_field') == 'name' ? 'selected' : ''); ?>>Name</option>
            <option value="code" <?php echo e(request('search_field') == 'code' ? 'selected' : ''); ?>>Code</option>
            <option value="mobile" <?php echo e(request('search_field') == 'mobile' ? 'selected' : ''); ?>>Mobile</option>
            <option value="telephone" <?php echo e(request('search_field') == 'telephone' ? 'selected' : ''); ?>>Telephone</option>
            <option value="email" <?php echo e(request('search_field') == 'email' ? 'selected' : ''); ?>>Email</option>
            <option value="city" <?php echo e(request('search_field') == 'city' ? 'selected' : ''); ?>>City</option>
            <option value="area_mgr_name" <?php echo e(request('search_field') == 'area_mgr_name' ? 'selected' : ''); ?>>Area Manager</option>
          </select>
        </div>
        <div class="col-md-9">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="salesmen-search" name="search" value="<?php echo e(request('search')); ?>" 
                   placeholder="Type to search..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
              <i class="bi bi-x-circle"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <div class="table-responsive" id="salesmen-table-wrapper" style="position: relative;">
    <div id="search-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    
    <table class="table align-middle mb-0" id="salesmen-table">
      <thead class="table-light">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-sales-men">
              <label class="form-check-label" for="select-all-sales-men">
                <span class="visually-hidden">Select All</span>
              </label>
            </div>
          </th>
          <th>#</th>
          <th>Code</th>
          <th>Name</th>
          <th>Mobile</th>
          <th>Email</th>
          <th>City</th>
          <th>Area Manager</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody id="salesmen-table-body">
        <?php $__empty_1 = true; $__currentLoopData = $salesMen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesMan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <div class="form-check">
                <input class="form-check-input sales-men-checkbox" type="checkbox" value="<?php echo e($salesMan->id); ?>" id="sales-men-<?php echo e($salesMan->id); ?>">
                <label class="form-check-label" for="sales-men-<?php echo e($salesMan->id); ?>">
                  <span class="visually-hidden">Select sales man</span>
                </label>
              </div>
            </td>
            <td><?php echo e(($salesMen->currentPage() - 1) * $salesMen->perPage() + $loop->iteration); ?></td>
            <td><?php echo e($salesMan->code); ?></td>
            <td><?php echo e($salesMan->name); ?></td>
            <td><?php echo e($salesMan->mobile); ?></td>
            <td><?php echo e($salesMan->email); ?></td>
            <td><?php echo e($salesMan->city); ?></td>
            <td><?php echo e($salesMan->areaManager ? ($salesMan->areaManager->code ? $salesMan->areaManager->code . ' - ' : '') . $salesMan->areaManager->name : ($salesMan->area_mgr_name ?: '-')); ?></td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary" onclick="viewSalesManDetails(<?php echo e($salesMan->id); ?>)" title="View">
                <i class="bi bi-eye"></i>
              </button>
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('admin.sales-men.edit', $salesMan)); ?>" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="<?php echo e(route('admin.sales-men.destroy', $salesMan)); ?>" method="POST" class="d-inline ajax-delete-form">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" 
                        data-delete-url="<?php echo e(route('admin.sales-men.destroy', $salesMan)); ?>"
                        data-delete-message="Delete this sales man?" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="9" class="text-center text-muted">No data</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="card-footer bg-light d-flex flex-column gap-2">
    <div class="align-self-start">
      Showing <?php echo e($salesMen->firstItem() ?? 0); ?>-<?php echo e($salesMen->lastItem() ?? 0); ?> of <?php echo e($salesMen->total()); ?>

    </div>
    <?php if($salesMen->hasMorePages()): ?>
      <div class="d-flex align-items-center justify-content-center gap-2">
        <div id="salesmen-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span id="salesmen-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
      </div>
      <div id="salesmen-sentinel" data-next-url="<?php echo e($salesMen->appends(request()->query())->nextPageUrl()); ?>" style="height: 1px;"></div>
    <?php endif; ?>
  </div>
</div>

<!-- Sales Man Details Modal -->
<div id="salesManDetailsModal" class="salesman-modal">
  <div class="salesman-modal-content">
    <div class="salesman-modal-header">
      <h5 class="salesman-modal-title">
        <i class="bi bi-person-badge me-2"></i>Sales Man Details
      </h5>
      <button type="button" class="btn-close-modal" onclick="closeSalesManModal()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="salesman-modal-body" id="salesManModalBody">
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading details...</div>
      </div>
    </div>
  </div>
</div>

<div id="salesManModalBackdrop" class="salesman-modal-backdrop"></div>

<!-- Scroll to Top Button -->
<button id="scrollToTop" type="button" title="Scroll to top" onclick="scrollToTopNow()">
  <i class="bi bi-arrow-up"></i>
</button>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('salesmen-table-body');
  const searchInput = document.getElementById('salesmen-search');
  const clearSearchBtn = document.getElementById('clear-search');
  const searchFieldSelect = document.getElementById('search_field');
  const filterForm = document.getElementById('salesmen-filter-form');

  let searchTimeout;
  let isLoading = false;
  let observer = null;

  // Real-time search implementation
  let isSearching = false;

  function performSearch() {
    if (isSearching) return;
    isSearching = true;

    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);

    // Show loading overlay
    const loadingSpinner = document.getElementById('search-loading');
    if (loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }

    // Add visual feedback to search input
    if (searchInput) {
      searchInput.style.opacity = '0.6';
    }

    fetch(`<?php echo e(route('admin.sales-men.index')); ?>?${params.toString()}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      // Create a temporary container to parse HTML
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;

      // Find the tbody in the temporary container
      const tempTbody = tempDiv.querySelector('#salesmen-table-body');

      if (!tempTbody) {
        console.error('Could not find salesmen-table-body in response');
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error: Table not found in response</td></tr>';
        return;
      }

      // Clear tbody FIRST before adding anything
      tbody.innerHTML = '';

      // Get all rows from the temporary tbody
      const tempRows = tempTbody.querySelectorAll('tr');

      // Add all rows (including "no sales men found" message if any)
      if (tempRows.length > 0) {
        tempRows.forEach(tr => {
          tbody.appendChild(tr.cloneNode(true));
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No sales men found</td></tr>';
      }

      // Update footer with pagination info
      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }

      // Reset infinite scroll observer completely
      isLoading = false;
      if (observer) {
        observer.disconnect();
        observer = null;
      }

      // Reinitialize infinite scroll after DOM update
      setTimeout(() => {
        initInfiniteScroll();
      }, 50);

      // Update count after search refresh
      setTimeout(() => {
        window.updateSalesMenSelectedCount && window.updateSalesMenSelectedCount();
      }, 100);
    })
    .catch(error => {
      console.error('Search error:', error);
      tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Search failed. Please try again.</td></tr>';
    })
    .finally(() => {
      isSearching = false;
      
      // Hide loading spinner
      const loadingSpinner = document.getElementById('search-loading');
      if (loadingSpinner) {
        loadingSpinner.style.display = 'none';
      }
      
      // Restore search input opacity
      if (searchInput) {
        searchInput.style.opacity = '1';
      }
    });
  }

  // Search input event listener
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(performSearch, 300);
    });
  }

  // Search field change event listener
  if (searchFieldSelect) {
    searchFieldSelect.addEventListener('change', function() {
      if (searchInput) {
        searchInput.value = '';
      }
      performSearch();
    });
  }

  // Clear search button
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
      if (searchInput) {
        searchInput.value = '';
      }
      if (searchFieldSelect) {
        searchFieldSelect.value = 'all';
      }
      performSearch();
    });
  }

  // Infinite scroll implementation
  function initInfiniteScroll() {
    const sentinel = document.getElementById('salesmen-sentinel');
    const spinner = document.getElementById('salesmen-spinner');
    const loadText = document.getElementById('salesmen-load-text');

    if (!sentinel || isLoading) return;

    observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
          const nextUrl = sentinel.getAttribute('data-next-url');
          if (nextUrl) {
            loadMore(nextUrl);
          }
        }
      });
    }, { rootMargin: '100px' });

    observer.observe(sentinel);
  }

  function loadMore(url) {
    if (isLoading) return;
    isLoading = true;

    const spinner = document.getElementById('salesmen-spinner');
    const loadText = document.getElementById('salesmen-load-text');

    if (spinner) spinner.classList.remove('d-none');
    if (loadText) loadText.textContent = 'Loading...';

    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);
    const urlWithParams = `${url}&${params.toString()}`;

    fetch(urlWithParams, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.text())
    .then(html => {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      const newRows = tempDiv.querySelectorAll('#salesmen-table-body tr');
      
      // Filter out empty message rows
      const realRows = Array.from(newRows).filter(tr => {
        const tds = tr.querySelectorAll('td');
        const hasColspan = tr.querySelector('td[colspan]');
        return !(tds.length === 1 && hasColspan);
      });

      // Append new rows
      realRows.forEach(tr => {
        tbody.appendChild(tr.cloneNode(true));
      });

      // Update footer
      const newFooter = tempDiv.querySelector('.card-footer');
      const currentFooter = document.querySelector('.card-footer');
      if (newFooter && currentFooter) {
        currentFooter.innerHTML = newFooter.innerHTML;
      }

      // Update sentinel
      const newSentinel = tempDiv.querySelector('#salesmen-sentinel');
      const currentSentinel = document.getElementById('salesmen-sentinel');
      if (newSentinel && currentSentinel) {
        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url') || '');
      } else if (currentSentinel) {
        currentSentinel.remove();
      }

      // Update count after infinite append
      setTimeout(() => {
        window.updateSalesMenSelectedCount && window.updateSalesMenSelectedCount();
      }, 100);
    })
    .catch(error => {
      console.error('Load more error:', error);
    })
    .finally(() => {
      isLoading = false;
      if (spinner) spinner.classList.add('d-none');
      if (loadText) loadText.textContent = 'Scroll for more';
    });
  }

  // Initialize infinite scroll on page load
  initInfiniteScroll();

  // Expose perform function for modal success callback
  window.performSalesMenSearch = performSearch;

  // Multiple delete functionality for sales men
  // Global function to update selected count for sales men
  window.updateSalesMenSelectedCount = function() {
    const checkedBoxes = document.querySelectorAll('.sales-men-checkbox:checked');
    const count = checkedBoxes.length;
    
    // Get fresh references each time
    const deleteBtn = document.getElementById('delete-selected-sales-men-btn');
    const countSpan = document.getElementById('selected-sales-men-count');
    const selectAllCheckbox = document.getElementById('select-all-sales-men');
    
    if (countSpan) {
      countSpan.textContent = count;
    }
    
    if (deleteBtn) {
      if (count > 0) {
        deleteBtn.classList.remove('d-none');
      } else {
        deleteBtn.classList.add('d-none');
      }
    }
    
    // Update select all checkbox state
    if (selectAllCheckbox) {
      const allCheckboxes = document.querySelectorAll('.sales-men-checkbox');
      if (count === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
      } else if (count === allCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
      } else {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
      }
    }
  };

  // Use event delegation for checkboxes - attach to tbody
  if(tbody) {
    tbody.addEventListener('change', function(e) {
      if(e.target && e.target.classList.contains('sales-men-checkbox')) {
        window.updateSalesMenSelectedCount();
      }
    });
  }

  // Handle select-all checkbox
  const selectAllCheckbox = document.getElementById('select-all-sales-men');
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.sales-men-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      window.updateSalesMenSelectedCount();
    });
  }

  // Global function to reattach event listeners for sales men
  window.reattachSalesMenEventListeners = function() {
    // With event delegation, we don't need to reattach individual checkbox listeners
    // Just update the count
    window.updateSalesMenSelectedCount();
  };

  // Initial count update
  window.updateSalesMenSelectedCount();

  // Scroll to top functionality
  const scrollToTopBtn = document.getElementById('scrollToTop');
  
  function toggleScrollButton() {
    if (window.pageYOffset > 200) {
      scrollToTopBtn.classList.add('show');
    } else {
      scrollToTopBtn.classList.remove('show');
    }
  }

  window.addEventListener('scroll', toggleScrollButton);
  
  window.scrollToTopNow = function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };
});

// Sales Man Modal Functions
function viewSalesManDetails(salesManId) {
  const modal = document.getElementById('salesManDetailsModal');
  const backdrop = document.getElementById('salesManModalBackdrop');
  const modalBody = document.getElementById('salesManModalBody');

  backdrop.style.display = 'block';
  modal.style.display = 'block';

  setTimeout(() => {
    backdrop.classList.add('show');
    modal.classList.add('show');
  }, 10);

  // Show loading spinner
  modalBody.innerHTML = `
    <div class="text-center py-3">
      <div class="spinner-border spinner-border-sm text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="mt-2 small">Loading details...</div>
    </div>
  `;

  // Fetch sales man details
  const url = `<?php echo e(url('/admin/sales-men')); ?>/${salesManId}`;
  fetch(url, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      populateSalesManData(data.data);
    } else {
      showErrorInModal(data.message || 'Failed to load sales man details');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showErrorInModal('Failed to load sales man details. Please try again.');
  });
}

function populateSalesManData(data) {
  const modalBody = document.getElementById('salesManModalBody');
  
  let html = '<div class="row g-3">';

  // Basic Information Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-person-fill me-2"></i>Basic Information</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Name:</small>
              <div class="fw-semibold">${data.name || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Code:</small>
              <div class="fw-semibold"><span class="badge bg-secondary">${data.code || '-'}</span></div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Email:</small>
              <div class="fw-semibold">${data.email || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Mobile:</small>
              <div class="fw-semibold">${data.mobile || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Telephone:</small>
              <div class="fw-semibold">${data.telephone || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">City:</small>
              <div class="fw-semibold">${data.city || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">PIN:</small>
              <div class="fw-semibold">${data.pin || '-'}</div>
            </div>
            <div class="col-12">
              <small class="text-muted">Address:</small>
              <div class="fw-semibold">${data.address || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Status:</small>
              <div class="fw-semibold">${data.status || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Sales & Delivery Configuration Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white py-2">
          <h6 class="mb-0"><i class="bi bi-briefcase-fill me-2"></i>Sales & Delivery Configuration</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Sales Type:</small>
              <div class="fw-semibold">
                ${data.sales_type == 'S' ? '<span class="badge bg-primary">Sales Man</span>' : 
                  data.sales_type == 'C' ? '<span class="badge bg-info">Collection Boy</span>' : 
                  '<span class="badge bg-success">Both</span>'}
              </div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Delivery Type:</small>
              <div class="fw-semibold">
                ${data.delivery_type == 'S' ? '<span class="badge bg-primary">Sales Man</span>' : 
                  data.delivery_type == 'D' ? '<span class="badge bg-warning">Delivery Man</span>' : 
                  '<span class="badge bg-success">Both</span>'}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Area Manager & Targets Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-dark py-2">
          <h6 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Area Manager & Targets</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Area Mgr. Code:</small>
              <div class="fw-semibold">${data.area_mgr_code || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Area Mgr. Name:</small>
              <div class="fw-semibold">${data.area_mgr_name || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Monthly Target:</small>
              <div class="fw-semibold">
                ${data.monthly_target > 0 ? '<span class="text-success">₹' + parseFloat(data.monthly_target).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</span>' : '<span class="text-muted">₹0.00</span>'}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Timestamps Section
  html += `
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-secondary text-white py-2">
          <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Timestamps</h6>
        </div>
        <div class="card-body py-2">
          <div class="row g-2">
            <div class="col-md-6">
              <small class="text-muted">Created At:</small>
              <div class="fw-semibold">${data.created_at || '-'}</div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Last Updated:</small>
              <div class="fw-semibold">${data.updated_at || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  html += '</div>';
  modalBody.innerHTML = html;
}

function showErrorInModal(message) {
  const modalBody = document.getElementById('salesManModalBody');
  modalBody.innerHTML = `
    <div class="text-center py-4">
      <div class="text-danger mb-3">
        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
      </div>
      <h6 class="text-danger">Error</h6>
      <p class="text-muted">${message}</p>
      <button class="btn btn-outline-secondary btn-sm" onclick="closeSalesManModal()">Close</button>
    </div>
  `;
}

function closeSalesManModal() {
  const modal = document.getElementById('salesManDetailsModal');
  const backdrop = document.getElementById('salesManModalBackdrop');

  modal.classList.remove('show');
  backdrop.classList.remove('show');

  setTimeout(() => {
    modal.style.display = 'none';
    backdrop.style.display = 'none';
  }, 300);
}

// Close modal when clicking backdrop
document.addEventListener('click', function (e) {
  if (e.target && e.target.id === 'salesManModalBackdrop') {
    closeSalesManModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('salesManDetailsModal');
    if (modal && modal.classList.contains('show')) {
      closeSalesManModal();
    }
  }
});

// Multiple delete confirmation function for sales men (global scope for onclick)
function confirmMultipleDeleteSalesMen() {
  const checkedBoxes = document.querySelectorAll('.sales-men-checkbox:checked');
  if (checkedBoxes.length === 0) {
    return;
  }

  const selectedItems = [];
  checkedBoxes.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const name = row.querySelector('td:nth-child(4)').textContent.trim(); // Name column after checkbox + # + code
    selectedItems.push({
      id: checkbox.value,
      name: name
    });
  });

  window.GlobalMultipleDelete.show({
    selectedItems: selectedItems,
    deleteUrl: '<?php echo e(route('admin.sales-men.multiple-delete')); ?>',
    itemType: 'sales-men',
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    onSuccess: function(data) {
      window.performSalesMenSearch();
    },
    onError: function(error) {
      console.error('Error deleting sales men:', error);
      if (window.crudNotification) {
        crudNotification.showToast('error', 'Error', 'Failed to delete selected sales men. Please try again.');
      }
    }
  });
}
</script>

<style>
/* Slide-in Modal Styles */
.salesman-modal {
  display: none;
  position: fixed;
  top: 70px;
  right: 0;
  width: 450px;
  height: calc(100vh - 100px);
  max-height: calc(100vh - 140px);
  z-index: 999999 !important;
  transform: translateX(100%);
  transition: transform 0.3s ease-in-out;
}

.salesman-modal.show {
  transform: translateX(0);
}

.salesman-modal-content {
  background: white;
  height: 100%;
  box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
}

.salesman-modal-header {
  padding: 1rem 1.25rem;
  border-bottom: 2px solid #dee2e6;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.salesman-modal-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #ffffff;
}

.btn-close-modal {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
}

.btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.salesman-modal-body {
  padding: 1rem;
  overflow-y: auto;
  flex: 1;
  background: #f8f9fa;
}

.salesman-modal-backdrop {
  display: none;
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  background-color: rgba(0, 0, 0, 0.5) !important;
  z-index: 999998 !important;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.salesman-modal-backdrop.show {
  display: block !important;
  opacity: 1 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .salesman-modal {
    width: 100%;
  }
  
  .salesman-modal-backdrop {
    left: 0; /* Full width on mobile */
    width: 100vw;
  }
}

@media (max-width: 576px) {
  .salesman-modal-body {
    padding: 0.75rem;
  }

  .salesman-modal-header {
    padding: 0.75rem 1rem;
  }
}

/* Card styling in modal */
.salesman-modal .card {
  margin-bottom: 1rem;
  border-radius: 0.5rem;
  overflow: hidden;
}

.salesman-modal .card:last-child {
  margin-bottom: 0;
}

.salesman-modal .card-header {
  font-size: 0.9rem;
  padding: 0.75rem 1rem;
  font-weight: 600;
}

.salesman-modal .card-body {
  padding: 1rem;
  background: white;
}

.salesman-modal .card-body small {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.salesman-modal .fw-semibold {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

/* Smooth scrollbar for modal */
.salesman-modal-body::-webkit-scrollbar {
  width: 8px;
}

.salesman-modal-body::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.salesman-modal-body::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.salesman-modal-body::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sales-men/index.blade.php ENDPATH**/ ?>