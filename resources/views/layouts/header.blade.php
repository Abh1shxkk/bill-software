<nav id="appHeader" class="navbar navbar-expand-lg navbar-light app-header"
  style="background-color: white; border-bottom: 1px solid #dee2e6;">
  <div class="container-fluid">
    <button class="btn btn-outline-dark me-2" id="headerSidebarToggle" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    

    <div class="collapse navbar-collapse" id="topbarNav">
      <ul class="navbar-nav mx-auto">
        <!-- Transaction Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Transaction
          </a>
          <ul class="dropdown-menu">
            <!-- Sale Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale.invoices') }}">Sale Invoices</a></li>
              </ul>
            </li>
            
           
            
            <!-- Purchase Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase.invoices') }}">Purchase Invoices</a></li>
              </ul>
            </li>
            
           
            
            <!-- Sale Return Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sales Return</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.transaction') }}">Transaction </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.modification') }}">Modification </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.index') }}">Sale Return Invoice</a></li>
              </ul>
            </li>
            
            <!-- Breakage/Expiry from customer Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Breakage/Expiry from customer</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.modification') }}">Modification </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.expiry-date') }}">Expiry Date Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.index') }}">Breakage/Expiry Invoice</a></li>
              </ul>
            </li>
            
            <!-- Purchase Return Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Return</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.index') }}">Purchase Return Invoice</a></li>
              </ul>
            </li>
            
            <!-- Breakage/Expiry to Supplier Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Breakage/Expiry to Supplier</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.issued-transaction') }}">Issued - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.issued-modification') }}">Issued - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-transaction') }}">Received - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-modification') }}">Received - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-transaction') }}">Unused Dump - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-modification') }}">Unused Dump - Modification</a></li>
              </ul>
            </li>

             <!-- Sale Challan Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Challan</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.invoices') }}">Sale Challan Invoice</a></li>
              </ul>
            </li>

             <!-- Purchase Challan Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Challan</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.invoices') }}">Purchase Challan Invoice</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Credit Note -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Credit Note</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.invoices') }}">Credit Note Invoices</a></li>
              </ul>
            </li>

            <!-- Debit Note -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Debit Note</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.invoices') }}">Debit Note Invoices</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Misc Transaction -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Misc Transaction</a>
              <ul class="dropdown-menu">
                <!-- Replacement Note -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Replacement Note</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.index') }}">Replacement Note Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Replacement Received -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Replacement Received</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.index') }}">Replacement Received Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Adjustment -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Adjustment</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.invoices') }}">Invoices</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Outgoing -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Outgoing</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.index') }}"> Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Outgoing Return -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Outgoing Return</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.index') }}"> Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Incoming -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Incoming</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Incoming Return -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Incoming Return</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Sample Issued -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sample Issued</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.create') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Sample Received -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sample Received</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.create') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.index') }}">Invoice</a></li>
                  </ul>
                </li>
              </ul>
            </li>
          </ul>
        </li>
      </ul>
      <li class="nav-item d-none d-sm-inline">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown"
          aria-expanded="false">
          <img
            src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : 'https://i.pravatar.cc/32' }}"
            class="rounded-circle me-2" width="32" height="32" alt="avatar">
          <span class="d-none d-sm-inline">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="px-3 py-2 small text-muted">
            {{ auth()->user()->email }}
          </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i
                  class="bi bi-gear me-2"></i>Settings</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                @csrf
                <button class="btn btn-sm btn-outline-danger w-100"><i
                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Header styling */
  .app-header {
    grid-area: header;
    z-index: 100;
    position: relative;
  }

  /* Sidebar toggle button */
  #headerSidebarToggle {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
  }

  #headerSidebarToggle:hover {
    background-color: rgba(0, 0, 0, 0.1);
    border: none !important;
  }

  #headerSidebarToggle:focus,
  #headerSidebarToggle:active {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background-color: rgba(0, 0, 0, 0.15);
  }

  /* Navbar toggler */
  .navbar-toggler {
    border: none;
  }

  /* Dropdown menu styling */
  .dropdown-menu {
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.08);
    padding: 0.25rem 0;
    display: none;
    font-size: 12px;
  }

  .dropdown-menu.show {
    display: block;
  }

  .dropdown-item {
    padding: 0.3rem 0.8rem;
    transition: all 0.2s ease;
    white-space: nowrap;
    font-size: 12px;
  }

  .dropdown-item:hover {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
  }

  /* Nested dropdown (submenu) */
  .dropdown-submenu {
    position: relative;
  }

  .dropdown-submenu > .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -4px;
    margin-left: 0;
    min-width: 160px;
    z-index: 1060;
  }

  /* Submenu opens upward when near bottom */
  .dropdown-submenu.dropup > .dropdown-menu {
    top: auto;
    bottom: 0;
  }

  /* Arrow for submenu items - Hidden */
  .dropdown-submenu > .dropdown-toggle::after {
    display: none;
  }

  /* Show submenu on hover */
  .dropdown-submenu:hover > .dropdown-menu {
    display: block !important;
  }

  /* Active/hover state for submenu toggle */
  .dropdown-submenu:hover > .dropdown-toggle {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
  }
