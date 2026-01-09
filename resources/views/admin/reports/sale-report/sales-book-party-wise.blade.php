@extends('layouts.admin')

@section('title', 'Party Wise Sale')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">PARTY WISE SALE</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_sale" value="1" {{ ($reportType ?? '1') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_sale">1. Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_return" value="2" {{ ($reportType ?? '') == '2' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_return">2. Sale Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_dn" value="3" {{ ($reportType ?? '') == '3' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_dn">3. Debit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_cn" value="4" {{ ($reportType ?? '') == '4' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_cn">4. Credit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_consolidated" value="5" {{ ($reportType ?? '') == '5' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_consolidated">5. Consolidated Sale</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sales-book-party-wise') }}">
                <input type="hidden" name="report_type" id="hidden_report_type" value="{{ $reportType ?? '1' }}">
                
                <div class="row g-2">
                    <!-- Row 1 -->
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
                            <span class="input-group-text">Selective</span>
                            <select name="selective" class="form-select text-uppercase">
                                <option value="Y" {{ ($selective ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($selective ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bill Wise</span>
                            <select name="bill_wise" class="form-select text-uppercase">
                                <option value="Y" {{ ($billWise ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($billWise ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">T/R</span>
                            <select name="tax_retail" class="form-select text-uppercase">
                                <option value="">All</option>
                                <option value="T" {{ ($taxRetail ?? '') == 'T' ? 'selected' : '' }}>T(ax)</option>
                                <option value="R" {{ ($taxRetail ?? '') == 'R' ? 'selected' : '' }}>R(etail)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tagged</span>
                            <select name="tagged_parties" class="form-select text-uppercase">
                                <option value="N" {{ ($taggedParties ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($taggedParties ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Remove Tags</span>
                            <select name="remove_tags" class="form-select text-uppercase">
                                <option value="N" {{ ($removeTags ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($removeTags ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag</span>
                            <input type="text" name="flag" class="form-control text-uppercase" value="{{ $flag ?? '' }}" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All Parties</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print Addr</span>
                            <select name="print_address" class="form-select text-uppercase">
                                <option value="N" {{ ($printAddress ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($printAddress ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print S.Tax</span>
                            <select name="print_stax" class="form-select text-uppercase">
                                <option value="N" {{ ($printStax ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($printStax ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sort By</span>
                            <select name="sort_by" class="form-select text-uppercase">
                                <option value="P" {{ ($sortBy ?? 'P') == 'P' ? 'selected' : '' }}>P(arty)</option>
                                <option value="A" {{ ($sortBy ?? '') == 'A' ? 'selected' : '' }}>A(mount)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">A/D</span>
                            <select name="asc_desc" class="form-select text-uppercase">
                                <option value="A" {{ ($ascDesc ?? 'A') == 'A' ? 'selected' : '' }}>A(sc)</option>
                                <option value="D" {{ ($ascDesc ?? '') == 'D' ? 'selected' : '' }}>D(esc)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Amt ></span>
                            <input type="number" name="amount_from" class="form-control" value="{{ $amountFrom ?? 0 }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Amt <</span>
                            <input type="number" name="amount_to" class="form-control" value="{{ $amountTo ?? 0 }}" placeholder="0">
                        </div>
                    </div>

                    <!-- Row 4 - Checkboxes -->
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="with_vat" id="withVat" value="1" {{ ($withVat ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="withVat">With Vat</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="bill_amount" id="billAmount" value="1" {{ ($billAmount ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="billAmount">Bill Amount</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="gst_summary" id="gstSummary" value="1" {{ ($gstSummary ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="gstSummary">GST Summary</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($groupedSales) && count($groupedSales) > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 30px;">#</th>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th>Area</th>
                            <th class="text-center">Bills</th>
                            <th class="text-end">NT Amount</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedSales ?? [] as $customerId => $customerSales)
                        @php
                            $firstSale = $customerSales->first();
                            $customerTotal = [
                                'nt_amount' => $customerSales->sum('nt_amount'),
                                'dis_amount' => $customerSales->sum('dis_amount'),
                                'tax_amount' => $customerSales->sum('tax_amount'),
                                'net_amount' => $customerSales->sum('net_amount'),
                            ];
                        @endphp
                        <tr class="table-info">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $firstSale->customer->code ?? '' }}</td>
                            <td class="fw-bold">{{ $firstSale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $firstSale->customer->area_name ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ $customerSales->count() }}</td>
                            <td class="text-end fw-bold">{{ number_format($customerTotal['nt_amount'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($customerTotal['dis_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($customerTotal['tax_amount'], 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($customerTotal['net_amount'], 2) }}</td>
                        </tr>
                        @if($billWise ?? true)
                            @foreach($customerSales as $sale)
                            <tr>
                                <td></td>
                                <td class="text-muted small">{{ $sale->sale_date->format('d-m') }}</td>
                                <td class="small ps-3">
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none">
                                        {{ $sale->series }}{{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td></td>
                                <td></td>
                                <td class="text-end small">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                                <td class="text-end small text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                                <td class="text-end small">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                                <td class="text-end small">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        @endif
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate Party Wise Sale report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-center">{{ $totals['count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Sync report type radio buttons with hidden field
document.querySelectorAll('input[name="report_type_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_type').value = this.value;
    });
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-book-party-wise") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sales-book-party-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
