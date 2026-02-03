# Implementation Plan: Reusable Item & Batch Modal Components

## 📋 Executive Summary

**Objective**: Create centralized, reusable **Item Selection Modal** and **Batch Selection Modal** Blade components that can be included in all transaction modules (37+ transaction views), eliminating code duplication and ensuring consistent behavior.

**Key Requirement**: **Batch modal should only show batches with available stock (qty > 0)**, not all batches.

**Impact Scope**: 
- ~37 transaction Blade files
- ~37 modification Blade files
- ~74 total files affected

**Estimated Effort**: Medium-High complexity | 3-5 working days

---

## 🎯 Problem Statement

### Current Issues:

1. **Code Duplication**: Every transaction module (Sale, Purchase, Sale Return, etc.) has its own:
   - Item Modal HTML (~100-200 lines each)
   - Batch Modal HTML (~100-150 lines each)
   - JavaScript functions for opening/closing modals (~200-400 lines each)
   - CSS styles for modals (~50-100 lines each)

2. **Inconsistent Behavior**: Different implementations may have:
   - Different column orders
   - Different search capabilities
   - Different batch filtering logic
   - Some show all batches, some filter by stock

3. **Maintenance Nightmare**: Any bug fix or feature addition requires changes in 74+ files

4. **Batch Stock Issue**: Current implementations show ALL batches, not just available ones

---

## 🏗️ Proposed Architecture

### Component Structure:

```
resources/views/components/
├── modals/
│   ├── item-selection.blade.php       # Reusable Item Selection Modal
│   ├── batch-selection.blade.php      # Reusable Batch Selection Modal
│   └── modal-base.blade.php           # Base modal structure (optional)
│
public/js/components/
├── item-modal.js                      # JavaScript for Item Modal
└── batch-modal.js                     # JavaScript for Batch Modal
│
public/css/components/
└── modal-components.css               # Shared modal styles
│
app/Http/Controllers/Api/
└── ItemBatchController.php            # Unified API for items & batches
```

### Component Features:

#### 1. Item Selection Modal Component
- **Configurable via Props:**
  - `module` - Module name (sale, purchase, sale-return, etc.)
  - `showStock` - Whether to show stock column (default: true)
  - `showRate` - Which rate to show (s_rate, pur_rate, etc.)
  - `multiSelect` - Allow multiple item selection (default: false)
  - `customColumns` - Additional columns to display
  - `filterCallback` - Custom filtering function name

#### 2. Batch Selection Modal Component  
- **Configurable via Props:**
  - `module` - Module name for context
  - `showOnlyAvailable` - **Only show batches with qty > 0** (default: true) ⭐
  - `rateColumn` - Which rate to highlight (s_rate, pur_rate)
  - `showCostDetails` - Show Cost+GST column
  - `onSelectCallback` - JavaScript callback when batch selected

---

## 📐 Detailed Technical Design

### Phase 1: Create Base Components (Day 1)

#### 1.1 Item Selection Modal Component

**File**: `resources/views/components/modals/item-selection.blade.php`

