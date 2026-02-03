/**
 * Batch Modal Component
 * Reusable batch selection modal for all transaction modules
 * 
 * KEY FEATURE: Only shows batches with available stock (qty > 0)
 * 
 * Usage:
 *   BatchModalComponent.open('modalId', { item: itemData });
 *   BatchModalComponent.close('modalId');
 */
const BatchModalComponent = (function () {
    'use strict';

    // Private state
    let batches = [];
    let filteredBatches = [];
    let selectedBatch = null;
    let selectedRowIndex = -1;
    let currentItem = null;
    let isLoading = false;

    // API endpoint template for fetching batches
    const API_ENDPOINT_TEMPLATE = '/admin/api/item-batches/{itemId}';

    /**
     * Open the batch modal for an item
     * @param {string} modalId - The modal element ID
     * @param {object} options - Configuration options including item data
     */
    function open(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');

        if (!modal || !backdrop) {
            console.error('[BatchModal] Modal not found:', modalId);
            return;
        }

        if (!options.item || !options.item.id) {
            console.error('[BatchModal] Item data required');
            return;
        }

        // Store current item
        currentItem = options.item;
        selectedBatch = null;
        selectedRowIndex = -1;

        // Store options
        modal.dataset.options = JSON.stringify(options);

        // Update item display
        updateItemDisplay(modalId, options.item);

        // Clear search
        const searchInput = document.getElementById(modalId + 'Search');
        if (searchInput) searchInput.value = '';

        // Load batches for item
        loadBatches(modalId, options.item.id);

        // Show modal with animation
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');

            // Focus search input
            if (searchInput) {
                searchInput.focus();
            }
        }, 10);

        // Add escape key listener
        document.addEventListener('keydown', handleEscapeKey);
    }

    /**
     * Close the batch modal
     * @param {string} modalId - The modal element ID
     */
    function close(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');

        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');

        // Clear state
        selectedBatch = null;
        selectedRowIndex = -1;
        currentItem = null;
        batches = [];

        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);
    }

    /**
     * Handle escape key to close modal
     */
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.component-modal.show[id*="batch"]');
            if (openModal) {
                close(openModal.id);
            }
        }
    }

    /**
     * Update item display in modal header
     * @param {string} modalId - The modal element ID
     * @param {object} item - Item data
     */
    function updateItemDisplay(modalId, item) {
        const itemNameEl = document.getElementById(modalId + 'ItemName');
        const brandEl = document.getElementById(modalId + 'Brand');
        const packingEl = document.getElementById(modalId + 'Packing');

        if (itemNameEl) itemNameEl.textContent = item.name || '---';
        if (brandEl) brandEl.textContent = item.name || '---';
        if (packingEl) packingEl.textContent = item.packing || '---';

        // Clear supplier info
        const supplierEl = document.getElementById(modalId + 'Supplier');
        const purchaseDateEl = document.getElementById(modalId + 'PurchaseDate');
        if (supplierEl) supplierEl.textContent = '---';
        if (purchaseDateEl) purchaseDateEl.textContent = '---';
    }

    /**
     * Load batches for an item
     * @param {string} modalId - The modal element ID
     * @param {number} itemId - Item ID
     */
    function loadBatches(modalId, itemId) {
        const modal = document.getElementById(modalId);
        const showOnlyAvailable = modal.dataset.showOnlyAvailable === 'true';
        const tbody = document.getElementById(modalId + 'Body');

        isLoading = true;

        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <span class="ms-2">Loading batches...</span>
                </td>
            </tr>
        `;

        // Build endpoint URL
        let endpoint = API_ENDPOINT_TEMPLATE.replace('{itemId}', itemId);
        if (showOnlyAvailable) {
            endpoint += '?available_only=1';
        }

        fetch(endpoint)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                let batchList = Array.isArray(data) ? data : (data.batches || []);

                // ⭐ CLIENT-SIDE FILTER: Extra safety - only show qty > 0
                if (showOnlyAvailable) {
                    batchList = batchList.filter(batch => {
                        const qty = parseFloat(batch.qty || batch.available_qty || 0);
                        return qty > 0;
                    });
                }

                batches = batchList;
                filteredBatches = [...batches];

                console.log('[BatchModal] Loaded', batches.length, 'batches for item', itemId);
                displayBatches(modalId, batches);
            })
            .catch(error => {
                console.error('[BatchModal] Error loading batches:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error loading batches. Please try again.
                        </td>
                    </tr>
                `;
            })
            .finally(() => {
                isLoading = false;
            });
    }

    /**
     * Display batches in the table
     * @param {string} modalId - The modal element ID
     * @param {array} batchesToDisplay - Batches to display
     */
    function displayBatches(modalId, batchesToDisplay) {
        const tbody = document.getElementById(modalId + 'Body');
        const modal = document.getElementById(modalId);
        const showOnlyAvailable = modal.dataset.showOnlyAvailable === 'true';

        if (!batchesToDisplay || batchesToDisplay.length === 0) {
            const message = showOnlyAvailable
                ? 'No batches with available stock found for this item'
                : 'No batches found for this item';

            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="bi bi-exclamation-circle text-warning me-2"></i>
                        ${message}
                    </td>
                </tr>
            `;
            return;
        }

        // Build table rows
        tbody.innerHTML = batchesToDisplay.map((batch, index) => {
            const qty = parseFloat(batch.qty || batch.available_qty || 0);
            const qtyClass = qty <= 10 ? 'text-danger fw-bold' : 'text-success fw-bold';

            const purchaseDate = formatDate(batch.purchase_date_display || batch.purchase_date);
            const expiryDate = formatExpiry(batch.expiry_display || batch.expiry_date);

            const rate = parseFloat(batch.avg_s_rate || batch.s_rate || 0).toFixed(2);
            const purRate = parseFloat(batch.avg_pur_rate || batch.pur_rate || 0).toFixed(2);
            const mrp = parseFloat(batch.avg_mrp || batch.mrp || 0).toFixed(2);

            return `
                <tr class="batch-row" 
                    data-batch='${escapeHtml(JSON.stringify(batch))}'
                    data-index="${index}"
                    ondblclick="BatchModalComponent.selectBatch('${modalId}', ${index})"
                    onclick="BatchModalComponent.highlightRow('${modalId}', ${index})"
                    style="cursor: pointer; background: #fff5f5;">
                    <td style="padding: 6px;">${escapeHtml(batch.batch_no || '')}</td>
                    <td style="text-align: center; padding: 6px;">${purchaseDate}</td>
                    <td style="text-align: right; padding: 6px;">${rate}</td>
                    <td style="text-align: right; padding: 6px;">${purRate}</td>
                    <td style="text-align: right; padding: 6px;">${mrp}</td>
                    <td style="text-align: right; padding: 6px; background: #e8f5e9;" class="${qtyClass}">${qty}</td>
                    <td style="text-align: center; padding: 6px;">${expiryDate}</td>
                    <td style="text-align: center; padding: 6px;">${escapeHtml(batch.bar_code || '')}</td>
                </tr>
            `;
        }).join('');

        filteredBatches = batchesToDisplay;

        // Auto-select first batch if only one exists
        if (batchesToDisplay.length === 1) {
            highlightRow(modalId, 0);
        }
    }

    /**
     * Filter batches based on search input
     * @param {string} modalId - The modal element ID
     */
    function filter(modalId) {
        const searchInput = document.getElementById(modalId + 'Search');
        const searchText = (searchInput?.value || '').toLowerCase().trim();

        if (!searchText) {
            displayBatches(modalId, batches);
            return;
        }

        // Filter batches by batch number
        const filtered = batches.filter(batch => {
            const batchNo = (batch.batch_no || '').toLowerCase();
            const barcode = (batch.bar_code || '').toLowerCase();
            return batchNo.includes(searchText) || barcode.includes(searchText);
        });

        displayBatches(modalId, filtered);
        selectedRowIndex = -1;
    }

    /**
     * Highlight selected row and update details
     * @param {string} modalId - The modal element ID
     * @param {number} index - Row index
     */
    function highlightRow(modalId, index) {
        const tbody = document.getElementById(modalId + 'Body');

        // Remove previous selection
        tbody.querySelectorAll('tr').forEach(r => r.classList.remove('row-selected'));

        // Add selection to clicked row
        const rows = tbody.querySelectorAll('tr.batch-row');
        if (rows[index]) {
            rows[index].classList.add('row-selected');
            selectedRowIndex = index;
            selectedBatch = filteredBatches[index];

            // Update batch details display
            updateBatchDetails(modalId, selectedBatch);
        }
    }

    /**
     * Update batch details in the details section
     * @param {string} modalId - The modal element ID
     * @param {object} batch - Batch data
     */
    function updateBatchDetails(modalId, batch) {
        const supplierEl = document.getElementById(modalId + 'Supplier');
        const purchaseDateEl = document.getElementById(modalId + 'PurchaseDate');

        if (supplierEl) {
            supplierEl.textContent = batch.supplier_name || '---';
        }
        if (purchaseDateEl) {
            purchaseDateEl.textContent = formatDate(batch.purchase_date_display || batch.purchase_date);
        }
    }

    /**
     * Handle keyboard navigation
     * @param {Event} e - Keyboard event
     * @param {string} modalId - The modal element ID
     */
    function handleKeyDown(e, modalId) {
        const tbody = document.getElementById(modalId + 'Body');
        const rows = tbody.querySelectorAll('tr.batch-row');

        if (rows.length === 0) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (selectedRowIndex < rows.length - 1) {
                    highlightRow(modalId, selectedRowIndex + 1);
                    rows[selectedRowIndex]?.scrollIntoView({ block: 'nearest' });
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (selectedRowIndex > 0) {
                    highlightRow(modalId, selectedRowIndex - 1);
                    rows[selectedRowIndex]?.scrollIntoView({ block: 'nearest' });
                } else if (selectedRowIndex === -1) {
                    highlightRow(modalId, rows.length - 1);
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (selectedRowIndex >= 0 && selectedRowIndex < filteredBatches.length) {
                    selectBatch(modalId, selectedRowIndex);
                } else if (filteredBatches.length === 1) {
                    // Auto-select if only one result
                    selectBatch(modalId, 0);
                }
                break;
        }
    }

    /**
     * Select batch from row double-click
     * @param {string} modalId - The modal element ID
     * @param {number} index - Batch index
     */
    function selectBatch(modalId, index) {
        highlightRow(modalId, index);
        confirmSelection(modalId);
    }

    /**
     * Confirm batch selection and call callback
     * @param {string} modalId - The modal element ID
     */
    function confirmSelection(modalId) {
        if (!selectedBatch) {
            alert('Please select a batch first');
            return;
        }

        console.log('[BatchModal] Selected batch:', selectedBatch.batch_no);

        // Get options from modal
        const modal = document.getElementById(modalId);
        const options = JSON.parse(modal.dataset.options || '{}');
        const callback = modal.dataset.onSelectCallback || options.onSelectCallback || 'onBatchSelected';

        // Store selected data
        const item = currentItem;
        const batch = selectedBatch;

        // Close modal
        close(modalId);

        // Call the callback with item + batch data
        if (typeof window[callback] === 'function') {
            window[callback](item, batch);
        } else if (typeof window.onItemBatchSelected === 'function') {
            window.onItemBatchSelected(item, batch);
        } else {
            console.warn('[BatchModal] No callback found:', callback);
        }
    }

    /**
     * Get currently selected batch
     * @returns {object|null} Selected batch data
     */
    function getSelectedBatch() {
        return selectedBatch;
    }

    /**
     * Get current item
     * @returns {object|null} Current item data
     */
    function getCurrentItem() {
        return currentItem;
    }

    /**
     * Format date for display (dd-mm-yy)
     * @param {string} dateStr - Date string
     * @returns {string} Formatted date
     */
    function formatDate(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        } catch (e) {
            return dateStr;
        }
    }

    /**
     * Format expiry date (mm/yy)
     * @param {string} dateStr - Date string
     * @returns {string} Formatted expiry
     */
    function formatExpiry(dateStr) {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;

            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).slice(-2);
            return `${month}/${year}`;
        } catch (e) {
            return dateStr;
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Public API
    return {
        open: open,
        close: close,
        filter: filter,
        highlightRow: highlightRow,
        handleKeyDown: handleKeyDown,
        selectBatch: selectBatch,
        confirmSelection: confirmSelection,
        getSelectedBatch: getSelectedBatch,
        getCurrentItem: getCurrentItem
    };
})();

// Expose globally
window.BatchModalComponent = BatchModalComponent;
