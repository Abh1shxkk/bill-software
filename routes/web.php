<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\HsnCodeController;
use App\Http\Controllers\Admin\GeneralLedgerController;
use App\Http\Controllers\Admin\CashBankBookController;
use App\Http\Controllers\Admin\SaleLedgerController;
use App\Http\Controllers\Admin\PurchaseLedgerController;
use App\Http\Controllers\Admin\AllLedgerController;
use App\Http\Controllers\Admin\SalesManController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\AreaManagerController;
use App\Http\Controllers\Admin\RegionalManagerController;
use App\Http\Controllers\Admin\MarketingManagerController;
use App\Http\Controllers\Admin\GeneralManagerController;
use App\Http\Controllers\Admin\DivisionalManagerController;
use App\Http\Controllers\Admin\CountryManagerController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CustomerLedgerController;
use App\Http\Controllers\Admin\CustomerDueController;
use App\Http\Controllers\Admin\CustomerSpecialRateController;
use App\Http\Controllers\Admin\CustomerDiscountController;
use App\Http\Controllers\Admin\CustomerChallanController;
use App\Http\Controllers\Admin\CustomerPrescriptionController;
use App\Http\Controllers\Admin\CustomerCopyDiscountController;
use App\Http\Controllers\Admin\PersonalDirectoryController;
use App\Http\Controllers\Admin\GeneralReminderController;
use App\Http\Controllers\Admin\GeneralNotebookController;
use App\Http\Controllers\Admin\ItemCategoryController;
use App\Http\Controllers\Admin\TransportMasterController;
use App\Http\Controllers\Admin\SaleTransactionController;
use App\Http\Controllers\Admin\PurchaseTransactionController;
use App\Http\Controllers\Admin\SaleReturnController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\PurchaseChallanTransactionController;
use App\Http\Controllers\Admin\BreakageExpiryController;
use App\Http\Controllers\Admin\BreakageSupplierController;
use App\Http\Controllers\Admin\SaleChallanController;
use App\Http\Controllers\Admin\CreditNoteController;
use App\Http\Controllers\Admin\DebitNoteController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockTransferOutgoingController;
use App\Http\Controllers\Admin\StockTransferOutgoingReturnController;
use App\Http\Controllers\Admin\StockTransferIncomingController;
use App\Http\Controllers\Admin\StockTransferIncomingReturnController;
use App\Http\Controllers\Admin\SampleIssuedController;
use App\Http\Controllers\Admin\SampleReceivedController;
use App\Http\Controllers\Admin\GodownBreakageExpiryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\PurchaseReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    // All logged-in users go to admin dashboard
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('auth.login');
});