```blade
@props([
    'id' => 'itemSelectionModal',
    'module' => 'generic',
    'showStock' => true,
    'showRate' => true,
    'rateType' => 's_rate',
    'multiSelect' => false,
    'onSelectCallback' => 'onItemSelected',
    'showCompany' => true,
    'showHsn' => true,
])

<!-- Modal Backdrop -->
<div id="{{ $id }}Backdrop" class="component-modal-backdrop"></div>

<!-- Item Selection Modal -->
<div id="{{ $id }}" class="component-modal" data-module="{{ $module }}">
    <div class="component-modal-content" style="max-width: 950px;">
        <div class="component-modal-header bg-orange">
            <h5 class="component-modal-title">
                <i class="bi bi-list-check me-2"></i>Select Item
            </h5>
            <button type="button" class="btn-close-modal" 
                    onclick="ItemModalComponent.close('{{ $id }}')" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="component-modal-body">
            <!-- Search Section -->
            <div class="p-3 bg-light border-bottom">
                <input type="text" 
                       class="form-control" 
                       id="{{ $id }}Search" 
                       placeholder="Search by Name, Code, HSN, Company..." 
                       autocomplete="off"
                       oninput="ItemModalComponent.filter('{{ $id }}')"
                       style="font-size: 12px;">
            </div>
            
            <!-- Items Table -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                        <tr>
                            <th style="width: 250px;">Name</th>
                            @if($showHsn)
                            <th style="width: 100px;">HSN</th>
                            @endif
                            @if($showStock)
                            <th style="width: 80px; text-align: right;">Stock</th>
                            @endif
                            @if($showRate)
                            <th style="width: 90px; text-align: right;">{{ $rateType === 'pur_rate' ? 'Pur.Rate' : 'S.Rate' }}</th>
                            @endif
                            <th style="width: 80px; text-align: right;">MRP</th>
                            @if($showCompany)
                            <th style="width: 150px;">Company</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="{{ $id }}Body">
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm"></div>
                                Loading items...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="component-modal-footer">
            @if($multiSelect)
            <button type="button" class="btn btn-primary btn-sm" 
                    onclick="ItemModalComponent.confirmSelection('{{ $id }}', '{{ $onSelectCallback }}')">
                <i class="bi bi-check-circle"></i> Add Selected Items
            </button>
            @endif
            <button type="button" class="btn btn-secondary btn-sm" 
                    onclick="ItemModalComponent.close('{{ $id }}')">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>
```

#### 1.2 Batch Selection Modal Component

**File**: `resources/views/components/modals/batch-selection.blade.php`

```blade
@props([
    'id' => 'batchSelectionModal',
    'module' => 'generic',
    'showOnlyAvailable' => true,  // ⭐ KEY: Only show batches with stock
    'rateType' => 's_rate',
    'showCostDetails' => false,
    'showSupplier' => true,
    'onSelectCallback' => 'onBatchSelected',
])

<!-- Modal Backdrop -->
<div id="{{ $id }}Backdrop" class="component-modal-backdrop"></div>

<!-- Batch Selection Modal -->
<div id="{{ $id }}" class="component-modal" 
     data-module="{{ $module }}"
     data-show-only-available="{{ $showOnlyAvailable ? 'true' : 'false' }}">
    <div class="component-modal-content" style="max-width: 900px;">
        <div class="component-modal-header bg-orange">
            <h5 class="component-modal-title">
                <i class="bi bi-boxes me-2"></i>Select Batch
            </h5>
            <button type="button" class="btn-close-modal" 
                    onclick="BatchModalComponent.close('{{ $id }}')" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="component-modal-body">
            <!-- Item Info Header -->
            <div class="p-3 bg-light border-bottom">
                <div class="mb-2">
                    <strong style="font-size: 14px;">
                        Item: <span id="{{ $id }}ItemName" style="color: #7c3aed; font-size: 16px;">---</span>
                    </strong>
                    @if($showOnlyAvailable)
                    <span class="badge bg-success ms-2" style="font-size: 10px;">
                        <i class="bi bi-check-circle"></i> Showing Available Stock Only
                    </span>
                    @endif
                </div>
                <input type="text" 
                       class="form-control" 
                       id="{{ $id }}Search" 
                       placeholder="Search by Batch No..." 
                       autocomplete="off"
                       oninput="BatchModalComponent.filter('{{ $id }}')"
                       style="font-size: 12px;">
            </div>
            
            <!-- Batches Table -->
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #ffcccc; z-index: 10; font-weight: bold;">
                        <tr>
                            <th style="width: 120px; padding: 8px;">BATCH</th>
                            <th style="width: 80px; text-align: center; padding: 8px;">DATE</th>
                            <th style="width: 90px; text-align: right; padding: 8px;">
                                {{ $rateType === 'pur_rate' ? 'PRATE' : 'RATE' }}
                            </th>
                            <th style="width: 90px; text-align: right; padding: 8px;">MRP</th>
                            <th style="width: 70px; text-align: right; padding: 8px; background: #c8e6c9;">QTY.</th>
                            <th style="width: 70px; text-align: center; padding: 8px;">EXP.</th>
                            <th style="width: 80px; text-align: center; padding: 8px;">CODE</th>
                            @if($showCostDetails)
                            <th style="width: 90px; text-align: right; padding: 8px;">Cost+GST</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="{{ $id }}Body">
                        <tr><td colspan="8" class="text-center py-4">Select an item first</td></tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Batch Details Section -->
            <div class="p-3 bg-white border-top">
                <div class="mb-2" style="font-weight: bold; font-size: 13px;">
                    <strong>BRAND: </strong>
                    <span id="{{ $id }}Brand" style="color: #7c3aed;">---</span>
                    <span class="float-end">
                        <strong>Packing: </strong>
                        <span id="{{ $id }}Packing" style="color: #7c3aed;">---</span>
                    </span>
                </div>
                @if($showSupplier)
                <div style="font-size: 11px;">
                    <strong>Supplier: </strong>
                    <span id="{{ $id }}Supplier" style="color: #0066cc; font-weight: bold;">---</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="component-modal-footer">
            <button type="button" class="btn btn-primary btn-sm" 
                    onclick="BatchModalComponent.confirmSelection('{{ $id }}', '{{ $onSelectCallback }}')">
                <i class="bi bi-check-circle"></i> Select Batch
            </button>
            <button type="button" class="btn btn-secondary btn-sm" 
                    onclick="BatchModalComponent.close('{{ $id }}')">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>
```

