/**
 * Index Page Keyboard Shortcuts
 * Handles page-specific shortcuts that only work on index/list pages
 * 
 * These are separate from Global navigation shortcuts.
 * 
 * Common Index Shortcuts:
 * - Insert: Add new record
 * - Delete: Delete selected record
 * - Ctrl+E: Edit selected record
 * - Ctrl+P: Print
 * - Ctrl+Shift+F: Focus search
 * - Ctrl+R: Refresh data
 */

(function () {
    'use strict';

    // Get configuration from Laravel (injected by Blade partial)
    const CONFIG = window.INDEX_SHORTCUTS_CONFIG || null;

    if (!CONFIG) {
        console.log('📋 Index shortcuts config not found. Index shortcuts disabled.');
        return;
    }

    const SHORTCUTS = CONFIG.shortcuts || {};
    const IS_DYNAMIC = CONFIG.isDynamic || false;

    /**
     * Build the shortcut key string from the event
     */
    function getShortcutKey(event) {
        const parts = [];

        if (event.ctrlKey || event.metaKey) parts.push('ctrl');
        if (event.shiftKey) parts.push('shift');
        if (event.altKey) parts.push('alt');

        let key = event.key.toLowerCase();

        // Handle special keys
        if (key === 'insert') key = 'insert';
        else if (key === 'delete') key = 'delete';
        else if (key === 'backspace') key = 'backspace';
        else if (key.startsWith('f') && key.length <= 3) key = key;

        parts.push(key);

        return parts.join('+');
    }

    /**
     * Check if any input element is focused
     */
    function isInputFocused() {
        const activeElement = document.activeElement;
        if (!activeElement) return false;

        const tagName = activeElement.tagName.toLowerCase();
        const isContentEditable = activeElement.isContentEditable;

        return tagName === 'input' ||
            tagName === 'textarea' ||
            tagName === 'select' ||
            isContentEditable;
    }

    /**
     * Show a toast notification
     */
    function showToast(message, type = 'info') {
        let toastContainer = document.getElementById('index-shortcut-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'index-shortcut-toast-container';
            toastContainer.className = 'position-fixed bottom-0 start-0 p-3';
            toastContainer.style.zIndex = '11000';
            document.body.appendChild(toastContainer);
        }

        const bgClass = type === 'success' ? 'text-bg-success' :
            type === 'danger' ? 'text-bg-danger' :
                type === 'warning' ? 'text-bg-warning' : 'text-bg-info';

        const toastId = 'index-toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-keyboard me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastElement, { delay: 1500 });
        bsToast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    /**
     * Get all data rows from the table (excluding empty state rows)
     */
    function getAllDataRows() {
        // Try to find table body with common IDs
        const tbody = document.querySelector('[id$="-table-body"], .module-table-body, table tbody');
        if (!tbody) return [];
        
        return Array.from(tbody.querySelectorAll('tr')).filter(tr => {
            const tds = tr.querySelectorAll('td');
            // If row has only 1 td with colspan, it's an empty state row
            if (tds.length === 1 && tds[0].hasAttribute('colspan')) {
                return false;
            }
            return tds.length > 0;
        });
    }

    /**
     * Get currently selected row
     */
    function getSelectedRow() {
        return document.querySelector('tr.row-selected, tr.selected, tr.table-active, tr[data-selected="true"]');
    }

    /**
     * Select a row visually
     */
    function selectRow(row) {
        if (!row) return;
        
        // Find the tbody
        const tbody = row.closest('tbody');
        if (!tbody) return;
        
        // Remove selection from all rows
        tbody.querySelectorAll('tr.row-selected').forEach(r => {
            r.classList.remove('row-selected');
        });
        
        // Select the new row
        row.classList.add('row-selected');
        
        // Scroll into view
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Handle arrow key navigation
     * NOTE: This is a fallback - module-shortcuts.blade.php handles this primarily
     */
    function handleArrowNavigation(event) {
        if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') return false;
        
        // Don't handle if typing in input
        if (isInputFocused()) return false;
        
        // Check if module-shortcuts is handling this page
        // If a specific module table body exists (like supplier-table-body, item-table-body),
        // then module-shortcuts.blade.php is included and will handle arrow navigation
        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        if (moduleTableBody && moduleTableBody.id !== 'module-table-body') {
            // Module shortcuts handles this - skip to avoid duplicate handling
            return false;
        }
        
        // Only handle if no module-specific table body exists (fallback for pages without module-shortcuts)
        event.preventDefault();
        event.stopPropagation();
        
        const rows = getAllDataRows();
        if (rows.length === 0) {
            showToast('No rows available', 'warning');
            return true;
        }
        
        const currentRow = getSelectedRow();
        let currentIndex = -1;
        
        if (currentRow) {
            currentIndex = rows.findIndex(r => r === currentRow);
        }
        
        // If no row selected, select first on ArrowDown, last on ArrowUp
        if (currentIndex === -1) {
            if (event.key === 'ArrowDown') {
                selectRow(rows[0]);
            } else {
                selectRow(rows[rows.length - 1]);
            }
            return true;
        }
        
        if (event.key === 'ArrowUp') {
            if (currentIndex > 0) {
                selectRow(rows[currentIndex - 1]);
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else if (event.key === 'ArrowDown') {
            if (currentIndex < rows.length - 1) {
                selectRow(rows[currentIndex + 1]);
            } else {
                // Check for infinite scroll sentinel
                const sentinel = document.querySelector('[id$="-sentinel"]');
                if (sentinel && sentinel.getAttribute('data-next-url')) {
                    sentinel.scrollIntoView({ behavior: 'smooth' });
                    // Wait for new rows to load
                    const currentRowCount = rows.length;
                    let checkCount = 0;
                    const checkForNewRows = setInterval(() => {
                        checkCount++;
                        const newRows = getAllDataRows();
                        if (newRows.length > currentRowCount) {
                            selectRow(newRows[currentRowCount]);
                            clearInterval(checkForNewRows);
                        } else if (checkCount > 20) {
                            clearInterval(checkForNewRows);
                        }
                    }, 100);
                }
            }
        }
        
        return true;
    }

    /**
     * Handle index-specific actions
     * NOTE: For pages with module-shortcuts, F3/Delete are handled there
     */
    function handleAction(action, shortcut) {
        // Check if module-shortcuts is handling this page
        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        const hasModuleShortcuts = moduleTableBody && moduleTableBody.id !== 'module-table-body';
        
        switch (action) {
            case 'add':
                // Look for "Add New" or "Create" button
                const addBtn = document.querySelector('[data-action="add"], .btn-add-new, a[href*="create"], button[onclick*="create"]');
                if (addBtn) {
                    showToast('Adding new record...', 'success');
                    addBtn.click();
                } else {
                    showToast('Add button not found', 'warning');
                }
                break;

            case 'delete':
                // Skip if module-shortcuts handles this page (it handles Delete key)
                if (hasModuleShortcuts) {
                    return;
                }
                // Look for selected row's delete button or global delete
                const selectedRow = document.querySelector('tr.row-selected, tr.selected, tr.table-active, tr[data-selected="true"]');
                if (selectedRow) {
                    const deleteBtn = selectedRow.querySelector('[data-action="delete"], .btn-delete, button[onclick*="delete"], button.ajax-delete');
                    if (deleteBtn) {
                        showToast('Deleting selected...', 'danger');
                        deleteBtn.click();
                    } else {
                        showToast('Delete button not found', 'warning');
                    }
                } else {
                    showToast('No row selected - Use Arrow keys to select', 'warning');
                }
                break;

            case 'edit':
                // Skip if module-shortcuts handles this page (it handles F3)
                if (hasModuleShortcuts) {
                    return;
                }
                // Look for selected row's edit button
                const editRow = document.querySelector('tr.row-selected, tr.selected, tr.table-active, tr[data-selected="true"]');
                if (editRow) {
                    const editBtn = editRow.querySelector('[data-action="edit"], .btn-edit, a[href*="edit"], a[title="Edit"]');
                    if (editBtn) {
                        showToast('Editing selected...', 'info');
                        if (editBtn.href) {
                            window.location.href = editBtn.href;
                        } else {
                            editBtn.click();
                        }
                    } else {
                        showToast('Edit button not found', 'warning');
                    }
                } else {
                    showToast('No row selected - Use Arrow keys to select', 'warning');
                }
                break;

            case 'print':
                // Trigger print
                const printBtn = document.querySelector('[data-action="print"], .btn-print, button[onclick*="print"]');
                if (printBtn) {
                    printBtn.click();
                } else {
                    window.print();
                }
                showToast('Printing...', 'info');
                break;

            case 'search':
                // Focus search input
                const searchInput = document.querySelector('[data-action="search"], input[type="search"], input[name="search"], .search-input, #searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                    showToast('Search focused', 'info');
                } else {
                    showToast('Search input not found', 'warning');
                }
                break;

            case 'refresh':
                // Refresh data or page
                const refreshBtn = document.querySelector('[data-action="refresh"], .btn-refresh');
                if (refreshBtn) {
                    refreshBtn.click();
                    showToast('Refreshing...', 'info');
                } else {
                    window.location.reload();
                }
                break;

            default:
                // Custom action or navigation
                if (shortcut.url) {
                    showToast('Navigating to ' + shortcut.description, 'info');
                    setTimeout(() => {
                        window.location.href = shortcut.url;
                    }, 300);
                }
                break;
        }
    }

    /**
     * Main keyboard event handler for index shortcuts
     */
    function handleKeyDown(event) {
        // Check if module-shortcuts is handling this page
        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        const hasModuleShortcuts = moduleTableBody && moduleTableBody.id !== 'module-table-body';
        
        // Skip arrow key handling if module-shortcuts handles this page
        if ((event.key === 'ArrowUp' || event.key === 'ArrowDown') && hasModuleShortcuts) {
            // Let module-shortcuts handle arrow navigation
            return;
        }
        
        // Handle arrow key navigation for pages without module-shortcuts
        if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
            if (handleArrowNavigation(event)) {
                return;
            }
        }
        
        // Skip if global help panel is handling
        if (event.key === 'F1' || event.key === 'Escape') {
            return;
        }

        // Skip if input is focused (except for specific shortcuts)
        if (isInputFocused()) {
            // Allow Ctrl combinations even when input is focused
            if (!event.ctrlKey && !event.altKey) {
                return;
            }
        }

        const shortcutKey = getShortcutKey(event);
        const shortcut = SHORTCUTS[shortcutKey];

        if (shortcut) {
            event.preventDefault();
            event.stopPropagation();

            if (shortcut.action) {
                handleAction(shortcut.action, shortcut);
            } else if (shortcut.url) {
                showToast('Navigating to ' + shortcut.description, 'info');
                setTimeout(() => {
                    window.location.href = shortcut.url;
                }, 300);
            }
        }
    }

    /**
     * Initialize
     */
    function init() {
        document.addEventListener('keydown', handleKeyDown, true);
        
        // Add row selection styles
        const style = document.createElement('style');
        style.textContent = `
            /* Row selection styling */
            table tbody tr {
                cursor: pointer;
                transition: background-color 0.15s ease;
            }
            table tbody tr:hover {
                background-color: #f0f9ff !important;
            }
            table tbody tr:hover > td {
                background-color: #f0f9ff !important;
            }
            table tbody tr.row-selected {
                background-color: #bfdbfe !important;
                border-left: 3px solid #3b82f6 !important;
                box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.2) !important;
            }
            table tbody tr.row-selected > td {
                background-color: #bfdbfe !important;
            }
            table tbody tr.row-selected:hover {
                background-color: #93c5fd !important;
            }
            table tbody tr.row-selected:hover > td {
                background-color: #93c5fd !important;
            }
        `;
        document.head.appendChild(style);
        
        // Add click to select row functionality
        document.addEventListener('click', function(e) {
            const row = e.target.closest('table tbody tr');
            if (!row) return;
            
            // Don't select if clicking on buttons/links/checkboxes
            if (e.target.closest('a, button, input, .form-check')) return;
            
            // Check if this is a data row (not empty state)
            const tds = row.querySelectorAll('td');
            if (tds.length === 1 && tds[0].hasAttribute('colspan')) return;
            
            selectRow(row);
        });

        const dynamicText = IS_DYNAMIC ? 'Database-driven' : 'Static';
        console.log(`📋 Index shortcuts initialized (${dynamicText}). Insert=Add, Delete=Remove. Arrow keys=Navigate rows.`);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose for debugging
    window.IndexShortcuts = {
        shortcuts: SHORTCUTS,
        isDynamic: IS_DYNAMIC
    };

})();
