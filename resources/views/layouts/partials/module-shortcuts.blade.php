{{--
  Module Shortcuts Partial
  Include this in any module's index page to add keyboard shortcuts
  
  Usage: @include('layouts.partials.module-shortcuts', [
      'moduleName' => 'Items',
      'createRoute' => route('admin.items.create'),
      'tableBodyId' => 'item-table-body',
      'checkboxClass' => 'item-checkbox',
      'extraShortcuts' => [
          ['key' => 'F5', 'label' => 'Batches', 'action' => 'batches'],
          ['key' => 'F10', 'label' => 'Stock Ledger', 'action' => 'stock-ledger'],
      ]
  ])
--}}

@php
  $hasMultipleRows = isset($extraShortcuts) && count($extraShortcuts) > 4;
@endphp

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
  @if($hasMultipleRows)
    {{-- 2 Row Layout for modules with many shortcuts --}}
    <div class="module-shortcuts-row">
      <span class="shortcut-btn" onclick="moduleShortcut('F9')"><kbd>F9</kbd> New</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F3')"><kbd>F3</kbd> Edit</span>
      <span class="shortcut-btn" onclick="moduleShortcut('Delete')"><kbd>Del</kbd> Delete</span>
      @foreach($extraShortcuts as $index => $shortcut)
        @if($index < 4)
          <span class="shortcut-btn" onclick="moduleShortcut('{{ $shortcut['key'] }}')">
            <kbd>{{ $shortcut['key'] }}</kbd> {{ $shortcut['label'] }}
          </span>
        @endif
      @endforeach
    </div>
    <div class="module-shortcuts-row">
      @foreach($extraShortcuts as $index => $shortcut)
        @if($index >= 4)
          <span class="shortcut-btn" onclick="moduleShortcut('{{ $shortcut['key'] }}')">
            <kbd>{{ $shortcut['key'] }}</kbd> {{ $shortcut['label'] }}
          </span>
        @endif
      @endforeach
      <span class="shortcut-btn" onclick="window.history.back()"><kbd>ESC</kbd> Exit</span>
    </div>
  @else
    {{-- Single Row Layout for modules with few shortcuts --}}
    <div class="module-shortcuts-row">
      <span class="shortcut-btn" onclick="moduleShortcut('F9')"><kbd>F9</kbd> New</span>
      <span class="shortcut-btn" onclick="moduleShortcut('F3')"><kbd>F3</kbd> Edit</span>
      <span class="shortcut-btn" onclick="moduleShortcut('Delete')"><kbd>Del</kbd> Delete</span>
      @if(isset($extraShortcuts))
        @foreach($extraShortcuts as $shortcut)
          <span class="shortcut-btn" onclick="moduleShortcut('{{ $shortcut['key'] }}')">
            <kbd>{{ $shortcut['key'] }}</kbd> {{ $shortcut['label'] }}
          </span>
        @endforeach
      @endif
      <span class="shortcut-btn" onclick="window.history.back()"><kbd>ESC</kbd> Exit</span>
    </div>
  @endif
</div>

