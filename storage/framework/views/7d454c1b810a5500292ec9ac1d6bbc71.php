


<style>
/* Exit Confirm Modal Backdrop */
.exit-confirm-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10050;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.exit-confirm-modal-backdrop.show {
    display: block;
    opacity: 1;
}

/* Exit Confirm Modal */
.exit-confirm-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 420px;
    max-width: 90%;
    z-index: 10051;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
.exit-confirm-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.exit-confirm-modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.exit-confirm-modal-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exit-confirm-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.exit-confirm-modal-body {
    padding: 1.5rem;
    text-align: center;
}

.exit-confirm-modal-body p {
    margin-bottom: 0.5rem;
    font-size: 1rem;
    color: #333;
}

.exit-confirm-modal-body .text-muted {
    font-size: 0.85rem;
}

.exit-confirm-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: center;
    gap: 12px;
}

.exit-confirm-modal-footer .btn {
    min-width: 140px;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.btn-close-exit-modal {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.btn-close-exit-modal:hover {
    opacity: 1;
}

/* Animation for warning icon */
.exit-confirm-modal .warning-icon {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-3px); }
    40%, 80% { transform: translateX(3px); }
}
</style>

<!-- Exit Confirmation Modal -->
<div id="exitConfirmModalBackdrop" class="exit-confirm-modal-backdrop"></div>
<div id="exitConfirmModal" class="exit-confirm-modal">
    <div class="exit-confirm-modal-content">
        <div class="exit-confirm-modal-header">
            <h5 class="exit-confirm-modal-title">
                <i class="bi bi-exclamation-triangle-fill warning-icon me-2"></i>
                Unsaved Changes
            </h5>
            <button type="button" class="btn-close-exit-modal" onclick="closeExitConfirmModal()">&times;</button>
        </div>
        <div class="exit-confirm-modal-body">
            <p><strong>You have unsaved changes!</strong></p>
            <p>Are you sure you want to leave without saving?</p>
            <p class="text-muted small mt-2">
                <i class="bi bi-info-circle me-1"></i>
                Your changes will be lost if you navigate away.
            </p>
        </div>
        <div class="exit-confirm-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeExitConfirmModal()">
                <i class="bi bi-arrow-left me-1"></i> Stay on Page
            </button>
            <button type="button" class="btn btn-danger" onclick="confirmExit()">
                <i class="bi bi-x-circle me-1"></i> Leave Anyway
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    // Form dirty state tracking
    let formDirty = false;
    let isSaving = false;
    let pendingExitCallback = null;
    let exitModalOpen = false;
    
    // Check if this is a transaction or modification page
    // Match URLs containing 'transaction' or 'modification' anywhere in the path
    const currentUrl = window.location.href.toLowerCase();
    const isTransactionPage = currentUrl.includes('transaction') || 
                               currentUrl.includes('modification');
    
    if (!isTransactionPage) {
        return; // Don't initialize on non-transaction pages
    }
    
    /**
     * Check if any modal (other than exit confirm modal) is open
     */
    function isAnyModalOpen() {
        const selectors = [
            '.modal.show',
            '[class*="-modal"].show'
        ];
        const openModals = document.querySelectorAll(selectors.join(', '));
        
        for (let modal of openModals) {
            // Exclude our own exit confirmation modal
            if (modal.id === 'exitConfirmModal' || modal.id === 'exitConfirmModalBackdrop') {
                continue;
            }
            // Skip if it's a Bootstrap modal backdrop
            if (modal.classList.contains('modal-backdrop')) {
                continue;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Mark form as dirty (has unsaved changes)
     */
    window.markFormDirty = function() {
        formDirty = true;
    };
    
    /**
     * Mark as saving (to prevent exit confirmation during save)
     */
    window.markAsSaving = function() {
        isSaving = true;
    };
    
    /**
     * Reset form dirty state (call after successful save)
     */
    window.resetFormDirty = function() {
        formDirty = false;
        isSaving = false;
    };
    
    /**
     * Check if form has unsaved changes
     */
    window.hasUnsavedChanges = function() {
        return formDirty && !isSaving;
    };
    
    /**
     * Show exit confirmation modal
     */
    window.showExitConfirmModal = function(callback) {
        // Don't show if already showing
        if (exitModalOpen) {
            return false;
        }
        
        // Don't show if another modal is open (like batch selection, item modal etc)
        if (isAnyModalOpen()) {
            return false;
        }
        
        exitModalOpen = true;
        pendingExitCallback = callback;
        
        const backdrop = document.getElementById('exitConfirmModalBackdrop');
        const modal = document.getElementById('exitConfirmModal');
        
        if (backdrop) backdrop.classList.add('show');
        if (modal) modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Focus on "Stay on Page" button
        setTimeout(function() {
            const stayBtn = document.querySelector('#exitConfirmModal .btn-secondary');
            if (stayBtn) stayBtn.focus();
        }, 100);
        
        return true;
    };
    
    /**
     * Close exit confirmation modal (stay on page)
     */
    window.closeExitConfirmModal = function() {
        exitModalOpen = false;
        
        const backdrop = document.getElementById('exitConfirmModalBackdrop');
        const modal = document.getElementById('exitConfirmModal');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
        pendingExitCallback = null;
        
        // Re-push state to prevent back button from navigating
        // This allows user to stay and the browser history stays intact
    };
    
    /**
     * Confirm exit and execute pending callback
     */
    window.confirmExit = function() {
        exitModalOpen = false;
        formDirty = false; // Reset to prevent beforeunload
        isSaving = false;
        
        const backdrop = document.getElementById('exitConfirmModalBackdrop');
        const modal = document.getElementById('exitConfirmModal');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
        
        if (pendingExitCallback) {
            const callback = pendingExitCallback;
            pendingExitCallback = null;
            callback();
        }
    };
    
    /**
     * Handle navigation attempt with unsaved changes
     */
    window.handleExitAttempt = function(targetUrl) {
        if (window.hasUnsavedChanges()) {
            return showExitConfirmModal(function() {
                if (targetUrl) {
                    window.location.href = targetUrl;
                } else {
                    window.history.back();
                }
            });
        }
        return false; // No interception needed
    };
    
    // Initialize tracking when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Track input changes on the main form
        document.addEventListener('input', function(e) {
            // Skip if it's inside our exit modal
            if (e.target.closest('#exitConfirmModal')) {
                return;
            }
            
            // Skip if it's a search input (Select2)
            if (e.target.classList.contains('select2-search__field')) {
                return;
            }
            
            // Mark as dirty for form inputs
            if (e.target.matches('input, textarea, select')) {
                formDirty = true;
            }
        });
        
        // Track changes in select elements (for Select2 and normal selects)
        document.addEventListener('change', function(e) {
            // Skip if inside exit modal
            if (e.target.closest('#exitConfirmModal')) {
                return;
            }
            
            if (e.target.matches('select, input[type="checkbox"], input[type="radio"]')) {
                formDirty = true;
            }
        });
        
        // Observe items table for row additions/deletions
        const tbody = document.getElementById('itemsTableBody');
        if (tbody) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && 
                        (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)) {
                        formDirty = true;
                    }
                });
            });
            
            observer.observe(tbody, { childList: true, subtree: false });
        }
        
        // Intercept Cancel button clicks
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button, a');
            if (!btn) return;
            
            // Skip if inside exit modal
            if (btn.closest('#exitConfirmModal')) {
                return;
            }
            
            // Check if it's a Cancel button
            const btnText = btn.textContent.trim().toLowerCase();
            const isCancelBtn = btnText.includes('cancel') || 
                               btn.classList.contains('btn-cancel') ||
                               btn.classList.contains('exit-trigger');
            
            if (isCancelBtn && window.hasUnsavedChanges()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Extract target URL from onclick or href
                let targetUrl = null;
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                    if (match) targetUrl = match[1];
                }
                if (!targetUrl && btn.tagName === 'A') {
                    targetUrl = btn.getAttribute('href');
                }
                
                window.handleExitAttempt(targetUrl);
            }
        }, true);
        
        // Also intercept clicks on backdrop to close our modal
        const backdrop = document.getElementById('exitConfirmModalBackdrop');
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                closeExitConfirmModal();
            });
        }
        
        // Handle browser back/forward buttons using popstate
        // Push a state so we can detect when user tries to go back
        if (history.pushState) {
            // Store the referrer (previous page) on page load
            const previousPageUrl = document.referrer || null;
            
            // Push a new state to intercept back button
            history.pushState({ exitConfirmPage: true }, '', window.location.href);
            
            window.addEventListener('popstate', function(e) {
                if (window.hasUnsavedChanges() && !exitModalOpen) {
                    // User pressed back button - show our modal
                    // First, push state back to prevent navigation
                    history.pushState({ exitConfirmPage: true }, '', window.location.href);
                    
                    // Show confirmation modal
                    showExitConfirmModal(function() {
                        // User confirmed exit - go to previous page using replace
                        // This removes current page from history so forward button won't work
                        formDirty = false;
                        if (previousPageUrl && previousPageUrl !== '' && previousPageUrl !== window.location.href) {
                            // Replace current page with previous page - this clears forward history
                            window.location.replace(previousPageUrl);
                        } else {
                            // Fallback: go to dashboard or reload
                            window.location.replace(window.location.origin + '/admin/dashboard');
                        }
                    });
                } else if (!window.hasUnsavedChanges() && !exitModalOpen) {
                    // No unsaved changes - go back and replace to prevent forward access
                    if (previousPageUrl && previousPageUrl !== '' && previousPageUrl !== window.location.href) {
                        window.location.replace(previousPageUrl);
                    } else {
                        history.go(-1);
                    }
                }
            });
        }
    });
    
    // Intercept fetch requests to detect save operations and reset dirty flag on success
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        const method = (options?.method || 'GET').toUpperCase();
        
        // Detect if this is likely a save request (POST to a store/update route)
        const urlStr = typeof url === 'string' ? url : url.toString();
        const isSaveRequest = method === 'POST' && (
            urlStr.includes('/store') ||
            urlStr.includes('/update') ||
            urlStr.includes('/save') ||
            urlStr.includes('.store') ||
            urlStr.includes('.update')
        );
        
        if (isSaveRequest) {
            isSaving = true;
        }
        
        return originalFetch.apply(this, arguments).then(function(response) {
            // Clone the response so we can read it and still return it
            const clonedResponse = response.clone();
            
            if (isSaveRequest && response.ok) {
                // Try to parse JSON response to check for success
                clonedResponse.json().then(function(data) {
                    if (data.success === true || data.status === 'success') {
                        // Reset form dirty state on successful save
                        formDirty = false;
                        isSaving = false;
                    } else {
                        isSaving = false;
                    }
                }).catch(function() {
                    // Not JSON response or parsing failed, reset saving flag
                    isSaving = false;
                });
            } else if (isSaveRequest) {
                isSaving = false;
            }
            
            return response;
        }).catch(function(error) {
            if (isSaveRequest) {
                isSaving = false;
            }
            throw error;
        });
    };
    
    // Handle browser beforeunload (for tab close, refresh only - not back button)
    window.addEventListener('beforeunload', function(e) {
        if (window.hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // ESC key handling - close exit modal if open
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (exitModalOpen) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeExitConfirmModal();
                return false;
            }
        }
    }, true); // Use capture phase for priority
    
})();
</script>
<?php /**PATH C:\xampp\htdocs\bill-software\resources\views/layouts/partials/exit-confirmation.blade.php ENDPATH**/ ?>