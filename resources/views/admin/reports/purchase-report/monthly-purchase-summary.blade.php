@extends('layouts.admin')

@section('title', 'Monthly Purchase/Sale Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">Monthly Purchase/Sale Summary</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.monthly-purchase-summary') }}">
                <div class="row g-2">
                    <!-- Row 1: Company -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company:</span>
                            <input type="text" name="company_code" class="form-control" value="{{ $companyCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All Companies/Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($companyId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5"></div>

                    <!-- Row 2: Date Range -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="month" name="month_from" class="form-control" value="{{ $monthFrom ?? date('Y-04') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="month" name="month_to" class="form-control" value="{{ $monthTo ?? date('Y-m') }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 3: Options -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show DN/CN:</span>
                            <select name="show_dn_cn" class="form-select">
                                <option value="Y" {{ ($showDnCn ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showDnCn ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show Br.Exp:</span>
                            <select name="show_br_exp" class="form-select">
                                <option value="Y" {{ ($showBrExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showBrExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($monthlySummary) && count($monthlySummary) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['bills'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Purchase</small>
                    <h6 class="mb-0">₹{{ number_format($totals['purchase'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Return</small>
                    <h6 class="mb-0">₹{{ number_format($totals['return'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        @if(($showDnCn ?? 'Y') == 'Y')
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">DN/CN</small>
                    <h6 class="mb-0">₹{{ number_format(($totals['dn'] ?? 0) - ($totals['cn'] ?? 0), 2) }}</h6>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Purchase</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Monthly Summary Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 100px;">Month</th>
                            <th class="text-center" style="width: 70px;">Bills</th>
                            <th class="text-end" style="width: 120px;">Purchase Amt</th>
                            <th class="text-end" style="width: 110px;">Return Amt</th>
                            @if(($showDnCn ?? 'Y') == 'Y')
                            <th class="text-end" style="width: 100px;">DN Amt</th>
                            <th class="text-end" style="width: 100px;">CN Amt</th>
                            @endif
                            <th class="text-end" style="width: 100px;">Tax</th>
                            <th class="text-end" style="width: 120px;">Net Purchase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlySummary ?? [] as $monthName => $data)
                        <tr>
                            <td>{{ $monthName }}</td>
                            <td class="text-center">{{ $data['bills'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($data['purchase'] ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($data['return'] ?? 0, 2) }}</td>
                            @if(($showDnCn ?? 'Y') == 'Y')
                            <td class="text-end text-info">{{ number_format($data['dn'] ?? 0, 2) }}</td>
                            <td class="text-end text-warning">{{ number_format($data['cn'] ?? 0, 2) }}</td>
                            @endif
                            <td class="text-end">{{ number_format($data['tax'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($data['net'] ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($showDnCn ?? 'Y') == 'Y' ? 8 : 6 }}" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($monthlySummary) && count($monthlySummary) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td>Grand Total</td>
                            <td class="text-center">{{ $totals['bills'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($totals['purchase'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['return'] ?? 0, 2) }}</td>
                            @if(($showDnCn ?? 'Y') == 'Y')
                            <td class="text-end">{{ number_format($totals['dn'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['cn'] ?? 0, 2) }}</td>
                            @endif
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Print button - opens print view in new tab
    document.getElementById('btnPrint').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) {
            viewTypeInput = document.createElement('input');
            viewTypeInput.type = 'hidden';
            viewTypeInput.name = 'view_type';
            form.appendChild(viewTypeInput);
        }
        viewTypeInput.value = 'print';
        form.target = '_blank';
        form.submit();
        form.target = '_self';
        viewTypeInput.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.85rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
