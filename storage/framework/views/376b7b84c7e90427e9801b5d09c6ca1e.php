
<?php
    $user = auth()->user();
?>

<nav class="nav flex-column small">
    <a class="nav-link text-white d-flex align-items-center px-2" href="<?php echo e(route('admin.dashboard')); ?>">
        <i class="bi bi-speedometer2 me-2"></i><span class="label">Dashboard</span>
    </a>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuCompanies" style="background:transparent;">
            <i class="bi bi-buildings me-2"></i> <span class="label">Companies</span>
        </button>
        <div class="collapse" id="menuCompanies">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.companies.create')); ?>">
                <span class="label">Add Company</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.companies.index')); ?>">
                <span class="label">All Companies</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuCustomers" style="background:transparent;">
            <i class="bi bi-people me-2"></i> <span class="label">Customers</span>
        </button>
        <div class="collapse" id="menuCustomers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.customers.create')); ?>">
                <span class="label">Add Customer</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.customers.index')); ?>">
                <span class="label">All Customers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuItems" style="background:transparent;">
            <i class="bi bi-box-seam me-2"></i> <span class="label">Items</span>
        </button>
        <div class="collapse" id="menuItems">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.items.create')); ?>">
                <span class="label">Add Item</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.items.index')); ?>">
                <span class="label">All Items</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuSuppliers" style="background:transparent;">
            <i class="bi bi-truck me-2"></i> <span class="label">Suppliers</span>
        </button>
        <div class="collapse" id="menuSuppliers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.suppliers.create')); ?>">
                <span class="label">Add Supplier</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.suppliers.index')); ?>">
                <span class="label">All Suppliers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuHsnCodes" style="background:transparent;">
            <i class="bi bi-upc-scan me-2"></i> <span class="label">HSN Master</span>
        </button>
        <div class="collapse" id="menuHsnCodes">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.hsn-codes.create')); ?>">
                <span class="label">Add HSN Code</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.hsn-codes.index')); ?>">
                <span class="label">All HSN Codes</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <a class="nav-link text-white d-flex align-items-center px-2" href="<?php echo e(route('admin.all-ledger.index')); ?>">
            <i class="bi bi-journal-check me-2"></i><span class="label">All Ledger</span>
        </a>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuGeneralLedger" style="background:transparent;">
            <i class="bi bi-journal-text me-2"></i> <span class="label">General Ledger</span>
        </button>
        <div class="collapse" id="menuGeneralLedger">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-ledger.create')); ?>">
                <span class="label">Add Account</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-ledger.index')); ?>">
                <span class="label">All Accounts</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuCashBank" style="background:transparent;">
            <i class="bi bi-cash-stack me-2"></i> <span class="label">Cash / Bank Books</span>
        </button>
        <div class="collapse" id="menuCashBank">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.cash-bank-books.create')); ?>">
                <span class="label">Add Transaction</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.cash-bank-books.index')); ?>">
                <span class="label">All Transactions</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuSaleLedger" style="background:transparent;">
            <i class="bi bi-cart-check me-2"></i> <span class="label">Sale Ledger</span>
        </button>
        <div class="collapse" id="menuSaleLedger">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.sale-ledger.create')); ?>">
                <span class="label">Add Sale Entry</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.sale-ledger.index')); ?>">
                <span class="label">All Sale Entries</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuPurchaseLedger" style="background:transparent;">
            <i class="bi bi-bag-check me-2"></i> <span class="label">Purchase Ledger</span>
        </button>
        <div class="collapse" id="menuPurchaseLedger">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.purchase-ledger.create')); ?>">
                <span class="label">Add Purchase Entry</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.purchase-ledger.index')); ?>">
                <span class="label">All Purchase Entries</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuSalesMen" style="background:transparent;">
            <i class="bi bi-person-badge me-2"></i> <span class="label">Sales Man</span>
        </button>
        <div class="collapse" id="menuSalesMen">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.sales-men.create')); ?>">
                <span class="label">Add Sales Man</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.sales-men.index')); ?>">
                <span class="label">All Sales Men</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuAreas" style="background:transparent;">
            <i class="bi bi-geo-alt me-2"></i> <span class="label">Area</span>
        </button>
        <div class="collapse" id="menuAreas">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.areas.create')); ?>">
                <span class="label">Add Area</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.areas.index')); ?>">
                <span class="label">All Areas</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuRoutes" style="background:transparent;">
            <i class="bi bi-signpost me-2"></i> <span class="label">Route</span>
        </button>
        <div class="collapse" id="menuRoutes">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.routes.create')); ?>">
                <span class="label">Add Route</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.routes.index')); ?>">
                <span class="label">All Routes</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuStates" style="background:transparent;">
            <i class="bi bi-map me-2"></i> <span class="label">State</span>
        </button>
        <div class="collapse" id="menuStates">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.states.create')); ?>">
                <span class="label">Add State</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.states.index')); ?>">
                <span class="label">All States</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuAreaManagers" style="background:transparent;">
            <i class="bi bi-person-workspace me-2"></i> <span class="label">Area Mgr.</span>
        </button>
        <div class="collapse" id="menuAreaManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.area-managers.create')); ?>">
                <span class="label">Add Area Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.area-managers.index')); ?>">
                <span class="label">All Area Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuRegionalManagers" style="background:transparent;">
            <i class="bi bi-people-fill me-2"></i> <span class="label">Regn.mgr</span>
        </button>
        <div class="collapse" id="menuRegionalManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.regional-managers.create')); ?>">
                <span class="label">Add Regional Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.regional-managers.index')); ?>">
                <span class="label">All Regional Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuMarketingManagers" style="background:transparent;">
            <i class="bi bi-megaphone me-2"></i> <span class="label">Mkt.mgr</span>
        </button>
        <div class="collapse" id="menuMarketingManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.marketing-managers.create')); ?>">
                <span class="label">Add Marketing Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.marketing-managers.index')); ?>">
                <span class="label">All Marketing Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuGeneralManagers" style="background:transparent;">
            <i class="bi bi-person-badge me-2"></i> <span class="label">Gen.mgr</span>
        </button>
        <div class="collapse" id="menuGeneralManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-managers.create')); ?>">
                <span class="label">Add General Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-managers.index')); ?>">
                <span class="label">All General Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuDivisionalManagers" style="background:transparent;">
            <i class="bi bi-diagram-3 me-2"></i> <span class="label">D.c.mgr</span>
        </button>
        <div class="collapse" id="menuDivisionalManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.divisional-managers.create')); ?>">
                <span class="label">Add Divisional Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.divisional-managers.index')); ?>">
                <span class="label">All Divisional Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuCountryManagers" style="background:transparent;">
            <i class="bi bi-globe me-2"></i> <span class="label">C.mgr</span>
        </button>
        <div class="collapse" id="menuCountryManagers">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.country-managers.create')); ?>">
                <span class="label">Add Country Manager</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.country-managers.index')); ?>">
                <span class="label">All Country Managers</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuPersonalDirectory" style="background:transparent;">
            <i class="bi bi-person-lines-fill me-2"></i> <span class="label">Personal Directory</span>
        </button>
        <div class="collapse" id="menuPersonalDirectory">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.personal-directory.create')); ?>">
                <span class="label">Add Entry</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.personal-directory.index')); ?>">
                <span class="label">All Entries</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuGeneralReminders" style="background:transparent;">
            <i class="bi bi-bell me-2"></i> <span class="label">General Reminders</span>
        </button>
        <div class="collapse" id="menuGeneralReminders">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-reminders.create')); ?>">
                <span class="label">Add Reminder</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-reminders.index')); ?>">
                <span class="label">All Reminders</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuGeneralNotebook" style="background:transparent;">
            <i class="bi bi-journal-text me-2"></i> <span class="label">General NoteBook</span>
        </button>
        <div class="collapse" id="menuGeneralNotebook">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-notebook.create')); ?>">
                <span class="label">Add Note</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.general-notebook.index')); ?>">
                <span class="label">All Notes</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuItemCategory" style="background:transparent;">
            <i class="bi bi-tag me-2"></i> <span class="label">Item Category</span>
        </button>
        <div class="collapse" id="menuItemCategory">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.item-category.create')); ?>">
                <span class="label">Add Category</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.item-category.index')); ?>">
                <span class="label">All Categories</span>
            </a>
        </div>
    </div>

    
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuTransportMaster" style="background:transparent;">
            <i class="bi bi-truck me-2"></i> <span class="label">Transport Master</span>
        </button>
        <div class="collapse" id="menuTransportMaster">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.transport-master.create')); ?>">
                <span class="label">Add Transport</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.transport-master.index')); ?>">
                <span class="label">All Transports</span>
            </a>
        </div>
    </div>

    
    <?php if($user->isAdmin()): ?>
    <div class="mt-2">
        <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
            data-bs-toggle="collapse" data-bs-target="#menuUserManagement" style="background:transparent;">
            <i class="bi bi-people-fill me-2"></i> <span class="label">User Management</span>
        </button>
        <div class="collapse" id="menuUserManagement">
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.users.create')); ?>">
                <span class="label">Add User</span>
            </a>
            <a class="nav-link ms-3 d-flex align-items-center" href="<?php echo e(route('admin.users.index')); ?>">
                <span class="label">All Users</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
</nav>
<?php /**PATH C:\xampp\htdocs\bill-software\resources\views/layouts/partials/sidebar-nav.blade.php ENDPATH**/ ?>