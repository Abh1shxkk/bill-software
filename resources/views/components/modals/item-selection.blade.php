{{--
    Reusable Item Selection Modal Component
    
    Usage - Include in your Blade template:
    @include('components.modals.item-selection', [
        'id' => 'chooseItemsModal',
        'module' => 'sale',
        'showStock' => true,
        'rateType' => 's_rate',
        'batchModalId' => 'batchSelectionModal',
    ])
    
    Available Props:
    - id: Modal element ID (default: 'itemSelectionModal')
    - module: Module context (sale, purchase, etc.)
    - showStock: Show stock column (default: true)
    - showRate: Show rate column (default: true)
    - rateType: Rate column type - 's_rate', 'pur_rate', 'ws_rate' (default: 's_rate')
    - showCompany: Show company column (default: true)
    - showHsn: Show HSN column (default: true)
    - batchModalId: ID of batch modal to open after item selection (default: 'batchSelectionModal')
--}}

@php
    $id = $id ?? 'itemSelectionModal';
    $module = $module ?? 'generic';
    $showStock = $showStock ?? true;
    $showRate = $showRate ?? true;
    $rateType = $rateType ?? 's_rate';
    $showCompany = $showCompany ?? true;
    $showHsn = $showHsn ?? true;
    $batchModalId = $batchModalId ?? 'batchSelectionModal';
@endphp

{{-- Modal Styles --}}
<style>
    /* Item Selection Modal Backdrop */
    #{{ $id }}Backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 99998;
        opacity: 0;
        transition: opacity 0.25s ease;
        backdrop-filter: blur(2px);
    }
    #{{ $id }}Backdrop.show {
        display: block;
        opacity: 1;
    }
    
    /* Item Selection Modal */
    #{{ $id }} {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.85);
        width: 90%;
        max-width: 950px;
        z-index: 99999;
        opacity: 0;
        transition: all 0.25s ease-in-out;
    }
    #{{ $id }}.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    #{{ $id }} .modal-content-box {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }
    #{{ $id }} .modal-header-box {
        padding: 0.9rem 1.25rem;
        background: linear-gradient(135deg, #ff6b35 0%, #f5576c 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #{{ $id }} .modal-title-box {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    #{{ $id }} .btn-close-modal {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        font-size: 1.25rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
    }
    #{{ $id }} .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    #{{ $id }} .modal-body-box {
        max-height: 70vh;
        overflow-y: auto;
    }
    #{{ $id }} .modal-footer-box {
        padding: 0.85rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    #{{ $id }} .item-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    #{{ $id }} .item-row:hover {
        background-color: #e3f2fd !important;
    }
    #{{ $id }} .item-row.row-selected,
    #{{ $id }} .item-row.row-selected td {
        background-color: #1976d2 !important;
        color: white !important;
    }
</style>

{{-- Modal Backdrop --}}
<div id="{{ $id }}Backdrop"></div>

{{-- Item Selection Modal --}}
<div id="{{ $id }}" 
     data-module="{{ $module }}"
     data-batch-modal-id="{{ $batchModalId }}">
    <div class="modal-content-box">
        {{-- Header --}}
        <div class="modal-header-box">
            <h5 class="modal-title-box">
                <i class="bi bi-list-check me-2"></i>Choose Items
            </h5>
            <button type="button" class="btn-close-modal" 
                    onclick="closeItemModal_{{ str_replace('-', '_', $id) }}()" title="Close (Esc)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        {{-- Body --}}
        <div class="modal-body-box">
            {{-- Search Section --}}
            <div class="p-3 bg-light border-bottom">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0" 
                           id="{{ $id }}Search" 
                           placeholder="Search by Name, Code, HSN, Company..." 
                           autocomplete="off"
                           oninput="filterItems_{{ str_replace('-', '_', $id) }}()"
                           onkeydown="handleItemKeyDown_{{ str_replace('-', '_', $id) }}(event)"
                           style="font-size: 12px;">
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Double-click to select item, or use ↑↓ arrows and Enter
                    </small>
                    <span class="badge bg-secondary" id="{{ $id }}Count">0 of 0 items</span>
                </div>
            </div>
            
            {{-- Items Table --}}
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;" id="{{ $id }}TableContainer">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                        <tr>
                            <th style="width: 250px; padding: 8px;">Name</th>
                            @if($showHsn)
                            <th style="width: 90px; padding: 8px;">HSN</th>
                            @endif
                            @if($showStock)
                            <th style="width: 70px; text-align: right; padding: 8px;">Stock</th>
                            @endif
                            @if($showRate)
                            <th style="width: 80px; text-align: right; padding: 8px;">
                                @if($rateType === 'pur_rate')
                                    Pur.Rate
                                @elseif($rateType === 'ws_rate')
                                    WS.Rate
                                @else
                                    S.Rate
                                @endif
                            </th>
                            @endif
                            <th style="width: 80px; text-align: right; padding: 8px;">MRP</th>
                            @if($showCompany)
                            <th style="width: 150px; padding: 8px;">Company</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="{{ $id }}Body">
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <span class="ms-2">Loading items...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                {{-- Load More Spinner (auto-triggers on scroll) --}}
                <div id="{{ $id }}LoadMore" class="text-center py-3" style="display: none; background: #f8f9fa; border-top: 1px solid #dee2e6;">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        <span class="text-muted">Loading more items...</span>
                        <span id="{{ $id }}LoadMoreCount" class="badge bg-secondary ms-2" style="font-size: 10px;">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="modal-footer-box">
            <button type="button" class="btn btn-secondary btn-sm" 
                    onclick="closeItemModal_{{ str_replace('-', '_', $id) }}()">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </button>
        </div>
    </div>