---

### Phase 2: Create JavaScript Components (Day 1-2)

#### 2.1 Item Modal JavaScript

**File**: `public/js/components/item-modal.js`

```javascript
/**
 * Item Modal Component
 * Reusable item selection modal for all transaction modules
 */
const ItemModalComponent = {
    // Store loaded items
    items: [],
    
    // Currently selected items (for multi-select)
    selectedItems: [],
    
    // Pending item (single selection waiting for batch)
    pendingItem: null,
    
    /**
     * Open the item modal
     * @param {string} modalId - The modal element ID
     * @param {object} options - Configuration options
     */
    open: function(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');
        
        if (!modal || !backdrop) {
            console.error('Item modal not found:', modalId);
            return;
        }
        
        // Store options
        modal.dataset.options = JSON.stringify(options);
        
        // Load items if not already loaded
        if (this.items.length === 0) {
            this.loadItems(modalId);
        } else {
            this.displayItems(modalId, this.items);
        }
        
        // Show modal
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');
            
            // Focus search input
            const searchInput = document.getElementById(modalId + 'Search');
            if (searchInput) searchInput.focus();
        }, 10);
    },
    
    /**
     * Close the item modal
     */
    close: function(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        // Clear selection
        this.selectedItems = [];
        this.pendingItem = null;
    },
    
    /**
     * Load items from server
     */
    loadItems: function(modalId) {
        const endpoint = '/admin/api/items/list';
        
        fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                this.items = data.items || data;
                this.displayItems(modalId, this.items);
            })
            .catch(error => {
                console.error('Error loading items:', error);
                document.getElementById(modalId + 'Body').innerHTML = 
                    '<tr><td colspan="6" class="text-center text-danger">Error loading items</td></tr>';
            });
    },
    
    /**
     * Display items in the table
     */
    displayItems: function(modalId, items) {
        const tbody = document.getElementById(modalId + 'Body');
        const modal = document.getElementById(modalId);
        const options = JSON.parse(modal.dataset.options || '{}');
        
        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No items found</td></tr>';
            return;
        }
        
        tbody.innerHTML = items.map(item => `
            <tr class="item-row" 
                data-item='${JSON.stringify(item)}'
                ondblclick="ItemModalComponent.selectItem('${modalId}', this)"
                onclick="ItemModalComponent.highlightRow(this)"
                style="cursor: pointer;">
                <td>${item.name || ''}</td>
                <td>${item.hsn_code || ''}</td>
                <td style="text-align: right;">${parseFloat(item.closing_stock || 0).toFixed(0)}</td>
                <td style="text-align: right;">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                <td style="text-align: right;">${parseFloat(item.mrp || 0).toFixed(2)}</td>
                <td>${item.company_name || ''}</td>
            </tr>
        `).join('');
    },
    
    /**
     * Filter items based on search input
     */
    filter: function(modalId) {
        const searchInput = document.getElementById(modalId + 'Search');
        const searchText = (searchInput.value || '').toLowerCase();
        const rows = document.querySelectorAll('#' + modalId + 'Body tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    },
    
    /**
     * Highlight selected row
     */
    highlightRow: function(row) {
        // Remove previous selection
        const tbody = row.closest('tbody');
        tbody.querySelectorAll('tr').forEach(r => r.classList.remove('row-selected'));
        
        // Add selection to clicked row
        row.classList.add('row-selected');
    },
    
    /**
     * Select item (on double-click)
     */
    selectItem: function(modalId, row) {
        const itemData = JSON.parse(row.dataset.item);
        const modal = document.getElementById(modalId);
        const options = JSON.parse(modal.dataset.options || '{}');
        
        // Store pending item
        this.pendingItem = itemData;
        
        // Close item modal
        this.close(modalId);
        
        // Open batch selection modal
        if (options.batchModalId) {
            BatchModalComponent.open(options.batchModalId, {
                item: itemData,
                onSelectCallback: options.onBatchSelectCallback
            });
        } else if (typeof window[options.onSelectCallback] === 'function') {
            window[options.onSelectCallback](itemData);
        }
    }
};
```

