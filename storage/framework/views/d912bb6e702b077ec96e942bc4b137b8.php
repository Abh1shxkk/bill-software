

<?php
    $id = $id ?? 'batchSelectionModal';
    $module = $module ?? 'generic';
    $showOnlyAvailable = $showOnlyAvailable ?? true;
    $rateType = $rateType ?? 's_rate';
    $showCostDetails = $showCostDetails ?? false;
    $showSupplier = $showSupplier ?? true;
    $showPurchaseRate = $showPurchaseRate ?? true;
?>


<style>
    /* Batch Selection Modal Backdrop */
    #<?php echo e($id); ?>Backdrop {
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
    #<?php echo e($id); ?>Backdrop.show {
        display: block;
        opacity: 1;
    }
    
    /* Batch Selection Modal */
    #<?php echo e($id); ?> {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.85);
        width: 90%;
        max-width: 900px;
        z-index: 99999;
        opacity: 0;
        transition: all 0.25s ease-in-out;
    }
    #<?php echo e($id); ?>.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    #<?php echo e($id); ?> .modal-content-box {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }
    #<?php echo e($id); ?> .modal-header-box {
        padding: 0.9rem 1.25rem;
        background: linear-gradient(135deg, #ff6b35 0%, #f5576c 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #<?php echo e($id); ?> .modal-title-box {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    #<?php echo e($id); ?> .btn-close-modal {
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
    #<?php echo e($id); ?> .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    #<?php echo e($id); ?> .modal-body-box {
        max-height: 70vh;
        overflow-y: auto;
    }
    #<?php echo e($id); ?> .modal-footer-box {
        padding: 0.85rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    #<?php echo e($id); ?> .batch-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
        background: #fff5f5;
    }
    #<?php echo e($id); ?> .batch-row:hover {
        background-color: #e3f2fd !important;
    }
    #<?php echo e($id); ?> .batch-row.row-selected,
    #<?php echo e($id); ?> .batch-row.row-selected td {
        background-color: #1976d2 !important;
        color: white !important;
    }
</style>


<div id="<?php echo e($id); ?>Backdrop"></div>