// Admin
Route::middleware(['admin', 'module.access'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management Routes (Admin Only - handled by module.access middleware)
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::post('users/multiple-delete', [UserController::class, 'multipleDelete'])->name('users.multiple-delete');
        
        // Company routes - MUST be before resource route
        Route::post('companies/multiple-delete', [CompanyController::class, 'multipleDelete'])->name('companies.multiple-delete');
        Route::get('companies/by-code/{code}', [CompanyController::class, 'getByCode'])->name('companies.by-code');
        Route::get('companies/get-all', [CompanyController::class, 'getAll'])->name('companies.get-all');
        Route::resource('companies', CompanyController::class);
        
        // Customer routes
        Route::post('customers/multiple-delete', [CustomerController::class, 'multipleDelete'])->name('customers.multiple-delete');
        Route::get('customers/{customer}/sales', [CustomerController::class, 'getSales'])->name('customers.sales');
        Route::get('customers/{customer}/challans', [CustomerController::class, 'challans'])->name('customers.challans');
        Route::resource('customers', CustomerController::class);
        Route::get('customers-all', [CustomerController::class, 'getAllCustomers'])->name('customers.all');
        
        // Customer Features Routes
        Route::get('customers/{customer}/ledger', [CustomerLedgerController::class, 'index'])->name('customers.ledger');
        Route::get('customers/{customer}/ledger/sale/{id}', [CustomerLedgerController::class, 'showSale'])->name('customers.ledger.sale');
        Route::get('customers/{customer}/ledger/sale-return/{id}', [CustomerLedgerController::class, 'showSaleReturn'])->name('customers.ledger.sale-return');
        Route::get('customers/{customer}/ledger/breakage-expiry/{id}', [CustomerLedgerController::class, 'showBreakageExpiry'])->name('customers.ledger.breakage-expiry');
        Route::post('customers/{customer}/ledger', [CustomerLedgerController::class, 'store'])->name('customers.ledger.store');
        Route::delete('customers/{customer}/ledger/{ledger}', [CustomerLedgerController::class, 'destroy'])->name('customers.ledger.destroy');
        
        Route::get('customers/{customer}/dues', [CustomerDueController::class, 'index'])->name('customers.dues');
        Route::get('customers/{customer}/dues/expiry-list', [CustomerDueController::class, 'expiryList'])->name('customers.dues.expiry-list');
        Route::post('customers/{customer}/dues', [CustomerDueController::class, 'store'])->name('customers.dues.store');
        Route::patch('customers/{customer}/dues/{due}/payment', [CustomerDueController::class, 'updatePayment'])->name('customers.dues.payment');
        Route::delete('customers/{customer}/dues/{due}', [CustomerDueController::class, 'destroy'])->name('customers.dues.destroy');
        
        Route::get('customers/{customer}/special-rates', [CustomerSpecialRateController::class, 'index'])->name('customers.special-rates');
        Route::post('customers/{customer}/special-rates', [CustomerSpecialRateController::class, 'store'])->name('customers.special-rates.store');
        Route::put('customers/{customer}/special-rates/{rate}', [CustomerSpecialRateController::class, 'update'])->name('customers.special-rates.update');
        Route::delete('customers/{customer}/special-rates/{rate}', [CustomerSpecialRateController::class, 'destroy'])->name('customers.special-rates.destroy');
        
        Route::get('customers/{customer}/discounts', [CustomerDiscountController::class, 'index'])->name('customers.discounts');
        Route::post('customers/{customer}/discounts', [CustomerDiscountController::class, 'store'])->name('customers.discounts.store');
        Route::put('customers/{customer}/discounts/{discount}', [CustomerDiscountController::class, 'update'])->name('customers.discounts.update');
        Route::delete('customers/{customer}/discounts/{discount}', [CustomerDiscountController::class, 'destroy'])->name('customers.discounts.destroy');
        
        // Note: customers/{customer}/challans route is defined above at line 78 using CustomerController::challans
        // which queries SaleChallanTransaction model for pending sale challans
        
        Route::get('customers/{customer}/prescriptions', [CustomerPrescriptionController::class, 'index'])->name('customers.prescriptions');
        Route::post('customers/{customer}/prescriptions', [CustomerPrescriptionController::class, 'store'])->name('customers.prescriptions.store');
        Route::put('customers/{customer}/prescriptions/{prescription}', [CustomerPrescriptionController::class, 'update'])->name('customers.prescriptions.update');
        Route::delete('customers/{customer}/prescriptions/{prescription}', [CustomerPrescriptionController::class, 'destroy'])->name('customers.prescriptions.destroy');
        
        Route::get('customers/{customer}/expiry-ledger', [CustomerLedgerController::class, 'expiryLedger'])->name('customers.expiry-ledger');
        Route::post('customers/{customer}/expiry-ledger', [CustomerLedgerController::class, 'storeExpiryLedger'])->name('customers.expiry-ledger.store');
        Route::delete('customers/{customer}/expiry-ledger/{ledger}', [CustomerLedgerController::class, 'destroyExpiryLedger'])->name('customers.expiry-ledger.destroy');
        
        Route::get('customers/{customer}/bills', [CustomerLedgerController::class, 'bills'])->name('customers.bills');
        
        Route::get('customers/{customer}/copy-discount', [CustomerCopyDiscountController::class, 'index'])->name('customers.copy-discount');
        Route::post('customers/{customer}/copy-discount', [CustomerCopyDiscountController::class, 'store'])->name('customers.copy-discount.store');
        Route::get('api/customer-discounts/{customerId}', [CustomerCopyDiscountController::class, 'getCustomerDiscounts'])->name('api.customer-discounts');
        
        // Item routes - MUST be before resource route
        Route::get('items/search', [ItemController::class, 'search'])->name('items.search');
        Route::get('items/all', [ItemController::class, 'getAllItems'])->name('items.all');
        Route::get('items/get-all', [ItemController::class, 'getAll'])->name('items.get-all');
        Route::get('items/get-by-code/{code}', [ItemController::class, 'getByCode'])->name('items.get-by-code');
        Route::get('items/{code}/last-batch', [BatchController::class, 'getLastBatch'])->name('items.last-batch');
        Route::post('items/multiple-delete', [ItemController::class, 'multipleDelete'])->name('items.multiple-delete');
        Route::get('api/item-total-qty/{itemId}', [ItemController::class, 'getItemTotalQty'])->name('api.item-total-qty');
        
        Route::resource('items', ItemController::class);
        Route::get('items/{item}/stock-ledger', [ItemController::class, 'stockLedger'])->name('items.stock-ledger');
        Route::get('items/{item}/stock-ledger-complete', [ItemController::class, 'stockLedgerComplete'])->name('items.stock-ledger-complete');
        Route::get('items/{item}/pending-orders', [ItemController::class, 'pendingOrders'])->name('items.pending-orders');
        
        // Transaction details routes for stock ledger
        Route::get('purchase-transactions/{id}/details', [ItemController::class, 'getPurchaseTransactionDetails'])->name('purchase-transactions.details');
        Route::get('sale-transactions/{id}/details', [ItemController::class, 'getSaleTransactionDetails'])->name('sale-transactions.details');
        Route::get('api/purchase-transactions/{id}/details', [ItemController::class, 'getPurchaseTransactionDetails'])->name('api.purchase-transactions.details');
        Route::get('api/sale-transactions/{id}/details', [ItemController::class, 'getSaleTransactionDetails'])->name('api.sale-transactions.details');
        Route::post('items/{item}/pending-orders', [ItemController::class, 'storePendingOrder'])->name('items.pending-orders.store');
        Route::put('items/pending-orders/{pendingOrder}/update-qty', [ItemController::class, 'updatePendingOrderQty'])->name('items.pending-orders.update-qty');
        Route::delete('items/{item}/pending-orders/{pendingOrder}', [ItemController::class, 'deletePendingOrder'])->name('items.pending-orders.delete');
        Route::get('items/{item}/godown-expiry', [ItemController::class, 'godownExpiry'])->name('items.godown-expiry');
        Route::post('items/{item}/godown-expiry', [ItemController::class, 'storeGodownExpiry'])->name('items.godown-expiry.store');
        Route::patch('items/{item}/godown-expiry/{godownExpiry}/mark-expired', [ItemController::class, 'markExpired'])->name('items.godown-expiry.mark-expired');
        Route::delete('items/{item}/godown-expiry/{godownExpiry}', [ItemController::class, 'deleteGodownExpiry'])->name('items.godown-expiry.delete');
        Route::get('items/{item}/expiry-ledger', [ItemController::class, 'expiryLedger'])->name('items.expiry-ledger');
        Route::get('items/expiry-ledger/data', [ItemController::class, 'getExpiryLedgerData'])->name('items.expiry-ledger.data');
        Route::get('items/expiry-ledger/export', [ItemController::class, 'exportExpiryLedger'])->name('items.expiry-ledger.export');
        Route::post('items/{item}/expiry-ledger', [ItemController::class, 'storeExpiryLedger'])->name('items.expiry-ledger.store');
        Route::delete('items/{item}/expiry-ledger/{expiryLedger}', [ItemController::class, 'deleteExpiryLedger'])->name('items.expiry-ledger.delete');
        
        // Breakage/Expiry Transaction Details Route
        Route::get('breakage-expiry/transaction/{id}/details', [BreakageExpiryController::class, 'getTransactionDetails'])->name('breakage-expiry.transaction.details');
        Route::get('breakage-expiry/transaction/{id}/adjustments', [BreakageExpiryController::class, 'getAdjustments'])->name('breakage-expiry.transaction.adjustments');
        
        // Batch routes - MUST be before resource route
        Route::get('batches/check-batch', [BatchController::class, 'checkBatch'])->name('batches.check-batch');
        Route::get('batches/all-batches/view', [BatchController::class, 'allBatches'])->name('batches.all');
        Route::get('batches/item/{itemId}/view', [BatchController::class, 'itemBatches'])->name('batches.item');
        Route::resource('batches', BatchController::class);
        Route::get('batches/{batch}/stock-ledger', [BatchController::class, 'stockLedger'])->name('batches.stock-ledger');
        Route::get('batches/expiry/report', [BatchController::class, 'expiryReport'])->name('batches.expiry-report');
        Route::get('api/item-batches/{itemId}', [BatchController::class, 'getItemBatches'])->name('api.item-batches');
        
        // Sales Reports
        Route::get('reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
        Route::get('reports/sales/sales-book', [SalesReportController::class, 'salesBook'])->name('reports.sales.sales-book');
        Route::get('reports/sales/sales-book-gstr', [SalesReportController::class, 'salesBookGstr'])->name('reports.sales.sales-book-gstr');
        Route::get('reports/sales/sales-book-extra-charges', [SalesReportController::class, 'salesBookExtraCharges'])->name('reports.sales.sales-book-extra-charges');
        Route::get('reports/sales/sales-book-tcs', [SalesReportController::class, 'salesBookTcs'])->name('reports.sales.sales-book-tcs');
        Route::get('reports/sales/tcs-eligibility', [SalesReportController::class, 'tcsEligibility'])->name('reports.sales.tcs-eligibility');
        Route::get('reports/sales/tds-input', [SalesReportController::class, 'tdsInput'])->name('reports.sales.tds-input');
        Route::get('reports/sales/sales-book-party-wise', [SalesReportController::class, 'salesBookPartyWise'])->name('reports.sales.sales-book-party-wise');
        Route::get('reports/sales/day-sales-summary-item-wise', [SalesReportController::class, 'daySalesSummaryItemWise'])->name('reports.sales.day-sales-summary-item-wise');
        Route::get('reports/sales/sales-summary', [SalesReportController::class, 'salesSummary'])->name('reports.sales.sales-summary');
        Route::get('reports/sales/sales-bills-printing', [SalesReportController::class, 'salesBillsPrinting'])->name('reports.sales.sales-bills-printing');
        Route::get('reports/sales/sale-sheet', [SalesReportController::class, 'saleSheet'])->name('reports.sales.sale-sheet');
        Route::get('reports/sales/dispatch-sheet', [SalesReportController::class, 'dispatchSheet'])->name('reports.sales.dispatch-sheet');
        Route::get('reports/sales/sale-return-book-item-wise', [SalesReportController::class, 'saleReturnBookItemWise'])->name('reports.sales.sale-return-book-item-wise');
        Route::get('reports/sales/local-central-sale-register', [SalesReportController::class, 'localCentralSaleRegister'])->name('reports.sales.local-central-sale-register');
        Route::get('reports/sales/sale-challan-reports', [SalesReportController::class, 'saleChallanReports'])->name('reports.sales.sale-challan-reports');
        Route::get('reports/sales/sale-challan-book', [SalesReportController::class, 'saleChallanBook'])->name('reports.sales.sale-challan-book');
        Route::get('reports/sales/pending-challans', [SalesReportController::class, 'pendingChallans'])->name('reports.sales.pending-challans');
        Route::get('reports/sales/sales-stock-summary', [SalesReportController::class, 'salesStockSummary'])->name('reports.sales.sales-stock-summary');
        Route::get('reports/sales/customer-visit-status', [SalesReportController::class, 'customerVisitStatus'])->name('reports.sales.customer-visit-status');
        Route::get('reports/sales/shortage-report', [SalesReportController::class, 'shortageReport'])->name('reports.sales.shortage-report');
        Route::get('reports/sales/sale-return-list', [SalesReportController::class, 'saleReturnList'])->name('reports.sales.sale-return-list');
        
        // Miscellaneous Sales Analysis Reports
        // Salesman Wise Sales Sub-Options
        Route::get('reports/sales/salesman-wise-sales', [SalesReportController::class, 'salesmanWiseSales'])->name('reports.sales.salesman-wise-sales');
        Route::get('reports/sales/salesman-wise-sales/all-salesman', [SalesReportController::class, 'salesmanWiseSalesAllSalesman'])->name('reports.sales.salesman-wise-sales.all-salesman');
        Route::get('reports/sales/salesman-wise-sales/bill-wise', [SalesReportController::class, 'salesmanWiseSalesBillWise'])->name('reports.sales.salesman-wise-sales.bill-wise');
        Route::get('reports/sales/salesman-wise-sales/customer-wise', [SalesReportController::class, 'salesmanWiseSalesCustomerWise'])->name('reports.sales.salesman-wise-sales.customer-wise');
        Route::get('reports/sales/salesman-wise-sales/item-wise', [SalesReportController::class, 'salesmanWiseSalesItemWise'])->name('reports.sales.salesman-wise-sales.item-wise');
        Route::get('reports/sales/salesman-wise-sales/company-wise', [SalesReportController::class, 'salesmanWiseSalesCompanyWise'])->name('reports.sales.salesman-wise-sales.company-wise');
        Route::get('reports/sales/salesman-wise-sales/area-wise', [SalesReportController::class, 'salesmanWiseSalesAreaWise'])->name('reports.sales.salesman-wise-sales.area-wise');
        Route::get('reports/sales/salesman-wise-sales/route-wise', [SalesReportController::class, 'salesmanWiseSalesRouteWise'])->name('reports.sales.salesman-wise-sales.route-wise');
        Route::get('reports/sales/salesman-wise-sales/state-wise', [SalesReportController::class, 'salesmanWiseSalesStateWise'])->name('reports.sales.salesman-wise-sales.state-wise');
        Route::get('reports/sales/salesman-wise-sales/salesman-wise', [SalesReportController::class, 'salesmanWiseSalesSalesmanWise'])->name('reports.sales.salesman-wise-sales.salesman-wise');
        Route::get('reports/sales/salesman-wise-sales/salesman-item-wise', [SalesReportController::class, 'salesmanWiseSalesSalesmanItemWise'])->name('reports.sales.salesman-wise-sales.salesman-item-wise');
        Route::get('reports/sales/salesman-wise-sales/item-invoice-wise', [SalesReportController::class, 'salesmanWiseSalesItemInvoiceWise'])->name('reports.sales.salesman-wise-sales.item-invoice-wise');
        Route::get('reports/sales/salesman-wise-sales/invoice-item-wise', [SalesReportController::class, 'salesmanWiseSalesInvoiceItemWise'])->name('reports.sales.salesman-wise-sales.invoice-item-wise');
        Route::get('reports/sales/salesman-wise-sales/month-wise-summary', [SalesReportController::class, 'salesmanWiseSalesMonthWiseSummary'])->name('reports.sales.salesman-wise-sales.month-wise-summary');
        Route::get('reports/sales/salesman-wise-sales/sale-book', [SalesReportController::class, 'salesmanWiseSalesSaleBook'])->name('reports.sales.salesman-wise-sales.sale-book');
        Route::get('reports/sales/salesman-wise-sales/monthly-target', [SalesReportController::class, 'salesmanWiseSalesMonthlyTarget'])->name('reports.sales.salesman-wise-sales.monthly-target');
        
        Route::get('reports/sales/area-wise-sale', [SalesReportController::class, 'areaWiseSale'])->name('reports.sales.area-wise-sale');
        
        // Area Wise Sales - Separate Routes
        Route::get('reports/sales/area-wise-sales/all-area', [SalesReportController::class, 'areaWiseSalesAllArea'])->name('reports.sales.area-wise-sales.all-area');
        Route::get('reports/sales/area-wise-sales/bill-wise', [SalesReportController::class, 'areaWiseSalesBillWise'])->name('reports.sales.area-wise-sales.bill-wise');
        Route::get('reports/sales/area-wise-sales/customer-wise', [SalesReportController::class, 'areaWiseSalesCustomerWise'])->name('reports.sales.area-wise-sales.customer-wise');
        Route::get('reports/sales/area-wise-sales/item-wise', [SalesReportController::class, 'areaWiseSalesItemWise'])->name('reports.sales.area-wise-sales.item-wise');
        Route::get('reports/sales/area-wise-sales/company-wise', [SalesReportController::class, 'areaWiseSalesCompanyWise'])->name('reports.sales.area-wise-sales.company-wise');
        Route::get('reports/sales/area-wise-sales/salesman-wise', [SalesReportController::class, 'areaWiseSalesSalesmanWise'])->name('reports.sales.area-wise-sales.salesman-wise');
        Route::get('reports/sales/area-wise-sales/route-wise', [SalesReportController::class, 'areaWiseSalesRouteWise'])->name('reports.sales.area-wise-sales.route-wise');
        Route::get('reports/sales/area-wise-sales/state-wise', [SalesReportController::class, 'areaWiseSalesStateWise'])->name('reports.sales.area-wise-sales.state-wise');
        Route::get('reports/sales/area-wise-sales/item-invoice-wise', [SalesReportController::class, 'areaWiseSalesItemInvoiceWise'])->name('reports.sales.area-wise-sales.item-invoice-wise');
        Route::get('reports/sales/area-wise-sales/invoice-item-wise', [SalesReportController::class, 'areaWiseSalesInvoiceItemWise'])->name('reports.sales.area-wise-sales.invoice-item-wise');
        Route::get('reports/sales/area-wise-sales/sale-book', [SalesReportController::class, 'areaWiseSalesSaleBook'])->name('reports.sales.area-wise-sales.sale-book');
        Route::get('reports/sales/area-wise-sales/month-wise/area-wise', [SalesReportController::class, 'areaWiseSalesMonthWiseAreaWise'])->name('reports.sales.area-wise-sales.month-wise.area-wise');
        Route::get('reports/sales/area-wise-sales/month-wise/area-item-wise', [SalesReportController::class, 'areaWiseSalesMonthWiseAreaItemWise'])->name('reports.sales.area-wise-sales.month-wise.area-item-wise');
        
        Route::get('reports/sales/route-wise-sale', [SalesReportController::class, 'routeWiseSale'])->name('reports.sales.route-wise-sale');
        
        // Route Wise Sale - Separate Routes
        Route::get('reports/sales/route-wise-sale/all-route', [SalesReportController::class, 'routeWiseSaleAllRoute'])->name('reports.sales.route-wise-sale.all-route');
        Route::get('reports/sales/route-wise-sale/bill-wise', [SalesReportController::class, 'routeWiseSaleBillWise'])->name('reports.sales.route-wise-sale.bill-wise');
        Route::get('reports/sales/route-wise-sale/customer-wise', [SalesReportController::class, 'routeWiseSaleCustomerWise'])->name('reports.sales.route-wise-sale.customer-wise');
        Route::get('reports/sales/route-wise-sale/item-wise', [SalesReportController::class, 'routeWiseSaleItemWise'])->name('reports.sales.route-wise-sale.item-wise');
        Route::get('reports/sales/route-wise-sale/company-wise', [SalesReportController::class, 'routeWiseSaleCompanyWise'])->name('reports.sales.route-wise-sale.company-wise');
        Route::get('reports/sales/route-wise-sale/salesman-wise', [SalesReportController::class, 'routeWiseSaleSalesmanWise'])->name('reports.sales.route-wise-sale.salesman-wise');
        Route::get('reports/sales/route-wise-sale/area-wise', [SalesReportController::class, 'routeWiseSaleAreaWise'])->name('reports.sales.route-wise-sale.area-wise');
        Route::get('reports/sales/route-wise-sale/state-wise', [SalesReportController::class, 'routeWiseSaleStateWise'])->name('reports.sales.route-wise-sale.state-wise');
        Route::get('reports/sales/route-wise-sale/item-invoice-wise', [SalesReportController::class, 'routeWiseSaleItemInvoiceWise'])->name('reports.sales.route-wise-sale.item-invoice-wise');
        Route::get('reports/sales/route-wise-sale/invoice-item-wise', [SalesReportController::class, 'routeWiseSaleInvoiceItemWise'])->name('reports.sales.route-wise-sale.invoice-item-wise');
        Route::get('reports/sales/route-wise-sale/sale-book', [SalesReportController::class, 'routeWiseSaleSaleBook'])->name('reports.sales.route-wise-sale.sale-book');
        Route::get('reports/sales/route-wise-sale/month-wise/route-wise', [SalesReportController::class, 'routeWiseSaleMonthWiseRouteWise'])->name('reports.sales.route-wise-sale.month-wise.route-wise');
        Route::get('reports/sales/route-wise-sale/month-wise/route-item-wise', [SalesReportController::class, 'routeWiseSaleMonthWiseRouteItemWise'])->name('reports.sales.route-wise-sale.month-wise.route-item-wise');
        
        Route::get('reports/sales/state-wise-sale', [SalesReportController::class, 'stateWiseSale'])->name('reports.sales.state-wise-sale');
        
        // State Wise Sale - Separate Routes
        Route::get('reports/sales/state-wise-sale/all-state', [SalesReportController::class, 'stateWiseSaleAllState'])->name('reports.sales.state-wise-sale.all-state');
        Route::get('reports/sales/state-wise-sale/bill-wise', [SalesReportController::class, 'stateWiseSaleBillWise'])->name('reports.sales.state-wise-sale.bill-wise');
        Route::get('reports/sales/state-wise-sale/customer-wise', [SalesReportController::class, 'stateWiseSaleCustomerWise'])->name('reports.sales.state-wise-sale.customer-wise');
        Route::get('reports/sales/state-wise-sale/item-wise', [SalesReportController::class, 'stateWiseSaleItemWise'])->name('reports.sales.state-wise-sale.item-wise');
        Route::get('reports/sales/state-wise-sale/company-wise', [SalesReportController::class, 'stateWiseSaleCompanyWise'])->name('reports.sales.state-wise-sale.company-wise');
        Route::get('reports/sales/state-wise-sale/salesman-wise', [SalesReportController::class, 'stateWiseSaleSalesmanWise'])->name('reports.sales.state-wise-sale.salesman-wise');
        Route::get('reports/sales/state-wise-sale/area-wise', [SalesReportController::class, 'stateWiseSaleAreaWise'])->name('reports.sales.state-wise-sale.area-wise');
        Route::get('reports/sales/state-wise-sale/route-wise', [SalesReportController::class, 'stateWiseSaleRouteWise'])->name('reports.sales.state-wise-sale.route-wise');
        Route::get('reports/sales/state-wise-sale/invoice-item-wise', [SalesReportController::class, 'stateWiseSaleInvoiceItemWise'])->name('reports.sales.state-wise-sale.invoice-item-wise');
        Route::get('reports/sales/state-wise-sale/month-wise/state-wise', [SalesReportController::class, 'stateWiseSaleMonthWiseState'])->name('reports.sales.state-wise-sale.month-wise.state-wise');
        Route::get('reports/sales/state-wise-sale/month-wise/state-item-wise', [SalesReportController::class, 'stateWiseSaleMonthWiseStateItem'])->name('reports.sales.state-wise-sale.month-wise.state-item-wise');
        
        Route::get('reports/sales/customer-wise-sale', [SalesReportController::class, 'customerWiseSale'])->name('reports.sales.customer-wise-sale');
        
        // Customer Wise Sale - Separate Routes
        Route::get('reports/sales/customer-wise-sale/all-customer', [SalesReportController::class, 'customerWiseSaleAllCustomer'])->name('reports.sales.customer-wise-sale.all-customer');
        Route::get('reports/sales/customer-wise-sale/bill-wise', [SalesReportController::class, 'customerWiseSaleBillWise'])->name('reports.sales.customer-wise-sale.bill-wise');
        Route::get('reports/sales/customer-wise-sale/item-wise', [SalesReportController::class, 'customerWiseSaleItemWise'])->name('reports.sales.customer-wise-sale.item-wise');
        Route::get('reports/sales/customer-wise-sale/company-wise', [SalesReportController::class, 'customerWiseSaleCompanyWise'])->name('reports.sales.customer-wise-sale.company-wise');
        Route::get('reports/sales/customer-wise-sale/item-invoice-wise', [SalesReportController::class, 'customerWiseSaleItemInvoiceWise'])->name('reports.sales.customer-wise-sale.item-invoice-wise');
        Route::get('reports/sales/customer-wise-sale/invoice-item-wise', [SalesReportController::class, 'customerWiseSaleInvoiceItemWise'])->name('reports.sales.customer-wise-sale.invoice-item-wise');
        Route::get('reports/sales/customer-wise-sale/quantity-wise-summary', [SalesReportController::class, 'customerWiseSaleQtySummary'])->name('reports.sales.customer-wise-sale.quantity-wise-summary');
        Route::get('reports/sales/customer-wise-sale/party-billwise-volume-discount', [SalesReportController::class, 'customerWiseSalePartyVolumeDiscount'])->name('reports.sales.customer-wise-sale.party-billwise-volume-discount');
        Route::get('reports/sales/customer-wise-sale/sale-with-area', [SalesReportController::class, 'customerWiseSaleWithArea'])->name('reports.sales.customer-wise-sale.sale-with-area');
        Route::get('reports/sales/customer-wise-sale/month-wise/customer-wise', [SalesReportController::class, 'customerWiseSaleMonthWiseCustomer'])->name('reports.sales.customer-wise-sale.month-wise.customer-wise');
        Route::get('reports/sales/customer-wise-sale/month-wise/customer-item-wise', [SalesReportController::class, 'customerWiseSaleMonthWiseCustomerItem'])->name('reports.sales.customer-wise-sale.month-wise.customer-item-wise');

        Route::get('reports/sales/company-wise-sales', [SalesReportController::class, 'companyWiseSales'])->name('reports.sales.company-wise-sales');
        
        // Company Wise Sales - Separate Routes
        Route::get('reports/sales/company-wise-sales/all-company', [SalesReportController::class, 'companyWiseSalesAllCompany'])->name('reports.sales.company-wise-sales.all-company');
        Route::get('reports/sales/company-wise-sales/bill-wise', [SalesReportController::class, 'companyWiseSalesBillWise'])->name('reports.sales.company-wise-sales.bill-wise');
        Route::get('reports/sales/company-wise-sales/item-wise', [SalesReportController::class, 'companyWiseSalesItemWise'])->name('reports.sales.company-wise-sales.item-wise');
        Route::get('reports/sales/company-wise-sales/salesman-wise', [SalesReportController::class, 'companyWiseSalesSalesmanWise'])->name('reports.sales.company-wise-sales.salesman-wise');
        Route::get('reports/sales/company-wise-sales/area-wise', [SalesReportController::class, 'companyWiseSalesAreaWise'])->name('reports.sales.company-wise-sales.area-wise');
        Route::get('reports/sales/company-wise-sales/route-wise', [SalesReportController::class, 'companyWiseSalesRouteWise'])->name('reports.sales.company-wise-sales.route-wise');
        Route::get('reports/sales/company-wise-sales/customer-wise', [SalesReportController::class, 'companyWiseSalesCustomerWise'])->name('reports.sales.company-wise-sales.customer-wise');
        Route::get('reports/sales/company-wise-sales/customer-item-invoice-wise', [SalesReportController::class, 'companyWiseSalesCustomerItemInvoiceWise'])->name('reports.sales.company-wise-sales.customer-item-invoice-wise');
        Route::get('reports/sales/company-wise-sales/customer-item-wise', [SalesReportController::class, 'companyWiseSalesCustomerItemWise'])->name('reports.sales.company-wise-sales.customer-item-wise');
        Route::get('reports/sales/company-wise-sales/month-wise/company-item-wise', [SalesReportController::class, 'companyWiseSalesMonthWiseCompanyItem'])->name('reports.sales.company-wise-sales.month-wise.company-item-wise');
        Route::get('reports/sales/company-wise-sales/month-wise/company-customer-wise', [SalesReportController::class, 'companyWiseSalesMonthWiseCompanyCustomer'])->name('reports.sales.company-wise-sales.month-wise.company-customer-wise');

        Route::get('reports/sales/item-wise-sales', [SalesReportController::class, 'itemWiseSales'])->name('reports.sales.item-wise-sales');
        
        // Item Wise Sales - Separate Routes
        Route::get('reports/sales/item-wise-sales/all-item-sale', [SalesReportController::class, 'itemWiseSalesAllItemSale'])->name('reports.sales.item-wise-sales.all-item-sale');
        Route::get('reports/sales/item-wise-sales/all-item-summary', [SalesReportController::class, 'itemWiseSalesAllItemSummary'])->name('reports.sales.item-wise-sales.all-item-summary');
        Route::get('reports/sales/item-wise-sales/bill-wise', [SalesReportController::class, 'itemWiseSalesBillWise'])->name('reports.sales.item-wise-sales.bill-wise');
        Route::get('reports/sales/item-wise-sales/salesman-wise', [SalesReportController::class, 'itemWiseSalesSalesmanWise'])->name('reports.sales.item-wise-sales.salesman-wise');
        Route::get('reports/sales/item-wise-sales/area-wise', [SalesReportController::class, 'itemWiseSalesAreaWise'])->name('reports.sales.item-wise-sales.area-wise');
        Route::get('reports/sales/item-wise-sales/area-wise-matrix', [SalesReportController::class, 'itemWiseSalesAreaWiseMatrix'])->name('reports.sales.item-wise-sales.area-wise-matrix');
        Route::get('reports/sales/item-wise-sales/route-wise', [SalesReportController::class, 'itemWiseSalesRouteWise'])->name('reports.sales.item-wise-sales.route-wise');
        Route::get('reports/sales/item-wise-sales/state-wise', [SalesReportController::class, 'itemWiseSalesStateWise'])->name('reports.sales.item-wise-sales.state-wise');
        Route::get('reports/sales/item-wise-sales/customer-wise', [SalesReportController::class, 'itemWiseSalesCustomerWise'])->name('reports.sales.item-wise-sales.customer-wise');
        Route::get('reports/sales/item-wise-sales/below-cost-item-sale', [SalesReportController::class, 'itemWiseSalesBelowCostItemSale'])->name('reports.sales.item-wise-sales.below-cost-item-sale');

        Route::get('reports/sales/discount-wise-sales', [SalesReportController::class, 'discountWiseSales'])->name('reports.sales.discount-wise-sales');
        
        // Discount Wise Sales - Separate Routes
        Route::get('reports/sales/discount-wise-sales/all-discount', [SalesReportController::class, 'discountWiseSalesAllDiscount'])->name('reports.sales.discount-wise-sales.all-discount');
        Route::get('reports/sales/discount-wise-sales/item-wise', [SalesReportController::class, 'discountWiseSalesItemWise'])->name('reports.sales.discount-wise-sales.item-wise');
        Route::get('reports/sales/discount-wise-sales/item-wise-invoice-wise', [SalesReportController::class, 'discountWiseSalesItemWiseInvoiceWise'])->name('reports.sales.discount-wise-sales.item-wise-invoice-wise');

        // Other Sales Reports
        Route::get('reports/sales/other/cash-coll-trnf-sale', [SalesReportController::class, 'cashCollTrnfSale'])->name('reports.sales.other.cash-coll-trnf-sale');
        Route::get('reports/sales/other/sale-bill-wise-discount', [SalesReportController::class, 'saleBillWiseDiscount'])->name('reports.sales.other.sale-bill-wise-discount');
        Route::get('reports/sales/other/sales-book-with-return', [SalesReportController::class, 'salesBookWithReturn'])->name('reports.sales.other.sales-book-with-return');
        Route::get('reports/sales/other/rate-difference', [SalesReportController::class, 'rateDifference'])->name('reports.sales.other.rate-difference');
        Route::get('reports/sales/other/sales-matrix', [SalesReportController::class, 'salesMatrix'])->name('reports.sales.other.sales-matrix');
        Route::get('reports/sales/other/minus-qty-sale', [SalesReportController::class, 'minusQtySale'])->name('reports.sales.other.minus-qty-sale');
        Route::get('reports/sales/other/sales-details', [SalesReportController::class, 'salesDetails'])->name('reports.sales.other.sales-details');
        Route::get('reports/sales/other/invoice-documents', [SalesReportController::class, 'invoiceDocuments'])->name('reports.sales.other.invoice-documents');
        Route::get('reports/sales/other/sale-remarks', [SalesReportController::class, 'saleRemarks'])->name('reports.sales.other.sale-remarks');
        Route::get('reports/sales/other/item-wise-discount', [SalesReportController::class, 'itemWiseDiscount'])->name('reports.sales.other.item-wise-discount');
        Route::get('reports/sales/other/item-wise-scheme', [SalesReportController::class, 'itemWiseScheme'])->name('reports.sales.other.item-wise-scheme');
        Route::get('reports/sales/other/tax-percentage-wise-sale', [SalesReportController::class, 'taxPercentageWiseSale'])->name('reports.sales.other.tax-percentage-wise-sale');
        Route::get('reports/sales/other/transaction-book-address', [SalesReportController::class, 'transactionBookAddress'])->name('reports.sales.other.transaction-book-address');
        Route::get('reports/sales/other/sale-stock-detail', [SalesReportController::class, 'saleStockDetail'])->name('reports.sales.other.sale-stock-detail');
        Route::get('reports/sales/other/customer-stock-details', [SalesReportController::class, 'customerStockDetails'])->name('reports.sales.other.customer-stock-details');
        Route::get('reports/sales/other/gst-sale-book', [SalesReportController::class, 'gstSaleBook'])->name('reports.sales.other.gst-sale-book');
        Route::get('reports/sales/other/customer-consistency', [SalesReportController::class, 'customerConsistency'])->name('reports.sales.other.customer-consistency');
        Route::get('reports/sales/other/sale-return-adjustment', [SalesReportController::class, 'saleReturnAdjustment'])->name('reports.sales.other.sale-return-adjustment');
        Route::get('reports/sales/other/pending-orders', [SalesReportController::class, 'pendingOrders'])->name('reports.sales.other.pending-orders');
        Route::get('reports/sales/other/st38-outword', [SalesReportController::class, 'st38Outword'])->name('reports.sales.other.st38-outword');
        Route::get('reports/sales/other/frige-item', [SalesReportController::class, 'frigeItem'])->name('reports.sales.other.frige-item');
        Route::get('reports/sales/other/volume-discount', [SalesReportController::class, 'volumeDiscount'])->name('reports.sales.other.volume-discount');
        Route::get('reports/sales/other/party-volume-discount', [SalesReportController::class, 'partyVolumeDiscount'])->name('reports.sales.other.party-volume-discount');
        Route::get('reports/sales/other/schedule-h1-drugs', [SalesReportController::class, 'scheduleH1Drugs'])->name('reports.sales.other.schedule-h1-drugs');
        Route::get('reports/sales/other/sale-book-sc', [SalesReportController::class, 'saleBookSc'])->name('reports.sales.other.sale-book-sc');
        Route::get('reports/sales/other/sale-book-summarised', [SalesReportController::class, 'saleBookSummarised'])->name('reports.sales.other.sale-book-summarised');

        Route::get('reports/sales/salesman-level-sale', [SalesReportController::class, 'salesmanLevelSale'])->name('reports.sales.salesman-level-sale');
        Route::get('reports/sales/scheme-issued', [SalesReportController::class, 'schemeIssued'])->name('reports.sales.scheme-issued');
        Route::get('reports/sales/mrp-wise-sales', [SalesReportController::class, 'mrpWiseSales'])->name('reports.sales.mrp-wise-sales');
        Route::get('reports/sales/display-amount-report', [SalesReportController::class, 'displayAmountReport'])->name('reports.sales.display-amount-report');
        Route::get('reports/sales/cancelled-invoices', [SalesReportController::class, 'cancelledInvoices'])->name('reports.sales.cancelled-invoices');
        Route::get('reports/sales/missing-invoices', [SalesReportController::class, 'missingInvoices'])->name('reports.sales.missing-invoices');
        
        Route::get('reports/sales/export-csv', [SalesReportController::class, 'exportCsv'])->name('reports.sales.export-csv');
        Route::get('reports/sales/export-pdf', [SalesReportController::class, 'exportPdf'])->name('reports.sales.export-pdf');
        Route::get('reports/sales/chart-data', [SalesReportController::class, 'getChartData'])->name('reports.sales.chart-data');
        
        // Purchase Reports - Main Index
        Route::get('reports/purchase', [PurchaseReportController::class, 'index'])->name('reports.purchase');
        Route::get('reports/purchase/export-csv', [PurchaseReportController::class, 'exportCsv'])->name('reports.purchase.export-csv');
        Route::get('reports/purchase/export-pdf', [PurchaseReportController::class, 'exportPdf'])->name('reports.purchase.export-pdf');
        
        // Purchase Reports - Individual Reports
        Route::get('reports/purchase/purchase-book', [PurchaseReportController::class, 'purchaseBook'])->name('reports.purchase.purchase-book');
        Route::get('reports/purchase/purchase-book-gstr', [PurchaseReportController::class, 'purchaseBookGstr'])->name('reports.purchase.purchase-book-gstr');
        Route::get('reports/purchase/purchase-book-tcs', [PurchaseReportController::class, 'purchaseBookTcs'])->name('reports.purchase.purchase-book-tcs');
        Route::get('reports/purchase/tds-output', [PurchaseReportController::class, 'tdsOutput'])->name('reports.purchase.tds-output');
        Route::get('reports/purchase/purchase-book-sale-value', [PurchaseReportController::class, 'purchaseBookSaleValue'])->name('reports.purchase.purchase-book-sale-value');
        Route::get('reports/purchase/party-wise-purchase', [PurchaseReportController::class, 'partyWisePurchase'])->name('reports.purchase.party-wise-purchase');
        Route::get('reports/purchase/monthly-purchase-summary', [PurchaseReportController::class, 'monthlyPurchaseSummary'])->name('reports.purchase.monthly-purchase-summary');
        Route::get('reports/purchase/debit-credit-note', [PurchaseReportController::class, 'debitCreditNote'])->name('reports.purchase.debit-credit-note');
        Route::get('reports/purchase/day-purchase-summary', [PurchaseReportController::class, 'dayPurchaseSummary'])->name('reports.purchase.day-purchase-summary');
        Route::get('reports/purchase/purchase-return-item-wise', [PurchaseReportController::class, 'purchaseReturnItemWise'])->name('reports.purchase.purchase-return-item-wise');
        Route::get('reports/purchase/local-central-register', [PurchaseReportController::class, 'localCentralRegister'])->name('reports.purchase.local-central-register');
        Route::get('reports/purchase/purchase-voucher-detail', [PurchaseReportController::class, 'purchaseVoucherDetail'])->name('reports.purchase.purchase-voucher-detail');
        Route::get('reports/purchase/short-expiry-received', [PurchaseReportController::class, 'shortExpiryReceived'])->name('reports.purchase.short-expiry-received');
        Route::get('reports/purchase/purchase-return-list', [PurchaseReportController::class, 'purchaseReturnList'])->name('reports.purchase.purchase-return-list');
        
        // GST SET OFF Reports
        Route::get('reports/purchase/gst-set-off', [PurchaseReportController::class, 'gstSetOff'])->name('reports.purchase.gst-set-off');
        Route::get('reports/purchase/gst-set-off-gstr', [PurchaseReportController::class, 'gstSetOffGstr'])->name('reports.purchase.gst-set-off-gstr');
        
        // Purchase Challan Reports
        Route::get('reports/purchase/challan/purchase-challan-book', [PurchaseReportController::class, 'purchaseChallanBook'])->name('reports.purchase.challan.purchase-challan-book');
        Route::get('reports/purchase/challan/pending-challans', [PurchaseReportController::class, 'pendingChallans'])->name('reports.purchase.challan.pending-challans');
        
        // Miscellaneous Purchase Analysis
        Route::get('reports/purchase/misc/purchase-with-item-details', [PurchaseReportController::class, 'purchaseWithItemDetails'])->name('reports.purchase.misc.purchase-with-item-details');
        
        // Supplier Wise Purchase Submenu
        Route::get('reports/purchase/misc/supplier/all-supplier', [PurchaseReportController::class, 'supplierAllSupplier'])->name('reports.purchase.misc.supplier.all-supplier');
        Route::get('reports/purchase/misc/supplier/all-supplier/print', [PurchaseReportController::class, 'supplierAllSupplierPrint'])->name('reports.purchase.misc.supplier.all-supplier.print');
        Route::get('reports/purchase/misc/supplier/bill-wise', [PurchaseReportController::class, 'supplierBillWise'])->name('reports.purchase.misc.supplier.bill-wise');
        Route::get('reports/purchase/misc/supplier/bill-wise/print', [PurchaseReportController::class, 'supplierBillWisePrint'])->name('reports.purchase.misc.supplier.bill-wise.print');
        Route::get('reports/purchase/misc/supplier/item-wise', [PurchaseReportController::class, 'supplierItemWise'])->name('reports.purchase.misc.supplier.item-wise');
        Route::get('reports/purchase/misc/supplier/item-wise/print', [PurchaseReportController::class, 'supplierItemWisePrint'])->name('reports.purchase.misc.supplier.item-wise.print');
        Route::get('reports/purchase/misc/supplier/item-invoice-wise', [PurchaseReportController::class, 'supplierItemInvoiceWise'])->name('reports.purchase.misc.supplier.item-invoice-wise');
        Route::get('reports/purchase/misc/supplier/item-invoice-wise/print', [PurchaseReportController::class, 'supplierItemInvoiceWisePrint'])->name('reports.purchase.misc.supplier.item-invoice-wise.print');
        Route::get('reports/purchase/misc/supplier/invoice-item-wise', [PurchaseReportController::class, 'supplierInvoiceItemWise'])->name('reports.purchase.misc.supplier.invoice-item-wise');
        Route::get('reports/purchase/misc/supplier/invoice-item-wise/print', [PurchaseReportController::class, 'supplierInvoiceItemWisePrint'])->name('reports.purchase.misc.supplier.invoice-item-wise.print');

        // Company Wise Purchase Submenu
        Route::get('reports/purchase/misc/company/all-company', [PurchaseReportController::class, 'companyAllCompany'])->name('reports.purchase.misc.company.all-company');
        Route::get('reports/purchase/misc/company/all-company/print', [PurchaseReportController::class, 'companyAllCompanyPrint'])->name('reports.purchase.misc.company.all-company.print');
        Route::get('reports/purchase/misc/company/item-wise', [PurchaseReportController::class, 'companyItemWise'])->name('reports.purchase.misc.company.item-wise');
        Route::get('reports/purchase/misc/company/item-wise/print', [PurchaseReportController::class, 'companyItemWisePrint'])->name('reports.purchase.misc.company.item-wise.print');
        Route::get('reports/purchase/misc/company/party-wise', [PurchaseReportController::class, 'companyPartyWise'])->name('reports.purchase.misc.company.party-wise');
        Route::get('reports/purchase/misc/company/party-wise/print', [PurchaseReportController::class, 'companyPartyWisePrint'])->name('reports.purchase.misc.company.party-wise.print');

        // Item Wise Purchase Submenu
        Route::get('reports/purchase/misc/item/bill-wise', [PurchaseReportController::class, 'itemBillWise'])->name('reports.purchase.misc.item.bill-wise');
        Route::get('reports/purchase/misc/item/bill-wise/print', [PurchaseReportController::class, 'itemBillWisePrint'])->name('reports.purchase.misc.item.bill-wise.print');
        Route::get('reports/purchase/misc/item/all-item-purchase', [PurchaseReportController::class, 'itemAllItemPurchase'])->name('reports.purchase.misc.item.all-item-purchase');
        Route::get('reports/purchase/misc/item/all-item-purchase/print', [PurchaseReportController::class, 'itemAllItemPurchasePrint'])->name('reports.purchase.misc.item.all-item-purchase.print');
        
        // Schemed Received Submenu
        Route::get('reports/purchase/misc/schemed/free-schemed', [PurchaseReportController::class, 'schemedFreeSchemed'])->name('reports.purchase.misc.schemed.free-schemed');
        Route::get('reports/purchase/misc/schemed/free-schemed/print', [PurchaseReportController::class, 'schemedFreeSchemedPrint'])->name('reports.purchase.misc.schemed.free-schemed.print');
        Route::get('reports/purchase/misc/schemed/half-schemed', [PurchaseReportController::class, 'schemedHalfSchemed'])->name('reports.purchase.misc.schemed.half-schemed');
        Route::get('reports/purchase/misc/schemed/half-schemed/print', [PurchaseReportController::class, 'schemedHalfSchemedPrint'])->name('reports.purchase.misc.schemed.half-schemed.print');
        Route::get('reports/purchase/misc/schemed/free-without-qty', [PurchaseReportController::class, 'schemedFreeWithoutQty'])->name('reports.purchase.misc.schemed.free-without-qty');
        Route::get('reports/purchase/misc/schemed/free-without-qty/print', [PurchaseReportController::class, 'schemedFreeWithoutQtyPrint'])->name('reports.purchase.misc.schemed.free-without-qty.print');

        // Other Purchase Reports
        Route::get('reports/purchase/other/supplier-visit-report', [PurchaseReportController::class, 'supplierVisitReport'])->name('reports.purchase.other.supplier-visit-report');
        Route::get('reports/purchase/other/supplier-wise-companies', [PurchaseReportController::class, 'supplierWiseCompanies'])->name('reports.purchase.other.supplier-wise-companies');
        Route::get('reports/purchase/other/purchase-book-item-details', [PurchaseReportController::class, 'purchaseBookItemDetails'])->name('reports.purchase.other.purchase-book-item-details');
        Route::get('reports/purchase/other/central-purchase-local-value', [PurchaseReportController::class, 'centralPurchaseLocalValue'])->name('reports.purchase.other.central-purchase-local-value');
        Route::get('reports/purchase/other/party-wise-all-purchase-details', [PurchaseReportController::class, 'partyWiseAllPurchaseDetails'])->name('reports.purchase.other.party-wise-all-purchase-details');
        Route::get('reports/purchase/other/register-schedule-h1-drugs', [PurchaseReportController::class, 'registerScheduleH1Drugs'])->name('reports.purchase.other.register-schedule-h1-drugs');
        
        Route::get('api/verify-batch-supplier', [PurchaseReturnController::class, 'verifyBatchSupplier'])->name('api.verify-batch-supplier');
        Route::get('api/party-details/{type}/{id}', [ItemController::class, 'getPartyDetails'])->name('api.party-details');
        
        // Inventory Reports
        Route::get('reports/inventory', [\App\Http\Controllers\Admin\InventoryReportController::class, 'index'])->name('reports.inventory');
        
        // Inventory Reports - Item Reports
        Route::get('reports/inventory/item/min-max-level', [\App\Http\Controllers\Admin\InventoryReportController::class, 'minimumMaximumLevelItems'])->name('reports.inventory.item.min-max-level');
        Route::get('reports/inventory/item/display-item-list', [\App\Http\Controllers\Admin\InventoryReportController::class, 'displayItemList'])->name('reports.inventory.item.display-item-list');
        Route::get('reports/inventory/item/tax-mrp-rate-range', [\App\Http\Controllers\Admin\InventoryReportController::class, 'itemListTaxMrpRateRange'])->name('reports.inventory.item.tax-mrp-rate-range');
        Route::get('reports/inventory/item/margin-wise', [\App\Http\Controllers\Admin\InventoryReportController::class, 'marginWiseItems'])->name('reports.inventory.item.margin-wise');
        Route::get('reports/inventory/item/margin-wise-running', [\App\Http\Controllers\Admin\InventoryReportController::class, 'marginWiseItemsRunning'])->name('reports.inventory.item.margin-wise-running');
        Route::get('reports/inventory/item/multi-rate', [\App\Http\Controllers\Admin\InventoryReportController::class, 'multiRateItems'])->name('reports.inventory.item.multi-rate');
        Route::get('reports/inventory/item/new-items-customers-suppliers', [\App\Http\Controllers\Admin\InventoryReportController::class, 'newItemsCustomersSuppliers'])->name('reports.inventory.item.new-items-customers-suppliers');
        Route::get('reports/inventory/item/rate-list', [\App\Http\Controllers\Admin\InventoryReportController::class, 'rateList'])->name('reports.inventory.item.rate-list');
        Route::get('reports/inventory/item/vat-wise', [\App\Http\Controllers\Admin\InventoryReportController::class, 'vatWiseItems'])->name('reports.inventory.item.vat-wise');
        Route::get('reports/inventory/item/item-list-with-salts', [\App\Http\Controllers\Admin\InventoryReportController::class, 'itemListWithSalts'])->name('reports.inventory.item.item-list-with-salts');
        Route::get('reports/inventory/item/list-of-schemes', [\App\Http\Controllers\Admin\InventoryReportController::class, 'listOfSchemes'])->name('reports.inventory.item.list-of-schemes');
        Route::get('reports/inventory/item/item-search-by-batch', [\App\Http\Controllers\Admin\InventoryReportController::class, 'itemSearchByBatch'])->name('reports.inventory.item.item-search-by-batch');
        Route::get('reports/inventory/item/item-ledger-printing', [\App\Http\Controllers\Admin\InventoryReportController::class, 'itemLedgerPrinting'])->name('reports.inventory.item.item-ledger-printing');
        
        // Inventory Reports - Stock Reports (Main Level - in main folder)
        Route::get('reports/inventory/stock/current-stock-status', [\App\Http\Controllers\Admin\InventoryReportController::class, 'currentStockStatus'])->name('reports.inventory.stock.current-stock-status');
        Route::get('reports/inventory/stock/category-wise-stock-status', [\App\Http\Controllers\Admin\InventoryReportController::class, 'categoryWiseStockStatus'])->name('reports.inventory.stock.category-wise-stock-status');
        Route::get('reports/inventory/stock/stock-and-sales-analysis', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockAndSalesAnalysis'])->name('reports.inventory.stock.stock-and-sales-analysis');
        Route::get('reports/inventory/stock/valuation-of-closing-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'valuationOfClosingStock'])->name('reports.inventory.stock.valuation-of-closing-stock');
        Route::get('reports/inventory/stock/category-wise-valuation-closing-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'categoryWiseValuationClosingStock'])->name('reports.inventory.stock.category-wise-valuation-closing-stock');
        Route::get('reports/inventory/stock/company-wise-stock-value', [\App\Http\Controllers\Admin\InventoryReportController::class, 'companyWiseStockValue'])->name('reports.inventory.stock.company-wise-stock-value');
        Route::get('reports/inventory/stock/stock-register-it-return', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockRegisterItReturn'])->name('reports.inventory.stock.stock-register-it-return');
        Route::get('reports/inventory/stock/list-of-old-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'listOfOldStock'])->name('reports.inventory.stock.list-of-old-stock');
        Route::get('reports/inventory/stock/sales-and-stock-variation', [\App\Http\Controllers\Admin\InventoryReportController::class, 'salesAndStockVariation'])->name('reports.inventory.stock.sales-and-stock-variation');
        Route::get('reports/inventory/stock/current-stock-status-supplier-wise', [\App\Http\Controllers\Admin\InventoryReportController::class, 'currentStockStatusSupplierWise'])->name('reports.inventory.stock.current-stock-status-supplier-wise');
        Route::get('reports/inventory/stock/annual-stock-ledger-summary', [\App\Http\Controllers\Admin\InventoryReportController::class, 'annualStockLedgerSummary'])->name('reports.inventory.stock.annual-stock-ledger-summary');
        Route::get('reports/inventory/stock/stock-register', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockRegisterOther'])->name('reports.inventory.stock.stock-register');
        Route::get('reports/inventory/stock/stock-and-sales-with-value', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockAndSalesWithValueOther'])->name('reports.inventory.stock.stock-and-sales-with-value');
        Route::get('reports/inventory/stock/batch-wise-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'batchWiseStockOther'])->name('reports.inventory.stock.batch-wise-stock');
        Route::get('reports/inventory/stock/location-wise-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'locationWiseStockOther'])->name('reports.inventory.stock.location-wise-stock');
        
        // Inventory Reports - Stock Reports - Others (in other folder)
        Route::get('reports/inventory/stock/other/stock-register', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockRegisterOther'])->name('reports.inventory.stock.other.stock-register');
        Route::get('reports/inventory/stock/other/stock-and-sales-with-value', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockAndSalesWithValueOther'])->name('reports.inventory.stock.other.stock-and-sales-with-value');
        Route::get('reports/inventory/stock/other/batch-wise-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'batchWiseStockOther'])->name('reports.inventory.stock.other.batch-wise-stock');
        Route::get('reports/inventory/stock/other/location-wise-stock', [\App\Http\Controllers\Admin\InventoryReportController::class, 'locationWiseStockOther'])->name('reports.inventory.stock.other.location-wise-stock');
        Route::get('reports/inventory/stock/other/category-wise-stock-status', [\App\Http\Controllers\Admin\InventoryReportController::class, 'categoryWiseStockStatusOther'])->name('reports.inventory.stock.other.category-wise-stock-status');
        
        // Inventory Reports - Reorder Reports
        Route::get('reports/inventory/reorder-sale-basis', [\App\Http\Controllers\Admin\InventoryReportController::class, 'reorderOnSaleBasis'])->name('reports.inventory.reorder-sale-basis');
        Route::get('reports/inventory/reorder-min-stock-basis', [\App\Http\Controllers\Admin\InventoryReportController::class, 'reorderOnMinStockBasis'])->name('reports.inventory.reorder-min-stock-basis');
        Route::get('reports/inventory/reorder-min-stock-sale-basis', [\App\Http\Controllers\Admin\InventoryReportController::class, 'reorderOnMinStockSaleBasis'])->name('reports.inventory.reorder-min-stock-sale-basis');
        Route::get('reports/inventory/order-form-3-column', [\App\Http\Controllers\Admin\InventoryReportController::class, 'orderForm3Column'])->name('reports.inventory.order-form-3-column');
        Route::get('reports/inventory/order-form-6-column', [\App\Http\Controllers\Admin\InventoryReportController::class, 'orderForm6Column'])->name('reports.inventory.order-form-6-column');
        
        // Inventory Reports - Others
        Route::get('reports/inventory/fifo-alteration', [\App\Http\Controllers\Admin\InventoryReportController::class, 'fifoAlteration'])->name('reports.inventory.fifo-alteration');
        Route::get('reports/inventory/list-hold-batches', [\App\Http\Controllers\Admin\InventoryReportController::class, 'listHoldBatches'])->name('reports.inventory.list-hold-batches');
        Route::get('reports/inventory/list-hold-batches-sr-pb', [\App\Http\Controllers\Admin\InventoryReportController::class, 'listHoldBatchesSrPb'])->name('reports.inventory.list-hold-batches-sr-pb');
        Route::get('reports/inventory/remove-batch-hold', [\App\Http\Controllers\Admin\InventoryReportController::class, 'removeBatchHold'])->name('reports.inventory.remove-batch-hold');
        Route::get('reports/inventory/others/fifo-ledger', [\App\Http\Controllers\Admin\InventoryReportController::class, 'fifoLedger'])->name('reports.inventory.others.fifo-ledger');
        Route::get('reports/inventory/others/stock-os-report-bank', [\App\Http\Controllers\Admin\InventoryReportController::class, 'stockOsReportBank'])->name('reports.inventory.others.stock-os-report-bank');
        
        // Management Reports
        // Due Reports
        Route::get('reports/management/due-reports/due-list', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueList'])->name('reports.management.due-reports.due-list');
        Route::get('reports/management/due-reports/bill-tagging', [\App\Http\Controllers\Admin\ManagementReportController::class, 'billTagging'])->name('reports.management.due-reports.bill-tagging');
        Route::get('reports/management/due-reports/due-list-with-pdc', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListWithPdc'])->name('reports.management.due-reports.due-list-with-pdc');
        Route::get('reports/management/due-reports/due-list-company-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListCompanyWise'])->name('reports.management.due-reports.due-list-company-wise');
        Route::get('reports/management/due-reports/due-list-account-ledger', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListAccountLedger'])->name('reports.management.due-reports.due-list-account-ledger');
        Route::get('reports/management/due-reports/ageing-analysis', [\App\Http\Controllers\Admin\ManagementReportController::class, 'ageingAnalysis'])->name('reports.management.due-reports.ageing-analysis');
        Route::get('reports/management/due-reports/ageing-analysis-account-ledger', [\App\Http\Controllers\Admin\ManagementReportController::class, 'ageingAnalysisAccountLedger'])->name('reports.management.due-reports.ageing-analysis-account-ledger');
        Route::get('reports/management/due-reports/list-of-pending-tags', [\App\Http\Controllers\Admin\ManagementReportController::class, 'listOfPendingTags'])->name('reports.management.due-reports.list-of-pending-tags');
        Route::get('reports/management/due-reports/bill-history', [\App\Http\Controllers\Admin\ManagementReportController::class, 'billHistory'])->name('reports.management.due-reports.bill-history');
        Route::get('reports/management/due-reports/due-list-summary', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListSummary'])->name('reports.management.due-reports.due-list-summary');
        Route::get('reports/management/due-reports/due-list-reminder-letter', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListReminderLetter'])->name('reports.management.due-reports.due-list-reminder-letter');
        Route::get('reports/management/due-reports/balance-confirmation-letter', [\App\Http\Controllers\Admin\ManagementReportController::class, 'balanceConfirmationLetter'])->name('reports.management.due-reports.balance-confirmation-letter');
        Route::get('reports/management/due-reports/balance-confirmation-letter-account-ledger', [\App\Http\Controllers\Admin\ManagementReportController::class, 'balanceConfirmationLetterAccountLedger'])->name('reports.management.due-reports.balance-confirmation-letter-account-ledger');
        Route::get('reports/management/due-reports/due-list-monthly', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListMonthly'])->name('reports.management.due-reports.due-list-monthly');
        Route::get('reports/management/due-reports/due-list-adjustment-analysis', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dueListAdjustmentAnalysis'])->name('reports.management.due-reports.due-list-adjustment-analysis');
        // Gross Profit Reports
        Route::get('reports/management/gross-profit/bill-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitBillWise'])->name('reports.management.gross-profit.bill-wise');
        Route::get('reports/management/gross-profit/item-bill-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitItemBillWise'])->name('reports.management.gross-profit.item-bill-wise');
        Route::get('reports/management/gross-profit/selective-all-items', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSelectiveAllItems'])->name('reports.management.gross-profit.selective-all-items');
        Route::get('reports/management/gross-profit/company-bill-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitCompanyBillWise'])->name('reports.management.gross-profit.company-bill-wise');
        Route::get('reports/management/gross-profit/selective-all-companies', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSelectiveAllCompanies'])->name('reports.management.gross-profit.selective-all-companies');
        Route::get('reports/management/gross-profit/customer-bill-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitCustomerBillWise'])->name('reports.management.gross-profit.customer-bill-wise');
        Route::get('reports/management/gross-profit/selective-all-customers', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSelectiveAllCustomers'])->name('reports.management.gross-profit.selective-all-customers');
        Route::get('reports/management/gross-profit/selective-all-suppliers', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSelectiveAllSuppliers'])->name('reports.management.gross-profit.selective-all-suppliers');
        Route::get('reports/management/gross-profit/salt-wise', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSaltWise'])->name('reports.management.gross-profit.salt-wise');
        Route::get('reports/management/gross-profit/claim-items-sold-on-loss', [\App\Http\Controllers\Admin\ManagementReportController::class, 'claimItemsSoldOnLoss'])->name('reports.management.gross-profit.claim-items-sold-on-loss');
        Route::get('reports/management/gross-profit/selective-all-salesman', [\App\Http\Controllers\Admin\ManagementReportController::class, 'grossProfitSelectiveAllSalesman'])->name('reports.management.gross-profit.selective-all-salesman');
        Route::get('reports/management/list-of-expired-items', [\App\Http\Controllers\Admin\ManagementReportController::class, 'listOfExpiredItems'])->name('reports.management.list-of-expired-items');
        Route::get('reports/management/sale-purchase-schemes', [\App\Http\Controllers\Admin\ManagementReportController::class, 'salePurchaseSchemes'])->name('reports.management.sale-purchase-schemes');
        Route::get('reports/management/suppliers-pending-order', [\App\Http\Controllers\Admin\ManagementReportController::class, 'suppliersPendingOrder'])->name('reports.management.suppliers-pending-order');
        Route::get('reports/management/customers-pending-order', [\App\Http\Controllers\Admin\ManagementReportController::class, 'customersPendingOrder'])->name('reports.management.customers-pending-order');
        Route::get('reports/management/non-moving-items', [\App\Http\Controllers\Admin\ManagementReportController::class, 'nonMovingItems'])->name('reports.management.non-moving-items');
        Route::get('reports/management/slow-moving-items', [\App\Http\Controllers\Admin\ManagementReportController::class, 'slowMovingItems'])->name('reports.management.slow-moving-items');
        Route::get('reports/management/performance-report', [\App\Http\Controllers\Admin\ManagementReportController::class, 'performanceReport'])->name('reports.management.performance-report');
        // Others
        Route::get('reports/management/others/day-check-list', [\App\Http\Controllers\Admin\ManagementReportController::class, 'dayCheckList'])->name('reports.management.others.day-check-list');
        Route::get('reports/management/others/prescription-reminder-list', [\App\Http\Controllers\Admin\ManagementReportController::class, 'prescriptionReminderList'])->name('reports.management.others.prescription-reminder-list');
        Route::get('reports/management/others/ledger-due-list-mismatch-report', [\App\Http\Controllers\Admin\ManagementReportController::class, 'ledgerDueListMismatchReport'])->name('reports.management.others.ledger-due-list-mismatch-report');
        Route::get('reports/management/others/salepurchase1-due-list-mismatch-report', [\App\Http\Controllers\Admin\ManagementReportController::class, 'salepurchase1DueListMismatchReport'])->name('reports.management.others.salepurchase1-due-list-mismatch-report');
        Route::get('reports/management/others/attendence-sheet', [\App\Http\Controllers\Admin\ManagementReportController::class, 'attendenceSheet'])->name('reports.management.others.attendence-sheet');
        Route::get('reports/management/others/list-of-modifications', [\App\Http\Controllers\Admin\ManagementReportController::class, 'listOfModifications'])->name('reports.management.others.list-of-modifications');
        Route::get('reports/management/others/list-of-master-modifications', [\App\Http\Controllers\Admin\ManagementReportController::class, 'listOfMasterModifications'])->name('reports.management.others.list-of-master-modifications');
        Route::get('reports/management/others/cl-sl-date-wise-ledger-summary', [\App\Http\Controllers\Admin\ManagementReportController::class, 'clSlDateWiseLedgerSummary'])->name('reports.management.others.cl-sl-date-wise-ledger-summary');
        Route::get('reports/management/others/user-work-summary', [\App\Http\Controllers\Admin\ManagementReportController::class, 'userWorkSummary'])->name('reports.management.others.user-work-summary');
        Route::get('reports/management/others/hsn-wise-sale-purchase-report', [\App\Http\Controllers\Admin\ManagementReportController::class, 'hsnWiseSalePurchaseReport'])->name('reports.management.others.hsn-wise-sale-purchase-report');
        
        // Management Report AJAX Lookups
        Route::get('reports/management/lookup/customer', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getCustomerByCode'])->name('reports.management.lookup.customer');
        Route::get('reports/management/lookup/supplier', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getSupplierByCode'])->name('reports.management.lookup.supplier');
        Route::get('reports/management/lookup/salesman', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getSalesmanByCode'])->name('reports.management.lookup.salesman');
        Route::get('reports/management/lookup/area', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getAreaByCode'])->name('reports.management.lookup.area');
        Route::get('reports/management/lookup/route', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getRouteByCode'])->name('reports.management.lookup.route');
        Route::get('reports/management/lookup/state', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getStateByCode'])->name('reports.management.lookup.state');
        Route::get('reports/management/lookup/company', [\App\Http\Controllers\Admin\ManagementReportController::class, 'getCompanyByCode'])->name('reports.management.lookup.company');
        
        // Supplier specific routes - MUST be before resource route
        Route::get('suppliers/{supplier}/pending-orders', [SupplierController::class, 'pendingOrders'])->name('suppliers.pending-orders');
        Route::post('suppliers/{supplier}/pending-orders', [SupplierController::class, 'storePendingOrder'])->name('suppliers.pending-orders.store');
        Route::put('suppliers/{supplier}/pending-orders/{pendingOrder}', [SupplierController::class, 'updatePendingOrder'])->name('suppliers.pending-orders.update');
        Route::delete('suppliers/{supplier}/pending-orders/{pendingOrder}', [SupplierController::class, 'deletePendingOrder'])->name('suppliers.pending-orders.delete');
        Route::get('suppliers/{supplier}/pending-orders/print/{orderNo}', [SupplierController::class, 'printPendingOrder'])->name('suppliers.pending-orders.print');
        Route::get('suppliers/{supplier}/pending-orders-data', [SupplierController::class, 'getPendingOrdersData'])->name('suppliers.pending-orders-data');
        Route::get('suppliers/{supplier}/pending-orders/{orderNo}/items', [SupplierController::class, 'getOrderItems'])->name('suppliers.pending-orders.items');
        
        // Supplier Ledger Routes
        Route::get('suppliers/{supplier}/ledger', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
        Route::get('suppliers/{supplier}/dues', [SupplierController::class, 'dues'])->name('suppliers.dues');
        Route::get('suppliers/{supplier}/bills', [SupplierController::class, 'bills'])->name('suppliers.bills');
        
        // Supplier resource routes
        Route::post('suppliers/multiple-delete', [SupplierController::class, 'multipleDelete'])->name('suppliers.multiple-delete');
        Route::resource('suppliers', SupplierController::class);
        
        // HSN Codes routes
        Route::post('hsn-codes/multiple-delete', [HsnCodeController::class, 'multipleDelete'])->name('hsn-codes.multiple-delete');
        Route::resource('hsn-codes', HsnCodeController::class);
        
        // Ledger Routes
        Route::get('all-ledger', [AllLedgerController::class, 'index'])->name('all-ledger.index');
        Route::get('all-ledger/details', [AllLedgerController::class, 'getLedgerDetails'])->name('all-ledger.details');
        Route::post('general-ledger/multiple-delete', [GeneralLedgerController::class, 'multipleDelete'])->name('general-ledger.multiple-delete');
        Route::resource('general-ledger', GeneralLedgerController::class);
        
        // Cash Bank Books routes
        Route::post('cash-bank-books/multiple-delete', [CashBankBookController::class, 'multipleDelete'])->name('cash-bank-books.multiple-delete');
        Route::resource('cash-bank-books', CashBankBookController::class);
        
        // Sale Ledger routes
        Route::post('sale-ledger/multiple-delete', [SaleLedgerController::class, 'multipleDelete'])->name('sale-ledger.multiple-delete');
        Route::resource('sale-ledger', SaleLedgerController::class);
        
        // Purchase Ledger routes
        Route::post('purchase-ledger/multiple-delete', [PurchaseLedgerController::class, 'multipleDelete'])->name('purchase-ledger.multiple-delete');
        Route::resource('purchase-ledger', PurchaseLedgerController::class);
        
        // Sales & Management Routes
        Route::post('sales-men/multiple-delete', [SalesManController::class, 'multipleDelete'])->name('sales-men.multiple-delete');
        Route::resource('sales-men', SalesManController::class);
        Route::get('salesmen/search', [SalesManController::class, 'search'])->name('salesmen.search');
        
        Route::post('areas/multiple-delete', [AreaController::class, 'multipleDelete'])->name('areas.multiple-delete');
        Route::resource('areas', AreaController::class)->except(['show']);
        Route::get('areas/search', [AreaController::class, 'search'])->name('areas.search');
        
        Route::post('routes/multiple-delete', [RouteController::class, 'multipleDelete'])->name('routes.multiple-delete');
        Route::resource('routes', RouteController::class)->except(['show']);
        Route::get('routes/search', [RouteController::class, 'search'])->name('routes.search');
        
        Route::post('states/multiple-delete', [StateController::class, 'multipleDelete'])->name('states.multiple-delete');
        Route::post('area-managers/multiple-delete', [AreaManagerController::class, 'multipleDelete'])->name('area-managers.multiple-delete');
        Route::post('regional-managers/multiple-delete', [RegionalManagerController::class, 'multipleDelete'])->name('regional-managers.multiple-delete');
        Route::post('marketing-managers/multiple-delete', [MarketingManagerController::class, 'multipleDelete'])->name('marketing-managers.multiple-delete');
        Route::post('general-managers/multiple-delete', [GeneralManagerController::class, 'multipleDelete'])->name('general-managers.multiple-delete');
        Route::post('divisional-managers/multiple-delete', [DivisionalManagerController::class, 'multipleDelete'])->name('divisional-managers.multiple-delete');
        Route::post('country-managers/multiple-delete', [CountryManagerController::class, 'multipleDelete'])->name('country-managers.multiple-delete');
        Route::resource('states', StateController::class)->except(['show']);
        Route::get('states/search', [StateController::class, 'search'])->name('states.search');
        
        Route::resource('area-managers', AreaManagerController::class);
        Route::resource('regional-managers', RegionalManagerController::class);
        Route::resource('marketing-managers', MarketingManagerController::class);
        Route::resource('general-managers', GeneralManagerController::class);
        Route::resource('divisional-managers', DivisionalManagerController::class);
        Route::resource('country-managers', CountryManagerController::class);
        
        // Search routes for AJAX lookups
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('companies/search', [CompanyController::class, 'search'])->name('companies.search');
        
        // New Modules Routes
        Route::post('personal-directory/multiple-delete', [PersonalDirectoryController::class, 'multipleDelete'])->name('personal-directory.multiple-delete');
        Route::resource('personal-directory', PersonalDirectoryController::class);
        Route::post('general-reminders/multiple-delete', [GeneralReminderController::class, 'multipleDelete'])->name('general-reminders.multiple-delete');
        Route::resource('general-reminders', GeneralReminderController::class);
        Route::post('general-notebook/multiple-delete', [GeneralNotebookController::class, 'multipleDelete'])->name('general-notebook.multiple-delete');
        Route::resource('general-notebook', GeneralNotebookController::class);
        Route::post('item-category/multiple-delete', [ItemCategoryController::class, 'multipleDelete'])->name('item-category.multiple-delete');
        Route::resource('item-category', ItemCategoryController::class);
        Route::post('transport-master/multiple-delete', [TransportMasterController::class, 'multipleDelete'])->name('transport-master.multiple-delete');
        Route::resource('transport-master', TransportMasterController::class);
        
        // Sale Routes
        Route::get('sale/transaction', [SaleTransactionController::class, 'transaction'])->name('sale.transaction');
        Route::post('sale/transaction', [SaleTransactionController::class, 'store'])->name('sale.store');
        Route::get('sale/get-items', [SaleTransactionController::class, 'getItems'])->name('sale.getItems');
        Route::get('sale/next-invoice-no', [SaleTransactionController::class, 'getNextInvoiceNo'])->name('sale.next-invoice-no');
        Route::get('sale/customer/{customerId}/due', [SaleTransactionController::class, 'getCustomerDue'])->name('sale.customer.due');
        
        // Sale Invoices Routes
        Route::get('sale/invoices', [SaleTransactionController::class, 'invoices'])->name('sale.invoices');
        Route::get('sale/{id}', [SaleTransactionController::class, 'show'])->where('id', '[0-9]+')->name('sale.show');
        Route::delete('sale/{id}', [SaleTransactionController::class, 'destroy'])->where('id', '[0-9]+')->name('sale.destroy');
        
        // Sale Modification Routes
        Route::get('sale/modification', [SaleTransactionController::class, 'modification'])->name('sale.modification');
        Route::get('sale/modification/invoices', [SaleTransactionController::class, 'getInvoices'])->name('sale.modification.invoices');
        Route::get('sale/modification/search', [SaleTransactionController::class, 'searchByInvoiceNo'])->name('sale.modification.search');
        Route::get('sale/modification/{id}', [SaleTransactionController::class, 'getTransaction'])->name('sale.modification.transaction');
        Route::post('sale/modification/{id}', [SaleTransactionController::class, 'updateTransaction'])->name('sale.modification.update');
        
        // Sale Challan Routes
        Route::get('sale-challan/transaction', [SaleChallanController::class, 'transaction'])->name('sale-challan.transaction');
        Route::post('sale-challan/store', [SaleChallanController::class, 'store'])->name('sale-challan.store');
        Route::get('sale-challan/get-items', [SaleChallanController::class, 'getItems'])->name('sale-challan.getItems');
        Route::get('sale-challan/next-challan-no', [SaleChallanController::class, 'getNextChallanNo'])->name('sale-challan.next-challan-no');
        Route::get('sale-challan/customer/{customerId}/due', [SaleChallanController::class, 'getCustomerDue'])->name('sale-challan.customer.due');
        Route::get('sale-challan/invoices', [SaleChallanController::class, 'invoices'])->name('sale-challan.invoices');
        Route::get('sale-challan/modification', [SaleChallanController::class, 'modification'])->name('sale-challan.modification');
        Route::get('sale-challan/modification/challans', [SaleChallanController::class, 'getChallans'])->name('sale-challan.modification.challans');
        Route::get('sale-challan/modification/search', [SaleChallanController::class, 'searchByChallanNo'])->name('sale-challan.modification.search');
        Route::get('sale-challan/modification/{id}', [SaleChallanController::class, 'getChallan'])->name('sale-challan.modification.challan');
        Route::post('sale-challan/modification/{id}', [SaleChallanController::class, 'updateChallan'])->name('sale-challan.modification.update');
        Route::get('sale-challan/pending', [SaleChallanController::class, 'getPendingChallans'])->name('sale-challan.pending');
        Route::get('sale-challan/{id}', [SaleChallanController::class, 'show'])->where('id', '[0-9]+')->name('sale-challan.show');
        Route::delete('sale-challan/{id}', [SaleChallanController::class, 'destroy'])->where('id', '[0-9]+')->name('sale-challan.destroy');
        
        // Purchase Transaction Routes (Consolidated - All using PurchaseTransactionController)
        Route::get('purchase/transaction', [PurchaseTransactionController::class, 'transaction'])->name('purchase.transaction');
        Route::get('purchase/modification/{trn_no?}', [PurchaseTransactionController::class, 'modification'])->name('purchase.modification');
        Route::get('purchase/invoices', [PurchaseTransactionController::class, 'invoices'])->name('purchase.invoices');
        Route::get('purchase/debug', [PurchaseTransactionController::class, 'debugPurchases'])->name('purchase.debug');
        Route::get('purchase/{id}/show', [PurchaseTransactionController::class, 'show'])->name('purchase.show');
        Route::get('purchase/invoice-list', [PurchaseTransactionController::class, 'getInvoiceList'])->name('purchase.invoice-list');
        Route::get('purchase/fetch-bill/{trnNo}', [PurchaseTransactionController::class, 'fetchBill'])->name('purchase.fetch-bill');
        Route::get('purchase/supplier/{supplierId}/name', [PurchaseTransactionController::class, 'getSupplierName'])->name('purchase.supplier-name');
        Route::get('purchase/supplier/{supplierId}/pending-challans', [PurchaseTransactionController::class, 'getPendingChallans'])->name('purchase.pending-challans');
        Route::get('purchase/challan/{challanId}/details', [PurchaseTransactionController::class, 'getChallanDetails'])->name('purchase.challan-details');
        
        // Purchase Transaction CRUD Routes
        Route::post('purchase/transaction/store', [PurchaseTransactionController::class, 'store'])->name('purchase.transaction.store');
        Route::get('purchase/transactions', [PurchaseTransactionController::class, 'index'])->name('purchase.transactions.index');
        Route::get('purchase/transactions/{id}', [PurchaseTransactionController::class, 'show'])->name('purchase.transactions.show');
        Route::get('purchase/transactions/{id}/edit', [PurchaseTransactionController::class, 'edit'])->name('purchase.transactions.edit');
        Route::put('purchase/transactions/{id}', [PurchaseTransactionController::class, 'update'])->name('purchase.transactions.update');
        Route::delete('purchase/transactions/{id}', [PurchaseTransactionController::class, 'destroy'])->name('purchase.transactions.destroy');
        Route::delete('purchase/{id}', [PurchaseTransactionController::class, 'destroy'])->name('purchase.destroy');
        
        // Sale Return Routes
        Route::get('sale-return', [SaleReturnController::class, 'index'])->name('sale-return.index');
        Route::get('sale-return/transaction', [SaleReturnController::class, 'transaction'])->name('sale-return.transaction');
        Route::get('sale-return/modification', [SaleReturnController::class, 'modification'])->name('sale-return.modification');
        Route::get('sale-return/get-past-invoices', [SaleReturnController::class, 'getPastInvoices'])->name('sale-return.get-past-invoices');
        Route::get('sale-return/get-details/{id}', [SaleReturnController::class, 'getDetails'])->name('sale-return.get-details');
        Route::get('sale-return/get-by-sr-no/{srNo}', [SaleReturnController::class, 'getBySrNo'])->name('sale-return.get-by-sr-no');
        Route::post('sale-return/update-modification', [SaleReturnController::class, 'updateModification'])->name('sale-return.update-modification');
        Route::post('sale-return/search-invoice', [SaleReturnController::class, 'searchInvoice'])->name('sale-return.search-invoice');
        Route::get('sale-return/transaction-details/{id}', [SaleReturnController::class, 'getTransactionDetails'])->name('sale-return.transaction-details');
        Route::post('sale-return/customer-invoices', [SaleReturnController::class, 'getCustomerInvoices'])->name('sale-return.customer-invoices');
        Route::post('sale-return/store', [SaleReturnController::class, 'store'])->name('sale-return.store');
        Route::post('sale-return/search-by-sr', [SaleReturnController::class, 'searchBySRNo'])->name('sale-return.search-by-sr');
        Route::get('sale-return/past-returns', [SaleReturnController::class, 'getPastReturns'])->name('sale-return.past-returns');
        Route::get('sale-return/details/{id}', [SaleReturnController::class, 'getSaleReturnDetails'])->name('sale-return.details');
        Route::post('sale-return/update/{id}', [SaleReturnController::class, 'update'])->name('sale-return.update');
        Route::get('sale-return/{id}/adjustments', [SaleReturnController::class, 'getAdjustments'])->name('sale-return.adjustments');
        Route::get('sale-return/{id}', [SaleReturnController::class, 'show'])->name('sale-return.show');
        Route::delete('sale-return/{id}', [SaleReturnController::class, 'destroy'])->name('sale-return.destroy');
        
        // Breakage/Expiry from Customer Routes
        Route::get('breakage-expiry', [BreakageExpiryController::class, 'index'])->name('breakage-expiry.index');
        Route::get('breakage-expiry/transaction', [BreakageExpiryController::class, 'transaction'])->name('breakage-expiry.transaction');
        Route::post('breakage-expiry/transaction', [BreakageExpiryController::class, 'storeTransaction'])->name('breakage-expiry.transaction.store');
        Route::get('breakage-expiry/modification', [BreakageExpiryController::class, 'modification'])->name('breakage-expiry.modification');
        Route::put('breakage-expiry/transaction/{id}', [BreakageExpiryController::class, 'updateTransaction'])->name('breakage-expiry.transaction.update');
        Route::get('breakage-expiry/expiry-date', [BreakageExpiryController::class, 'expiryDate'])->name('breakage-expiry.expiry-date');
        Route::get('breakage-expiry/get-by-sr-no/{srNo}', [BreakageExpiryController::class, 'getBySrNo'])->name('breakage-expiry.get-by-sr-no');
        Route::get('breakage-expiry/{id}', [BreakageExpiryController::class, 'show'])->name('breakage-expiry.show');
        Route::delete('breakage-expiry/{id}', [BreakageExpiryController::class, 'destroy'])->name('breakage-expiry.destroy');
        
        // Stock Transfer Outgoing Routes
        Route::get('stock-transfer-outgoing', [StockTransferOutgoingController::class, 'index'])->name('stock-transfer-outgoing.index');
        Route::get('stock-transfer-outgoing/transaction', [StockTransferOutgoingController::class, 'transaction'])->name('stock-transfer-outgoing.transaction');
        Route::post('stock-transfer-outgoing/transaction', [StockTransferOutgoingController::class, 'storeTransaction'])->name('stock-transfer-outgoing.transaction.store');
        Route::get('stock-transfer-outgoing/modification', [StockTransferOutgoingController::class, 'modification'])->name('stock-transfer-outgoing.modification');
        Route::put('stock-transfer-outgoing/transaction/{id}', [StockTransferOutgoingController::class, 'updateTransaction'])->name('stock-transfer-outgoing.transaction.update');
        Route::get('stock-transfer-outgoing/get-by-sr-no/{srNo}', [StockTransferOutgoingController::class, 'getBySrNo'])->name('stock-transfer-outgoing.get-by-sr-no');
        Route::get('stock-transfer-outgoing/{id}', [StockTransferOutgoingController::class, 'show'])->name('stock-transfer-outgoing.show');
        Route::delete('stock-transfer-outgoing/{id}', [StockTransferOutgoingController::class, 'destroy'])->name('stock-transfer-outgoing.destroy');
        
        // Stock Transfer Outgoing Return Routes
        Route::get('stock-transfer-outgoing-return', [StockTransferOutgoingReturnController::class, 'index'])->name('stock-transfer-outgoing-return.index');
        Route::get('stock-transfer-outgoing-return/transaction', [StockTransferOutgoingReturnController::class, 'transaction'])->name('stock-transfer-outgoing-return.transaction');
        Route::post('stock-transfer-outgoing-return/transaction', [StockTransferOutgoingReturnController::class, 'storeTransaction'])->name('stock-transfer-outgoing-return.transaction.store');
        Route::get('stock-transfer-outgoing-return/modification', [StockTransferOutgoingReturnController::class, 'modification'])->name('stock-transfer-outgoing-return.modification');
        Route::put('stock-transfer-outgoing-return/transaction/{id}', [StockTransferOutgoingReturnController::class, 'updateTransaction'])->name('stock-transfer-outgoing-return.transaction.update');
        Route::get('stock-transfer-outgoing-return/get-by-sr-no/{srNo}', [StockTransferOutgoingReturnController::class, 'getBySrNo'])->name('stock-transfer-outgoing-return.get-by-sr-no');
        Route::get('stock-transfer-outgoing-return/{id}', [StockTransferOutgoingReturnController::class, 'show'])->name('stock-transfer-outgoing-return.show');
        Route::delete('stock-transfer-outgoing-return/{id}', [StockTransferOutgoingReturnController::class, 'destroy'])->name('stock-transfer-outgoing-return.destroy');
        
        // Stock Transfer Incoming Routes
        Route::get('stock-transfer-incoming', [StockTransferIncomingController::class, 'index'])->name('stock-transfer-incoming.index');
        Route::get('stock-transfer-incoming/transaction', [StockTransferIncomingController::class, 'transaction'])->name('stock-transfer-incoming.transaction');
        Route::post('stock-transfer-incoming', [StockTransferIncomingController::class, 'store'])->name('stock-transfer-incoming.store');
        Route::get('stock-transfer-incoming/modification', [StockTransferIncomingController::class, 'modification'])->name('stock-transfer-incoming.modification');
        Route::get('stock-transfer-incoming/past-transactions', [StockTransferIncomingController::class, 'getPastTransactions'])->name('stock-transfer-incoming.past-transactions');
        Route::get('stock-transfer-incoming/{id}/details', [StockTransferIncomingController::class, 'getDetails'])->name('stock-transfer-incoming.details');
        Route::put('stock-transfer-incoming/{id}', [StockTransferIncomingController::class, 'update'])->name('stock-transfer-incoming.update');
        Route::get('stock-transfer-incoming/{id}', [StockTransferIncomingController::class, 'show'])->name('stock-transfer-incoming.show');
        Route::delete('stock-transfer-incoming/{id}', [StockTransferIncomingController::class, 'destroy'])->name('stock-transfer-incoming.destroy');
        
        // Stock Transfer Incoming Return Routes
        Route::get('stock-transfer-incoming-return', [StockTransferIncomingReturnController::class, 'index'])->name('stock-transfer-incoming-return.index');
        Route::get('stock-transfer-incoming-return/transaction', [StockTransferIncomingReturnController::class, 'transaction'])->name('stock-transfer-incoming-return.transaction');
        Route::post('stock-transfer-incoming-return', [StockTransferIncomingReturnController::class, 'store'])->name('stock-transfer-incoming-return.store');
        Route::get('stock-transfer-incoming-return/modification', [StockTransferIncomingReturnController::class, 'modification'])->name('stock-transfer-incoming-return.modification');
        Route::get('stock-transfer-incoming-return/past-transactions', [StockTransferIncomingReturnController::class, 'getPastTransactions'])->name('stock-transfer-incoming-return.past-transactions');
        Route::get('stock-transfer-incoming-return/{id}/details', [StockTransferIncomingReturnController::class, 'getDetails'])->name('stock-transfer-incoming-return.details');
        Route::put('stock-transfer-incoming-return/{id}', [StockTransferIncomingReturnController::class, 'update'])->name('stock-transfer-incoming-return.update');
        Route::get('stock-transfer-incoming-return/{id}', [StockTransferIncomingReturnController::class, 'show'])->name('stock-transfer-incoming-return.show');
        Route::delete('stock-transfer-incoming-return/{id}', [StockTransferIncomingReturnController::class, 'destroy'])->name('stock-transfer-incoming-return.destroy');
        
        // Sample Issued Routes
        Route::get('sample-issued', [SampleIssuedController::class, 'index'])->name('sample-issued.index');
        Route::get('sample-issued/create', [SampleIssuedController::class, 'create'])->name('sample-issued.create');
        Route::post('sample-issued', [SampleIssuedController::class, 'store'])->name('sample-issued.store');
        Route::get('sample-issued/get-items', [SampleIssuedController::class, 'getItems'])->name('sample-issued.getItems');
        Route::get('sample-issued/load-by-trn-no', [SampleIssuedController::class, 'loadByTrnNo'])->name('sample-issued.loadByTrnNo');
        Route::get('sample-issued/get-party-list', [SampleIssuedController::class, 'getPartyList'])->name('sample-issued.getPartyList');
        Route::get('sample-issued/get-past-invoices', [SampleIssuedController::class, 'getPastInvoices'])->name('sample-issued.getPastInvoices');
        Route::get('sample-issued/modification', [SampleIssuedController::class, 'modification'])->name('sample-issued.modification');
        Route::get('sample-issued/{id}', [SampleIssuedController::class, 'show'])->name('sample-issued.show');
        Route::get('sample-issued/{id}/edit', [SampleIssuedController::class, 'edit'])->name('sample-issued.edit');
        Route::put('sample-issued/{id}', [SampleIssuedController::class, 'update'])->name('sample-issued.update');
        Route::delete('sample-issued/{id}', [SampleIssuedController::class, 'destroy'])->name('sample-issued.destroy');
        
        // Sample Received Routes
        Route::get('sample-received', [SampleReceivedController::class, 'index'])->name('sample-received.index');
        Route::get('sample-received/create', [SampleReceivedController::class, 'create'])->name('sample-received.create');
        Route::post('sample-received', [SampleReceivedController::class, 'store'])->name('sample-received.store');
        Route::get('sample-received/get-items', [SampleReceivedController::class, 'getItems'])->name('sample-received.getItems');
        Route::get('sample-received/load-by-trn-no', [SampleReceivedController::class, 'loadByTrnNo'])->name('sample-received.loadByTrnNo');
        Route::get('sample-received/get-party-list', [SampleReceivedController::class, 'getPartyList'])->name('sample-received.getPartyList');
        Route::get('sample-received/get-past-invoices', [SampleReceivedController::class, 'getPastInvoices'])->name('sample-received.getPastInvoices');
        Route::get('sample-received-modification', [SampleReceivedController::class, 'modification'])->name('sample-received.modification');
        Route::get('sample-received/{id}', [SampleReceivedController::class, 'show'])->name('sample-received.show');
        Route::get('sample-received/{id}/edit', [SampleReceivedController::class, 'edit'])->name('sample-received.edit');
        Route::put('sample-received/{id}', [SampleReceivedController::class, 'update'])->name('sample-received.update');
        Route::delete('sample-received/{id}', [SampleReceivedController::class, 'destroy'])->name('sample-received.destroy');
        
        // Godown Breakage/Expiry Routes
        Route::get('godown-breakage-expiry', [GodownBreakageExpiryController::class, 'index'])->name('godown-breakage-expiry.index');
        Route::get('godown-breakage-expiry/create', [GodownBreakageExpiryController::class, 'create'])->name('godown-breakage-expiry.create');
        Route::post('godown-breakage-expiry', [GodownBreakageExpiryController::class, 'store'])->name('godown-breakage-expiry.store');
        Route::get('godown-breakage-expiry/get-items', [GodownBreakageExpiryController::class, 'getItems'])->name('godown-breakage-expiry.getItems');
        Route::get('godown-breakage-expiry/get-past-invoices', [GodownBreakageExpiryController::class, 'getPastInvoices'])->name('godown-breakage-expiry.getPastInvoices');
        Route::get('godown-breakage-expiry-modification', [GodownBreakageExpiryController::class, 'modification'])->name('godown-breakage-expiry.modification');
        Route::get('godown-breakage-expiry/{id}', [GodownBreakageExpiryController::class, 'show'])->name('godown-breakage-expiry.show');
        Route::put('godown-breakage-expiry/{id}', [GodownBreakageExpiryController::class, 'update'])->name('godown-breakage-expiry.update');
        Route::delete('godown-breakage-expiry/{id}', [GodownBreakageExpiryController::class, 'destroy'])->name('godown-breakage-expiry.destroy');
        
        // Purchase Return Routes
        Route::get('purchase-return', [PurchaseReturnController::class, 'index'])->name('purchase-return.index');
        Route::get('purchase-return/transaction', [PurchaseReturnController::class, 'transaction'])->name('purchase-return.transaction');
        Route::get('purchase-return/modification', [PurchaseReturnController::class, 'modification'])->name('purchase-return.modification');
        Route::get('purchase-return/next-trn-no', [PurchaseReturnController::class, 'getNextTransactionNumber'])->name('purchase-return.next-trn-no');
        Route::get('purchase-return/batches', [PurchaseReturnController::class, 'getBatches'])->name('purchase-return.batches');
        Route::post('purchase-return/verify-batch-supplier', [PurchaseReturnController::class, 'verifyBatchSupplier'])->name('purchase-return.verify-batch-supplier');
        Route::post('purchase-return/store', [PurchaseReturnController::class, 'store'])->name('purchase-return.store');
        Route::get('purchase-return/supplier-invoices/{supplierId}', [PurchaseReturnController::class, 'getSupplierInvoices'])->name('purchase-return.supplier-invoices');
        Route::get('purchase-return/past-returns', [PurchaseReturnController::class, 'getPastReturns'])->name('purchase-return.past-returns');
        Route::get('purchase-return/details/{id}', [PurchaseReturnController::class, 'getReturnDetails'])->name('purchase-return.details');
        Route::get('purchase-return/get-by-pr-no/{prNo}', [PurchaseReturnController::class, 'getByPrNo'])->name('purchase-return.get-by-pr-no');
        Route::put('purchase-return/{id}', [PurchaseReturnController::class, 'update'])->name('purchase-return.update');
        Route::get('purchase-return/{id}', [PurchaseReturnController::class, 'show'])->name('purchase-return.show');
        Route::delete('purchase-return/{id}', [PurchaseReturnController::class, 'destroy'])->name('purchase-return.destroy');
        
        // Purchase Challan Routes
        Route::get('purchase-challan/transaction', [PurchaseChallanTransactionController::class, 'transaction'])->name('purchase-challan.transaction');
        Route::get('purchase-challan/modification/{challan_no?}', [PurchaseChallanTransactionController::class, 'modification'])->name('purchase-challan.modification');
        Route::get('purchase-challan/invoices', [PurchaseChallanTransactionController::class, 'invoices'])->name('purchase-challan.invoices');
        Route::get('purchase-challan/invoice-list', [PurchaseChallanTransactionController::class, 'getInvoiceList'])->name('purchase-challan.invoice-list');
        Route::get('purchase-challan/fetch-bill/{challanNo}', [PurchaseChallanTransactionController::class, 'fetchBill'])->name('purchase-challan.fetch-bill');
        Route::get('purchase-challan/supplier/{supplierId}/challans', [PurchaseChallanTransactionController::class, 'getSupplierChallans'])->name('purchase-challan.supplier-challans');
        Route::get('purchase-challan/all-pending', [PurchaseChallanTransactionController::class, 'getAllPendingChallans'])->name('purchase-challan.all-pending');
        Route::get('purchase-challan/all-challans', [PurchaseChallanTransactionController::class, 'getAllChallans'])->name('purchase-challan.all-challans');
        Route::get('purchase-challan/next-challan-no', [PurchaseChallanTransactionController::class, 'getNextChallanNo'])->name('purchase-challan.next-challan-no');
        
        // Purchase Challan CRUD Routes
        Route::post('purchase-challan/store', [PurchaseChallanTransactionController::class, 'store'])->name('purchase-challan.store');
        Route::put('purchase-challan/{id}', [PurchaseChallanTransactionController::class, 'update'])->name('purchase-challan.update');
        Route::delete('purchase-challan/{id}', [PurchaseChallanTransactionController::class, 'destroy'])->name('purchase-challan.destroy');
        Route::get('purchase-challan/{id}/details', [PurchaseChallanTransactionController::class, 'getChallanDetails'])->name('purchase-challan.details');
        Route::get('purchase-challan/{id}/show', [PurchaseChallanTransactionController::class, 'show'])->name('purchase-challan.show');
        Route::post('purchase-challan/{id}/mark-invoiced', [PurchaseChallanTransactionController::class, 'markAsInvoiced'])->name('purchase-challan.mark-invoiced');
        
        // Breakage/Expiry to Supplier Routes - Issued
        Route::get('breakage-supplier/issued', [BreakageSupplierController::class, 'issuedIndex'])->name('breakage-supplier.issued-index');
        Route::get('breakage-supplier/issued-transaction', [BreakageSupplierController::class, 'issuedTransaction'])->name('breakage-supplier.issued-transaction');
        Route::post('breakage-supplier/issued', [BreakageSupplierController::class, 'storeIssued'])->name('breakage-supplier.store-issued');
        Route::get('breakage-supplier/issued-modification', [BreakageSupplierController::class, 'issuedModification'])->name('breakage-supplier.issued-modification');
        Route::get('breakage-supplier/issued/{id}', [BreakageSupplierController::class, 'showIssued'])->name('breakage-supplier.show-issued');
        Route::put('breakage-supplier/issued/{id}', [BreakageSupplierController::class, 'updateIssued'])->name('breakage-supplier.update-issued');
        Route::delete('breakage-supplier/issued/{id}', [BreakageSupplierController::class, 'destroyIssued'])->name('breakage-supplier.destroy-issued');
        Route::get('breakage-supplier/get-items', [BreakageSupplierController::class, 'getItems'])->name('breakage-supplier.get-items');
        Route::get('breakage-supplier/get-batches/{itemId}', [BreakageSupplierController::class, 'getBatches'])->name('breakage-supplier.get-batches');
        Route::get('breakage-supplier/get-issued-past-invoices', [BreakageSupplierController::class, 'getIssuedPastInvoices'])->name('breakage-supplier.get-issued-past-invoices');
        Route::get('breakage-supplier/next-trn-no', [BreakageSupplierController::class, 'getNextTrnNo'])->name('breakage-supplier.next-trn-no');
        
        // Breakage/Expiry to Supplier Routes - Received & Unused Dump
        Route::get('breakage-supplier/received-transaction', [BreakageSupplierController::class, 'receivedTransaction'])->name('breakage-supplier.received-transaction');
        Route::post('breakage-supplier/store-received', [BreakageSupplierController::class, 'storeReceived'])->name('breakage-supplier.store-received');
        Route::get('breakage-supplier/received-modification', [BreakageSupplierController::class, 'receivedModification'])->name('breakage-supplier.received-modification');
        Route::get('breakage-supplier/get-received-past-invoices', [BreakageSupplierController::class, 'getReceivedPastInvoices'])->name('breakage-supplier.get-received-past-invoices');
        Route::get('breakage-supplier/received-details/{id}', [BreakageSupplierController::class, 'getReceivedDetails'])->name('breakage-supplier.received-details');
        Route::get('breakage-supplier/received/{id}', [BreakageSupplierController::class, 'showReceived'])->name('breakage-supplier.show-received');
        Route::post('breakage-supplier/update-received/{id}', [BreakageSupplierController::class, 'updateReceived'])->name('breakage-supplier.update-received');
        Route::put('breakage-supplier/received/{id}', [BreakageSupplierController::class, 'updateReceived'])->name('breakage-supplier.put-received');
        Route::delete('breakage-supplier/delete-received/{id}', [BreakageSupplierController::class, 'deleteReceived'])->name('breakage-supplier.delete-received');
        Route::get('breakage-supplier/supplier-purchases/{supplierId}', [BreakageSupplierController::class, 'getSupplierPurchases'])->name('breakage-supplier.supplier-purchases');
        Route::get('breakage-supplier/unused-dump-index', [BreakageSupplierController::class, 'unusedDumpIndex'])->name('breakage-supplier.unused-dump-index');
        Route::get('breakage-supplier/unused-dump-transaction', [BreakageSupplierController::class, 'unusedDumpTransaction'])->name('breakage-supplier.unused-dump-transaction');
        Route::post('breakage-supplier/unused-dump-transaction', [BreakageSupplierController::class, 'storeUnusedDump'])->name('breakage-supplier.store-unused-dump');
        Route::get('breakage-supplier/unused-dump-modification', [BreakageSupplierController::class, 'unusedDumpModification'])->name('breakage-supplier.unused-dump-modification');
        Route::delete('breakage-supplier/unused-dump/{id}', [BreakageSupplierController::class, 'destroyUnusedDump'])->name('breakage-supplier.destroy-unused-dump');
        Route::get('breakage-supplier/get-dump-past-invoices', [BreakageSupplierController::class, 'getDumpPastInvoices'])->name('breakage-supplier.get-dump-past-invoices');
        Route::get('breakage-supplier/unused-dump/{id}', [BreakageSupplierController::class, 'showUnusedDump'])->name('breakage-supplier.show-unused-dump');
        Route::put('breakage-supplier/unused-dump/{id}', [BreakageSupplierController::class, 'updateUnusedDump'])->name('breakage-supplier.update-unused-dump');
        
        Route::get('/api/countries', [CustomerController::class, 'getCountries'])->name('api.countries');
        Route::get('/api/states/{country}', [CustomerController::class, 'getStates'])->name('api.states');
        Route::get('/api/cities/{country}/{state}', [CustomerController::class, 'getCities'])->name('api.cities');

        // Credit Note Routes
        Route::get('credit-note/transaction', [CreditNoteController::class, 'transaction'])->name('credit-note.transaction');
        Route::get('credit-note/modification/{credit_note_no?}', [CreditNoteController::class, 'modification'])->name('credit-note.modification');
        Route::get('credit-note/invoices', [CreditNoteController::class, 'invoices'])->name('credit-note.invoices');
        Route::get('credit-note/fetch/{creditNoteNo}', [CreditNoteController::class, 'fetchByNumber'])->name('credit-note.fetch');
        Route::get('credit-note/next-number', [CreditNoteController::class, 'getNextCreditNoteNo'])->name('credit-note.next-number');
        Route::post('credit-note/store', [CreditNoteController::class, 'store'])->name('credit-note.store');
        Route::post('credit-note/party-invoices', [CreditNoteController::class, 'getPartyInvoices'])->name('credit-note.party-invoices');
        Route::get('credit-note/{id}/adjustments', [CreditNoteController::class, 'getAdjustments'])->name('credit-note.adjustments');
        Route::post('credit-note/{id}/save-adjustments', [CreditNoteController::class, 'saveAdjustments'])->name('credit-note.save-adjustments');
        Route::put('credit-note/{id}', [CreditNoteController::class, 'update'])->name('credit-note.update');
        Route::delete('credit-note/{id}', [CreditNoteController::class, 'destroy'])->name('credit-note.destroy');
        Route::get('credit-note/{id}/show', [CreditNoteController::class, 'show'])->name('credit-note.show');

        // Debit Note Routes
        Route::get('debit-note/transaction', [DebitNoteController::class, 'transaction'])->name('debit-note.transaction');
        Route::get('debit-note/modification/{debit_note_no?}', [DebitNoteController::class, 'modification'])->name('debit-note.modification');
        Route::get('debit-note/invoices', [DebitNoteController::class, 'invoices'])->name('debit-note.invoices');
        Route::get('debit-note/fetch/{debitNoteNo}', [DebitNoteController::class, 'fetchByNumber'])->name('debit-note.fetch');
        Route::get('debit-note/next-number', [DebitNoteController::class, 'getNextDebitNoteNo'])->name('debit-note.next-number');
        Route::post('debit-note/store', [DebitNoteController::class, 'store'])->name('debit-note.store');
        Route::put('debit-note/{id}', [DebitNoteController::class, 'update'])->name('debit-note.update');
        Route::delete('debit-note/{id}', [DebitNoteController::class, 'destroy'])->name('debit-note.destroy');
        Route::get('debit-note/{id}/show', [DebitNoteController::class, 'show'])->name('debit-note.show');
        
        // Debit Note Adjustment Routes
        Route::get('debit-note/supplier/{supplierId}/purchase-invoices', [DebitNoteController::class, 'getSupplierPurchaseInvoices'])->name('debit-note.supplier-purchase-invoices');
        Route::get('debit-note/supplier/{supplierId}/credit-notes', [DebitNoteController::class, 'getSupplierCreditNotes'])->name('debit-note.supplier-credit-notes');
        Route::get('debit-note/supplier/{supplierId}/past-adjustments', [DebitNoteController::class, 'getSupplierPastAdjustments'])->name('debit-note.supplier-past-adjustments');
        Route::get('debit-note/{debitNoteId}/adjustments', [DebitNoteController::class, 'getAdjustments'])->name('debit-note.adjustments');
        Route::post('debit-note/{id}/save-adjustments', [DebitNoteController::class, 'saveAdjustments'])->name('debit-note.save-adjustments');
        Route::post('debit-note/adjustment/save', [DebitNoteController::class, 'saveAdjustment'])->name('debit-note.adjustment.save');
        Route::delete('debit-note/adjustment/{adjustmentId}', [DebitNoteController::class, 'deleteAdjustment'])->name('debit-note.adjustment.delete');

        // Replacement Note Routes
        Route::get('replacement-note', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'index'])->name('replacement-note.index');
        Route::get('replacement-note/transaction', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'transaction'])->name('replacement-note.transaction');
        Route::get('replacement-note/modification', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'modification'])->name('replacement-note.modification');
        Route::post('replacement-note/store', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'store'])->name('replacement-note.store');
        Route::get('replacement-note/past-notes', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'getPastNotes'])->name('replacement-note.past-notes');
        Route::get('replacement-note/details/{id}', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'getDetails'])->name('replacement-note.details');
        Route::get('replacement-note/get-by-rn-no/{rnNo}', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'getByRnNo'])->name('replacement-note.get-by-rn-no');
        Route::post('replacement-note/update/{id}', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'update'])->name('replacement-note.update');
        Route::get('replacement-note/{id}', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'show'])->name('replacement-note.show');
        Route::delete('replacement-note/{id}', [\App\Http\Controllers\Admin\ReplacementNoteController::class, 'destroy'])->name('replacement-note.destroy');

        // Replacement Received Routes
        Route::get('replacement-received', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'index'])->name('replacement-received.index');
        Route::get('replacement-received/transaction', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'transaction'])->name('replacement-received.transaction');
        Route::get('replacement-received/modification', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'modification'])->name('replacement-received.modification');
        Route::post('replacement-received/store', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'store'])->name('replacement-received.store');
        Route::get('replacement-received/past-transactions', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'getPastTransactions'])->name('replacement-received.past-transactions');
        Route::get('replacement-received/details/{id}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'getDetails'])->name('replacement-received.details');
        Route::get('replacement-received/supplier-purchase-returns/{supplierId}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'getSupplierPurchaseReturns'])->name('replacement-received.supplier-purchase-returns');
        Route::get('replacement-received/adjustments/{id}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'getAdjustments'])->name('replacement-received.adjustments');
        Route::post('replacement-received/update/{id}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'update'])->name('replacement-received.update');
        Route::get('replacement-received/{id}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'show'])->name('replacement-received.show');
        Route::delete('replacement-received/{id}', [\App\Http\Controllers\Admin\ReplacementReceivedController::class, 'destroy'])->name('replacement-received.destroy');

        // Stock Adjustment Routes
        Route::get('stock-adjustment/transaction', [StockAdjustmentController::class, 'transaction'])->name('stock-adjustment.transaction');
        Route::get('stock-adjustment/modification/{trn_no?}', [StockAdjustmentController::class, 'modification'])->name('stock-adjustment.modification');
        Route::get('stock-adjustment/invoices', [StockAdjustmentController::class, 'invoices'])->name('stock-adjustment.invoices');
        Route::get('stock-adjustment/fetch/{trnNo}', [StockAdjustmentController::class, 'fetchByTrnNo'])->name('stock-adjustment.fetch');
        Route::get('stock-adjustment/next-trn-no', [StockAdjustmentController::class, 'getNextTrnNo'])->name('stock-adjustment.next-trn-no');
        Route::get('stock-adjustment/past-adjustments', [StockAdjustmentController::class, 'getPastAdjustments'])->name('stock-adjustment.past-adjustments');
        Route::post('stock-adjustment/store', [StockAdjustmentController::class, 'store'])->name('stock-adjustment.store');
        Route::put('stock-adjustment/{id}', [StockAdjustmentController::class, 'update'])->name('stock-adjustment.update');
        Route::delete('stock-adjustment/{id}', [StockAdjustmentController::class, 'destroy'])->name('stock-adjustment.destroy');
        Route::get('stock-adjustment/{id}/details', [StockAdjustmentController::class, 'getDetails'])->name('stock-adjustment.details');

        // Quotation Routes
        Route::get('quotation', [\App\Http\Controllers\Admin\QuotationController::class, 'index'])->name('quotation.index');
        Route::get('quotation/transaction', [\App\Http\Controllers\Admin\QuotationController::class, 'transaction'])->name('quotation.transaction');
        Route::get('quotation/modification', [\App\Http\Controllers\Admin\QuotationController::class, 'modification'])->name('quotation.modification');
        Route::get('quotation/get-items', [\App\Http\Controllers\Admin\QuotationController::class, 'getItems'])->name('quotation.getItems');
        Route::get('quotation/get-batches/{itemId}', [\App\Http\Controllers\Admin\QuotationController::class, 'getBatches'])->name('quotation.getBatches');
        Route::get('quotation/get-quotations', [\App\Http\Controllers\Admin\QuotationController::class, 'getQuotations'])->name('quotation.getQuotations');
        Route::post('quotation', [\App\Http\Controllers\Admin\QuotationController::class, 'store'])->name('quotation.store');
        Route::get('quotation/{id}', [\App\Http\Controllers\Admin\QuotationController::class, 'show'])->name('quotation.show');
        Route::get('quotation/{id}/edit', [\App\Http\Controllers\Admin\QuotationController::class, 'edit'])->name('quotation.edit');
        Route::put('quotation/{id}', [\App\Http\Controllers\Admin\QuotationController::class, 'update'])->name('quotation.update');
        Route::post('quotation/{id}/cancel', [\App\Http\Controllers\Admin\QuotationController::class, 'cancel'])->name('quotation.cancel');

        // Pending Order Item Routes
        Route::get('pending-order-item', [\App\Http\Controllers\Admin\PendingOrderItemController::class, 'index'])->name('pending-order-item.index');
        Route::get('pending-order-item/transaction', [\App\Http\Controllers\Admin\PendingOrderItemController::class, 'transaction'])->name('pending-order-item.transaction');
        Route::get('pending-order-item/get-items', [\App\Http\Controllers\Admin\PendingOrderItemController::class, 'getItems'])->name('pending-order-item.getItems');
        Route::post('pending-order-item', [\App\Http\Controllers\Admin\PendingOrderItemController::class, 'store'])->name('pending-order-item.store');
        Route::delete('pending-order-item/{id}', [\App\Http\Controllers\Admin\PendingOrderItemController::class, 'destroy'])->name('pending-order-item.destroy');

        // Claim to Supplier Routes
        Route::get('claim-to-supplier', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'index'])->name('claim-to-supplier.index');
        Route::get('claim-to-supplier/transaction', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'transaction'])->name('claim-to-supplier.transaction');
        Route::get('claim-to-supplier/modification', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'modification'])->name('claim-to-supplier.modification');
        Route::get('claim-to-supplier/next-trn-no', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'getNextTransactionNumber'])->name('claim-to-supplier.next-trn-no');
        Route::get('claim-to-supplier/batches', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'getBatches'])->name('claim-to-supplier.batches');
        Route::post('claim-to-supplier/store', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'store'])->name('claim-to-supplier.store');
        Route::get('claim-to-supplier/past-claims', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'getPastClaims'])->name('claim-to-supplier.past-claims');
        Route::get('claim-to-supplier/details/{id}', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'getClaimDetails'])->name('claim-to-supplier.details');
        Route::get('claim-to-supplier/get-by-claim-no/{claimNo}', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'getByClaimNo'])->name('claim-to-supplier.get-by-claim-no');
        Route::put('claim-to-supplier/{id}', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'update'])->name('claim-to-supplier.update');
        Route::get('claim-to-supplier/{id}', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'show'])->name('claim-to-supplier.show');
        Route::delete('claim-to-supplier/{id}', [\App\Http\Controllers\Admin\ClaimToSupplierController::class, 'destroy'])->name('claim-to-supplier.destroy');

        // Customer Receipt Routes
        Route::get('customer-receipt', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'index'])->name('customer-receipt.index');
        Route::get('customer-receipt/transaction', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'transaction'])->name('customer-receipt.transaction');
        Route::get('customer-receipt/modification', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'modification'])->name('customer-receipt.modification');
        Route::get('customer-receipt/get-receipts', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'getReceipts'])->name('customer-receipt.get-receipts');
        Route::get('customer-receipt/get-by-trn/{trnNo}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'getByTrnNo'])->name('customer-receipt.get-by-trn');
        Route::get('customer-receipt/next-trn-no', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'getNextTrnNo'])->name('customer-receipt.next-trn-no');
        Route::get('customer-receipt/customer-outstanding/{customerId}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'getCustomerOutstanding'])->name('customer-receipt.customer-outstanding');
        Route::get('customer-receipt/details/{id}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'getDetails'])->name('customer-receipt.details');
        Route::post('customer-receipt', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'store'])->name('customer-receipt.store');
        Route::get('customer-receipt/{id}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'show'])->name('customer-receipt.show');
        Route::put('customer-receipt/{id}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'update'])->name('customer-receipt.update');
        Route::delete('customer-receipt/{id}', [\App\Http\Controllers\Admin\CustomerReceiptController::class, 'destroy'])->name('customer-receipt.destroy');

        // Cheque Return Unpaid Routes
        Route::get('cheque-return', [\App\Http\Controllers\Admin\ChequeReturnController::class, 'index'])->name('cheque-return.index');
        Route::get('cheque-return/get-cheques', [\App\Http\Controllers\Admin\ChequeReturnController::class, 'getCheques'])->name('cheque-return.get-cheques');
        Route::post('cheque-return/return', [\App\Http\Controllers\Admin\ChequeReturnController::class, 'returnCheque'])->name('cheque-return.return');
        Route::post('cheque-return/cancel', [\App\Http\Controllers\Admin\ChequeReturnController::class, 'cancelReturn'])->name('cheque-return.cancel');
        Route::get('cheque-return/history', [\App\Http\Controllers\Admin\ChequeReturnController::class, 'getHistory'])->name('cheque-return.history');

        // Deposit Slip Routes
        Route::get('deposit-slip', [\App\Http\Controllers\Admin\DepositSlipController::class, 'index'])->name('deposit-slip.index');
        Route::get('deposit-slip/get-cheques', [\App\Http\Controllers\Admin\DepositSlipController::class, 'getCheques'])->name('deposit-slip.get-cheques');
        Route::post('deposit-slip/store', [\App\Http\Controllers\Admin\DepositSlipController::class, 'store'])->name('deposit-slip.store');
        Route::post('deposit-slip/unpost', [\App\Http\Controllers\Admin\DepositSlipController::class, 'unpost'])->name('deposit-slip.unpost');
        Route::get('deposit-slip/summary', [\App\Http\Controllers\Admin\DepositSlipController::class, 'getSummary'])->name('deposit-slip.summary');

        // Supplier Payment Routes
        Route::get('supplier-payment', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'index'])->name('supplier-payment.index');
        Route::get('supplier-payment/transaction', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'transaction'])->name('supplier-payment.transaction');
        Route::get('supplier-payment/modification', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'modification'])->name('supplier-payment.modification');
        Route::get('supplier-payment/get-payments', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'getPayments'])->name('supplier-payment.get-payments');
        Route::get('supplier-payment/get-by-trn/{trnNo}', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'getByTrnNo'])->name('supplier-payment.get-by-trn');
        Route::get('supplier-payment/next-trn-no', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'getNextTrnNo'])->name('supplier-payment.next-trn-no');
        Route::get('supplier-payment/supplier-outstanding/{supplierId}', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'getSupplierOutstanding'])->name('supplier-payment.supplier-outstanding');
        Route::post('supplier-payment', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'store'])->name('supplier-payment.store');
        Route::get('supplier-payment/{id}', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'show'])->name('supplier-payment.show');
        Route::put('supplier-payment/{id}', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'update'])->name('supplier-payment.update');
        Route::delete('supplier-payment/{id}', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'destroy'])->name('supplier-payment.destroy');

        // Sale Voucher Routes (HSN based sale without stock)
        Route::get('sale-voucher', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'index'])->name('sale-voucher.index');
        Route::get('sale-voucher/transaction', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'transaction'])->name('sale-voucher.transaction');
        Route::get('sale-voucher/modification', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'modification'])->name('sale-voucher.modification');
        Route::get('sale-voucher/get-vouchers', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'getVouchers'])->name('sale-voucher.get-vouchers');
        Route::get('sale-voucher/search', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'searchVoucher'])->name('sale-voucher.search');
        Route::get('sale-voucher/{id}/details', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'getDetails'])->name('sale-voucher.details');
        Route::post('sale-voucher', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'store'])->name('sale-voucher.store');
        Route::put('sale-voucher/{id}', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'update'])->name('sale-voucher.update');
        Route::get('sale-voucher/hsn-codes', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'getHsnCodes'])->name('sale-voucher.hsn-codes');
        Route::delete('sale-voucher/{id}', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'destroy'])->name('sale-voucher.destroy');

        // Voucher Entry Routes
        Route::get('voucher-entry', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'index'])->name('voucher-entry.index');
        Route::get('voucher-entry/transaction', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'transaction'])->name('voucher-entry.transaction');
        Route::get('voucher-entry/modification', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'modification'])->name('voucher-entry.modification');
        Route::post('voucher-entry', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'store'])->name('voucher-entry.store');
        Route::get('voucher-entry/{id}', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'show'])->name('voucher-entry.show');
        Route::get('voucher-entry/{id}/details', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'getDetails'])->name('voucher-entry.details');
        Route::put('voucher-entry/{id}', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'update'])->name('voucher-entry.update');
        Route::delete('voucher-entry/{id}', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'destroy'])->name('voucher-entry.destroy');
        Route::get('voucher-entry-list', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'getVouchers'])->name('voucher-entry.get-vouchers');
        Route::get('voucher-entry-search', [\App\Http\Controllers\Admin\VoucherEntryController::class, 'searchVoucher'])->name('voucher-entry.search');

        // Voucher Purchase (Input GST) Routes
        Route::get('voucher-purchase', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'index'])->name('voucher-purchase.index');
        Route::get('voucher-purchase/transaction', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'transaction'])->name('voucher-purchase.transaction');
        Route::get('voucher-purchase/modification', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'modification'])->name('voucher-purchase.modification');
        Route::post('voucher-purchase', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'store'])->name('voucher-purchase.store');
        Route::get('voucher-purchase/{id}', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'show'])->name('voucher-purchase.show');
        Route::get('voucher-purchase/{id}/details', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'getDetails'])->name('voucher-purchase.details');
        Route::put('voucher-purchase/{id}', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'update'])->name('voucher-purchase.update');
        Route::delete('voucher-purchase/{id}', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'destroy'])->name('voucher-purchase.destroy');
        Route::get('voucher-purchase-list', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'getVouchers'])->name('voucher-purchase.get-vouchers');
        Route::get('voucher-purchase-search', [\App\Http\Controllers\Admin\VoucherPurchaseController::class, 'searchVoucher'])->name('voucher-purchase.search');

        // Voucher Income (Output GST) Routes
        Route::get('voucher-income', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'index'])->name('voucher-income.index');
        Route::get('voucher-income/transaction', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'transaction'])->name('voucher-income.transaction');
        Route::get('voucher-income/modification', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'modification'])->name('voucher-income.modification');
        Route::post('voucher-income', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'store'])->name('voucher-income.store');
        Route::get('voucher-income/{id}', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'show'])->name('voucher-income.show');
        Route::get('voucher-income/{id}/details', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'getDetails'])->name('voucher-income.details');
        Route::put('voucher-income/{id}', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'update'])->name('voucher-income.update');
        Route::delete('voucher-income/{id}', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'destroy'])->name('voucher-income.destroy');
        Route::get('voucher-income-list', [\App\Http\Controllers\Admin\VoucherIncomeController::class, 'getVouchers'])->name('voucher-income.get-vouchers');
        // Sale Voucher Routes
        Route::get('sale-voucher', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'index'])->name('sale-voucher.index');
        Route::get('sale-voucher/transaction', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'transaction'])->name('sale-voucher.transaction');
        Route::get('sale-voucher/modification', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'modification'])->name('sale-voucher.modification');
        Route::get('sale-voucher/get-vouchers', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'getVouchers'])->name('sale-voucher.get-vouchers');
        Route::get('sale-voucher/search', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'searchVoucher'])->name('sale-voucher.search');
        Route::get('sale-voucher/{id}/details', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'getDetails'])->name('sale-voucher.details');
        Route::post('sale-voucher', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'store'])->name('sale-voucher.store');
        Route::get('sale-voucher/{id}', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'show'])->name('sale-voucher.show');
        Route::put('sale-voucher/{id}', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'update'])->name('sale-voucher.update');
        Route::delete('sale-voucher/{id}', [\App\Http\Controllers\Admin\SaleVoucherController::class, 'destroy'])->name('sale-voucher.destroy');

        // Purchase Voucher Routes
        Route::get('purchase-voucher', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'index'])->name('purchase-voucher.index');
        Route::get('purchase-voucher/transaction', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'transaction'])->name('purchase-voucher.transaction');
        Route::get('purchase-voucher/modification', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'modification'])->name('purchase-voucher.modification');
        Route::get('purchase-voucher/get-vouchers', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'getVouchers'])->name('purchase-voucher.get-vouchers');
        Route::get('purchase-voucher/search', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'searchVoucher'])->name('purchase-voucher.search');
        Route::get('purchase-voucher/{id}/details', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'getDetails'])->name('purchase-voucher.details');
        Route::post('purchase-voucher', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'store'])->name('purchase-voucher.store');
        Route::get('purchase-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'show'])->name('purchase-voucher.show');
        Route::put('purchase-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'update'])->name('purchase-voucher.update');
        Route::delete('purchase-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseVoucherController::class, 'destroy'])->name('purchase-voucher.destroy');

        // Sale Return Voucher Routes
        Route::get('sale-return-voucher', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'index'])->name('sale-return-voucher.index');
        Route::get('sale-return-voucher/transaction', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'transaction'])->name('sale-return-voucher.transaction');
        Route::get('sale-return-voucher/modification', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'modification'])->name('sale-return-voucher.modification');
        Route::get('sale-return-voucher/get-vouchers', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'getVouchers'])->name('sale-return-voucher.get-vouchers');
        Route::get('sale-return-voucher/search', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'searchVoucher'])->name('sale-return-voucher.search');
        Route::get('sale-return-voucher/{id}/details', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'getDetails'])->name('sale-return-voucher.details');
        Route::post('sale-return-voucher', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'store'])->name('sale-return-voucher.store');
        Route::get('sale-return-voucher/{id}', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'show'])->name('sale-return-voucher.show');
        Route::put('sale-return-voucher/{id}', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'update'])->name('sale-return-voucher.update');
        Route::delete('sale-return-voucher/{id}', [\App\Http\Controllers\Admin\SaleReturnVoucherController::class, 'destroy'])->name('sale-return-voucher.destroy');

        // Purchase Return Voucher Routes
        Route::get('purchase-return-voucher', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'index'])->name('purchase-return-voucher.index');
        Route::get('purchase-return-voucher/transaction', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'transaction'])->name('purchase-return-voucher.transaction');
        Route::get('purchase-return-voucher/modification', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'modification'])->name('purchase-return-voucher.modification');
        Route::get('purchase-return-voucher/get-vouchers', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'getVouchers'])->name('purchase-return-voucher.get-vouchers');
        Route::get('purchase-return-voucher/search', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'searchVoucher'])->name('purchase-return-voucher.search');
        Route::get('purchase-return-voucher/{id}/details', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'getDetails'])->name('purchase-return-voucher.details');
        Route::post('purchase-return-voucher', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'store'])->name('purchase-return-voucher.store');
        Route::get('purchase-return-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'show'])->name('purchase-return-voucher.show');
        Route::put('purchase-return-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'update'])->name('purchase-return-voucher.update');
        Route::delete('purchase-return-voucher/{id}', [\App\Http\Controllers\Admin\PurchaseReturnVoucherController::class, 'destroy'])->name('purchase-return-voucher.destroy');

        // Multi Voucher Routes
        Route::get('multi-voucher', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'index'])->name('multi-voucher.index');
        Route::get('multi-voucher/transaction', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'transaction'])->name('multi-voucher.transaction');
        Route::get('multi-voucher/modification', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'modification'])->name('multi-voucher.modification');
        Route::get('multi-voucher/get/{voucherNo}', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'getByVoucherNo'])->name('multi-voucher.get');
        Route::post('multi-voucher', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'store'])->name('multi-voucher.store');
        Route::get('multi-voucher/{id}', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'show'])->name('multi-voucher.show');
        Route::put('multi-voucher/{id}', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'update'])->name('multi-voucher.update');
        Route::delete('multi-voucher/{id}', [\App\Http\Controllers\Admin\MultiVoucherController::class, 'destroy'])->name('multi-voucher.destroy');

        // Bank Transaction Routes
        Route::get('bank-transaction', [\App\Http\Controllers\Admin\BankTransactionController::class, 'index'])->name('bank-transaction.index');
        Route::get('bank-transaction/transaction', [\App\Http\Controllers\Admin\BankTransactionController::class, 'transaction'])->name('bank-transaction.transaction');
        Route::post('bank-transaction', [\App\Http\Controllers\Admin\BankTransactionController::class, 'store'])->name('bank-transaction.store');
        Route::get('bank-transaction/{id}', [\App\Http\Controllers\Admin\BankTransactionController::class, 'show'])->name('bank-transaction.show');
        Route::delete('bank-transaction/{id}', [\App\Http\Controllers\Admin\BankTransactionController::class, 'destroy'])->name('bank-transaction.destroy');

        // Sale Return Replacement Routes
        Route::get('sale-return-replacement', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'index'])->name('sale-return-replacement.index');
        Route::get('sale-return-replacement/transaction', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'transaction'])->name('sale-return-replacement.transaction');
        Route::get('sale-return-replacement/modification', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'modification'])->name('sale-return-replacement.modification');
        Route::get('sale-return-replacement/get/{trnNo}', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'getByTrnNo'])->name('sale-return-replacement.get');
        Route::post('sale-return-replacement', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'store'])->name('sale-return-replacement.store');
        Route::get('sale-return-replacement/{id}', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'show'])->name('sale-return-replacement.show');
        Route::put('sale-return-replacement/{id}', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'update'])->name('sale-return-replacement.update');
        Route::delete('sale-return-replacement/{id}', [\App\Http\Controllers\Admin\SaleReturnReplacementController::class, 'destroy'])->name('sale-return-replacement.destroy');
        
        // =============== ADMINISTRATION ===============
        Route::prefix('administration')->name('administration.')->group(function () {
            // Hotkeys Management
            Route::get('hotkeys', [\App\Http\Controllers\Admin\HotkeyController::class, 'index'])->name('hotkeys.index');
            Route::get('hotkeys/data', [\App\Http\Controllers\Admin\HotkeyController::class, 'getData'])->name('hotkeys.data');
            Route::get('hotkeys/{hotkey}/edit', [\App\Http\Controllers\Admin\HotkeyController::class, 'edit'])->name('hotkeys.edit');
            Route::put('hotkeys/{hotkey}', [\App\Http\Controllers\Admin\HotkeyController::class, 'update'])->name('hotkeys.update');
            Route::post('hotkeys/{hotkey}/toggle-status', [\App\Http\Controllers\Admin\HotkeyController::class, 'toggleStatus'])->name('hotkeys.toggle-status');
            Route::post('hotkeys/check-key', [\App\Http\Controllers\Admin\HotkeyController::class, 'checkKey'])->name('hotkeys.check-key');
            Route::post('hotkeys/reset-to-default', [\App\Http\Controllers\Admin\HotkeyController::class, 'resetToDefault'])->name('hotkeys.reset-to-default');
        });

        // Page Content Settings
        Route::get('page-settings', [\App\Http\Controllers\Admin\PageSettingController::class, 'index'])->name('page-settings.index');
        Route::put('page-settings', [\App\Http\Controllers\Admin\PageSettingController::class, 'update'])->name('page-settings.update');
        
        // Hotkeys JSON API (for keyboard-shortcuts.js)
        Route::get('api/hotkeys', [\App\Http\Controllers\Admin\HotkeyController::class, 'getHotkeysJson'])->name('api.hotkeys');
    });
    // Profile settings page
    Route::get('/profile', function () {
        return view('admin.settings.profile');
    })->name('profile.settings');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password/change', [ProfileController::class, 'showChangePassword'])->name('password.change.form');
    Route::post('/password/change', [ProfileController::class, 'changePassword'])->name('password.change');
});

// Static Pages (accessible to all)
Route::get('/privacy-policy', [\App\Http\Controllers\PagesController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [\App\Http\Controllers\PagesController::class, 'terms'])->name('pages.terms');
Route::get('/support', [\App\Http\Controllers\PagesController::class, 'support'])->name('pages.support');
Route::get('/documentation', [\App\Http\Controllers\PagesController::class, 'documentation'])->name('pages.documentation');