#### 2.2 Batch Modal JavaScript

**File**: `public/js/components/batch-modal.js`

```javascript
/**
 * Batch Modal Component
 * Reusable batch selection modal for all transaction modules
 * KEY FEATURE: Only shows batches with available stock (qty > 0)
 */
const BatchModalComponent = {
    // Currently loaded batches
    batches: [],
    
    // Currently selected batch
    selectedBatch: null,
    
    // Current item context
    currentItem: null,
    
    /**
     * Open batch modal for an item
     * @param {string} modalId - The modal element ID
     * @param {object} options - Configuration options including item data
     */
    open: function(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');
        
        if (!modal || !backdrop) {
            console.error('Batch modal not found:', modalId);
            return;
        }
        
        // Store current item
        this.currentItem = options.item;
        this.selectedBatch = null;
        
        // Update item name display
        const itemNameEl = document.getElementById(modalId + 'ItemName');
        if (itemNameEl) {
            itemNameEl.textContent = options.item?.name || '---';
        }
        
        // Update brand and packing
        const brandEl = document.getElementById(modalId + 'Brand');
        const packingEl = document.getElementById(modalId + 'Packing');
        if (brandEl) brandEl.textContent = options.item?.name || '---';
        if (packingEl) packingEl.textContent = options.item?.packing || '---';
        
        // Store options
        modal.dataset.options = JSON.stringify(options);
        
        // Load batches for item
        this.loadBatches(modalId, options.item.id);
        
        // Show modal
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');
            
            const searchInput = document.getElementById(modalId + 'Search');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
        }, 10);
    },
    
    /**
     * Close batch modal
     */
    close: function(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        this.selectedBatch = null;
        this.currentItem = null;
    },
    
    /**
     * Load batches for an item
     * ⭐ KEY: Only fetches batches with available stock
     */
    loadBatches: function(modalId, itemId) {
        const modal = document.getElementById(modalId);
        const showOnlyAvailable = modal.dataset.showOnlyAvailable === 'true';
        
        // Add parameter to only get available batches
        const endpoint = `/admin/api/item-batches/${itemId}` + 
                         (showOnlyAvailable ? '?available_only=1' : '');
        
        const tbody = document.getElementById(modalId + 'Body');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> Loading batches...</td></tr>';
        
        fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                let batches = Array.isArray(data) ? data : (data.batches || []);
                
                // ⭐ CLIENT-SIDE FILTER: Extra safety - only show qty > 0
                if (showOnlyAvailable) {
                    batches = batches.filter(batch => {
                        const qty = parseFloat(batch.qty || batch.available_qty || 0);
                        return qty > 0;
                    });
                }
                
                this.batches = batches;
                this.displayBatches(modalId, batches);
            })
            .catch(error => {
                console.error('Error loading batches:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading batches</td></tr>';
            });
    },
    
    /**
     * Display batches in the table
     */
    displayBatches: function(modalId, batches) {
        const tbody = document.getElementById(modalId + 'Body');
        
        if (!batches || batches.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-exclamation-circle text-warning me-2"></i>
                        No batches with available stock found for this item
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = batches.map((batch, idx) => {
            // Format dates
            const purchaseDate = this.formatDate(batch.purchase_date_display || batch.purchase_date);
            const expiryDate = this.formatExpiry(batch.expiry_display || batch.expiry_date);
            
            // Get values
            const rate = parseFloat(batch.avg_s_rate || batch.s_rate || 0).toFixed(2);
            const mrp = parseFloat(batch.avg_mrp || batch.mrp || 0).toFixed(2);
            const qty = parseFloat(batch.qty || batch.available_qty || 0);
            
            // Highlight low stock
            const qtyClass = qty <= 10 ? 'text-danger fw-bold' : 'text-success';
            
            return `
                <tr class="batch-row" 
                    data-batch='${JSON.stringify(batch)}'
                    data-index="${idx}"
                    ondblclick="BatchModalComponent.selectBatch('${modalId}', this)"
                    onclick="BatchModalComponent.highlightRow('${modalId}', this)"
                    style="cursor: pointer; background: #ffcccc;">
                    <td style="padding: 6px;">${batch.batch_no || ''}</td>
                    <td style="text-align: center; padding: 6px;">${purchaseDate}</td>
                    <td style="text-align: right; padding: 6px;">${rate}</td>
                    <td style="text-align: right; padding: 6px;">${mrp}</td>
                    <td style="text-align: right; padding: 6px; background: #c8e6c9;" class="${qtyClass}">${qty}</td>
                    <td style="text-align: center; padding: 6px;">${expiryDate}</td>
                    <td style="text-align: center; padding: 6px;">${batch.bar_code || ''}</td>
                </tr>
            `;
        }).join('');
    },
    
    /**
     * Format date for display
     */
    formatDate: function(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { 
                day: '2-digit', 
                month: '2-digit', 
                year: '2-digit' 
            }).replace(/\//g, '-');
        } catch (e) { return dateStr; }
    },
    
    /**
     * Format expiry date (MM/YY)
     */
    formatExpiry: function(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { 
                month: '2-digit', 
                year: '2-digit' 
            }).replace(/\//g, '/');
        } catch (e) { return dateStr; }
    },
    
    /**
     * Filter batches
     */
    filter: function(modalId) {
        const searchInput = document.getElementById(modalId + 'Search');
        const searchText = (searchInput.value || '').toLowerCase();
        const rows = document.querySelectorAll('#' + modalId + 'Body tr.batch-row');
        
        rows.forEach(row => {
            const batchNo = (row.cells[0]?.textContent || '').toLowerCase();
            row.style.display = batchNo.includes(searchText) ? '' : 'none';
        });
    },
    
    /**
     * Highlight and preview selected batch
     */
    highlightRow: function(modalId, row) {
        const tbody = row.closest('tbody');
        tbody.querySelectorAll('tr').forEach(r => r.classList.remove('row-selected'));
        row.classList.add('row-selected');
        
        // Store selected batch
        this.selectedBatch = JSON.parse(row.dataset.batch);
        
        // Update supplier info
        const supplierEl = document.getElementById(modalId + 'Supplier');
        if (supplierEl) {
            supplierEl.textContent = this.selectedBatch.supplier_name || '---';
        }
    },
    
    /**
     * Select batch (on double-click)
     */
    selectBatch: function(modalId, row) {
        const batchData = JSON.parse(row.dataset.batch);
        this.selectedBatch = batchData;
        this.confirmSelection(modalId);
    },
    
    /**
     * Confirm batch selection
     */
    confirmSelection: function(modalId, callbackName) {
        if (!this.selectedBatch) {
            alert('Please select a batch first');
            return;
        }
        
        const modal = document.getElementById(modalId);
        const options = JSON.parse(modal.dataset.options || '{}');
        const callback = callbackName || options.onSelectCallback;
        
        // Close modal
        this.close(modalId);
        
        // Call the callback with item + batch data
        if (typeof window[callback] === 'function') {
            window[callback](this.currentItem, this.selectedBatch);
        } else if (typeof window.onItemBatchSelected === 'function') {
            window.onItemBatchSelected(this.currentItem, this.selectedBatch);
        }
    }
};

// Expose globally
window.ItemModalComponent = ItemModalComponent;
window.BatchModalComponent = BatchModalComponent;
```