</style>
<script>
  // Sidebar toggle functionality
  document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('headerSidebarToggle');
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
      });
    }

    // Close all submenus
    function closeAllSubmenus() {
      document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
        menu.classList.remove('show');
      });
      document.querySelectorAll('.dropdown-submenu').forEach(function(submenu) {
        submenu.classList.remove('dropup');
      });
    }

    // Function to check if submenu should open upward
    function adjustSubmenuPosition(submenu, submenuDropdown) {
      if (!submenuDropdown) return;
      
      // Reset position first
      submenu.classList.remove('dropup');
      submenuDropdown.style.top = '';
      submenuDropdown.style.bottom = '';
      
      // Get positions
      const rect = submenu.getBoundingClientRect();
      const menuHeight = submenuDropdown.offsetHeight || 200;
      const viewportHeight = window.innerHeight;
      const spaceBelow = viewportHeight - rect.top;
      
      // If not enough space below, open upward
      if (spaceBelow < menuHeight) {
        submenu.classList.add('dropup');
        submenuDropdown.style.top = 'auto';
        submenuDropdown.style.bottom = '0';
      }
    }

    // Submenu functionality for nested dropdowns
    document.querySelectorAll('.dropdown-submenu').forEach(function (submenu) {
      const submenuDropdown = submenu.querySelector(':scope > .dropdown-menu');
      const toggle = submenu.querySelector(':scope > .dropdown-toggle');
      
      // Mouse enter - open submenu
      submenu.addEventListener('mouseenter', function () {
        if (submenuDropdown) {
          // Close sibling submenus first
          const parentMenu = submenu.closest('.dropdown-menu');
          if (parentMenu) {
            parentMenu.querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu').forEach(function(menu) {
              if (menu !== submenuDropdown) {
                menu.classList.remove('show');
              }
            });
          }
          submenuDropdown.classList.add('show');
          setTimeout(function() { adjustSubmenuPosition(submenu, submenuDropdown); }, 10);
        }
      });

      // Mouse leave - close submenu
      submenu.addEventListener('mouseleave', function () {
        if (submenuDropdown) {
          submenuDropdown.classList.remove('show');
          submenu.classList.remove('dropup');
        }
      });

      // Click support for mobile/touch devices
      if (toggle) {
        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          
          if (submenuDropdown) {
            const isShowing = submenuDropdown.classList.contains('show');
            
            // Close sibling submenus
            const parentMenu = submenu.closest('.dropdown-menu');
            if (parentMenu) {
              parentMenu.querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu').forEach(function(menu) {
                menu.classList.remove('show');
              });
            }
            
            if (!isShowing) {
              submenuDropdown.classList.add('show');
              setTimeout(function() { adjustSubmenuPosition(submenu, submenuDropdown); }, 10);
            } else {
              submenuDropdown.classList.remove('show');
              submenu.classList.remove('dropup');
            }
          }
        });
      }
    });

    // Close submenus when main dropdown closes
    document.querySelectorAll('.nav-item.dropdown').forEach(function(dropdown) {
      dropdown.addEventListener('hidden.bs.dropdown', function () {
        closeAllSubmenus();
      });
    });

    // Close submenus when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.dropdown-menu') && !e.target.closest('.dropdown-toggle')) {
        closeAllSubmenus();
      }
    });
  });
</script>