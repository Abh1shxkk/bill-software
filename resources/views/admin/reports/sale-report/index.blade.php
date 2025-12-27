@extends('layouts.admin')

@section('title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sales Reports</h4>
    </div>

    <!-- Main Sales Reports -->
    <h5 class="text-primary mb-3"><i class="bi bi-journal-text me-2"></i>Main Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Sales Book -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Book</h6>
                    <p class="card-text small text-muted">Date wise sales register</p>
                    <a href="{{ route('admin.reports.sales.sales-book') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sale Book Party Wise -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-people text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Sale Book Party Wise</h6>
                    <p class="card-text small text-muted">Customer wise sales summary</p>
                    <a href="{{ route('admin.reports.sales.sales-book-party-wise') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Day Sales Summary -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Day Sales Summary</h6>
                    <p class="card-text small text-muted">Item wise daily summary</p>
                    <a href="{{ route('admin.reports.sales.day-sales-summary-item-wise') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sales Summary -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Summary</h6>
                    <p class="card-text small text-muted">Period wise summary</p>
                    <a href="{{ route('admin.reports.sales.sales-summary') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sales Bills Printing -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-printer text-secondary fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Bills Printing</h6>
                    <p class="card-text small text-muted">Bulk bill printing</p>
                    <a href="{{ route('admin.reports.sales.sales-bills-printing') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sale Sheet -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-file-spreadsheet text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Sale Sheet</h6>
                    <p class="card-text small text-muted">Detailed item wise report</p>
                    <a href="{{ route('admin.reports.sales.sale-sheet') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Dispatch Sheet -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-truck text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Dispatch Sheet</h6>
                    <p class="card-text small text-muted">Delivery tracking</p>
                    <a href="{{ route('admin.reports.sales.dispatch-sheet') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sale/Return Book Item Wise -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-left-right text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Sale/Return Item Wise</h6>
                    <p class="card-text small text-muted">Combined sale & return</p>
                    <a href="{{ route('admin.reports.sales.sale-return-book-item-wise') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Local/Central Sale Register -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Local/Central Register</h6>
                    <p class="card-text small text-muted">GST type wise sales</p>
                    <a href="{{ route('admin.reports.sales.local-central-sale-register') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sale Challan Book -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-receipt text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Sale Challan Book</h6>
                    <p class="card-text small text-muted">Challan register</p>
                    <a href="{{ route('admin.reports.sales.sale-challan-book') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Pending Challans -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Pending Challans</h6>
                    <p class="card-text small text-muted">Un-invoiced challans</p>
                    <a href="{{ route('admin.reports.sales.pending-challans') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sales Stock Summary -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Stock Summary</h6>
                    <p class="card-text small text-muted">Stock movement summary</p>
                    <a href="{{ route('admin.reports.sales.sales-stock-summary') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Customer Visit Status -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-person-check text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Customer Visit Status</h6>
                    <p class="card-text small text-muted">Customer order tracking</p>
                    <a href="{{ route('admin.reports.sales.customer-visit-status') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Shortage Report -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Shortage Report</h6>
                    <p class="card-text small text-muted">Low/out of stock items</p>
                    <a href="{{ route('admin.reports.sales.shortage-report') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sale Return List -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-return-left text-secondary fs-1 mb-3"></i>
                    <h6 class="card-title">Sale Return List</h6>
                    <p class="card-text small text-muted">Return transactions</p>
                    <a href="{{ route('admin.reports.sales.sale-return-list') }}" class="btn btn-outline-secondary btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- GST Reports -->
    <h5 class="text-success mb-3"><i class="bi bi-file-earmark-ruled me-2"></i>GST / Tax Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Sales Book GSTR -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-ruled text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Book GSTR</h6>
                    <p class="card-text small text-muted">GST return format</p>
                    <a href="{{ route('admin.reports.sales.sales-book-gstr') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sales Book Extra Charges -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Book Extra Charges</h6>
                    <p class="card-text small text-muted">Additional charges report</p>
                    <a href="{{ route('admin.reports.sales.sales-book-extra-charges') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Sales Book With TCS -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-percent text-success fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Book With TCS</h6>
                    <p class="card-text small text-muted">TCS applicable sales</p>
                    <a href="{{ route('admin.reports.sales.sales-book-tcs') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- TCS Eligibility -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge text-success fs-1 mb-3"></i>
                    <h6 class="card-title">TCS Eligibility</h6>
                    <p class="card-text small text-muted">TCS eligible parties</p>
                    <a href="{{ route('admin.reports.sales.tcs-eligibility') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- TDS Input -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calculator text-success fs-1 mb-3"></i>
                    <h6 class="card-title">TDS Input</h6>
                    <p class="card-text small text-muted">TDS deduction report</p>
                    <a href="{{ route('admin.reports.sales.tds-input') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Miscellaneous Sales Analysis -->
    <h5 class="text-info mb-3"><i class="bi bi-pie-chart me-2"></i>Miscellaneous Sales Analysis</h5>
    <div class="row g-3 mb-4">
        <!-- Sales Man Wise Sales -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-person-workspace text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Man Wise Sales</h6>
                    <p class="card-text small text-muted">Salesman performance analysis</p>
                    <a href="{{ route('admin.reports.sales.salesman-wise-sales') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Area Wise Sale -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-geo text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Area Wise Sale</h6>
                    <p class="card-text small text-muted">Area wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.area-wise-sale') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Route Wise Sale -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-signpost-2 text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Route Wise Sale</h6>
                    <p class="card-text small text-muted">Route wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.route-wise-sale') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- State Wise Sale -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-map text-info fs-1 mb-3"></i>
                    <h6 class="card-title">State Wise Sale</h6>
                    <p class="card-text small text-muted">State wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.state-wise-sale') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Customer Wise Sale -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Customer Wise Sale</h6>
                    <p class="card-text small text-muted">Customer wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.customer-wise-sale') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Company Wise Sales -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-building text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Company Wise Sales</h6>
                    <p class="card-text small text-muted">Company wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.company-wise-sales') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Item Wise Sales -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-box text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Item Wise Sales</h6>
                    <p class="card-text small text-muted">Item wise sales analysis</p>
                    <a href="{{ route('admin.reports.sales.item-wise-sales') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Discount Wise Sales -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body text-center">
                    <i class="bi bi-tags text-info fs-1 mb-3"></i>
                    <h6 class="card-title">Discount Wise Sales</h6>
                    <p class="card-text small text-muted">Discount analysis report</p>
                    <a href="{{ route('admin.reports.sales.discount-wise-sales') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Reports -->
    <h5 class="text-warning mb-3"><i class="bi bi-folder2-open me-2"></i>Other Reports</h5>
    <div class="row g-3 mb-4">
        <!-- Sales Man and other Level Sale -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-diagram-3 text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Sales Man Level Sale</h6>
                    <p class="card-text small text-muted">Multi-level sales analysis</p>
                    <a href="{{ route('admin.reports.sales.salesman-level-sale') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Scheme Issued -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-gift text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Scheme Issued</h6>
                    <p class="card-text small text-muted">Scheme/offer tracking</p>
                    <a href="{{ route('admin.reports.sales.scheme-issued') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- MRP Wise Sales -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-currency-rupee text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">MRP Wise Sales</h6>
                    <p class="card-text small text-muted">MRP based sales analysis</p>
                    <a href="{{ route('admin.reports.sales.mrp-wise-sales') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Display Amount Report -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-display text-warning fs-1 mb-3"></i>
                    <h6 class="card-title">Display Amount Report</h6>
                    <p class="card-text small text-muted">Display amount tracking</p>
                    <a href="{{ route('admin.reports.sales.display-amount-report') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- List of Cancelled Invoices -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Cancelled Invoices</h6>
                    <p class="card-text small text-muted">List of cancelled bills</p>
                    <a href="{{ route('admin.reports.sales.cancelled-invoices') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- List of Missing Invoices -->
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm hover-shadow border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle text-danger fs-1 mb-3"></i>
                    <h6 class="card-title">Missing Invoices</h6>
                    <p class="card-text small text-muted">Gap in invoice numbers</p>
                    <a href="{{ route('admin.reports.sales.missing-invoices') }}" class="btn btn-outline-danger btn-sm">View Report</a>
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