---

### Phase 3: Create API Endpoints (Day 2)

#### 3.1 Unified ItemBatch Controller

**File**: `app/Http/Controllers/Api/ItemBatchController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Http\Request;

class ItemBatchController extends Controller
{
    /**
     * Get all items for the current organization
     */
    public function getItems(Request $request)
    {
        $organizationId = auth()->user()->organization_id;
        
        $items = Item::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->select([
                'id', 'name', 'bar_code', 'hsn_code', 'packing', 'unit',
                'mrp', 's_rate', 'pur_rate', 'ws_rate', 'spl_rate',
                'cgst_percent', 'sgst_percent', 'cess_percent',
                'company_id', 'category_id', 'closing_stock',
                'location', 'case_qty', 'box_qty'
            ])
            ->with(['company:id,name'])
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->company_name = $item->company->name ?? '';
                return $item;
            });
        
        return response()->json(['items' => $items]);
    }
    
    /**
     * Get batches for an item
     * ⭐ KEY: Filter by available stock when available_only=1
     */
    public function getBatches(Request $request, $itemId)
    {
        $organizationId = auth()->user()->organization_id;
        $availableOnly = $request->boolean('available_only', false);
        
        $query = Batch::where('organization_id', $organizationId)
            ->where('item_id', $itemId)
            ->with(['supplier:id,name']);
        
        // ⭐ FILTER: Only show batches with available stock
        if ($availableOnly) {
            $query->where('qty', '>', 0);
        }
        
        $batches = $query->orderByDesc('created_at')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'bar_code' => $batch->bar_code,
                    'qty' => $batch->qty,
                    'available_qty' => $batch->qty,
                    'mrp' => $batch->mrp,
                    'avg_mrp' => $batch->avg_mrp ?? $batch->mrp,
                    's_rate' => $batch->s_rate,
                    'avg_s_rate' => $batch->avg_s_rate ?? $batch->s_rate,
                    'pur_rate' => $batch->pur_rate,
                    'avg_pur_rate' => $batch->avg_pur_rate ?? $batch->pur_rate,
                    'cost_gst' => $batch->cost_gst,
                    'avg_cost_gst' => $batch->avg_cost_gst ?? $batch->cost_gst,
                    'expiry_date' => $batch->expiry_date,
                    'expiry_display' => $batch->expiry_date 
                        ? date('m/y', strtotime($batch->expiry_date)) 
                        : 'N/A',
                    'purchase_date' => $batch->purchase_date,
                    'purchase_date_display' => $batch->purchase_date 
                        ? date('d-m-y', strtotime($batch->purchase_date)) 
                        : 'N/A',
                    'supplier_id' => $batch->supplier_id,
                    'supplier_name' => $batch->supplier->name ?? '',
                ];
            });
        
        return response()->json(['batches' => $batches, 'count' => $batches->count()]);
    }
}
```

