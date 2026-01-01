@extends('layouts.admin')

@section('title', 'Monthly Purchase Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2e3e5 0%, #f8f9fa 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-secondary fst-italic fw-bold">MONTHLY PURCHASE SALES SUMMARY</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Year</span>
                            <select name="year" class="form-select">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Summary Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Month</th>
                            <th class="text-center">Bills</th>
                            <th class="text-end">Purchase Amt</th>
                            <th class="text-end">Return Amt</th>
                            <th class="text-end">Net Purchase</th>
                            <th class="text-end">Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $months = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar']; @endphp
                        @foreach($months as $month)
                        <tr>
                            <td>{{ $month }}</td>
                            <td class="text-center">{{ $monthlySummary[$month]['bills'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($monthlySummary[$month]['purchase'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($monthlySummary[$month]['return'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($monthlySummary[$month]['net'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($monthlySummary[$month]['tax'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td>Total</td>
                            <td class="text-center">{{ $totals['bills'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($totals['purchase'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['return'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.5rem; font-size: 0.85rem; }
</style>
@endpush
