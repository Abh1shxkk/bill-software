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
use App\Http\Controllers\ProfileController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    // Redirect logged-in users to their dashboard
    if (auth()->check()) {
        $role = auth()->user()->role;
        return $role === 'admin' 
            ? redirect('/admin/dashboard') 
            : redirect('/user/dashboard');
    }
    return view('auth.login');
});

// Admin
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::prefix('admin')->name('admin.')->group(function () {
        // Company routes - MUST be before resource route
        Route::post('companies/multiple-delete', [CompanyController::class, 'multipleDelete'])->name('companies.multiple-delete');
        
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
        Route::get('api/verify-batch-supplier', [PurchaseReturnController::class, 'verifyBatchSupplier'])->name('api.verify-batch-supplier');
        Route::get('api/party-details/{type}/{id}', [ItemController::class, 'getPartyDetails'])->name('api.party-details');
        
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
        Route::post('areas/multiple-delete', [AreaController::class, 'multipleDelete'])->name('areas.multiple-delete');
        Route::resource('areas', AreaController::class)->except(['show']);
        Route::post('routes/multiple-delete', [RouteController::class, 'multipleDelete'])->name('routes.multiple-delete');
        Route::resource('routes', RouteController::class)->except(['show']);
        Route::post('states/multiple-delete', [StateController::class, 'multipleDelete'])->name('states.multiple-delete');
        Route::post('area-managers/multiple-delete', [AreaManagerController::class, 'multipleDelete'])->name('area-managers.multiple-delete');
        Route::post('regional-managers/multiple-delete', [RegionalManagerController::class, 'multipleDelete'])->name('regional-managers.multiple-delete');
        Route::post('marketing-managers/multiple-delete', [MarketingManagerController::class, 'multipleDelete'])->name('marketing-managers.multiple-delete');
        Route::post('general-managers/multiple-delete', [GeneralManagerController::class, 'multipleDelete'])->name('general-managers.multiple-delete');
        Route::post('divisional-managers/multiple-delete', [DivisionalManagerController::class, 'multipleDelete'])->name('divisional-managers.multiple-delete');
        Route::post('country-managers/multiple-delete', [CountryManagerController::class, 'multipleDelete'])->name('country-managers.multiple-delete');
        Route::resource('states', StateController::class)->except(['show']);
        Route::resource('area-managers', AreaManagerController::class);
        Route::resource('regional-managers', RegionalManagerController::class);
        Route::resource('marketing-managers', MarketingManagerController::class);
        Route::resource('general-managers', GeneralManagerController::class);
        Route::resource('divisional-managers', DivisionalManagerController::class);
        Route::resource('country-managers', CountryManagerController::class);
        
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
        
        // Breakage/Expiry to Supplier Routes
        Route::get('breakage-supplier/issued-transaction', [BreakageSupplierController::class, 'issuedTransaction'])->name('breakage-supplier.issued-transaction');
        Route::get('breakage-supplier/issued-modification', [BreakageSupplierController::class, 'issuedModification'])->name('breakage-supplier.issued-modification');
        Route::get('breakage-supplier/received-transaction', [BreakageSupplierController::class, 'receivedTransaction'])->name('breakage-supplier.received-transaction');
        Route::get('breakage-supplier/received-modification', [BreakageSupplierController::class, 'receivedModification'])->name('breakage-supplier.received-modification');
        Route::get('breakage-supplier/unused-dump-transaction', [BreakageSupplierController::class, 'unusedDumpTransaction'])->name('breakage-supplier.unused-dump-transaction');
        Route::get('breakage-supplier/unused-dump-modification', [BreakageSupplierController::class, 'unusedDumpModification'])->name('breakage-supplier.unused-dump-modification');
        
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
    });
    // Profile settings page
    Route::get('/profile', function () {
        return view('admin.settings.profile');
    })->name('profile.settings');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password/change', [ProfileController::class, 'showChangePassword'])->name('password.change.form');
    Route::post('/password/change', [ProfileController::class, 'changePassword'])->name('password.change');
});

// User
Route::middleware(['user'])->group(function () {
    Route::view('/user/dashboard', 'user.dashboard');
});

