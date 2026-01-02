@extends('layouts.admin')

@section('title', 'Purchase Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Reports</h4>
    </div>

    <!-- Main Purchase Reports -->
    <h5 class="text-primary mb-3"><i class="bi bi-journal-text me-2"></i>Main Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Purchase Book -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase Book</h6>
                    <p class="card-text small text-muted">Date wise purchase register</p>
                    <a href="{{ route('admin.reports.purchase.purchase-book') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Purchase Book With Sale Value -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase Book With Sale Value</h6>
                    <p class="card-text small text-muted">Compare purchase and sale values</p>
                    <a href="{{ route('admin.reports.purchase.purchase-book-sale-value') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Party Wise Purchase -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Party Wise Purchase</h6>
                    <p class="card-text small text-muted">Supplier wise purchase summary</p>
                    <a href="{{ route('admin.reports.purchase.party-wise-purchase') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Monthly Purchase Sales Summary -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-month text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Monthly Purchase Summary</h6>
                    <p class="card-text small text-muted">Month wise purchase summary</p>
                    <a href="{{ route('admin.reports.purchase.monthly-purchase-summary') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Debit / Credit Note - Report -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-receipt-cutoff text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Debit / Credit Note Report</h6>
                    <p class="card-text small text-muted">DN/CN register for purchases</p>
                    <a href="{{ route('admin.reports.purchase.debit-credit-note') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Day Purchase Summary - Item Wise -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Day Purchase Summary</h6>
                    <p class="card-text small text-muted">Item wise daily summary</p>
                    <a href="{{ route('admin.reports.purchase.day-purchase-summary') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Purchase / Return Book Item wise -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-left-right text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase/Return Item Wise</h6>
                    <p class="card-text small text-muted">Combined purchase & return</p>
                    <a href="{{ route('admin.reports.purchase.purchase-return-item-wise') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Local / Central Purchase Register -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Local/Central Register</h6>
                    <p class="card-text small text-muted">GST type wise purchases</p>
                    <a href="{{ route('admin.reports.purchase.local-central-register') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Purchase Voucher Detail -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-file-text text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase Voucher Detail</h6>
                    <p class="card-text small text-muted">Voucher wise details</p>
                    <a href="{{ route('admin.reports.purchase.purchase-voucher-detail') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Short Expiry Received -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Short Expiry Received</h6>
                    <p class="card-text small text-muted">Near expiry items received</p>
                    <a href="{{ route('admin.reports.purchase.short-expiry-received') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Purchase Return List -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-return-left text-secondary fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase Return List</h6>
                    <p class="card-text small text-muted">Return transactions</p>
                    <a href="{{ route('admin.reports.purchase.purchase-return-list') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- GST Reports -->
    <h5 class="text-success mb-3"><i class="bi bi-file-earmark-ruled me-2"></i>GST / Tax Reports</h5>
    <div class="row g-3 mb-4">
        <!-- GST SET OFF -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-receipt text-success fs-1 mb-3"></i>
                    <h6 class="card-title">GST SET OFF</h6>
                    <p class="card-text small text-muted">Input/Output GST adjustment</p>
                    <a href="{{ route('admin.reports.purchase.gst-set-off') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Challan Reports -->
    <h5 class="text-warning mb-3"><i class="bi bi-receipt me-2"></i>Purchase Challan Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Purchase Challan Book -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-journal-bookmark text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Purchase Challan Book</h6>
                    <p class="card-text small text-muted">Challan register</p>
                    <a href="{{ route('admin.reports.purchase.challan.purchase-challan-book') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Pending Challans -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Pending Challans</h6>
                    <p class="card-text small text-muted">Un-invoiced challans</p>
                    <a href="{{ route('admin.reports.purchase.challan.pending-challans') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Miscellaneous Purchase Analysis -->
    <h5 class="text-info mb-3"><i class="bi bi-pie-chart me-2"></i>Miscellaneous Purchase Analysis</h5>
    <div class="row g-3 mb-4">
        <!-- Supplier Wise Purchase -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-person-workspace text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Supplier Wise Purchase</h6>
                    <p class="card-text small text-muted">Supplier performance analysis</p>
                    <a href="{{ route('admin.reports.purchase.misc.supplier.all-supplier') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Area Wise Purchase -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-geo text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Area Wise Purchase</h6>
                    <p class="card-text small text-muted">Area wise purchase analysis</p>
                    <a href="#" class="btn btn-outline-info btn-sm disabled">View Report</a>
                </div>
            </div>
        </div>

        <!-- Company Wise Purchase -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-building text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Company Wise Purchase</h6>
                    <p class="card-text small text-muted">Company wise purchase analysis</p>
                    <a href="{{ route('admin.reports.purchase.misc.company.all-company') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Item Wise Purchase -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-box text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Item Wise Purchase</h6>
                    <p class="card-text small text-muted">Item wise purchase analysis</p>
                    <a href="{{ route('admin.reports.purchase.misc.item.all-item-purchase') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Reports -->
    <h5 class="text-secondary mb-3"><i class="bi bi-folder2-open me-2"></i>Other Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Supplier List -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill text-secondary fs-1 mb-3"></i>
                    <h6 class="card-title">Supplier List</h6>
                    <p class="card-text small text-muted">All supplier records</p>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Item List -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-list-ul text-secondary fs-1 mb-3"></i>
                    <h6 class="card-title">Item List</h6>
                    <p class="card-text small text-muted">All item records</p>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Cancelled Invoices -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Cancelled Invoices</h6>
                    <p class="card-text small text-muted">List of cancelled bills</p>
                    <a href="#" class="btn btn-outline-danger btn-sm disabled">View Report</a>
                </div>
            </div>
        </div>

        <!-- Missing Invoices -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Missing Invoices</h6>
                    <p class="card-text small text-muted">Gap in invoice numbers</p>
                    <a href="#" class="btn btn-outline-danger btn-sm disabled">View Report</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transition: all 0.3s ease;
}
.card {
    transition: all 0.3s ease;
}
</style>
@endpush
