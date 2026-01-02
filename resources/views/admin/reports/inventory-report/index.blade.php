@extends('layouts.admin')

@section('title', 'Inventory Reports')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">INVENTORY REPORTS</h4>
        </div>
    </div>

    <div class="row">
        <!-- Item Reports -->
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-bold">Item Reports</h6>
                </div>
                <div class="card-body p-2">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><a href="{{ route('admin.reports.inventory.item.min-max-level') }}" class="text-decoration-none">Minimum / Maximum Level Items</a></li>
                        <li class="mb-1"><a href="{{ route('admin.reports.inventory.item.display-item-list') }}" class="text-decoration-none">Display Item List</a></li>
                        <li class="mb-1"><a href="{{ route('admin.reports.inventory.item.tax-mrp-rate-range') }}" class="text-decoration-none">Item List - Tax / Mrp / Rate Range</a></li>
                        <li class="mb-1"><a href="{{ route('admin.reports.inventory.item.margin-wise') }}" class="text-decoration-none">Margin-Wise Items</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Margin-Wise Items (Running Items)</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Multi Rate Items</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">New Items / Customers / Suppliers</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Rate List</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Vat-Wise Items</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Item List with Salts</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">List of Schemes</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Item Search By Batch</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Item Ledger Printing</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stock Reports -->
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-bold">Stock Reports</h6>
                </div>
                <div class="card-body p-2">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Stock reports coming soon...</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Others -->
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-bold">Others</h6>
                </div>
                <div class="card-body p-2">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">FiFo Alteration Report</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Reorder on Sale Basis</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Reorder on Minimum Stock Basis</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Reorder on Minimum Stock & Sale Basis</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Order Form 3 Column</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Order Form 6 Column</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">List of Hold Batches</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">Remove Batch Hold Status</a></li>
                        <li class="mb-1"><a href="#" class="text-decoration-none text-muted">List of Hold Batches (SR,PB)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card { border-radius: 0; border: 1px solid #ccc; }
.card-header { border-radius: 0; }
ul li a { color: #333; }
ul li a:hover { color: #007bff; text-decoration: underline !important; }
ul li a.text-muted:hover { color: #6c757d !important; text-decoration: none !important; }
</style>
@endpush
