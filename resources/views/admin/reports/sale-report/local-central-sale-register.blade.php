@extends('layouts.admin')

@section('title', 'Sale Book Local Central')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">SALE BOOK LOCAL CENTRAL</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range -->
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
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Report Type -->
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1. Sale / 2. Sale Return / 3. Debit Note / 4. Credit Note / 5. Consolidated</span>
                            <select name="report_type" class="form-select" style="width: 60px;">
                                <option value="1" {{ ($reportType ?? '5') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($reportType ?? '') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ ($reportType ?? '') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ ($reportType ?? '') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ ($reportType ?? '5') == '5' ? 'selected' : '' }}>5</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 3: Party -->
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-2">
                    <!-- Row 4: L/C/B -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal) / C(entral) / B(oth)</span>
                            <select name="local_central" class="form-select" style="width: 50px;">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-2">
                    <!-- Row 5: Cancelled, Tax/Retail, Series -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cancelled [Y/N]</span>
                            <select name="cancelled" class="form-select" style="width: 50px;">
                                <option value="N" {{ ($cancelled ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($cancelled ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">T(ax) / R(etail)</span>
                            <input type="text" name="tax_retail" class="form-control" value="{{ $taxRetail ?? '' }}" style="width: 50px;">
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
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($totals) && ($totals['total']['count'] ?? 0) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body py-2 text-center">
                    <small class="text-white-50">LOCAL ({{ $totals['local']['count'] ?? 0 }} Bills)</small>
                    <h6 class="mb-0">₹{{ number_format($totals['local']['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body py-2 text-center">
                    <small class="text-white-50">CENTRAL ({{ $totals['central']['count'] ?? 0 }} Bills)</small>
                    <h6 class="mb-0">₹{{ number_format($totals['central']['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 text-center">
                    <small class="text-white-50">TOTAL ({{ $totals['total']['count'] ?? 0 }} Bills)</small>
                    <h6 class="mb-0">₹{{ number_format($totals['total']['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th style="width: 50px;">Code</th>
                            <th>Party Name</th>
                            <th style="width: 50px;">L/C</th>
                            <th style="width: 100px;">GSTN</th>
                            <th class="text-end" style="width: 90px;">NT Amount</th>
                            <th class="text-end" style="width: 80px;">CGST</th>
                            <th class="text-end" style="width: 80px;">SGST</th>
                            <th class="text-end" style="width: 80px;">IGST</th>
                            <th class="text-end" style="width: 100px;">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $srNo = 0; @endphp
                        @if(($localCentral ?? 'B') !== 'C' && isset($localSales) && $localSales->count() > 0)
                        <tr class="table-success">
                            <td colspan="12" class="fw-bold">LOCAL SALES ({{ $localSales->count() }} Bills)</td>
                        </tr>
                        @foreach($localSales as $sale)
                        @php $srNo++; @endphp
                        <tr>
                            <td class="text-center">{{ $srNo }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none">{{ $sale->series }}{{ $sale->invoice_no }}</a></td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                            <td class="text-center"><span class="badge bg-success">L</span></td>
                            <td class="small">{{ $sale->customer->gst_number ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float)($sale->nt_amount ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($sale->cgst_amount ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($sale->sgst_amount ?? 0), 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-secondary">
                            <td colspan="7" class="text-end fw-bold">Local Total:</td>
                            <td class="text-end fw-bold">{{ number_format($totals['local']['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totals['local']['cgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totals['local']['sgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-bold">{{ number_format($totals['local']['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                        @endif

                        @if(($localCentral ?? 'B') !== 'L' && isset($centralSales) && $centralSales->count() > 0)
                        <tr class="table-info">
                            <td colspan="12" class="fw-bold">CENTRAL SALES ({{ $centralSales->count() }} Bills)</td>
                        </tr>
                        @foreach($centralSales as $sale)
                        @php $srNo++; @endphp
                        <tr>
                            <td class="text-center">{{ $srNo }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none">{{ $sale->series }}{{ $sale->invoice_no }}</a></td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                            <td class="text-center"><span class="badge bg-info">C</span></td>
                            <td class="small">{{ $sale->customer->gst_number ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float)($sale->nt_amount ?? 0), 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format((float)($sale->igst_amount ?? 0), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-secondary">
                            <td colspan="7" class="text-end fw-bold">Central Total:</td>
                            <td class="text-end fw-bold">{{ number_format($totals['central']['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-bold">{{ number_format($totals['central']['igst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totals['central']['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                        @endif

                        @if(!isset($sales) || $sales->count() == 0)
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters to generate Local/Central Sale Register
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    @if(isset($totals) && ($totals['total']['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Grand Total ({{ $totals['total']['count'] ?? 0 }} Bills):</td>
                            <td class="text-end">{{ number_format($totals['total']['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['local']['cgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['local']['sgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['central']['igst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total']['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel me-1"></i>Excel
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                </div>
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
    window.open('{{ route("admin.reports.sales.local-central-sale-register") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.local-central-sale-register") }}?' + params.toString(), 'LocalCentralRegister', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: auto; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.25rem 0.4rem; font-size: 0.73rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
