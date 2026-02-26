

<?php
  $hasMultipleRows = isset($extraShortcuts) && count($extraShortcuts) > 4;
?>

<style>
  .module-shortcuts-wrapper {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .module-shortcuts-row {
    display: flex;
    gap: 4px;
    flex-wrap: nowrap;
    align-items: center;
  }
  .module-shortcuts-row .shortcut-btn {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    background: white;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.15s ease;
    color: #475569;
    text-decoration: none;
    white-space: nowrap;
  }
  .module-shortcuts-row .shortcut-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
  }
  .module-shortcuts-row .shortcut-btn kbd {
    background: #4f46e5;
    border: none;
    border-radius: 2px;
    padding: 1px 4px;
    font-size: 0.55rem;
    font-family: 'Consolas', monospace;
    color: white;
  }
  /* Row selection styling for module tables */
  .module-table-body tr {
    cursor: pointer;
    transition: background-color 0.15s ease;
  }
  .module-table-body tr:hover {
    background-color: #f0f9ff !important;
  }
  .module-table-body tr:hover > td {
    background-color: #f0f9ff !important;
  }
  .module-table-body tr.row-selected,
  #item-table-body tr.row-selected,
  table tbody tr.row-selected {
    background-color: #bfdbfe !important;
    border-left: 3px solid #3b82f6 !important;
    box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.2) !important;
  }
  .module-table-body tr.row-selected > td,
  #item-table-body tr.row-selected > td,
  table tbody tr.row-selected > td {
    background-color: #bfdbfe !important;
  }
  .module-table-body tr.row-selected:hover,
  #item-table-body tr.row-selected:hover,
  table tbody tr.row-selected:hover {
    background-color: #93c5fd !important;
  }
  .module-table-body tr.row-selected:hover > td,
  #item-table-body tr.row-selected:hover > td,
  table tbody tr.row-selected:hover > td {
    background-color: #93c5fd !important;
  }
</style>

