@extends('layouts.admin')

@section('title', 'Sale Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">SALE SUMMARY</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Series</span>
                            <select name="series" class="form-select">
                                <option value="">All</option>
                                @foreach($seriesList ?? [] as $s)
                                    <option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">No From</span>
                            <input type="number" name="number_from" class="form-control" value="{{ $numberFrom ?? 0 }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="number" name="number_to" class="form-control" value="{{ $numberTo ?? 0 }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">Excel</button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="viewReport()">View</button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($summary) && $summary->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($grandTotals['invoices'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">NT Amount</small>
                    <h6 class="mb-0">₹{{ number_format($grandTotals['nt_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Discount</small>
                    <h6 class="mb-0">₹{{ number_format($grandTotals['dis_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Tax</small>
                    <h6 class="mb-0">₹{{ number_format($grandTotals['tax_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($grandTotals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 60px;">Series</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 100px;">NT Amount</th>
                            <th class="text-end" style="width: 80px;">Discount</th>
                            <th class="text-end" style="width: 80px;">Tax</th>
                            <th class="text-end" style="width: 100px;">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales ?? [] as $index => $sale)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>{{ $sale->series ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold">
                                    {{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                            <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate Sale Summary
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($sales) && $sales->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total ({{ $sales->count() }} Bills):</td>
                            <td class="text-end">{{ number_format($grandTotals['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotals['net_amount'] ?? 0, 2) }}</td>
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
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-summary") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.sales-summary") }}?' + params.toString(), 'SaleSummary', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
