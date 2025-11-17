@extends('layouts.admin')

@section('title', 'All Ledger')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-journal-text me-2"></i>All Ledger
            </h4>
            <div class="text-muted small">View all ledger accounts</div>
        </div>
    </div>

    <div class="card shadow-sm">
        <!-- Advanced Search Filter -->
        <div class="card-body">
            <form method="GET" action="{{ route('admin.all-ledger.index') }}" class="row g-3" id="ledger-filter-form">
                <div class="col-md-3">
                    <label for="search_field" class="form-label">
                        <i class="bi bi-funnel me-1"></i>Search By
                    </label>
                    <select class="form-select" id="search_field" name="search_field">
                        <option value="all" {{ request('search_field', 'all') == 'all' ? 'selected' : '' }}>All Fields
                        </option>
                        <option value="split_name" {{ request('search_field') == 'split_name' ? 'selected' : '' }}>Split Name
                        </option>
                        <option value="alter_code" {{ request('search_field') == 'alter_code' ? 'selected' : '' }}>AlterCode
                        </option>
                        <option value="mobile" {{ request('search_field') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="telephone" {{ request('search_field') == 'telephone' ? 'selected' : '' }}>Telephone
                        </option>
                        <option value="address" {{ request('search_field') == 'address' ? 'selected' : '' }}>Address</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">
                        <i class="bi bi-search me-1"></i>Search
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="ledger-search" name="search"
                            value="{{ request('search') }}" placeholder="Type to search..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="ledger_type_filter" class="form-label">
                        <i class="bi bi-filter me-1"></i>Ledger Type
                    </label>
                    <select class="form-select" id="ledger-type-filter" name="ledger_type">
                        <option value="">All Types</option>
                        <option value="Customer" {{ request('ledger_type') == 'Customer' ? 'selected' : '' }}>Customer
                        </option>
                        <option value="Supplier" {{ request('ledger_type') == 'Supplier' ? 'selected' : '' }}>Supplier
                        </option>
                        <option value="General Ledger" {{ request('ledger_type') == 'General Ledger' ? 'selected' : '' }}>
                            General Ledger</option>
                        <option value="Cash / Bank" {{ request('ledger_type') == 'Cash / Bank' ? 'selected' : '' }}>Cash /
                            Bank</option>
                        <option value="Sale Ledger" {{ request('ledger_type') == 'Sale Ledger' ? 'selected' : '' }}>Sale
                            Ledger</option>
                        <option value="Purchase Ledger" {{ request('ledger_type') == 'Purchase Ledger' ? 'selected' : '' }}>
                            Purchase Ledger</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-1">
                    <button type="button" class="btn btn-primary" id="filter-btn" title="Apply Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clear-filter-btn" title="Clear All Filters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive" id="ledger-table-wrapper" style="position: relative;">
            <div id="search-loading"
                style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Name</th>
                        <th style="width: 15%;">Code</th>
                        <th style="width: 15%;">Ledger Type</th>
                        <th style="width: 12%;" class="text-end">Debit</th>
                        <th style="width: 12%;" class="text-end">Credit</th>
                        <th style="width: 11%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="ledger-table-body">
                    @forelse($paginator->items() as $index => $ledger)
                        <tr class="ledger-row" data-ledger-type="{{ $ledger['ledger_type'] }}">
                            <td>{{ ($paginator->currentPage() - 1) * $paginator->perPage() + $loop->iteration }}</td>
                            <td>
                                <div>{{ $ledger['name'] }}</div>
                            </td>
                            <td>{{ $ledger['code'] }}</td>
                            <td>
                                <span>{{ $ledger['ledger_type'] }}</span>
                            </td>
                            <td class="text-end">
                                @if($ledger['debit'] > 0)
                                    <span>₹{{ number_format($ledger['debit'], 2) }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($ledger['credit'] > 0)
                                    <span>₹{{ number_format($ledger['credit'], 2) }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewLedgerDetails('{{ $ledger['ledger_type'] }}', '{{ $ledger['id'] }}')"
                                    title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox me-2"></i>No ledger entries found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex flex-column gap-2">
            <div class="align-self-start">Showing {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of
                {{ $paginator->total() }}</div>
            @if($paginator->hasMorePages())
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <div id="ledger-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;"
                        role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span id="ledger-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
                </div>
                <div id="ledger-sentinel" data-next-url="{{ $paginator->appends(request()->query())->nextPageUrl() }}"
                    style="height: 1px;"></div>
            @endif
        </div>
    </div>

    <div id="ledgerDetailsModal" class="ledger-modal">
        <div class="ledger-modal-content">
            <div class="ledger-modal-header">
                <h5 class="ledger-modal-title">
                    <i class="bi bi-info-circle me-2"></i>Ledger Details
                </h5>
                <button type="button" class="btn-close-modal" onclick="closeLedgerModal()" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="ledger-modal-body" id="ledgerModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading details...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModalBackdrop" class="ledger-modal-backdrop"></div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.height = '';

            const existingBackdrops = document.querySelectorAll('.modal-backdrop');
            existingBackdrops.forEach(backdrop => backdrop.remove());

            const searchInput = document.getElementById('ledger-search');
            const clearSearchBtn = document.getElementById('clear-search');
            const searchFieldSelect = document.getElementById('search_field');
            const ledgerTypeSelect = document.getElementById('ledger-type-filter');
            const filterBtn = document.getElementById('filter-btn');
            const clearFilterBtn = document.getElementById('clear-filter-btn');
            const filterForm = document.getElementById('ledger-filter-form');
            const sentinel = document.getElementById('ledger-sentinel');
            const spinner = document.getElementById('ledger-spinner');
            const loadText = document.getElementById('ledger-load-text');
            const tbody = document.getElementById('ledger-table-body');
            let searchTimeout;
            let isSearching = false;

            function performSearch() {
                if (isSearching) return;
                isSearching = true;

                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);

                const loadingSpinner = document.getElementById('search-loading');
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'flex';
                }

                if (searchInput) {
                    searchInput.style.opacity = '0.6';
                }

                fetch(`{{ route('admin.all-ledger.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        const tempTbody = tempDiv.querySelector('#ledger-table-body');
                        const tempRows = tempTbody.querySelectorAll('tr');

                        const realRows = Array.from(tempRows).filter(tr => {
                            const tds = tr.querySelectorAll('td');
                            const hasColspan = tr.querySelector('td[colspan]');
                            return !(tds.length === 1 && hasColspan);
                        });

                        tbody.innerHTML = '';
                        if (realRows.length) {
                            realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));
                        } else {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No ledgers found</td></tr>';
                        }

                        const newFooter = tempDiv.querySelector('.card-footer');
                        const currentFooter = document.querySelector('.card-footer');
                        if (newFooter && currentFooter) {
                            currentFooter.innerHTML = newFooter.innerHTML;
                        }

                        setupInfiniteScroll();
                    })
                    .catch(error => {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
                    })
                    .finally(() => {
                        isSearching = false;

                        const loadingSpinner = document.getElementById('search-loading');
                        if (loadingSpinner) {
                            loadingSpinner.style.display = 'none';
                        }

                        if (searchInput) {
                            searchInput.style.opacity = '1';
                        }
                        const s = document.getElementById('ledger-spinner');
                        const t = document.getElementById('ledger-load-text');
                        s && s.classList.add('d-none');
                        t && (t.textContent = 'Scroll for more');
                    });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(performSearch, 300);
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                        performSearch();
                    }
                });
            }

            if (searchFieldSelect) {
                searchFieldSelect.addEventListener('change', function () {
                    performSearch();
                });
            }

            if (filterBtn) {
                filterBtn.addEventListener('click', function () {
                    performSearch();
                });
            }

            if (clearFilterBtn) {
                clearFilterBtn.addEventListener('click', function () {
                    if (searchInput) searchInput.value = '';
                    if (searchFieldSelect) searchFieldSelect.value = 'all';
                    if (ledgerTypeSelect) ledgerTypeSelect.value = '';

                    performSearch();

                    if (searchInput) searchInput.focus();
                });
            }

            if (ledgerTypeSelect) {
                ledgerTypeSelect.addEventListener('change', function () {
                    setTimeout(() => {
                        performSearch();
                    }, 100);
                });
            }

            let isLoading = false;
            let observer = null;

            function setupInfiniteScroll() {
                const currentSentinel = document.getElementById('ledger-sentinel');
                if (!currentSentinel || !tbody) return;

                if (observer) {
                    observer.disconnect();
                }

                const contentDiv = document.querySelector('.content');

                observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !isLoading) {
                            loadMore();
                        }
                    });
                }, {
                    root: contentDiv,
                    threshold: 0.1,
                    rootMargin: '100px'
                });

                observer.observe(currentSentinel);
            }

            async function loadMore() {
                if (isLoading) return;
                const currentSentinel = document.getElementById('ledger-sentinel');
                if (!currentSentinel) return;

                const nextUrl = currentSentinel.getAttribute('data-next-url');
                if (!nextUrl) return;

                isLoading = true;
                const spinner = document.getElementById('ledger-spinner');
                const loadText = document.getElementById('ledger-load-text');
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
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const tempTbody = tempDiv.querySelector('#ledger-table-body');
                    const tempRows = tempTbody.querySelectorAll('tr');
                    const realRows = Array.from(tempRows).filter(tr => {
                        const tds = tr.querySelectorAll('td');
                        return !(tds.length === 1 && tr.querySelector('td[colspan]'));
                    });
                    realRows.forEach(tr => tbody.appendChild(tr.cloneNode(true)));

                    const newSentinel = tempDiv.querySelector('#ledger-sentinel');
                    if (newSentinel) {
                        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                        spinner && spinner.classList.add('d-none');
                        loadText && (loadText.textContent = 'Scroll for more');
                        isLoading = false;
                    } else {
                        observer && observer.disconnect();
                        currentSentinel.remove();
                        spinner && spinner.remove();
                        loadText && loadText.remove();
                    }
                } catch (e) {
                    spinner && spinner.classList.add('d-none');
                    loadText && (loadText.textContent = 'Error loading');
                    isLoading = false;
                }
            }

            setupInfiniteScroll();
            
            // Preload first few items after a short delay
            setTimeout(() => {
                preloadVisibleItems();
            }, 1000);
        });

        // Cache for storing ledger data to avoid repeated API calls
        const ledgerCache = new Map();
        
        // Preload data for visible items (optional optimization)
        function preloadVisibleItems() {
            const visibleRows = document.querySelectorAll('.ledger-row');
            visibleRows.forEach((row, index) => {
                if (index < 5) { // Preload first 5 items
                    const button = row.querySelector('button[onclick*="viewLedgerDetails"]');
                    if (button) {
                        const onclick = button.getAttribute('onclick');
                        const matches = onclick.match(/viewLedgerDetails\('([^']+)',\s*'([^']+)'\)/);
                        if (matches) {
                            const [, ledgerType, ledgerId] = matches;
                            const cacheKey = `${ledgerType}_${ledgerId}`;
                            
                            if (!ledgerCache.has(cacheKey)) {
                                // Preload in background without showing modal
                                fetch(`{{ route('admin.all-ledger.details') }}?ledger_type=${encodeURIComponent(ledgerType)}&ledger_id=${encodeURIComponent(ledgerId)}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    priority: 'low'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        ledgerCache.set(cacheKey, data.data);
                                    }
                                })
                                .catch(() => {}); // Silent fail for preloading
                            }
                        }
                    }
                }
            });
        }

        function viewLedgerDetails(ledgerType, ledgerId) {
            const modal = document.getElementById('ledgerDetailsModal');
            const backdrop = document.getElementById('ledgerModalBackdrop');
            const modalBody = document.getElementById('ledgerModalBody');
            const header = document.querySelector('.navbar');
            const sidebar = document.querySelector('.sidebar');

            // Prevent header and sidebar from being affected by modal
            if (header) {
                header.style.zIndex = '999999';
                header.style.position = 'relative';
            }
            if (sidebar) {
                sidebar.style.zIndex = '999999';
                sidebar.style.position = 'relative';
            }

            backdrop.style.display = 'block';
            modal.style.display = 'block';

            setTimeout(() => {
                backdrop.classList.add('show');
                modal.classList.add('show');
            }, 10);

            // Create cache key
            const cacheKey = `${ledgerType}_${ledgerId}`;

            // Check if data is already cached
            if (ledgerCache.has(cacheKey)) {
                // Load from cache instantly
                populateLedgerData(ledgerCache.get(cacheKey));
                return;
            }

            // Show loading spinner
            modalBody.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 small">Loading details...</div>
            </div>
        `;

            // Optimized fetch with faster options
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

            fetch(`{{ route('admin.all-ledger.details') }}?ledger_type=${encodeURIComponent(ledgerType)}&ledger_id=${encodeURIComponent(ledgerId)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                signal: controller.signal,
                priority: 'high' // High priority fetch
            })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Cache the data for future use
                        ledgerCache.set(cacheKey, data.data);
                        populateLedgerData(data.data);
                    } else {
                        showErrorInModal(data.message || 'Failed to load ledger details');
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    if (error.name === 'AbortError') {
                        showErrorInModal('Request timed out. Please try again.');
                    } else {
                        console.error('Error:', error);
                        showErrorInModal('Error loading ledger details. Please try again.');
                    }
                });
        }

        function closeLedgerModal() {
            const modal = document.getElementById('ledgerDetailsModal');
            const backdrop = document.getElementById('ledgerModalBackdrop');
            const header = document.querySelector('.navbar');
            const sidebar = document.querySelector('.sidebar');

            // Restore header and sidebar styles when modal closes
            if (header) {
                header.style.zIndex = '';
                header.style.position = '';
            }
            if (sidebar) {
                sidebar.style.zIndex = '';
                sidebar.style.position = '';
            }

            // Remove show classes for slide-out animation
            modal.classList.remove('show');
            backdrop.classList.remove('show');

            // Hide modal after animation
            setTimeout(() => {
                modal.style.display = 'none';
                backdrop.style.display = 'none';
            }, 300);
        }

        function populateLedgerData(data) {
            const modalBody = document.getElementById('ledgerModalBody');

            let html = '<div class="row g-3">';

            // Basic Information Section
            if (data.basic_info && Object.keys(data.basic_info).length > 0) {
                html += `
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
            `;

                for (const [key, value] of Object.entries(data.basic_info)) {
                    if (value !== null && value !== '') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        html += `
                        <div class="col-md-6">
                            <small class="text-muted">${displayKey}:</small>
                            <div class="fw-semibold">${value}</div>
                        </div>
                    `;
                    }
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Contact Information Section
            if (data.contact_info && Object.keys(data.contact_info).length > 0) {
                html += `
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
            `;

                for (const [key, value] of Object.entries(data.contact_info)) {
                    if (value !== null && value !== '') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        html += `
                        <div class="col-md-6">
                            <small class="text-muted">${displayKey}:</small>
                            <div class="fw-semibold">${value}</div>
                        </div>
                    `;
                    }
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Balance Information Section
            if (data.balance_info && Object.keys(data.balance_info).length > 0) {
                html += `
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark py-2">
                            <h6 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Balance Information</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
            `;

                for (const [key, value] of Object.entries(data.balance_info)) {
                    if (value !== null && value !== '') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        let displayValue = value;

                        // Format currency values
                        if (key.includes('balance') || key.includes('amount') || key.includes('debit') || key.includes('credit')) {
                            displayValue = `₹${parseFloat(value).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
                        }

                        html += `
                        <div class="col-md-6">
                            <small class="text-muted">${displayKey}:</small>
                            <div class="fw-semibold">${displayValue}</div>
                        </div>
                    `;
                    }
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Additional Information Section
            if (data.additional_info && Object.keys(data.additional_info).length > 0) {
                html += `
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Additional Information</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
            `;

                for (const [key, value] of Object.entries(data.additional_info)) {
                    if (value !== null && value !== '') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        html += `
                        <div class="col-md-6">
                            <small class="text-muted">${displayKey}:</small>
                            <div class="fw-semibold">${value}</div>
                        </div>
                    `;
                    }
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Bank Details Section
            if (data.bank_details && Object.keys(data.bank_details).length > 0) {
                html += `
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Bank Details</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
            `;

                for (const [key, value] of Object.entries(data.bank_details)) {
                    if (value !== null && value !== '') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        html += `
                        <div class="col-md-6">
                            <small class="text-muted">${displayKey}:</small>
                            <div class="fw-semibold">${value}</div>
                        </div>
                    `;
                    }
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            html += '</div>';

            modalBody.innerHTML = html;
        }

        function showErrorInModal(message) {
            const modalBody = document.getElementById('ledgerModalBody');
            modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                </div>
                <h6 class="text-danger">Error</h6>
                <p class="text-muted">${message}</p>
                <button class="btn btn-outline-secondary btn-sm" onclick="closeLedgerModal()">Close</button>
            </div>
        `;
        }

        // Close modal when clicking backdrop
        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'ledgerModalBackdrop') {
                closeLedgerModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('ledgerDetailsModal');
                if (modal && modal.classList.contains('show')) {
                    closeLedgerModal();
                }
            }
        });
    </script>

    <style>
        .ledger-row {
            transition: background-color 0.2s ease;
        }

        .ledger-row:hover {
            background-color: #f8f9fa;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        /* Ensure scroll-to-top button doesn't interfere */
        #scrollToTop {
            z-index: 9999 !important;
        }

        /* Ensure header and sidebar stay above modal backdrop */
        .navbar, .sidebar {
            z-index: 999999 !important;
            position: relative !important;
        }

        /* Prevent modal backdrop from affecting header */
        .navbar {
            background: white !important;
        }

        /* Slide-in Modal Styles */
        .ledger-modal {
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

        .ledger-modal.show {
            transform: translateX(0);
        }

        .ledger-modal-content {
            background: white;
            height: 100%;
            box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
        }

        .ledger-modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #dee2e6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .ledger-modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffffff;
        }

        /* Close Button in Header */
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

        .ledger-modal-body {
            padding: 1rem;
            overflow-y: auto;
            flex: 1;
            background: #f8f9fa;
        }


           .ledger-modal-backdrop {
            display: none;
            position: fixed;
            top: 70px; /* Start below header */
            left: 260px; /* Start after sidebar width */
            width: calc(100vw - 260px); /* Exclude sidebar width */
            height: calc(100vh - 70px); /* Exclude header height */
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 999997 !important; /* Lower than header and sidebar */
            opacity: 0;
            transition: all 0.3s ease;
        }

        .ledger-modal-backdrop.show {
            opacity: 0.7;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .ledger-modal {
                width: 100%;
            }
            
            .ledger-modal-backdrop {
                left: 0; /* Full width on mobile */
                width: 100vw;
            }
        }

        @media (max-width: 576px) {
            .ledger-modal-body {
                padding: 0.75rem;
            }

            .ledger-modal-header {
                padding: 0.75rem 1rem;
            }

        }

        /* Card styling in modal */
        .ledger-modal .card {
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .ledger-modal .card:last-child {
            margin-bottom: 0;
        }

        .ledger-modal .card-header {
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .ledger-modal .card-body {
            padding: 1rem;
            background: white;
        }

        .ledger-modal .card-body small {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .ledger-modal .fw-semibold {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        /* Smooth scrollbar for modal */
        .ledger-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .ledger-modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .ledger-modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .ledger-modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush