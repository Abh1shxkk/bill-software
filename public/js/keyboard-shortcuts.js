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
            'breakage': '#f97316',
            'index': '#0ea5e9'
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
     * Build index shortcuts content from INDEX_SHORTCUTS_CONFIG
     */
    function buildIndexShortcutsContent() {
        const indexConfig = window.INDEX_SHORTCUTS_CONFIG;
        if (!indexConfig) return '';

        let html = '';
        const categoryColors = { 'index': '#0ea5e9' };

        // Check if index shortcuts exist
        const indexCategoryHotkeys = indexConfig.categoryHotkeys || {};
        const indexShortcuts = indexConfig.shortcuts || {};

        if (indexConfig.isDynamic && Object.keys(indexCategoryHotkeys).length > 0) {
            for (const [catKey, hotkeys] of Object.entries(indexCategoryHotkeys)) {
                if (!hotkeys || hotkeys.length === 0) continue;

                const catConfig = indexConfig.categories?.[catKey] || { name: 'Index Page Actions', icon: 'bi-list-ul' };
                const color = categoryColors[catKey] || '#0ea5e9';

                html += `
                    <div class="shortcut-category index-shortcut">
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
        } else if (Object.keys(indexShortcuts).length > 0) {
            // Static fallback
            html += `
                <div class="shortcut-category index-shortcut">
                    <h6 style="color: #0ea5e9"><i class="bi-list-ul me-1"></i>Index Page Actions</h6>
            `;

            for (const [key, data] of Object.entries(indexShortcuts)) {
                html += `
                    <div class="shortcut-item">
                        <span class="keys">${formatKeyForDisplay(key)}</span>
                        <span class="module-name">${data.description}</span>
                    </div>
                `;
            }

            html += '</div>';
        }

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
        const indexContent = buildIndexShortcutsContent();
        const hasIndexShortcuts = indexContent.length > 0;

        panel.innerHTML = `
            <div class="shortcut-help-header">
                <h5><i class="bi bi-keyboard me-2"></i>Keyboard Shortcuts${dynamicSource}</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('shortcut-help-panel').remove(); document.getElementById('shortcut-help-backdrop')?.remove(); window._shortcutHelpVisible = false;"></button>
            </div>
            <div class="shortcut-tabs">
                <button class="shortcut-tab active" data-tab="global" onclick="window._switchShortcutTab('global')">
                    <i class="bi bi-globe me-1"></i>Global Navigation
                </button>
                <button class="shortcut-tab" data-tab="index" onclick="window._switchShortcutTab('index')">
                    <i class="bi bi-list-ul me-1"></i>Index Page Actions
                </button>
            </div>
            <div id="shortcut-tab-global" class="shortcut-help-body shortcut-tab-content active">
                ${buildDynamicHelpContent()}
            </div>
            <div id="shortcut-tab-index" class="shortcut-help-body shortcut-tab-content" style="display: none;">
                ${hasIndexShortcuts ? indexContent : '<div class="text-center py-4 text-muted"><i class="bi bi-info-circle me-2"></i>No index shortcuts configured. Add them from the database.</div>'}
            </div>
        `;

        document.body.appendChild(panel);
        helpPanelVisible = true;
        window._shortcutHelpVisible = true;

        // Tab switching function
        window._switchShortcutTab = function (tabName) {
            // Update tab buttons
            document.querySelectorAll('.shortcut-tab').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.tab === tabName);
            });
            // Update tab contents
            document.querySelectorAll('.shortcut-tab-content').forEach(content => {
                const isActive = content.id === 'shortcut-tab-' + tabName;
                content.style.display = isActive ? 'grid' : 'none';
                content.classList.toggle('active', isActive);
            });
        };
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
                left: 260px;
                right: 10px;
                transform: translateY(-50%);
                background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
                color: #1e293b;
                border-radius: 16px;
                box-shadow: 
                    0 25px 80px rgba(0, 0, 0, 0.35),
                    0 10px 30px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
                z-index: 100000;
                width: calc(100vw - 280px);
                max-width: none;
                max-height: 85vh;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.5);
                margin-left: 5px;
            }

            .shortcut-help-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 24px;
                background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
                border-bottom: none;
            }

            .shortcut-help-header h5 {
                margin: 0;
                font-weight: 700;
                font-size: 1.2rem;
                color: #ffffff;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .shortcut-help-header .btn-close {
                filter: invert(1);
                opacity: 0.8;
            }

            .shortcut-help-header .btn-close:hover {
                opacity: 1;
            }

            .shortcut-tabs {
                display: flex;
                background: linear-gradient(180deg, #e2e8f0 0%, #cbd5e1 100%);
                padding: 0;
                border-bottom: 2px solid #94a3b8;
                box-shadow: inset 0 -2px 4px rgba(0,0,0,0.05);
            }

            .shortcut-tab {
                flex: 1;
                background: transparent;
                border: none;
                padding: 14px 20px;
                font-size: 0.95rem;
                font-weight: 600;
                color: #64748b;
                cursor: pointer;
                border-bottom: 3px solid transparent;
                margin-bottom: -2px;
            }

            .shortcut-tab:hover {
                color: #475569;
                background: rgba(241, 245, 249, 0.8);
            }

            .shortcut-tab.active {
                color: #4f46e5;
                background: linear-gradient(180deg, #f1f5f9 0%, #e0e7ff 100%);
                border-bottom-color: #4f46e5;
            }

            .shortcut-tab i {
                opacity: 0.7;
            }

            .shortcut-tab.active i {
                opacity: 1;
            }

            .shortcut-help-body {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                padding: 16px;
                max-height: calc(85vh - 130px);
                overflow-y: auto;
                overflow-x: hidden;
                background: linear-gradient(145deg, #f1f5f9 0%, #e2e8f0 100%);
            }

            .shortcut-category {
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                border-radius: 10px;
                padding: 12px 14px;
                border: 1px solid #e2e8f0;
                box-shadow: 
                    0 4px 12px rgba(0,0,0,0.06),
                    0 1px 3px rgba(0,0,0,0.04);
            }

            .shortcut-category h6 {
                margin: 0 0 8px 0;
                font-size: 0.75rem;
                font-weight: 700;
                padding-bottom: 6px;
                border-bottom: 2px solid #e2e8f0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .shortcut-item {
                font-size: 0.72rem;
                padding: 4px 0;
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
                gap: 2px;
                flex-shrink: 0;
            }
            
            .shortcut-item .module-name {
                margin-left: 8px;
                color: #1e293b;
                font-weight: 600;
                font-size: 0.7rem;
            }

            .shortcut-item kbd {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                color: white;
                border: none;
                border-radius: 3px;
                padding: 2px 5px;
                font-size: 0.58rem;
                font-family: 'Consolas', 'Monaco', 'SF Mono', monospace;
                font-weight: 600;
                box-shadow: 0 1px 2px rgba(79, 70, 229, 0.3);
            }

            /* Scrollbar styling */
            .shortcut-help-body::-webkit-scrollbar {
                width: 10px;
            }

            .shortcut-help-body::-webkit-scrollbar-track {
                background: #e2e8f0;
                border-radius: 5px;
            }

            .shortcut-help-body::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, #94a3b8, #64748b);
                border-radius: 5px;
                border: 2px solid #e2e8f0;
            }

            .shortcut-help-body::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, #64748b, #475569);
            }

            /* Modal backdrop with blur - content area only */
            #shortcut-help-backdrop {
                position: fixed;
                top: 50px;
                left: 260px;
                width: calc(100vw - 260px);
                height: calc(100vh - 50px);
                background: rgba(0, 0, 0, 0.35);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 99999;
            }

            /* Mobile responsive */
            @media (max-width: 1400px) {
                .shortcut-help-body {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            @media (max-width: 1200px) {
                .shortcut-help-body {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 992px) {
                .shortcut-help-body {
                    grid-template-columns: repeat(2, 1fr);
                }
                
                #shortcut-help-panel {
                    width: calc(100vw - 40px);
                    left: 20px;
                    right: 20px;
                    margin-left: 0;
                    transform: translateY(-50%);
                }
            }

            @media (max-width: 768px) {
                #shortcut-help-panel {
                    width: calc(100vw - 20px);
                    max-height: 90vh;
                    left: 10px;
                    right: 10px;
                }

                .shortcut-help-body {
                    grid-template-columns: repeat(2, 1fr);
                    padding: 12px;
                    gap: 8px;
                }

                #shortcut-help-backdrop {
                    left: 0;
                    top: 0;
                    width: 100vw;
                    height: 100vh;
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
        refresh: function () {
            // Reload page to get fresh shortcuts from database
            window.location.reload();
        }
    };

    // Expose createHelpPanel globally for header button
    window.createHelpPanel = createHelpPanel;

})();