</div>

{{-- Modal JavaScript --}}
<script>
(function() {
    // State for this modal instance
    var modalId = '{{ $id }}';
    var batchModalId = '{{ $batchModalId }}';
    var items_{{ str_replace('-', '_', $id) }} = [];
    var filteredItems_{{ str_replace('-', '_', $id) }} = [];
    var selectedRowIndex_{{ str_replace('-', '_', $id) }} = -1;
    var isLoading_{{ str_replace('-', '_', $id) }} = false;
    
    // Pagination state
    var currentPage_{{ str_replace('-', '_', $id) }} = 0;
    var totalItems_{{ str_replace('-', '_', $id) }} = 0;
    var hasMore_{{ str_replace('-', '_', $id) }} = false;
    var currentSearch_{{ str_replace('-', '_', $id) }} = '';
    
    // Open modal function
    window.openItemModal_{{ str_replace('-', '_', $id) }} = function() {
        var modal = document.getElementById('{{ $id }}');
        var backdrop = document.getElementById('{{ $id }}Backdrop');
        
        if (!modal || !backdrop) return;
        
        selectedRowIndex_{{ str_replace('-', '_', $id) }} = -1;
        
        // Clear search
        var searchInput = document.getElementById('{{ $id }}Search');
        if (searchInput) searchInput.value = '';
        currentSearch_{{ str_replace('-', '_', $id) }} = '';
        
        // Reset and load fresh items
        items_{{ str_replace('-', '_', $id) }} = [];
        currentPage_{{ str_replace('-', '_', $id) }} = 0;
        loadItems_{{ str_replace('-', '_', $id) }}(true);
        
        // Show modal
        setTimeout(function() {
            modal.classList.add('show');
            backdrop.classList.add('show');
            if (searchInput) searchInput.focus();
        }, 10);
        
        // Escape key listener
        document.addEventListener('keydown', escapeHandler_{{ str_replace('-', '_', $id) }});
    };
    
    // Close modal function
    window.closeItemModal_{{ str_replace('-', '_', $id) }} = function() {
        var modal = document.getElementById('{{ $id }}');
        var backdrop = document.getElementById('{{ $id }}Backdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        document.removeEventListener('keydown', escapeHandler_{{ str_replace('-', '_', $id) }});
    };
    
    // Escape handler
    function escapeHandler_{{ str_replace('-', '_', $id) }}(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeItemModal_{{ str_replace('-', '_', $id) }}();
        }
    }
    
    // Load items from API (with pagination)
    window.loadItems_{{ str_replace('-', '_', $id) }} = function(isReset) {
        if (isLoading_{{ str_replace('-', '_', $id) }}) return;
        
        isLoading_{{ str_replace('-', '_', $id) }} = true;
        var tbody = document.getElementById('{{ $id }}Body');
        var loadMoreDiv = document.getElementById('{{ $id }}LoadMore');
        var loadingMoreDiv = document.getElementById('{{ $id }}LoadingMore');
        
        if (isReset) {
            currentPage_{{ str_replace('-', '_', $id) }} = 1;
            items_{{ str_replace('-', '_', $id) }} = [];
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading items...</td></tr>';
        } else {
            currentPage_{{ str_replace('-', '_', $id) }}++;
            if (loadMoreDiv) loadMoreDiv.style.display = 'none';
            if (loadingMoreDiv) loadingMoreDiv.style.display = 'block';
        }
        
        var searchParam = currentSearch_{{ str_replace('-', '_', $id) }} ? '&search=' + encodeURIComponent(currentSearch_{{ str_replace('-', '_', $id) }}) : '';
        var url = '{{ url("/admin/api/items/list") }}?page=' + currentPage_{{ str_replace('-', '_', $id) }} + '&limit=50' + searchParam;
        
        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var newItems = data.items || [];
                totalItems_{{ str_replace('-', '_', $id) }} = data.total || 0;
                hasMore_{{ str_replace('-', '_', $id) }} = data.has_more || false;
                
                // Append or replace items
                if (isReset) {
                    items_{{ str_replace('-', '_', $id) }} = newItems;
                } else {
                    items_{{ str_replace('-', '_', $id) }} = items_{{ str_replace('-', '_', $id) }}.concat(newItems);
                }
                
                filteredItems_{{ str_replace('-', '_', $id) }} = items_{{ str_replace('-', '_', $id) }}.slice();
                displayItems_{{ str_replace('-', '_', $id) }}(items_{{ str_replace('-', '_', $id) }});
                updateLoadMoreButton_{{ str_replace('-', '_', $id) }}();
            })
            .catch(function(error) {
                console.error('Error loading items:', error);
                if (isReset) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error loading items</td></tr>';
                }
            })
            .finally(function() {
                isLoading_{{ str_replace('-', '_', $id) }} = false;
                if (loadingMoreDiv) loadingMoreDiv.style.display = 'none';
            });
    };
    
    // Load more items function
    window.loadMoreItems_{{ str_replace('-', '_', $id) }} = function() {
        if (hasMore_{{ str_replace('-', '_', $id) }} && !isLoading_{{ str_replace('-', '_', $id) }}) {
            loadItems_{{ str_replace('-', '_', $id) }}(false);
        }
    };
    
    // Update Load More spinner visibility and count
    function updateLoadMoreButton_{{ str_replace('-', '_', $id) }}() {
        var loadMoreDiv = document.getElementById('{{ $id }}LoadMore');
        var loadMoreCount = document.getElementById('{{ $id }}LoadMoreCount');
        
        if (loadMoreDiv) {
            if (hasMore_{{ str_replace('-', '_', $id) }}) {
                loadMoreDiv.style.display = 'block';
                var remaining = totalItems_{{ str_replace('-', '_', $id) }} - items_{{ str_replace('-', '_', $id) }}.length;
                if (loadMoreCount) loadMoreCount.textContent = remaining + ' more';
            } else {
                loadMoreDiv.style.display = 'none';
            }
        }
    }
    
    // Setup Intersection Observer for auto-loading
    var loadMoreObserver_{{ str_replace('-', '_', $id) }} = null;
    function setupLoadMoreObserver_{{ str_replace('-', '_', $id) }}() {
        var loadMoreDiv = document.getElementById('{{ $id }}LoadMore');
        if (!loadMoreDiv) return;
        
        // Disconnect previous observer if exists
        if (loadMoreObserver_{{ str_replace('-', '_', $id) }}) {
            loadMoreObserver_{{ str_replace('-', '_', $id) }}.disconnect();
        }
        
        // Create new Intersection Observer
        loadMoreObserver_{{ str_replace('-', '_', $id) }} = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && hasMore_{{ str_replace('-', '_', $id) }} && !isLoading_{{ str_replace('-', '_', $id) }}) {
                    loadMoreItems_{{ str_replace('-', '_', $id) }}();
                }
            });
        }, {
            root: document.getElementById('{{ $id }}TableContainer'),
            rootMargin: '50px',
            threshold: 0.1
        });
        
        loadMoreObserver_{{ str_replace('-', '_', $id) }}.observe(loadMoreDiv);
    }
    
    // Initialize observer when modal opens
    var originalOpenModal_{{ str_replace('-', '_', $id) }} = window.openItemModal_{{ str_replace('-', '_', $id) }};
    window.openItemModal_{{ str_replace('-', '_', $id) }} = function() {
        originalOpenModal_{{ str_replace('-', '_', $id) }}();
        // Setup observer after a small delay to ensure DOM is ready
        setTimeout(function() {
            setupLoadMoreObserver_{{ str_replace('-', '_', $id) }}();
        }, 100);
    };
    
    // Display items in table
    window.displayItems_{{ str_replace('-', '_', $id) }} = function(itemsToDisplay) {
        var tbody = document.getElementById('{{ $id }}Body');
        var countBadge = document.getElementById('{{ $id }}Count');
        
        if (countBadge) {
            var total = totalItems_{{ str_replace('-', '_', $id) }} || itemsToDisplay.length;
            countBadge.textContent = itemsToDisplay.length + ' of ' + total + ' items';
        }
        
        if (!itemsToDisplay || itemsToDisplay.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No items found</td></tr>';
            return;
        }
        
        var html = '';
        for (var i = 0; i < itemsToDisplay.length; i++) {
            var item = itemsToDisplay[i];
            var stock = parseFloat(item.closing_stock || 0);
            var stockClass = stock <= 0 ? 'text-danger' : (stock < 10 ? 'text-warning' : 'text-success');
            
            html += '<tr class="item-row" data-index="' + i + '" ondblclick="selectItem_{{ str_replace('-', '_', $id) }}(' + i + ')" onclick="highlightItemRow_{{ str_replace('-', '_', $id) }}(' + i + ')">';
            html += '<td style="padding: 6px;">' + escapeHtml(item.name || '') + '</td>';
            @if($showHsn)
            html += '<td style="padding: 6px;">' + escapeHtml(item.hsn_code || '') + '</td>';
            @endif
            @if($showStock)
            html += '<td style="text-align: right; padding: 6px;" class="' + stockClass + '">' + stock.toFixed(0) + '</td>';
            @endif
            @if($showRate)
            html += '<td style="text-align: right; padding: 6px;">' + parseFloat(item.{{ $rateType }} || 0).toFixed(2) + '</td>';
            @endif
            html += '<td style="text-align: right; padding: 6px;">' + parseFloat(item.mrp || 0).toFixed(2) + '</td>';
            @if($showCompany)
            html += '<td style="padding: 6px;">' + escapeHtml(item.company_name || '') + '</td>';
            @endif
            html += '</tr>';
        }
        
        tbody.innerHTML = html;
        filteredItems_{{ str_replace('-', '_', $id) }} = itemsToDisplay;
    };
    
    // Debounce timer for search
    var searchTimer_{{ str_replace('-', '_', $id) }} = null;
    
    // Filter items (client-side first, then server-side)
    window.filterItems_{{ str_replace('-', '_', $id) }} = function() {
        var searchInput = document.getElementById('{{ $id }}Search');
        var searchText = (searchInput.value || '').trim();
        
        // Clear previous timer
        if (searchTimer_{{ str_replace('-', '_', $id) }}) {
            clearTimeout(searchTimer_{{ str_replace('-', '_', $id) }});
        }
        
        if (!searchText) {
            // If no search text, reset to first page
            currentSearch_{{ str_replace('-', '_', $id) }} = '';
            if (items_{{ str_replace('-', '_', $id) }}.length === 0 || currentPage_{{ str_replace('-', '_', $id) }} > 1) {
                loadItems_{{ str_replace('-', '_', $id) }}(true);
            } else {
                displayItems_{{ str_replace('-', '_', $id) }}(items_{{ str_replace('-', '_', $id) }});
                updateLoadMoreButton_{{ str_replace('-', '_', $id) }}();
            }
            return;
        }
        
        // Client-side filter on already loaded items (instant feedback)
        var searchLower = searchText.toLowerCase();
        var filtered = items_{{ str_replace('-', '_', $id) }}.filter(function(item) {
            var name = (item.name || '').toLowerCase();
            var hsn = (item.hsn_code || '').toLowerCase();
            var barcode = (item.bar_code || '').toLowerCase();
            var company = (item.company_name || '').toLowerCase();
            return name.indexOf(searchLower) !== -1 || hsn.indexOf(searchLower) !== -1 || barcode.indexOf(searchLower) !== -1 || company.indexOf(searchLower) !== -1;
        });
        
        filteredItems_{{ str_replace('-', '_', $id) }} = filtered;
        displayItems_{{ str_replace('-', '_', $id) }}(filtered);
        selectedRowIndex_{{ str_replace('-', '_', $id) }} = -1;
        
        // Debounced server-side search for comprehensive results (with 2+ chars)
        if (searchText.length >= 2) {
            searchTimer_{{ str_replace('-', '_', $id) }} = setTimeout(function() {
                currentSearch_{{ str_replace('-', '_', $id) }} = searchText;
                loadItems_{{ str_replace('-', '_', $id) }}(true);
            }, 400); // 400ms debounce
        }
    };
    
    // Highlight row
    window.highlightItemRow_{{ str_replace('-', '_', $id) }} = function(index) {
        var tbody = document.getElementById('{{ $id }}Body');
        var rows = tbody.querySelectorAll('tr.item-row');
        
        rows.forEach(function(r) { r.classList.remove('row-selected'); });
        
        if (rows[index]) {
            rows[index].classList.add('row-selected');
            selectedRowIndex_{{ str_replace('-', '_', $id) }} = index;
        }
    };
    
    // Handle keyboard navigation
    window.handleItemKeyDown_{{ str_replace('-', '_', $id) }} = function(e) {
        var tbody = document.getElementById('{{ $id }}Body');
        var rows = tbody.querySelectorAll('tr.item-row');
        
        if (rows.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            // If nothing selected, start at first row
            if (selectedRowIndex_{{ str_replace('-', '_', $id) }} < 0) {
                highlightItemRow_{{ str_replace('-', '_', $id) }}(0);
            } else if (selectedRowIndex_{{ str_replace('-', '_', $id) }} < rows.length - 1) {
                highlightItemRow_{{ str_replace('-', '_', $id) }}(selectedRowIndex_{{ str_replace('-', '_', $id) }} + 1);
            }
            if (rows[selectedRowIndex_{{ str_replace('-', '_', $id) }}]) {
                rows[selectedRowIndex_{{ str_replace('-', '_', $id) }}].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (selectedRowIndex_{{ str_replace('-', '_', $id) }} > 0) {
                highlightItemRow_{{ str_replace('-', '_', $id) }}(selectedRowIndex_{{ str_replace('-', '_', $id) }} - 1);
                rows[selectedRowIndex_{{ str_replace('-', '_', $id) }}].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('[KB-ItemModal] Enter pressed', { selectedRowIndex: selectedRowIndex_{{ str_replace('-', '_', $id) }}, filteredCount: filteredItems_{{ str_replace('-', '_', $id) }}.length });
            
            // If a row is selected, use it; otherwise use first visible item
            if (selectedRowIndex_{{ str_replace('-', '_', $id) }} >= 0) {
                selectItem_{{ str_replace('-', '_', $id) }}(selectedRowIndex_{{ str_replace('-', '_', $id) }});
            } else if (filteredItems_{{ str_replace('-', '_', $id) }}.length > 0) {
                // Select first row if nothing is highlighted
                selectItem_{{ str_replace('-', '_', $id) }}(0);
            }
        }
    };

    // Global capture fallback:
    // If some page-level Enter handler swallows key events, this ensures item selection still works.
    function isItemModalOpen_{{ str_replace('-', '_', $id) }}() {
        var modal = document.getElementById('{{ $id }}');
        return !!(modal && modal.classList.contains('show'));
    }

    function modalKeyCaptureHandler_{{ str_replace('-', '_', $id) }}(e) {
        if (!isItemModalOpen_{{ str_replace('-', '_', $id) }}()) return;
        if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp' && e.key !== 'Enter') return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        console.log('[KB-ItemModal][Capture] key', {
            key: e.key,
            activeId: document.activeElement ? document.activeElement.id : null
        });

        handleItemKeyDown_{{ str_replace('-', '_', $id) }}(e);
    }

    window.addEventListener('keydown', modalKeyCaptureHandler_{{ str_replace('-', '_', $id) }}, true);
    
    // Select item
    window.selectItem_{{ str_replace('-', '_', $id) }} = function(index) {
        var item = filteredItems_{{ str_replace('-', '_', $id) }}[index];
        if (!item) return;
        
        // Store selected item globally
        window.selectedItemFromModal = item;
        
        // Close item modal
        closeItemModal_{{ str_replace('-', '_', $id) }}();
        
        // Open batch modal if exists
        if (typeof window.openBatchModal_{{ str_replace('-', '_', $batchModalId) }} === 'function') {
            window.openBatchModal_{{ str_replace('-', '_', $batchModalId) }}(item);
        } else if (typeof window.onItemSelectedFromModal === 'function') {
            window.onItemSelectedFromModal(item);
        }
    };
    
    // Helper function
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>