#### 3.2 Add Routes

**File**: `routes/web.php` (add to API routes section)

```php
// Unified Item & Batch API (for reusable components)
Route::get('api/items/list', [App\Http\Controllers\Api\ItemBatchController::class, 'getItems'])
    ->name('api.items.list');
Route::get('api/item-batches/{itemId}', [App\Http\Controllers\Api\ItemBatchController::class, 'getBatches'])
    ->name('api.item-batches');
```

---

### Phase 4: Create CSS Styles (Day 2)

**File**: `public/css/components/modal-components.css`

```css
/* ====================================== */
/* REUSABLE MODAL COMPONENT STYLES        */
/* ====================================== */

/* Modal Backdrop */
.component-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.component-modal-backdrop.show {
    display: block;
    opacity: 1;
}

/* Modal Container */
.component-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 90%;
    z-index: 9999;
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.component-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

/* Modal Content Box */
.component-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

/* Modal Header */
.component-modal-header {
    padding: 1rem 1.5rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid rgba(0, 0, 0, 0.1);
}

.component-modal-header.bg-orange {
    background: #ff6b35;
}

.component-modal-header.bg-purple {
    background: #6f42c1;
}

.component-modal-header.bg-blue {
    background: #0d6efd;
}

.component-modal-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Close Button */
.btn-close-modal {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background 0.2s;
}

.btn-close-modal:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Modal Body */
.component-modal-body {
    padding: 0;
    background: #fff;
}

/* Modal Footer */
.component-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Row Styles */
.component-modal .row-selected {
    background-color: #007bff !important;
    color: white !important;
}

.component-modal .row-selected td {
    background-color: #007bff !important;
    color: white !important;
}

.component-modal .item-row:hover,
.component-modal .batch-row:hover {
    background-color: #e3f2fd !important;
}
```