@push('scripts')
<script>
(function() {
  // Module config from Blade
  const moduleConfig = {
    createRoute: '{{ $createRoute ?? "" }}',
    tableBodyId: '{{ $tableBodyId ?? "module-table-body" }}',
    checkboxClass: '{{ $checkboxClass ?? "module-checkbox" }}',
    extraShortcuts: @json($extraShortcuts ?? [])
  };
  
  // Select a specific row (only visual selection, no checkbox tick)
  function selectRow(row) {
    const tbody = document.getElementById(moduleConfig.tableBodyId);
    if (!tbody || !row) return;
    
    // Remove selection from all rows (only visual class, don't touch checkboxes)
    tbody.querySelectorAll('tr.row-selected').forEach(r => {
      r.classList.remove('row-selected');
    });
    
    // Select the new row (only visual, no checkbox)
    row.classList.add('row-selected');
    
    // Scroll row into view
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  
  // Get selected row
  function getSelectedRow() {
    const tbody = document.getElementById(moduleConfig.tableBodyId);
    if (!tbody) return null;
    
    // Check for click-selected row
    const clickSelected = tbody.querySelector('tr.row-selected');
    if (clickSelected) return clickSelected;
    
    // Check for checked checkbox
    const checkedBox = document.querySelector('.' + moduleConfig.checkboxClass + ':checked');
    if (checkedBox) return checkedBox.closest('tr');
    
    return null;
  }
  
  // Get all data rows (excluding empty state row)
  function getAllRows() {
    const tbody = document.getElementById(moduleConfig.tableBodyId);
    if (!tbody) return [];
    return Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.querySelector('td[colspan]'));
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
  
  // Keyboard shortcut handler
  document.addEventListener('keydown', function(e) {
    const activeEl = document.activeElement;
    const isTyping = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.isContentEditable);
    
    // Arrow Up/Down navigation (not when typing)
    if (!isTyping && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
      e.preventDefault();
      const rows = getAllRows();
      if (rows.length === 0) return;
      
      const currentRow = getSelectedRow();
      let currentIndex = currentRow ? rows.indexOf(currentRow) : -1;
      
      if (e.key === 'ArrowUp') {
        // Move up - if at first row, scroll to top of page (to show filters)
        if (currentIndex > 0) {
          currentIndex--;
          selectRow(rows[currentIndex]);
        } else if (currentIndex === 0) {
          // Already at first row - scroll to very top to show header/filters
          const contentDiv = document.querySelector('.content');
          if (contentDiv) {
            contentDiv.scrollTo({ top: 0, behavior: 'smooth' });
          }
          window.scrollTo({ top: 0, behavior: 'smooth' });
          document.documentElement.scrollTo({ top: 0, behavior: 'smooth' });
        }
      } else {
        // Arrow Down
        if (currentIndex < rows.length - 1) {
          // Not at last row, just move down
          currentIndex++;
          selectRow(rows[currentIndex]);
        } else {
          // At last row - check if there's a sentinel for load more
          const sentinel = document.querySelector('[id$="-sentinel"]');
          if (sentinel && sentinel.getAttribute('data-next-url')) {
            // Trigger load more by scrolling sentinel into view
            sentinel.scrollIntoView({ behavior: 'smooth' });
            
            // Wait for new rows to load, then select the next one
            const currentRowCount = rows.length;
            let checkCount = 0;
            const checkForNewRows = setInterval(() => {
              checkCount++;
              const newRows = getAllRows();
              if (newRows.length > currentRowCount) {
                // New rows loaded, select the first new row
                selectRow(newRows[currentRowCount]);
                clearInterval(checkForNewRows);
              } else if (checkCount > 20) {
                // Timeout after 2 seconds, stay at current row
                clearInterval(checkForNewRows);
              }
            }, 100);
          }
          // If no more rows to load, stay at last row (no wrap)
        }
      }
      return;
    }
    
    // Allow F-keys even when typing
    if (isTyping && !e.key.startsWith('F') && e.key !== 'Escape' && e.key !== 'Delete') {
      return;
    }
    
    const selectedRow = getSelectedRow();
    
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
      if (selectedRow) {
        const editBtn = selectedRow.querySelector('a[title="Edit"], a[href*="edit"]');
        if (editBtn) {
          showToast('Opening Edit Form...');
          window.location.href = editBtn.href;
        }
      } else {
        showToast('Select a row first');
      }
      return;
    }
    
    // Delete key
    if (e.key === 'Delete' && !isTyping) {
      e.preventDefault();
      if (selectedRow) {
        const deleteBtn = selectedRow.querySelector('button.ajax-delete, button[type="submit"][class*="danger"]');
        if (deleteBtn) {
          showToast('Confirming delete...');
          deleteBtn.click();
        }
      } else {
        showToast('Select a row first');
      }
      return;
    }
    
    // Handle extra shortcuts
    moduleConfig.extraShortcuts.forEach(function(shortcut) {
      if (e.key === shortcut.key) {
        e.preventDefault();
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
          showToast('Select a row first');
        }
      }
    });
  });
  
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
@endpush