<div id="<?php echo e($id); ?>" 
     data-module="<?php echo e($module); ?>"
     data-show-only-available="<?php echo e($showOnlyAvailable ? 'true' : 'false'); ?>">
    <div class="modal-content-box">
        
        <div class="modal-header-box">
            <h5 class="modal-title-box">
                <i class="bi bi-boxes me-2"></i>Select Batch
            </h5>
            <button type="button" class="btn-close-modal" 
                    onclick="closeBatchModal_<?php echo e(str_replace('-', '_', $id)); ?>()" title="Close (Esc)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        
        <div class="modal-body-box">
            
            <div class="p-3 bg-light border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong style="font-size: 14px;">
                            Item: <span id="<?php echo e($id); ?>ItemName" style="color: #7c3aed; font-size: 16px;">---</span>
                        </strong>
                    </div>
                    <?php if($showOnlyAvailable): ?>
                    <span class="badge bg-success" style="font-size: 10px;">
                        <i class="bi bi-check-circle me-1"></i>Available Stock Only
                    </span>
                    <?php else: ?>
                    <span class="badge bg-secondary" style="font-size: 10px;">
                        <i class="bi bi-list me-1"></i>All Batches
                    </span>
                    <?php endif; ?>
                </div>
                
                
                <div class="mb-2">
                    <label style="font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">
                        <i class="bi bi-funnel me-1"></i>Sort By:
                    </label>
                    <div class="btn-group w-100" role="group" style="font-size: 11px;">
                        <input type="radio" class="btn-check" name="<?php echo e($id); ?>SortOption" id="<?php echo e($id); ?>SortExpiry" value="expiry" checked 
                               onchange="sortBatches_<?php echo e(str_replace('-', '_', $id)); ?>(this.value)">
                        <label class="btn btn-outline-primary btn-sm" for="<?php echo e($id); ?>SortExpiry" style="font-size: 11px; padding: 4px 8px;">
                            <i class="bi bi-calendar-x me-1"></i>Expiry (FIFO)
                        </label>
                        
                        <input type="radio" class="btn-check" name="<?php echo e($id); ?>SortOption" id="<?php echo e($id); ?>SortLastPurchase" value="last_purchase" 
                               onchange="sortBatches_<?php echo e(str_replace('-', '_', $id)); ?>(this.value)">
                        <label class="btn btn-outline-success btn-sm" for="<?php echo e($id); ?>SortLastPurchase" style="font-size: 11px; padding: 4px 8px;">
                            <i class="bi bi-clock-history me-1"></i>Last Purchase
                        </label>
                        
                        <input type="radio" class="btn-check" name="<?php echo e($id); ?>SortOption" id="<?php echo e($id); ?>SortPurchaseHistory" value="purchase_history" 
                               onchange="sortBatches_<?php echo e(str_replace('-', '_', $id)); ?>(this.value)">
                        <label class="btn btn-outline-info btn-sm" for="<?php echo e($id); ?>SortPurchaseHistory" style="font-size: 11px; padding: 4px 8px;">
                            <i class="bi bi-list-ol me-1"></i>Purchase History
                        </label>
                    </div>
                </div>
                
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0" 
                           id="<?php echo e($id); ?>Search" 
                           placeholder="Search by Batch No..." 
                           autocomplete="off"
                           oninput="filterBatches_<?php echo e(str_replace('-', '_', $id)); ?>()"
                           onkeydown="handleBatchKeyDown_<?php echo e(str_replace('-', '_', $id)); ?>(event)"
                           style="font-size: 12px;">
                </div>
            </div>
            
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="<?php echo e($id); ?>TableContainer">
                <table class="table table-bordered mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #ffcccc; z-index: 10; font-weight: bold;">
                        <tr>
                            <th style="width: 110px; padding: 8px;">BATCH</th>
                            <th style="width: 75px; text-align: center; padding: 8px;">DATE</th>
                            <th style="width: 80px; text-align: right; padding: 8px;">
                                <?php if($rateType === 'pur_rate'): ?>
                                    PRATE
                                <?php else: ?>
                                    RATE
                                <?php endif; ?>
                            </th>
                            <?php if($showPurchaseRate && $rateType !== 'pur_rate'): ?>
                            <th style="width: 80px; text-align: right; padding: 8px;">PRATE</th>
                            <?php endif; ?>
                            <th style="width: 80px; text-align: right; padding: 8px;">MRP</th>
                            <th style="width: 65px; text-align: right; padding: 8px; background: #c8e6c9;">QTY.</th>
                            <th style="width: 65px; text-align: center; padding: 8px;">EXP.</th>
                            <th style="width: 70px; text-align: center; padding: 8px;">CODE</th>
                            <?php if($showCostDetails): ?>
                            <th style="width: 85px; text-align: right; padding: 8px;">Cost+GST</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="<?php echo e($id); ?>Body">
                        <tr>
                            <td colspan="<?php echo e($showCostDetails ? 9 : 8); ?>" class="text-center py-4">
                                Select an item first
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            
            <div class="p-3 bg-white border-top">
                <div class="row" style="font-size: 12px;">
                    <div class="col-md-6">
                        <div class="mb-1">
                            <strong>BRAND:</strong> 
                            <span id="<?php echo e($id); ?>Brand" style="color: #7c3aed;">---</span>
                        </div>
                        <div>
                            <strong>Packing:</strong> 
                            <span id="<?php echo e($id); ?>Packing" style="color: #7c3aed;">---</span>
                        </div>
                    </div>
                    <?php if($showSupplier): ?>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-1">
                            <strong>Supplier:</strong> 
                            <span id="<?php echo e($id); ?>Supplier" style="color: #0066cc; font-weight: bold;">---</span>
                        </div>
                        <div>
                            <strong>Purchase Date:</strong> 
                            <span id="<?php echo e($id); ?>PurchaseDate" style="color: #666;">---</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        
        <div class="modal-footer-box">
            <button type="button" class="btn btn-primary btn-sm" 
                    onclick="confirmBatchSelection_<?php echo e(str_replace('-', '_', $id)); ?>()">
                <i class="bi bi-check-circle me-1"></i> Select Batch
            </button>
            <button type="button" class="btn btn-secondary btn-sm" 
                    onclick="closeBatchModal_<?php echo e(str_replace('-', '_', $id)); ?>()">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </button>
        </div>
    </div>
</div>