---

### Phase 5: Integration Strategy (Day 3-4)

#### 5.1 Integration Steps for Each Module

For each transaction module (e.g., `sale/transaction.blade.php`):

**Step 1**: Replace inline modal HTML with component includes

```blade
{{-- BEFORE: Inline modal HTML (100+ lines) --}}
<div id="chooseItemsModal" class="choose-items-modal">
    ... 100-200 lines of HTML ...
</div>

{{-- AFTER: Single line component include --}}
<x-modals.item-selection 
    id="chooseItemsModal"
    module="sale"
    :showStock="true"
    :rateType="'s_rate'"
    onSelectCallback="onItemSelected"
/>

<x-modals.batch-selection 
    id="batchSelectionModal"
    module="sale"
    :showOnlyAvailable="true"
    :rateType="'s_rate'"
    onSelectCallback="onBatchSelected"
/>
```

**Step 2**: Replace inline JavaScript functions with component calls

```javascript
// BEFORE: Module-specific function
function openChooseItemsModal() {
    const modal = document.getElementById('chooseItemsModal');
    // ... 20+ lines of code
}

// AFTER: Component call
function openChooseItemsModal() {
    ItemModalComponent.open('chooseItemsModal', {
        batchModalId: 'batchSelectionModal',
        onBatchSelectCallback: 'onBatchSelected'
    });
}
```

**Step 3**: Create unified callback function

```javascript
// Unified callback for item + batch selection
function onBatchSelected(item, batch) {
    // Add item to table with selected batch
    addItemToTable(item, batch);
}
```

#### 5.2 Module-Specific Configurations

| Module | Show Stock | Rate Type | Available Only | Show Cost |
|--------|------------|-----------|----------------|-----------|
| Sale Transaction | ✅ | s_rate | ✅ | ❌ |
| Sale Return | ✅ | s_rate | ✅ | ❌ |
| Purchase Transaction | ✅ | pur_rate | ❌ | ✅ |
| Purchase Return | ✅ | pur_rate | ✅ | ✅ |
| Stock Adjustment | ✅ | - | ✅ | ❌ |
| Stock Transfer Out | ✅ | - | ✅ | ❌ |
| Stock Transfer In | ❌ | - | ❌ | ❌ |
| Breakage/Expiry | ✅ | - | ✅ | ❌ |
| Sample Issued | ✅ | - | ✅ | ❌ |
| Sample Received | ❌ | - | ❌ | ❌ |
| Quotation | ✅ | s_rate | ✅ | ❌ |

---

### Phase 6: Testing & Migration (Day 4-5)

#### 6.1 Testing Checklist

For each migrated module:

- [ ] **Modal Opens**: Component modal opens correctly
- [ ] **Search Works**: Items filter correctly on search
- [ ] **Item Selection**: Double-click selects item and opens batch modal
- [ ] **Batch Filter**: Only batches with qty > 0 are shown
- [ ] **Batch Selection**: Double-click or button selects batch
- [ ] **Data Population**: Item + batch data correctly populates transaction row
- [ ] **Calculations**: Row amounts calculate correctly
- [ ] **Keyboard Navigation**: Tab/Enter navigation works
- [ ] **Modal Close**: Escape and X button close modals
- [ ] **Multiple Items**: Can add multiple items sequentially

#### 6.2 Migration Order (Priority)

**High Priority (Critical Modules)**:
1. `sale/transaction.blade.php`
2. `sale/modification.blade.php`
3. `purchase/transaction.blade.php`
4. `purchase/modification.blade.php`
5. `sale-return/transaction.blade.php`

**Medium Priority**:
6. `sale-challan/transaction.blade.php`
7. `purchase-return/transaction.blade.php`
8. `stock-adjustment/transaction.blade.php`
9. `quotation/transaction.blade.php`

**Lower Priority (Migrate Later)**:
10. All remaining transaction modules
11. All modification modules

---

## 📋 Rollback Strategy

In case of issues, maintain backward compatibility:

1. Keep original inline code commented out
2. Can switch back by removing component include
3. Feature flags in config for gradual rollout

```php
// config/components.php
return [
    'use_modal_components' => env('USE_MODAL_COMPONENTS', true),
];
```

```blade
@if(config('components.use_modal_components'))
    <x-modals.item-selection ... />
@else
    {{-- Original inline modal --}}
@endif
```

---

## 📊 Success Metrics

| Metric | Before | After |
|--------|--------|-------|
| Lines of Modal HTML | ~7,400 (100 lines × 74 files) | ~150 (2 component files) |
| Lines of Modal JS | ~14,800 (200 lines × 74 files) | ~400 (2 JS files) |
| Time to Add New Feature | ~4 hours (74 files) | ~15 minutes (2 files) |
| Bug Fix Propagation | ~2 hours (find all instances) | ~5 minutes (1 location) |
| Batch Stock Filtering | Inconsistent | 100% Consistent |

---

## 🚨 Risk Mitigation

### Risk 1: Breaking Existing Functionality
- **Mitigation**: Phased rollout, starting with Sale module
- **Fallback**: Feature flag to disable new components

### Risk 2: Performance Impact
- **Mitigation**: Items loaded once and cached in JS
- **Monitoring**: Console timing for API calls

### Risk 3: Module-Specific Edge Cases
- **Mitigation**: Callback system allows module-specific logic
- **Testing**: Thorough testing per module before migration

---

## 📅 Implementation Timeline

| Day | Phase | Tasks |
|-----|-------|-------|
| Day 1 | Phase 1-2 | Create Blade components, JavaScript files |
| Day 2 | Phase 3-4 | Create API controller, routes, CSS |
| Day 3 | Phase 5 | Migrate Sale transaction & modification |
| Day 4 | Phase 5 | Migrate Purchase, Sale Return modules |
| Day 5 | Phase 6 | Testing, bug fixes, documentation |
| Day 6+ | Phase 5-6 | Migrate remaining modules (ongoing) |

---

## ✅ Acceptance Criteria

1. **Reusable Components Created**: Item and Batch modal components exist
2. **Available Stock Filter**: Batch modal shows only batches with qty > 0
3. **Sale Module Migrated**: Sale transaction uses new components
4. **Backward Compatible**: Original functionality preserved
5. **Documentation Complete**: Usage instructions documented
6. **Zero Regression**: All existing tests pass

---

## 📝 Next Steps

After approval:

1. Create component files in `resources/views/components/modals/`
2. Create JavaScript files in `public/js/components/`
3. Create CSS file in `public/css/components/`
4. Create API controller
5. Add routes
6. Migrate Sale → Transaction module as pilot
7. Test thoroughly
8. Document any module-specific adjustments
9. Roll out to remaining modules

---

**Document Version**: 1.0  
**Created**: 2026-01-31  
**Author**: Claude AI  
**Status**: Ready for Review
