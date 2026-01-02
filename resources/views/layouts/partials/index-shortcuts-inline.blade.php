{{-- Index Page Keyboard Shortcuts - Inline Script --}}
<script>
/**
 * Index Page Keyboard Shortcuts
 * Handles page-specific shortcuts that only work on index/list pages
 */

(function () {
    'use strict';

    const CONFIG = window.INDEX_SHORTCUTS_CONFIG || null;

    if (!CONFIG) {
        console.log('📋 Index shortcuts config not found. Index shortcuts disabled.');
        return;
    }

    const SHORTCUTS = CONFIG.shortcuts || {};
    const IS_DYNAMIC = CONFIG.isDynamic || false;

    function getShortcutKey(event) {
        const parts = [];
        if (event.ctrlKey || event.metaKey) parts.push('ctrl');
        if (event.shiftKey) parts.push('shift');
        if (event.altKey) parts.push('alt');

        let key = event.key.toLowerCase();
        if (key === 'insert') key = 'insert';
        else if (key === 'delete') key = 'delete';
        else if (key === 'backspace') key = 'backspace';
        else if (key.startsWith('f') && key.length <= 3) key = key;

        parts.push(key);
        return parts.join('+');
    }

    function isInputFocused() {
        const activeElement = document.activeElement;
        if (!activeElement) return false;
        const tagName = activeElement.tagName.toLowerCase();
        return tagName === 'input' || tagName === 'textarea' || tagName === 'select' || activeElement.isContentEditable;
    }

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
            <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-keyboard me-2"></i>${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastElement, { delay: 1500 });
        bsToast.show();
        toastElement.addEventListener('hidden.bs.toast', function () { toastElement.remove(); });
    }

    function getAllDataRows() {
        const tbody = document.querySelector('[id$="-table-body"], .module-table-body, table tbody');
        if (!tbody) return [];
        return Array.from(tbody.querySelectorAll('tr')).filter(tr => {
            const tds = tr.querySelectorAll('td');
            if (tds.length === 1 && tds[0].hasAttribute('colspan')) return false;
            return tds.length > 0;
        });
    }

    function getSelectedRow() {
        return document.querySelector('tr.row-selected, tr.selected, tr.table-active, tr[data-selected="true"]');
    }

    function selectRow(row) {
        if (!row) return;
        const tbody = row.closest('tbody');
        if (!tbody) return;
        tbody.querySelectorAll('tr.row-selected').forEach(r => r.classList.remove('row-selected'));
        row.classList.add('row-selected');
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function handleArrowNavigation(event) {
        if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') return false;
        if (isInputFocused()) return false;

        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        if (moduleTableBody && moduleTableBody.id !== 'module-table-body') return false;

        event.preventDefault();
        event.stopPropagation();

        const rows = getAllDataRows();
        if (rows.length === 0) {
            showToast('No rows available', 'warning');
            return true;
        }

        const currentRow = getSelectedRow();
        let currentIndex = currentRow ? rows.findIndex(r => r === currentRow) : -1;

        if (currentIndex === -1) {
            selectRow(event.key === 'ArrowDown' ? rows[0] : rows[rows.length - 1]);
            return true;
        }

        if (event.key === 'ArrowUp' && currentIndex > 0) {
            selectRow(rows[currentIndex - 1]);
        } else if (event.key === 'ArrowDown' && currentIndex < rows.length - 1) {
            selectRow(rows[currentIndex + 1]);
        }
        return true;
    }

    function handleAction(action, shortcut) {
        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        const hasModuleShortcuts = moduleTableBody && moduleTableBody.id !== 'module-table-body';

        switch (action) {
            case 'add':
                const addBtn = document.querySelector('[data-action="add"], .btn-add-new, a[href*="create"]');
                if (addBtn) { showToast('Adding new record...', 'success'); addBtn.click(); }
                else { showToast('Add button not found', 'warning'); }
                break;
            case 'delete':
                if (hasModuleShortcuts) return;
                const selectedRow = getSelectedRow();
                if (selectedRow) {
                    const deleteBtn = selectedRow.querySelector('[data-action="delete"], .btn-delete, button.ajax-delete');
                    if (deleteBtn) { showToast('Deleting selected...', 'danger'); deleteBtn.click(); }
                    else { showToast('Delete button not found', 'warning'); }
                } else { showToast('No row selected', 'warning'); }
                break;
            case 'edit':
                if (hasModuleShortcuts) return;
                const editRow = getSelectedRow();
                if (editRow) {
                    const editBtn = editRow.querySelector('[data-action="edit"], .btn-edit, a[href*="edit"]');
                    if (editBtn) {
                        showToast('Editing selected...', 'info');
                        if (editBtn.href) window.location.href = editBtn.href;
                        else editBtn.click();
                    } else { showToast('Edit button not found', 'warning'); }
                } else { showToast('No row selected', 'warning'); }
                break;
            case 'print':
                const printBtn = document.querySelector('[data-action="print"], .btn-print');
                if (printBtn) printBtn.click();
                else window.print();
                showToast('Printing...', 'info');
                break;
            case 'search':
                const searchInput = document.querySelector('input[type="search"], input[name="search"], #searchInput');
                if (searchInput) { searchInput.focus(); searchInput.select(); showToast('Search focused', 'info'); }
                else { showToast('Search input not found', 'warning'); }
                break;
            case 'refresh':
                const refreshBtn = document.querySelector('[data-action="refresh"], .btn-refresh');
                if (refreshBtn) { refreshBtn.click(); showToast('Refreshing...', 'info'); }
                else { window.location.reload(); }
                break;
            default:
                if (shortcut.url) {
                    showToast('Navigating to ' + shortcut.description, 'info');
                    setTimeout(() => { window.location.href = shortcut.url; }, 300);
                }
                break;
        }
    }

    function handleKeyDown(event) {
        const moduleTableBody = document.querySelector('[id$="-table-body"]');
        const hasModuleShortcuts = moduleTableBody && moduleTableBody.id !== 'module-table-body';

        if ((event.key === 'ArrowUp' || event.key === 'ArrowDown') && hasModuleShortcuts) return;
        if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
            if (handleArrowNavigation(event)) return;
        }
        if (event.key === 'F1' || event.key === 'Escape') return;
        if (isInputFocused() && !event.ctrlKey && !event.altKey) return;

        const shortcutKey = getShortcutKey(event);
        const shortcut = SHORTCUTS[shortcutKey];

        if (shortcut) {
            event.preventDefault();
            event.stopPropagation();
            if (shortcut.action) handleAction(shortcut.action, shortcut);
            else if (shortcut.url) {
                showToast('Navigating to ' + shortcut.description, 'info');
                setTimeout(() => { window.location.href = shortcut.url; }, 300);
            }
        }
    }

    function init() {
        document.addEventListener('keydown', handleKeyDown, true);

        const style = document.createElement('style');
        style.textContent = `
            table tbody tr { cursor: pointer; transition: background-color 0.15s ease; }
            table tbody tr:hover { background-color: #f0f9ff !important; }
            table tbody tr:hover > td { background-color: #f0f9ff !important; }
            table tbody tr.row-selected { background-color: #bfdbfe !important; border-left: 3px solid #3b82f6 !important; }
            table tbody tr.row-selected > td { background-color: #bfdbfe !important; }
            table tbody tr.row-selected:hover { background-color: #93c5fd !important; }
            table tbody tr.row-selected:hover > td { background-color: #93c5fd !important; }
        `;
        document.head.appendChild(style);

        document.addEventListener('click', function(e) {
            const row = e.target.closest('table tbody tr');
            if (!row) return;
            if (e.target.closest('a, button, input, .form-check')) return;
            const tds = row.querySelectorAll('td');
            if (tds.length === 1 && tds[0].hasAttribute('colspan')) return;
            selectRow(row);
        });

        console.log(`📋 Index shortcuts initialized (${IS_DYNAMIC ? 'Database-driven' : 'Static'}).`);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.IndexShortcuts = { shortcuts: SHORTCUTS, isDynamic: IS_DYNAMIC };
})();
</script>
