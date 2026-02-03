/**
 * Item Modal Component
 * Reusable item selection modal for all transaction modules
 * 
 * Usage:
 *   ItemModalComponent.open('modalId', { batchModalId: 'batchModal' });
 *   ItemModalComponent.close('modalId');
 */
const ItemModalComponent = (function () {
    'use strict';

    // Private state
    let items = [];
    let filteredItems = [];
    let selectedItem = null;
    let selectedRowIndex = -1;
    let isLoading = false;

    // API endpoint for fetching items
    const API_ENDPOINT = '/admin/api/items/list';

    /**
     * Open the item modal
     * @param {string} modalId - The modal element ID
     * @param {object} options - Configuration options
     */
    function open(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');

        if (!modal || !backdrop) {
            console.error('[ItemModal] Modal not found:', modalId);
            return;
        }

        // Store options in modal dataset
        modal.dataset.options = JSON.stringify(options);

        // Reset state
        selectedItem = null;
        selectedRowIndex = -1;

        // Clear search
        const searchInput = document.getElementById(modalId + 'Search');
        if (searchInput) searchInput.value = '';

        // Load items if not already loaded
        if (items.length === 0 && !isLoading) {
            loadItems(modalId);
        } else {
            displayItems(modalId, items);
        }

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
     * Close the item modal
     * @param {string} modalId - The modal element ID
     */
    function close(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + 'Backdrop');

        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');

        // Clear selection
        selectedItem = null;
        selectedRowIndex = -1;

        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);
    }

    /**
     * Handle escape key to close modal
     */
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            // Find any open modal and close it
            const openModal = document.querySelector('.component-modal.show[id*="item"]');
            if (openModal) {
                close(openModal.id);
            }
        }
    }

    /**
     * Load items from server
     * @param {string} modalId - The modal element ID
     */
    function loadItems(modalId) {
        isLoading = true;
        const tbody = document.getElementById(modalId + 'Body');

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <span class="ms-2">Loading items...</span>
                </td>
            </tr>
        `;

        fetch(API_ENDPOINT)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                items = data.items || data || [];
                filteredItems = [...items];
                console.log('[ItemModal] Loaded', items.length, 'items');
                displayItems(modalId, items);
            })
            .catch(error => {
                console.error('[ItemModal] Error loading items:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error loading items. Please try again.
                        </td>
                    </tr>
                `;
            })
            .finally(() => {
                isLoading = false;
            });
    }

    /**
     * Display items in the table
     * @param {string} modalId - The modal element ID
     * @param {array} itemsToDisplay - Items to display
     */
    function displayItems(modalId, itemsToDisplay) {
        const tbody = document.getElementById(modalId + 'Body');
        const countBadge = document.getElementById(modalId + 'Count');
        const modal = document.getElementById(modalId);

        // Update count
        if (countBadge) {
            countBadge.textContent = itemsToDisplay.length + ' items';
        }

        if (!itemsToDisplay || itemsToDisplay.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox me-2"></i>No items found
                    </td>
                </tr>
            `;
            return;
        }

        // Build table rows
        tbody.innerHTML = itemsToDisplay.map((item, index) => {
            const stock = parseFloat(item.closing_stock || 0);
            const stockClass = stock <= 0 ? 'text-danger' : (stock < 10 ? 'text-warning' : 'text-success');

            return `
                <tr class="item-row" 
                    data-item='${escapeHtml(JSON.stringify(item))}'
                    data-index="${index}"
                    ondblclick="ItemModalComponent.selectItem('${modalId}', ${index})"
                    onclick="ItemModalComponent.highlightRow('${modalId}', ${index})"
                    style="cursor: pointer;">
                    <td style="padding: 6px;">${escapeHtml(item.name || '')}</td>
                    <td style="padding: 6px;">${escapeHtml(item.hsn_code || '')}</td>
                    <td style="text-align: right; padding: 6px;" class="${stockClass}">${stock.toFixed(0)}</td>
                    <td style="text-align: right; padding: 6px;">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                    <td style="text-align: right; padding: 6px;">${parseFloat(item.mrp || 0).toFixed(2)}</td>
                    <td style="padding: 6px;">${escapeHtml(item.company_name || item.company || '')}</td>
                </tr>
            `;
        }).join('');

        filteredItems = itemsToDisplay;
    }

    /**
     * Filter items based on search input
     * @param {string} modalId - The modal element ID
     */
    function filter(modalId) {
        const searchInput = document.getElementById(modalId + 'Search');
        const searchText = (searchInput?.value || '').toLowerCase().trim();

        if (!searchText) {
            displayItems(modalId, items);
            return;
        }

        // Filter items
        const filtered = items.filter(item => {
            const name = (item.name || '').toLowerCase();
            const hsn = (item.hsn_code || '').toLowerCase();
            const barcode = (item.bar_code || '').toLowerCase();
            const company = (item.company_name || item.company || '').toLowerCase();

            return name.includes(searchText) ||
                hsn.includes(searchText) ||
                barcode.includes(searchText) ||
                company.includes(searchText);
        });

        displayItems(modalId, filtered);
        selectedRowIndex = -1;
    }

    /**
     * Highlight selected row
     * @param {string} modalId - The modal element ID
     * @param {number} index - Row index
     */
    function highlightRow(modalId, index) {
        const tbody = document.getElementById(modalId + 'Body');

        // Remove previous selection
        tbody.querySelectorAll('tr').forEach(r => r.classList.remove('row-selected'));

        // Add selection to clicked row
        const rows = tbody.querySelectorAll('tr.item-row');
        if (rows[index]) {
            rows[index].classList.add('row-selected');
            selectedRowIndex = index;
            selectedItem = filteredItems[index];
        }
    }

    /**
     * Handle keyboard navigation
     * @param {Event} e - Keyboard event
     * @param {string} modalId - The modal element ID
     */
    function handleKeyDown(e, modalId) {
        const tbody = document.getElementById(modalId + 'Body');
        const rows = tbody.querySelectorAll('tr.item-row');

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
                }
                // Do nothing when at first row or no selection - prevents looping
                break;

            case 'Enter':
                e.preventDefault();
                if (selectedRowIndex >= 0 && selectedRowIndex < filteredItems.length) {
                    selectItem(modalId, selectedRowIndex);
                } else if (filteredItems.length === 1) {
                    // Auto-select if only one result
                    selectItem(modalId, 0);
                }
                break;
        }
    }

    /**
     * Select item and open batch modal
     * @param {string} modalId - The modal element ID
     * @param {number} index - Item index in filtered list
     */
    function selectItem(modalId, index) {
        const item = filteredItems[index];
        if (!item) {
            console.error('[ItemModal] Item not found at index:', index);
            return;
        }

        selectedItem = item;
        console.log('[ItemModal] Selected item:', item.name);

        // Get options from modal
        const modal = document.getElementById(modalId);
        const options = JSON.parse(modal.dataset.options || '{}');
        const batchModalId = modal.dataset.batchModalId || options.batchModalId;
        const callback = modal.dataset.onSelectCallback || options.onSelectCallback;

        // Close item modal
        close(modalId);

        // Open batch selection modal if configured
        if (batchModalId && typeof BatchModalComponent !== 'undefined') {
            BatchModalComponent.open(batchModalId, {
                item: item,
                onSelectCallback: callback
            });
        } else if (typeof window[callback] === 'function') {
            // Direct callback without batch selection
            window[callback](item);
        }
    }

    /**
     * Get currently selected item
     * @returns {object|null} Selected item data
     */
    function getSelectedItem() {
        return selectedItem;
    }

    /**
     * Refresh items list from server
     * @param {string} modalId - The modal element ID
     */
    function refresh(modalId) {
        items = [];
        loadItems(modalId);
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
        selectItem: selectItem,
        getSelectedItem: getSelectedItem,
        refresh: refresh
    };
})();

// Expose globally
window.ItemModalComponent = ItemModalComponent;