<!-- Inline Shortcut Buttons -->
<div class="module-shortcuts-wrapper">
  <?php if($hasMultipleRows): ?>
    
    <div class="module-shortcuts-row">
      <span class="shortcut-btn" onclick="moduleShortcut('F9')"><kbd>F9</kbd> New</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F3')"><kbd>F3</kbd> Edit</span>
      <span class="shortcut-btn" onclick="moduleShortcut('Delete')"><kbd>Del</kbd> Delete</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F8')"><kbd>F8</kbd> Del Multi</span>
      <?php $__currentLoopData = $extraShortcuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $shortcut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($index < 3): ?>
          <span class="shortcut-btn" onclick="moduleShortcut('<?php echo e($shortcut['key']); ?>')">
            <kbd><?php echo e($shortcut['key']); ?></kbd> <?php echo e($shortcut['label']); ?>

          </span>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="module-shortcuts-row">
      <?php $__currentLoopData = $extraShortcuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $shortcut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($index >= 3): ?>
          <span class="shortcut-btn" onclick="moduleShortcut('<?php echo e($shortcut['key']); ?>')">
            <kbd><?php echo e($shortcut['key']); ?></kbd> <?php echo e($shortcut['label']); ?>

          </span>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <span class="shortcut-btn" onclick="window.history.back()"><kbd>ESC</kbd> Exit</span>
    </div>
  <?php else: ?>
    
    <div class="module-shortcuts-row">
      <span class="shortcut-btn" onclick="moduleShortcut('F9')"><kbd>F9</kbd> New</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F3')"><kbd>F3</kbd> Edit</span>
      <span class="shortcut-btn" onclick="moduleShortcut('Delete')"><kbd>Del</kbd> Delete</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F8')"><kbd>F8</kbd> Del Multi</span>
      <?php if(isset($extraShortcuts)): ?>
        <?php $__currentLoopData = $extraShortcuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shortcut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <span class="shortcut-btn" onclick="moduleShortcut('<?php echo e($shortcut['key']); ?>')">
            <kbd><?php echo e($shortcut['key']); ?></kbd> <?php echo e($shortcut['label']); ?>

          </span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
      <span class="shortcut-btn" onclick="window.history.back()"><kbd>ESC</kbd> Exit</span>
    </div>
  <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
  // Module config from Blade
  const moduleConfig = {
    createRoute: '<?php echo e($createRoute ?? ""); ?>',
    tableBodyId: '<?php echo e($tableBodyId ?? "module-table-body"); ?>',
    checkboxClass: '<?php echo e($checkboxClass ?? "module-checkbox"); ?>',
    extraShortcuts: <?php echo json_encode($extraShortcuts ?? [], 15, 512) ?>
  };
  
  // Select a specific row (only visual selection, no checkbox tick)
  function selectRow(row) {
    if (!row) return;
    
    // Find tbody - try specific ID first, then parent
    let tbody = document.getElementById(moduleConfig.tableBodyId);
    if (!tbody) {
      tbody = row.closest('tbody');
    }
    if (!tbody) return;
    
    // Remove selection from all rows in this tbody
    tbody.querySelectorAll('tr.row-selected').forEach(r => {
      r.classList.remove('row-selected');
    });
    
    // Select the new row
    row.classList.add('row-selected');
    
    // Scroll row into view
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  
  // Get selected row
  function getSelectedRow() {
    // Try specific tbody first
    let tbody = document.getElementById(moduleConfig.tableBodyId);
    
    // Fallback to any table tbody
    if (!tbody) {
      tbody = document.querySelector('table tbody');
    }
    
    if (!tbody) return null;
    
    // Check for row-selected class first
    const clickSelected = tbody.querySelector('tr.row-selected');
    if (clickSelected) {
      return clickSelected;
    }
    
    // Check for checked checkbox
    if (moduleConfig.checkboxClass) {
      const checkedBox = document.querySelector('.' + moduleConfig.checkboxClass + ':checked');
      if (checkedBox) {
        return checkedBox.closest('tr');
      }
    }
    
    return null;
  }
  
  // Get all data rows (excluding empty state row)
  function getAllRows() {
    // Try specific tbody first
    let tbody = document.getElementById(moduleConfig.tableBodyId);
    
    // Fallback to any table tbody
    if (!tbody) {
      tbody = document.querySelector('table tbody');
    }
    
    if (!tbody) return [];
    
    // Get all rows and filter out empty state rows
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    return allRows.filter(tr => {
      const tds = tr.querySelectorAll('td');
      // If row has only 1 td with colspan, it's an empty state row
      if (tds.length === 1 && tds[0].hasAttribute('colspan')) {
        return false;
      }
      // Valid data row must have at least one td
      return tds.length > 0;
    });
  }
  
  // Show toast notification
  function showToast(message) {
    const existing = document.getElementById('module-shortcut-toast');
    if (existing) existing.remove();
    
    const toast = document.createElement('div');
    toast.id = 'module-shortcut-toast';
    toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 px-4 py-2 rounded-pill shadow-lg text-white';
    toast.style.cssText = 'z-index: 99999; background: linear-gradient(135deg, #4f46e5, #7c3aed);';
    toast.innerHTML = `<i class="bi bi-keyboard me-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 2000);
  }
  
  // Global trigger function
  window.moduleShortcut = function(key) {
    const event = new KeyboardEvent('keydown', { key: key, bubbles: true });
    document.dispatchEvent(event);
  };
  
  // Keyboard shortcut handler - use capture phase to run before other handlers
  document.addEventListener('keydown', function(e) {
    const activeEl = document.activeElement;
    const isTyping = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.isContentEditable);
    
    // Arrow Up/Down navigation (not when typing)
    if (!isTyping && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
      // Skip if a modal is open (let modal handle its own navigation)
      const openModal = document.querySelector('.pending-orders-modal.show, .modal.show, [class*="modal"].show');
      if (openModal) {
        return; // Let the modal's keyboard handler take over
      }
      
      const rows = getAllRows();
      if (rows.length === 0) {
        showToast('No rows available');
        return;
      }
      
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation(); // Stop other handlers from running
      
      const currentRow = getSelectedRow();
      let currentIndex = -1;
      
      // Find current row index in the rows array
      if (currentRow) {
        currentIndex = rows.findIndex(r => r === currentRow);
      }
      
      // If no row is selected or current row not found, select first row on ArrowDown, last on ArrowUp
      if (currentIndex === -1) {
        if (e.key === 'ArrowDown') {
          selectRow(rows[0]);
        } else {
          selectRow(rows[rows.length - 1]);
        }
        return;
      }
      
      if (e.key === 'ArrowUp') {
        // Move up
        if (currentIndex > 0) {
          selectRow(rows[currentIndex - 1]);
        } else {
          // Already at first row - scroll to top to show filters
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      } else if (e.key === 'ArrowDown') {
        // Move down
        if (currentIndex < rows.length - 1) {
          selectRow(rows[currentIndex + 1]);
        } else {
          // At last row - check for load more sentinel
          const sentinel = document.querySelector('[id$="-sentinel"]');
          if (sentinel && sentinel.getAttribute('data-next-url')) {
            sentinel.scrollIntoView({ behavior: 'smooth' });
            const currentRowCount = rows.length;
            let checkCount = 0;
            const checkForNewRows = setInterval(() => {
              checkCount++;
              const newRows = getAllRows();
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
      return;
    }
    
    // Allow F-keys even when typing
    if (isTyping && !e.key.startsWith('F') && e.key !== 'Escape' && e.key !== 'Delete' && e.key !== 'Enter') {
      return;
    }
    
    // Enter key - Toggle checkbox of selected row
    if (e.key === 'Enter' && !isTyping) {
      e.preventDefault();
      e.stopPropagation();
      const selectedRow = getSelectedRow();
      if (selectedRow) {
        const checkbox = selectedRow.querySelector('.' + moduleConfig.checkboxClass);
        if (checkbox) {
          checkbox.checked = !checkbox.checked;
          // Trigger change event for any listeners
          checkbox.dispatchEvent(new Event('change', { bubbles: true }));
          
          // Update selected count if function exists
          const updateFnName = 'update' + moduleConfig.checkboxClass.replace(/-/g, '').replace('checkbox', '') + 'SelectedCount';
          // Try common update function names
          if (typeof window.updateItemsSelectedCount === 'function') {
            window.updateItemsSelectedCount();
          } else if (typeof window.updateSuppliersSelectedCount === 'function') {
            window.updateSuppliersSelectedCount();
          } else if (typeof window.updateCustomersSelectedCount === 'function') {
            window.updateCustomersSelectedCount();
          } else if (typeof window.updateCompaniesSelectedCount === 'function') {
            window.updateCompaniesSelectedCount();
          }
          
          const checkedCount = document.querySelectorAll('.' + moduleConfig.checkboxClass + ':checked').length;
          showToast(checkbox.checked ? `Selected (${checkedCount} total)` : `Deselected (${checkedCount} total)`);
        }
      } else {
        showToast('No row selected - Use Arrow keys to select');
      }
      return;
    }
    
    // F8 - Delete Multiple (selected checkboxes)
    if (e.key === 'F8') {
      e.preventDefault();
      e.stopPropagation();
      const checkedBoxes = document.querySelectorAll('.' + moduleConfig.checkboxClass + ':checked');
      if (checkedBoxes.length === 0) {
        showToast('No items selected - Use Enter to select rows');
        return;
      }
      
      // Find and click the delete selected button
      const deleteSelectedBtn = document.querySelector('#delete-selected-btn, #delete-selected-items-btn, #delete-selected-suppliers-btn, #delete-selected-customers-btn, [id*="delete-selected"]');
      if (deleteSelectedBtn && !deleteSelectedBtn.classList.contains('d-none')) {
        showToast(`Deleting ${checkedBoxes.length} selected items...`);
        deleteSelectedBtn.click();
      } else {
        // Try to trigger the confirmMultipleDelete function directly
        if (typeof window.confirmMultipleDelete === 'function') {
          showToast(`Deleting ${checkedBoxes.length} selected items...`);
          window.confirmMultipleDelete();
        } else if (typeof window.confirmMultipleDeleteSuppliers === 'function') {
          showToast(`Deleting ${checkedBoxes.length} selected suppliers...`);
          window.confirmMultipleDeleteSuppliers();
        } else if (typeof window.confirmMultipleDeleteCustomers === 'function') {
          showToast(`Deleting ${checkedBoxes.length} selected customers...`);
          window.confirmMultipleDeleteCustomers();
        } else {
          showToast(`${checkedBoxes.length} items selected - Delete button not found`);
        }
      }
      return;
    }
    
    // F9 - New
    if (e.key === 'F9' && moduleConfig.createRoute) {
      e.preventDefault();
      showToast('Opening New Form...');
      window.location.href = moduleConfig.createRoute;
      return;
    }
    
    // F3 - Edit
    if (e.key === 'F3') {
      e.preventDefault();
      const selectedRow = getSelectedRow();
      if (selectedRow) {
        const editBtn = selectedRow.querySelector('a[title="Edit"], a[href*="edit"]');
        if (editBtn) {
          showToast('Opening Edit Form...');
          window.location.href = editBtn.href;
        } else {
          showToast('Edit option not available');
        }
      } else {
        showToast('No row selected - Use Arrow keys to select');
      }
      return;
    }
    
    // Delete key
    if (e.key === 'Delete' && !isTyping) {
      e.preventDefault();
      const selectedRow = getSelectedRow();
      if (selectedRow) {
        const deleteBtn = selectedRow.querySelector('button.ajax-delete, button[type="submit"][class*="danger"]');
        if (deleteBtn) {
          showToast('Confirming delete...');
          deleteBtn.click();
        } else {
          showToast('Delete option not available');
        }
      } else {
        showToast('No row selected - Use Arrow keys to select');
      }
      return;
    }
    
    // Handle extra shortcuts
    moduleConfig.extraShortcuts.forEach(function(shortcut) {
      if (e.key === shortcut.key) {
        e.preventDefault();
        const selectedRow = getSelectedRow();
        if (selectedRow) {
          // Find button with matching title or href
          const btn = selectedRow.querySelector(`a[title*="${shortcut.label}"], a[href*="${shortcut.action}"]`);
          if (btn) {
            showToast(`Opening ${shortcut.label}...`);
            window.location.href = btn.href;
          } else {
            showToast(`${shortcut.label} - Feature coming soon`);
          }
        } else {
          showToast('No row selected - Use Arrow keys to select');
        }
      }
    });
  }, true); // Use capture phase to run before other handlers
  
  // Row click selection (no auto-select on load)
  document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById(moduleConfig.tableBodyId);
    if (!tbody) return;
    
    // Add class for styling
    tbody.classList.add('module-table-body');
    
    // Click to select row
    tbody.addEventListener('click', function(e) {
      const row = e.target.closest('tr');
      if (!row) return;
      
      // Don't select if clicking on buttons/links/checkboxes
      if (e.target.closest('a, button, input, .form-check')) return;
      
      selectRow(row);
    });
  });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\bill-software\resources\views/layouts/partials/module-shortcuts.blade.php ENDPATH**/ ?>