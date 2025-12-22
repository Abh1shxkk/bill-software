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
     * Handle index-specific actions
     */
    function handleAction(action, shortcut) {
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
                // Look for selected row's delete button or global delete
                const selectedRow = document.querySelector('tr.selected, tr.table-active, tr[data-selected="true"]');
                if (selectedRow) {
                    const deleteBtn = selectedRow.querySelector('[data-action="delete"], .btn-delete, button[onclick*="delete"]');
                    if (deleteBtn) {
                        showToast('Deleting selected...', 'danger');
                        deleteBtn.click();
                    } else {
                        showToast('Select a row first', 'warning');
                    }
                } else {
                    showToast('No row selected', 'warning');
                }
                break;

            case 'edit':
                // Look for selected row's edit button
                const editRow = document.querySelector('tr.selected, tr.table-active, tr[data-selected="true"]');
                if (editRow) {
                    const editBtn = editRow.querySelector('[data-action="edit"], .btn-edit, a[href*="edit"]');
                    if (editBtn) {
                        showToast('Editing selected...', 'info');
                        editBtn.click();
                    } else {
                        showToast('Edit button not found', 'warning');
                    }
                } else {
                    showToast('No row selected', 'warning');
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
        // Skip if global help panel is handling
        if (event.key === 'F1' || event.key === 'Escape') {
            return;
        }

        // Skip if input is focused (except for specific shortcuts)
        if (isInputFocused()) {
            const key = event.key.toLowerCase();
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

        const dynamicText = IS_DYNAMIC ? 'Database-driven' : 'Static';
        console.log(`📋 Index shortcuts initialized (${dynamicText}). Insert=Add, Delete=Remove.`);
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
