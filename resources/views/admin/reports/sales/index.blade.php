@extends('layouts.admin')

@section('title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sales Reports</h4>
    </div>

    <div class="row g-3">
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
