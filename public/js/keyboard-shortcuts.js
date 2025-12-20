/**
 * EasySol-style Global Keyboard Shortcuts
 * Replicates the hotkey functionality from EasySol software
 * 
 * All navigation shortcuts work GLOBALLY from any page.
 * ESC key works globally to go back from any screen.
 * 
 * FULLY DYNAMIC: Reads shortcuts from database via window.KEYBOARD_SHORTCUTS_CONFIG
 */

(function () {
    'use strict';

    // Get configuration from Laravel (injected by Blade partial)
    const CONFIG = window.KEYBOARD_SHORTCUTS_CONFIG || null;

    if (!CONFIG) {
        console.warn('⌨️ Keyboard shortcuts config not found. Shortcuts disabled.');
        return;
    }

    const SHORTCUTS = CONFIG.shortcuts;
    const DASHBOARD_URL = CONFIG.dashboardUrl;
    const CATEGORIES = CONFIG.categories || {};
    const CATEGORY_HOTKEYS = CONFIG.categoryHotkeys || {};
    const IS_DYNAMIC = CONFIG.isDynamic || false;

    /**
     * Check if current page is the Dashboard
     */
    function isDashboardPage() {
        const currentPath = window.location.pathname.toLowerCase();
        const dashboardPath = new URL(DASHBOARD_URL, window.location.origin).pathname.toLowerCase();

        return currentPath === dashboardPath ||
            currentPath === dashboardPath + '/' ||
            currentPath.endsWith('/admin/dashboard');
    }

    // Floating help panel state
    let helpPanelVisible = false;

    /**
     * Format key combination for display (e.g., "ctrl+f9" -> "Ctrl + F9")
     */
    function formatKeyForDisplay(key) {
        return key.split('+').map(part => {
            // Capitalize first letter of each part
            if (part === 'ctrl') return 'Ctrl';
            if (part === 'shift') return 'Shift';
            if (part === 'alt') return 'Alt';
            if (part === 'insert') return 'Ins';
            if (part === 'delete') return 'Del';
            if (part === 'backspace') return 'Back';
            return part.toUpperCase();
        }).map(k => `<kbd>${k}</kbd>`).join('+');
    }

    /**
     * Build help panel content dynamically from database
     */
    function buildDynamicHelpContent() {
        let html = '';
        
        // Custom colors for categories
        const categoryColors = {
            'masters': '#eab308',
            'transactions': '#22c55e',
            'receipts': '#ef4444',
            'notes': '#a855f7',
            'stock': '#06b6d4',
            'ledgers': '#ec4899',
            'managers': '#14b8a6',
            'utilities': '#6b7280',
            'breakage': '#f97316'
        };

        // If using database hotkeys, build from categoryHotkeys
        if (IS_DYNAMIC && Object.keys(CATEGORY_HOTKEYS).length > 0) {
            for (const [catKey, hotkeys] of Object.entries(CATEGORY_HOTKEYS)) {
                if (!hotkeys || hotkeys.length === 0) continue;
                
                const catConfig = CATEGORIES[catKey] || { name: catKey, icon: 'bi-folder', color: 'text-secondary' };
                const color = categoryColors[catKey] || '#6b7280';
                
                html += `
                    <div class="shortcut-category">
                        <h6 style="color: ${color}"><i class="${catConfig.icon} me-1"></i>${catConfig.name}</h6>
                `;
                
                for (const hotkey of hotkeys) {
                    html += `
                        <div class="shortcut-item">
                            <span class="keys">${formatKeyForDisplay(hotkey.key)}</span>
                            <span class="module-name">${hotkey.name}</span>
                        </div>
                    `;
                }
                
                html += '</div>';
            }
        } else {
            // Build from SHORTCUTS (static fallback)
            const grouped = {};
            for (const [key, data] of Object.entries(SHORTCUTS)) {
                const cat = data.category || 'utilities';
                if (!grouped[cat]) grouped[cat] = [];
                grouped[cat].push({ key, name: data.description });
            }
            
            for (const [catKey, hotkeys] of Object.entries(grouped)) {
                const catConfig = CATEGORIES[catKey] || { name: catKey, icon: 'bi-folder', color: 'text-secondary' };
                const color = categoryColors[catKey] || '#6b7280';
                
                html += `
                    <div class="shortcut-category">
                        <h6 style="color: ${color}"><i class="${catConfig.icon} me-1"></i>${catConfig.name}</h6>
                `;
                
                for (const hotkey of hotkeys) {
                    html += `
                        <div class="shortcut-item">
                            <span class="keys">${formatKeyForDisplay(hotkey.key)}</span>
                            <span class="module-name">${hotkey.name}</span>
                        </div>
                    `;
                }
                
                html += '</div>';
            }
        }

        // Add system shortcuts that are always available
        html += `
            <div class="shortcut-category">
                <h6 style="color: #64748b"><i class="bi-info-circle me-1"></i>System</h6>
                <div class="shortcut-item">
                    <span class="keys"><kbd>ESC</kbd></span>
                    <span class="module-name">Go Back</span>
                </div>
                <div class="shortcut-item">
                    <span class="keys"><kbd>F1</kbd></span>
                    <span class="module-name">Toggle Help</span>
                </div>
                <div class="shortcut-item">
                    <span class="keys"><kbd>End</kbd></span>
                    <span class="module-name">Save Form</span>
                </div>
                <div class="shortcut-item">
                    <span class="keys"><kbd>Enter</kbd></span>
                    <span class="module-name">Next Field</span>
                </div>
            </div>
        `;

        return html;
    }

    /**
     * Create and show the floating shortcut help panel
     */
    function createHelpPanel() {
        // Remove existing panel and backdrop if any
        const existingPanel = document.getElementById('shortcut-help-panel');
        const existingBackdrop = document.getElementById('shortcut-help-backdrop');
        if (existingPanel) {
            existingPanel.remove();
            if (existingBackdrop) existingBackdrop.remove();
            helpPanelVisible = false;
            return;
        }

        // Create backdrop for blur effect
        const backdrop = document.createElement('div');
        backdrop.id = 'shortcut-help-backdrop';
        document.body.appendChild(backdrop);

        const panel = document.createElement('div');
        panel.id = 'shortcut-help-panel';
        
        const dynamicSource = IS_DYNAMIC ? ' (Database)' : ' (Static)';
        
        panel.innerHTML = `
            <div class="shortcut-help-header">
                <h5><i class="bi bi-keyboard me-2"></i>Keyboard Shortcuts${dynamicSource}</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('shortcut-help-panel').remove(); document.getElementById('shortcut-help-backdrop')?.remove(); window._shortcutHelpVisible = false;"></button>
            </div>
            <div class="shortcut-help-body">
                ${buildDynamicHelpContent()}
            </div>
        `;

        document.body.appendChild(panel);
        helpPanelVisible = true;
        window._shortcutHelpVisible = true;
    }

    /**
     * Build the shortcut key string from the event
     */
    function getShortcutKey(event) {
        const parts = [];

        // Order matters for consistency
        if (event.ctrlKey || event.metaKey) parts.push('ctrl');
        if (event.shiftKey) parts.push('shift');
        if (event.altKey) parts.push('alt');

        // Get the key name
        let key = event.key.toLowerCase();

        // Handle special keys
        if (key === 'insert') key = 'insert';
        else if (key === 'delete') key = 'delete';
        else if (key === 'backspace') key = 'backspace';
        else if (key.startsWith('f') && key.length <= 3) key = key; // F1-F12

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
     * Show a toast notification for the navigation
     */
    function showNavigationToast(description) {
        // Check if toast container exists
        let toastContainer = document.getElementById('shortcut-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'shortcut-toast-container';
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '11000';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'shortcut-toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-keyboard me-2"></i>Navigating to <strong>${description}</strong>...
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastElement, { delay: 1500 });
        bsToast.show();

        // Clean up after toast hides
        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    /**
     * Main keyboard event handler
     */
    function handleKeyDown(event) {
        // ESC key - Go back (works globally on all pages)
        if (event.key === 'Escape' && !event.ctrlKey && !event.shiftKey && !event.altKey) {
            // Don't go back if a modal is open - let the modal handle ESC
            const openModals = document.querySelectorAll('.modal.show');
            if (openModals.length > 0) {
                return; // Let Bootstrap handle modal close
            }

            // Don't go back if help panel is open - close it instead
            const helpPanel = document.getElementById('shortcut-help-panel');
            if (helpPanel) {
                helpPanel.remove();
                const backdrop = document.getElementById('shortcut-help-backdrop');
                if (backdrop) backdrop.remove();
                window._shortcutHelpVisible = false;
                return;
            }

            // Don't go back if we're already on dashboard
            if (isDashboardPage()) {
                return;
            }

            event.preventDefault();
            window.history.back();
            return;
        }

        // F1 for help panel (works on ALL pages)
        if (event.key === 'F1' && !event.ctrlKey && !event.shiftKey && !event.altKey) {
            event.preventDefault();
            createHelpPanel();
            return;
        }

        // Ctrl+Shift+K for Calculator - check if active in shortcuts
        if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'k') {
            const calcShortcut = SHORTCUTS['ctrl+shift+k'];
            if (calcShortcut && calcShortcut.action === 'calculator') {
                event.preventDefault();
                if (typeof openHeaderCalculator === 'function') {
                    openHeaderCalculator();
                }
                return;
            }
        }

        // All navigation shortcuts now work GLOBALLY from any page

        // Skip if typing in an input field (except for F-keys and special combinations)
        if (isInputFocused()) {
            const key = event.key.toLowerCase();
            // Allow F-keys and Ctrl combinations even when input is focused
            if (!key.startsWith('f') && !event.ctrlKey && !event.altKey) {
                return;
            }
        }

        // Build the shortcut key
        const shortcutKey = getShortcutKey(event);

        // Check if this shortcut exists and is active
        const shortcut = SHORTCUTS[shortcutKey];

        if (shortcut) {
            event.preventDefault();
            event.stopPropagation();

            // Handle special actions
            if (shortcut.action === 'calculator') {
                // Use global header calculator function
                if (typeof openHeaderCalculator === 'function') {
                    showNavigationToast('Opening Calculator');
                    openHeaderCalculator();
                } else {
                    showNavigationToast('Calculator not available');
                }
                return;
            }

            // Show navigation toast
            showNavigationToast(shortcut.description);

            // Navigate after a brief delay to show the toast
            setTimeout(function () {
                window.location.href = shortcut.url;
            }, 300);
        }
    }

    /**
     * Initialize the keyboard shortcuts
     */
    function init() {
        // Add keyboard event listener
        document.addEventListener('keydown', handleKeyDown, true);

        // Add styles for the help panel and toast
        const style = document.createElement('style');
        style.textContent = `
            #shortcut-help-panel {
                position: fixed;
                top: 50%;
                left: calc(50% + 120px);
                transform: translate(-50%, -50%);
                background: #f1f5f9;
                color: #1e293b;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                z-index: 100000;
                width: 85%;
                max-width: 1000px;
                max-height: 75vh;
                overflow: hidden;
                animation: slideIn 0.2s ease-out;
                border: 1px solid #cbd5e1;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -50%) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translate(-50%, -50%) scale(1);
                }
            }

            .shortcut-help-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 14px 20px;
                background: linear-gradient(135deg, #475569 0%, #334155 100%);
                border-bottom: none;
            }

            .shortcut-help-header h5 {
                margin: 0;
                font-weight: 700;
                font-size: 1.1rem;
                color: #ffffff;
            }

            .shortcut-help-header .btn-close {
                filter: invert(1);
                opacity: 0.7;
            }

            .shortcut-help-header .btn-close:hover {
                opacity: 1;
            }

            .shortcut-help-body {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 10px;
                padding: 15px;
                max-height: calc(75vh - 55px);
                overflow-y: auto;
                background: #f1f5f9;
            }

            .shortcut-category {
                background: #ffffff;
                border-radius: 8px;
                padding: 10px 12px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .shortcut-category h6 {
                margin: 0 0 8px 0;
                font-size: 0.75rem;
                font-weight: 700;
                padding-bottom: 6px;
                border-bottom: 2px solid #e2e8f0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .shortcut-item {
                font-size: 0.72rem;
                padding: 3px 0;
                display: flex;
                align-items: center;
                color: #374151;
                font-weight: 500;
                border-bottom: 1px dotted #e5e7eb;
            }
            
            .shortcut-item:last-child {
                border-bottom: none;
            }

            .shortcut-item .keys {
                display: inline-flex;
                align-items: center;
                gap: 1px;
                min-width: 85px;
                flex-shrink: 0;
            }
            
            .shortcut-item .module-name {
                margin-left: 6px;
                color: #1e293b;
                font-weight: 600;
                font-size: 0.7rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .shortcut-item kbd {
                background: #4f46e5;
                color: white;
                border: none;
                border-radius: 3px;
                padding: 1px 4px;
                font-size: 0.6rem;
                font-family: 'Consolas', 'Monaco', monospace;
                font-weight: 600;
            }

            /* Scrollbar styling */
            .shortcut-help-body::-webkit-scrollbar {
                width: 8px;
            }

            .shortcut-help-body::-webkit-scrollbar-track {
                background: #e2e8f0;
                border-radius: 4px;
            }

            .shortcut-help-body::-webkit-scrollbar-thumb {
                background: #94a3b8;
                border-radius: 4px;
            }

            .shortcut-help-body::-webkit-scrollbar-thumb:hover {
                background: #64748b;
            }

            /* Modal backdrop with blur - content area only */
            #shortcut-help-backdrop {
                position: fixed;
                top: 50px;
                left: 260px;
                width: calc(100vw - 260px);
                height: calc(100vh - 50px);
                background: rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
                z-index: 99999;
            }

            /* Mobile responsive */
            @media (max-width: 1200px) {
                .shortcut-help-body {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            @media (max-width: 992px) {
                .shortcut-help-body {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 768px) {
                #shortcut-help-panel {
                    width: 95%;
                    max-height: 85vh;
                    left: 50%;
                }

                .shortcut-help-body {
                    grid-template-columns: repeat(2, 1fr);
                }

                #shortcut-help-backdrop {
                    left: 0;
                    width: 100vw;
                }
            }

            @media (max-width: 480px) {
                .shortcut-help-body {
                    grid-template-columns: 1fr;
                }
            }
        `;
        document.head.appendChild(style);

        const dynamicText = IS_DYNAMIC ? 'Database-driven' : 'Static';
        console.log(`⌨️ Keyboard shortcuts initialized (${dynamicText}). ESC = Go Back. F1 = Help.`);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose the shortcuts to window for debugging
    window.KeyboardShortcuts = {
        shortcuts: SHORTCUTS,
        showHelp: createHelpPanel,
        isDashboard: isDashboardPage,
        isDynamic: IS_DYNAMIC,
        refresh: function() {
            // Reload page to get fresh shortcuts from database
            window.location.reload();
        }
    };

    // Expose createHelpPanel globally for header button
    window.createHelpPanel = createHelpPanel;

})();
