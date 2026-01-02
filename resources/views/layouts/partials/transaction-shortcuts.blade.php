{{-- Transaction Shortcuts Partial --}}
{{-- End key to save transaction/modification --}}
{{-- Enter key to move to next field (like traditional billing software) --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // ENTER KEY TO MOVE TO NEXT FIELD
    // ============================================
    
    // Get all focusable elements in form order
    function getFocusableElements() {
        const selectors = [
            'input:not([type="hidden"]):not([readonly]):not([disabled])',
            'select:not([disabled])',
            'textarea:not([readonly]):not([disabled])',
            'button:not([disabled])'
        ].join(', ');
        
        const elements = Array.from(document.querySelectorAll(selectors));
        
        // Filter visible elements and sort by tabindex/DOM order
        return elements.filter(el => {
            // Check if element is visible
            const style = window.getComputedStyle(el);
            if (style.display === 'none' || style.visibility === 'hidden') return false;
            
            // Check if element or parent is hidden
            let parent = el.parentElement;
            while (parent) {
                const parentStyle = window.getComputedStyle(parent);
                if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') return false;
                parent = parent.parentElement;
            }
            
            return true;
        });
    }
    
    // Move focus to next element
    function focusNextElement(currentElement) {
        const focusable = getFocusableElements();
        const currentIndex = focusable.indexOf(currentElement);
        
        if (currentIndex > -1 && currentIndex < focusable.length - 1) {
            const nextElement = focusable[currentIndex + 1];
            nextElement.focus();
            
            // Select text if it's an input
            if (nextElement.tagName === 'INPUT' && nextElement.type !== 'checkbox' && nextElement.type !== 'radio') {
                nextElement.select();
            }
            
            return true;
        }
        return false;
    }
    
    // Move focus to previous element
    function focusPrevElement(currentElement) {
        const focusable = getFocusableElements();
        const currentIndex = focusable.indexOf(currentElement);
        
        if (currentIndex > 0) {
            const prevElement = focusable[currentIndex - 1];
            prevElement.focus();
            
            // Select text if it's an input
            if (prevElement.tagName === 'INPUT' && prevElement.type !== 'checkbox' && prevElement.type !== 'radio') {
                prevElement.select();
            }
            
            return true;
        }
        return false;
    }
    
    // Track if we should navigate on keyup (for select elements)
    let pendingNavigation = null;
    
    // Global Enter key handler for form navigation
    document.addEventListener('keydown', function(e) {
        const activeElement = document.activeElement;
        const tagName = activeElement?.tagName?.toLowerCase();
        
        // Enter key - move to next field
        if (e.key === 'Enter') {
            // Don't interfere with buttons - let them click
            if (tagName === 'button') {
                return;
            }
            
            // Don't interfere with textarea - allow new lines with Enter
            if (tagName === 'textarea') {
                return;
            }
            
            // Don't interfere with select2 dropdowns when open
            if (document.querySelector('.select2-container--open')) {
                return;
            }
            
            // Don't interfere with autocomplete/datalist dropdowns
            if (activeElement?.list || document.querySelector('.ui-autocomplete:visible, .tt-menu:visible, .awesomplete > ul:not([hidden])')) {
                return;
            }
            
            // For SELECT elements - prevent dropdown from opening and navigate
            if (tagName === 'select') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Store navigation info for keyup
                pendingNavigation = {
                    element: activeElement,
                    shiftKey: e.shiftKey
                };
                return false;
            }
            
            // For INPUT elements
            if (tagName === 'input') {
                e.preventDefault();
                e.stopPropagation();
                
                if (e.shiftKey) {
                    focusPrevElement(activeElement);
                } else {
                    focusNextElement(activeElement);
                }
            }
        }
    }, true); // Use capture phase to intercept before browser handles it
    
    // Handle navigation on keyup for select elements
    document.addEventListener('keyup', function(e) {
        if (e.key === 'Enter' && pendingNavigation) {
            const { element, shiftKey } = pendingNavigation;
            pendingNavigation = null;
            
            if (shiftKey) {
                focusPrevElement(element);
            } else {
                focusNextElement(element);
            }
        }
    });
    
    // Arrow key navigation (Ctrl+Arrow)
    document.addEventListener('keydown', function(e) {
        const activeElement = document.activeElement;
        const tagName = activeElement?.tagName?.toLowerCase();
        
        // Arrow Down - move to next field (alternative)
        if (e.key === 'ArrowDown' && e.ctrlKey) {
            if (tagName === 'input' || tagName === 'select') {
                e.preventDefault();
                focusNextElement(activeElement);
            }
        }
        
        // Arrow Up - move to previous field (alternative)
        if (e.key === 'ArrowUp' && e.ctrlKey) {
            if (tagName === 'input' || tagName === 'select') {
                e.preventDefault();
                focusPrevElement(activeElement);
            }
        }
    });
    
    // ============================================
    // END KEY TO SAVE TRANSACTION / FORM SUBMIT
    // ============================================
    
    document.addEventListener('keydown', function(e) {
        // Check if End key is pressed
        if (e.key === 'End') {
            // Don't trigger if user is in a textarea or contenteditable
            const activeElement = document.activeElement;
            const tagName = activeElement?.tagName?.toLowerCase();
            
            // Allow End key normal behavior in textarea
            if (tagName === 'textarea') {
                return;
            }
            
            // Prevent default End key behavior (scroll to bottom)
            e.preventDefault();
            
            // Try to call the appropriate save function based on what's available
            // Transaction save functions
            if (typeof saveSale === 'function') {
                saveSale();
            } else if (typeof savePurchase === 'function') {
                savePurchase();
            } else if (typeof saveTransaction === 'function') {
                saveTransaction();
            } else if (typeof saveChallan === 'function') {
                saveChallan();
            } else if (typeof savePurchaseChallan === 'function') {
                savePurchaseChallan();
            } else if (typeof saveCreditNote === 'function') {
                saveCreditNote();
            } else if (typeof saveDebitNote === 'function') {
                saveDebitNote();
            } else if (typeof saveReceipt === 'function') {
                saveReceipt();
            } else if (typeof savePayment === 'function') {
                savePayment();
            } else if (typeof saveVoucher === 'function') {
                saveVoucher();
            } else if (typeof saveQuotation === 'function') {
                saveQuotation();
            }
            // Modification update functions
            else if (typeof updateCreditNote === 'function') {
                updateCreditNote();
            } else if (typeof updateDebitNote === 'function') {
                updateDebitNote();
            } else if (typeof updatePayment === 'function') {
                updatePayment();
            } else if (typeof updateVoucher === 'function') {
                updateVoucher();
            } else if (typeof updateQuotation === 'function') {
                updateQuotation();
            } else if (typeof updateTransaction === 'function') {
                updateTransaction();
            }
            // jQuery button click fallback for sale-return-replacement
            else if (typeof $ !== 'undefined' && ($('#saveBtn').length || $('#btnUpdate').length)) {
                if ($('#saveBtn').length) {
                    $('#saveBtn').click();
                } else if ($('#btnUpdate').length) {
                    $('#btnUpdate').click();
                }
            }
            // For Master modules (create/edit pages) - submit the form directly
            else {
                // Find submit button and click it, or submit the form
                const submitBtn = document.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.click();
                } else {
                    // Find the main form and submit
                    const form = document.querySelector('form:not([id*="filter"]):not([id*="search"]):not([id*="logout"])');
                    if (form) {
                        form.submit();
                    }
                }
            }
        }
    });
    
    // ============================================
    // AUTO FOCUS FIRST FIELD ON PAGE LOAD
    // ============================================
    
    // Check if this is a transaction or modification page
    const isTransactionPage = window.location.href.includes('/transaction') || 
                              window.location.href.includes('/modification') ||
                              window.location.href.includes('/create');
    
    if (isTransactionPage) {
        // Small delay to ensure page is fully loaded
        setTimeout(function() {
            const focusable = getFocusableElements();
            
            // Find first suitable input/select (skip hidden, readonly)
            for (let el of focusable) {
                const tagName = el.tagName.toLowerCase();
                
                // Skip buttons for auto-focus
                if (tagName === 'button') continue;
                
                // Focus the first input/select
                if (tagName === 'input' || tagName === 'select') {
                    el.focus();
                    if (tagName === 'input' && el.type !== 'checkbox' && el.type !== 'radio') {
                        el.select();
                    }
                    break;
                }
            }
        }, 100);
    }
});
</script>