<script>
(function() {
    // State for this modal instance
    var modalId = '<?php echo e($id); ?>';
    var showOnlyAvailable = <?php echo e($showOnlyAvailable ? 'true' : 'false'); ?>;
    var batches_<?php echo e(str_replace('-', '_', $id)); ?> = [];
    var filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?> = [];
    var selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> = -1;
    var currentItem_<?php echo e(str_replace('-', '_', $id)); ?> = null;
    var selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?> = null;
    var currentSortOption_<?php echo e(str_replace('-', '_', $id)); ?> = 'expiry'; // Default sort by expiry
    
    // Open modal function
    window.openBatchModal_<?php echo e(str_replace('-', '_', $id)); ?> = function(item) {
        var modal = document.getElementById('<?php echo e($id); ?>');
        var backdrop = document.getElementById('<?php echo e($id); ?>Backdrop');
        
        if (!modal || !backdrop) return;
        if (!item || !item.id) {
            console.error('Item data required');
            return;
        }
        
        currentItem_<?php echo e(str_replace('-', '_', $id)); ?> = item;
        selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?> = null;
        selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> = -1;
        
        // Update item display
        document.getElementById('<?php echo e($id); ?>ItemName').textContent = item.name || '---';
        document.getElementById('<?php echo e($id); ?>Brand').textContent = item.name || '---';
        document.getElementById('<?php echo e($id); ?>Packing').textContent = item.packing || '---';
        
        <?php if($showSupplier): ?>
        document.getElementById('<?php echo e($id); ?>Supplier').textContent = '---';
        document.getElementById('<?php echo e($id); ?>PurchaseDate').textContent = '---';
        <?php endif; ?>
        
        // Clear search
        var searchInput = document.getElementById('<?php echo e($id); ?>Search');
        if (searchInput) searchInput.value = '';
        
        // Load batches
        loadBatches_<?php echo e(str_replace('-', '_', $id)); ?>(item.id);
        
        // Show modal
        setTimeout(function() {
            modal.classList.add('show');
            backdrop.classList.add('show');
            if (searchInput) searchInput.focus();
        }, 10);
        
        // Escape key listener
        document.addEventListener('keydown', escapeHandler_<?php echo e(str_replace('-', '_', $id)); ?>);
    };
    
    // Close modal function
    window.closeBatchModal_<?php echo e(str_replace('-', '_', $id)); ?> = function() {
        var modal = document.getElementById('<?php echo e($id); ?>');
        var backdrop = document.getElementById('<?php echo e($id); ?>Backdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?> = null;
        currentItem_<?php echo e(str_replace('-', '_', $id)); ?> = null;
        
        document.removeEventListener('keydown', escapeHandler_<?php echo e(str_replace('-', '_', $id)); ?>);
    };
    
    // Escape handler
    function escapeHandler_<?php echo e(str_replace('-', '_', $id)); ?>(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeBatchModal_<?php echo e(str_replace('-', '_', $id)); ?>();
        }
    }
    
    // Load batches from API
    window.loadBatches_<?php echo e(str_replace('-', '_', $id)); ?> = function(itemId) {
        var tbody = document.getElementById('<?php echo e($id); ?>Body');
        
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading batches...</td></tr>';
        
        var endpoint = '<?php echo e(url("/admin/api/item-batches")); ?>/' + itemId;
        if (showOnlyAvailable) {
            endpoint += '?available_only=1';
        }
        
        fetch(endpoint)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var batchList = Array.isArray(data) ? data : (data.batches || data || []);
                
                // Client-side filter for available only (extra safety)
                if (showOnlyAvailable) {
                    batchList = batchList.filter(function(batch) {
                        var qty = parseFloat(batch.qty || batch.available_qty || 0);
                        return qty > 0;
                    });
                }
                
                batches_<?php echo e(str_replace('-', '_', $id)); ?> = batchList;
                
                // Apply default sort (expiry)
                sortBatches_<?php echo e(str_replace('-', '_', $id)); ?>(currentSortOption_<?php echo e(str_replace('-', '_', $id)); ?>);
            })
            .catch(function(error) {
                console.error('Error loading batches:', error);
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading batches</td></tr>';
            });
    };
    
    // Sort batches function
    window.sortBatches_<?php echo e(str_replace('-', '_', $id)); ?> = function(sortOption) {
        currentSortOption_<?php echo e(str_replace('-', '_', $id)); ?> = sortOption;
        
        var sortedBatches = batches_<?php echo e(str_replace('-', '_', $id)); ?>.slice(); // Create copy
        
        if (sortOption === 'expiry') {
            // Sort by expiry date (FIFO - First Expiry First Out)
            // Earliest expiry date comes first
            sortedBatches.sort(function(a, b) {
                var dateA = parseExpiryDate(a.expiry_display || a.expiry_date || a.expiry);
                var dateB = parseExpiryDate(b.expiry_display || b.expiry_date || b.expiry);
                
                if (!dateA && !dateB) return 0;
                if (!dateA) return 1; // No expiry goes to end
                if (!dateB) return -1;
                
                return dateA - dateB; // Ascending (earliest first)
            });
        } else if (sortOption === 'last_purchase') {
            // Sort by purchase date (Latest purchase first)
            sortedBatches.sort(function(a, b) {
                var dateA = parsePurchaseDate(a.purchase_date_display || a.purchase_date);
                var dateB = parsePurchaseDate(b.purchase_date_display || b.purchase_date);
                
                if (!dateA && !dateB) return 0;
                if (!dateA) return 1;
                if (!dateB) return -1;
                
                return dateB - dateA; // Descending (latest first)
            });
        } else if (sortOption === 'purchase_history') {
            // Sort by purchase date (Oldest purchase first - chronological order)
            sortedBatches.sort(function(a, b) {
                var dateA = parsePurchaseDate(a.purchase_date_display || a.purchase_date);
                var dateB = parsePurchaseDate(b.purchase_date_display || b.purchase_date);
                
                if (!dateA && !dateB) return 0;
                if (!dateA) return 1;
                if (!dateB) return -1;
                
                return dateA - dateB; // Ascending (oldest first)
            });
        }
        
        batches_<?php echo e(str_replace('-', '_', $id)); ?> = sortedBatches;
        filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?> = sortedBatches.slice();
        displayBatches_<?php echo e(str_replace('-', '_', $id)); ?>(sortedBatches);
    };
    
    // Parse expiry date (MM/YY or YYYY-MM-DD format)
    function parseExpiryDate(dateStr) {
        if (!dateStr || dateStr === 'N/A') return null;
        
        try {
            // Try MM/YY format first
            if (dateStr.indexOf('/') !== -1) {
                var parts = dateStr.split('/');
                if (parts.length === 2) {
                    var month = parseInt(parts[0], 10);
                    var year = parseInt(parts[1], 10);
                    // Assume 20xx for 2-digit year
                    if (year < 100) year += 2000;
                    return new Date(year, month - 1, 1);
                }
            }
            
            // Try standard date format
            var date = new Date(dateStr);
            if (!isNaN(date.getTime())) return date;
        } catch (e) {
            console.error('Error parsing expiry date:', dateStr, e);
        }
        
        return null;
    }
    
    // Parse purchase date (DD-MM-YY or YYYY-MM-DD format)
    function parsePurchaseDate(dateStr) {
        if (!dateStr || dateStr === 'N/A') return null;
        
        try {
            // Try DD-MM-YY format first
            if (dateStr.indexOf('-') !== -1) {
                var parts = dateStr.split('-');
                if (parts.length === 3) {
                    var day = parseInt(parts[0], 10);
                    var month = parseInt(parts[1], 10);
                    var year = parseInt(parts[2], 10);
                    // Assume 20xx for 2-digit year
                    if (year < 100) year += 2000;
                    return new Date(year, month - 1, day);
                }
            }
            
            // Try standard date format
            var date = new Date(dateStr);
            if (!isNaN(date.getTime())) return date;
        } catch (e) {
            console.error('Error parsing purchase date:', dateStr, e);
        }
        
        return null;
    }
    
    // Display batches in table
    window.displayBatches_<?php echo e(str_replace('-', '_', $id)); ?> = function(batchesToDisplay) {
        var tbody = document.getElementById('<?php echo e($id); ?>Body');
        
        if (!batchesToDisplay || batchesToDisplay.length === 0) {
            var msg = showOnlyAvailable ? 'No batches with available stock found' : 'No batches found';
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><i class="bi bi-exclamation-circle text-warning me-2"></i>' + msg + '</td></tr>';
            return;
        }
        
        var html = '';
        for (var i = 0; i < batchesToDisplay.length; i++) {
            var batch = batchesToDisplay[i];
            var qty = parseFloat(batch.qty || batch.available_qty || 0);
            var qtyClass = qty <= 10 ? 'text-danger fw-bold' : 'text-success fw-bold';
            
            var purchaseDate = formatDate(batch.purchase_date_display || batch.purchase_date);
            var expiryDate = formatExpiry(batch.expiry_display || batch.expiry_date);
            
            var rate = parseFloat(batch.avg_s_rate || batch.s_rate || 0).toFixed(2);
            var purRate = parseFloat(batch.avg_pur_rate || batch.pur_rate || 0).toFixed(2);
            var mrp = parseFloat(batch.avg_mrp || batch.mrp || 0).toFixed(2);
            
            html += '<tr class="batch-row" data-index="' + i + '" ondblclick="selectBatch_<?php echo e(str_replace('-', '_', $id)); ?>(' + i + ')" onclick="highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(' + i + ')">';
            html += '<td style="padding: 6px;">' + escapeHtml(batch.batch_no || '') + '</td>';
            html += '<td style="text-align: center; padding: 6px;">' + purchaseDate + '</td>';
            <?php if($rateType === 'pur_rate'): ?>
            html += '<td style="text-align: right; padding: 6px;">' + purRate + '</td>';
            <?php else: ?>
            html += '<td style="text-align: right; padding: 6px;">' + rate + '</td>';
            <?php endif; ?>
            <?php if($showPurchaseRate && $rateType !== 'pur_rate'): ?>
            html += '<td style="text-align: right; padding: 6px;">' + purRate + '</td>';
            <?php endif; ?>
            html += '<td style="text-align: right; padding: 6px;">' + mrp + '</td>';
            html += '<td style="text-align: right; padding: 6px; background: #e8f5e9;" class="' + qtyClass + '">' + qty + '</td>';
            html += '<td style="text-align: center; padding: 6px;">' + expiryDate + '</td>';
            html += '<td style="text-align: center; padding: 6px;">' + escapeHtml(batch.bar_code || '') + '</td>';
            <?php if($showCostDetails): ?>
            html += '<td style="text-align: right; padding: 6px;">' + parseFloat(batch.cost_gst || 0).toFixed(2) + '</td>';
            <?php endif; ?>
            html += '</tr>';
        }
        
        tbody.innerHTML = html;
        filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?> = batchesToDisplay;
        
        // Auto-select first batch if only one
        if (batchesToDisplay.length === 1) {
            highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(0);
        }
    };
    
    // Filter batches
    window.filterBatches_<?php echo e(str_replace('-', '_', $id)); ?> = function() {
        var searchInput = document.getElementById('<?php echo e($id); ?>Search');
        var searchText = (searchInput.value || '').toLowerCase().trim();
        
        if (!searchText) {
            // No search text - show all batches with current sort
            displayBatches_<?php echo e(str_replace('-', '_', $id)); ?>(batches_<?php echo e(str_replace('-', '_', $id)); ?>);
            return;
        }
        
        // Filter batches based on search text
        var filtered = batches_<?php echo e(str_replace('-', '_', $id)); ?>.filter(function(batch) {
            var batchNo = (batch.batch_no || '').toLowerCase();
            var barcode = (batch.bar_code || '').toLowerCase();
            return batchNo.indexOf(searchText) !== -1 || barcode.indexOf(searchText) !== -1;
        });
        
        displayBatches_<?php echo e(str_replace('-', '_', $id)); ?>(filtered);
        selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> = -1;
    };
    
    // Highlight row
    window.highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?> = function(index) {
        var tbody = document.getElementById('<?php echo e($id); ?>Body');
        var rows = tbody.querySelectorAll('tr.batch-row');
        
        rows.forEach(function(r) { r.classList.remove('row-selected'); });
        
        if (rows[index]) {
            rows[index].classList.add('row-selected');
            selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> = index;
            selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?> = filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?>[index];
            
            // Update details
            <?php if($showSupplier): ?>
            document.getElementById('<?php echo e($id); ?>Supplier').textContent = selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?>.supplier_name || '---';
            document.getElementById('<?php echo e($id); ?>PurchaseDate').textContent = formatDate(selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?>.purchase_date_display || selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?>.purchase_date);
            <?php endif; ?>
        }
    };
    
    // Handle keyboard navigation
    window.handleBatchKeyDown_<?php echo e(str_replace('-', '_', $id)); ?> = function(e) {
        var tbody = document.getElementById('<?php echo e($id); ?>Body');
        var rows = tbody.querySelectorAll('tr.batch-row');
        
        if (rows.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            // If nothing selected, start at first row
            if (selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> < 0) {
                highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(0);
            } else if (selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> < rows.length - 1) {
                highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> + 1);
            }
            if (rows[selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?>]) {
                rows[selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?>].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> > 0) {
                highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> - 1);
                rows[selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?>].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('[KB-BatchModal] Enter pressed', { selectedRowIndex: selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?>, filteredCount: filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?>.length });
            
            // If a row is selected, use it; otherwise use first batch
            if (selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?> >= 0) {
                selectBatch_<?php echo e(str_replace('-', '_', $id)); ?>(selectedRowIndex_<?php echo e(str_replace('-', '_', $id)); ?>);
            } else if (filteredBatches_<?php echo e(str_replace('-', '_', $id)); ?>.length > 0) {
                // Select first row if nothing is highlighted
                selectBatch_<?php echo e(str_replace('-', '_', $id)); ?>(0);
            }
        }
    };

    // Global capture fallback:
    // If page-level handlers consume Enter, keep modal keyboard selection reliable.
    function isBatchModalOpen_<?php echo e(str_replace('-', '_', $id)); ?>() {
        var modal = document.getElementById('<?php echo e($id); ?>');
        return !!(modal && modal.classList.contains('show'));
    }

    function modalKeyCaptureHandler_<?php echo e(str_replace('-', '_', $id)); ?>(e) {
        if (!isBatchModalOpen_<?php echo e(str_replace('-', '_', $id)); ?>()) return;
        if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp' && e.key !== 'Enter') return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        console.log('[KB-BatchModal][Capture] key', {
            key: e.key,
            activeId: document.activeElement ? document.activeElement.id : null
        });

        handleBatchKeyDown_<?php echo e(str_replace('-', '_', $id)); ?>(e);
    }

    window.addEventListener('keydown', modalKeyCaptureHandler_<?php echo e(str_replace('-', '_', $id)); ?>, true);
    
    // Select batch
    window.selectBatch_<?php echo e(str_replace('-', '_', $id)); ?> = function(index) {
        highlightBatchRow_<?php echo e(str_replace('-', '_', $id)); ?>(index);
        confirmBatchSelection_<?php echo e(str_replace('-', '_', $id)); ?>();
    };
    
    // Confirm batch selection
    window.confirmBatchSelection_<?php echo e(str_replace('-', '_', $id)); ?> = function() {
        if (!selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?>) {
            alert('Please select a batch first');
            return;
        }
        
        var item = currentItem_<?php echo e(str_replace('-', '_', $id)); ?>;
        var batch = selectedBatch_<?php echo e(str_replace('-', '_', $id)); ?>;
        
        // Store globally
        window.selectedBatchFromModal = batch;
        window.selectedItemFromModal = item;
        
        // Close modal
        closeBatchModal_<?php echo e(str_replace('-', '_', $id)); ?>();
        
        // Call callback if exists
        if (typeof window.onItemBatchSelectedFromModal === 'function') {
            window.onItemBatchSelectedFromModal(item, batch);
        } else if (typeof window.onBatchSelectedFromModal === 'function') {
            window.onBatchSelectedFromModal(item, batch);
        }
    };
    
    // Helper functions
    function formatDate(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            var date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            var day = String(date.getDate()).padStart(2, '0');
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var year = String(date.getFullYear()).slice(-2);
            return day + '-' + month + '-' + year;
        } catch (e) { return dateStr; }
    }
    
    function formatExpiry(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            var date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var year = String(date.getFullYear()).slice(-2);
            return month + '/' + year;
        } catch (e) { return dateStr; }
    }
    
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>
<?php /**PATH C:\xampp\htdocs\bill-software\resources\views/components/modals/batch-selection.blade.php ENDPATH**/ ?>