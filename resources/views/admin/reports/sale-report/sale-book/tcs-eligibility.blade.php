@extends('layouts.admin')

@section('title', 'TCS Eligibility Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">TCS ELIGIBILITY REPORT</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.tcs-eligibility') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range & Party Type -->
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
                            <span class="input-group-text">Party Type</span>
                            <select name="party_type" class="form-select">
                                <option value="C" {{ ($partyType ?? 'C') == 'C' ? 'selected' : '' }}>Customer</option>
                                <option value="S" {{ ($partyType ?? '') == 'S' ? 'selected' : '' }}>Supplier</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Amount >=</span>
                            <input type="number" name="amount_threshold" class="form-control" value="{{ $amountThreshold ?? 5000000 }}" step="100000">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: State Filter -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">State</span>
                            <select name="state_id" class="form-select">
                                <option value="">All</option>
                                @foreach($states ?? [] as $state)
                                    <option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-2" onclick="exportToExcel()">
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
    @if(request()->has('view'))
    <!-- Info Box -->
    <div class="alert alert-info py-2 mb-2 small">
        <i class="bi bi-info-circle"></i> 
        TCS (Tax Collected at Source) @ 0.1% is applicable on sales exceeding ₹50 Lakhs to a single party in a financial year.
        Threshold Amount: <strong>₹{{ number_format($amountThreshold ?? 5000000) }}</strong>
    </div>

    @if(isset($parties) && $parties->count() > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">No. of Parties</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['total_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">TCS Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 70px;">Party Code</th>
                            <th>Party Name</th>
                            <th style="width: 140px;">GST No</th>
                            <th style="width: 110px;">PAN No</th>
                            <th class="text-end" style="width: 120px;">Amount</th>
                            <th class="text-center" style="width: 60px;">TCS%</th>
                            <th class="text-end" style="width: 100px;">TCS Amt</th>
                            <th class="text-center" style="width: 80px;">TCS Appl.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parties ?? [] as $index => $party)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $party->code ?? '' }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $party->id) }}" class="text-decoration-none">
                                    {{ Str::limit($party->name ?? 'N/A', 30) }}
                                </a>
                            </td>
                            <td class="small">{{ $party->gst_number ?? '-' }}</td>
                            <td class="small">{{ $party->pan_number ?? '-' }}</td>
                            <td class="text-end fw-bold">{{ number_format($party->total_amount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($party->tcs_rate ?? 0, 2) }}%</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($party->tcs_amount ?? 0, 2) }}</td>
                            <td class="text-center">
                                @if($party->tcs_applicable)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No parties found with sales exceeding ₹{{ number_format($amountThreshold ?? 5000000) }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($parties) && $parties->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Parties):</td>
                            <td class="text-end">{{ number_format($totals['total_amount'] ?? 0, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</td>
                            <td></td>
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
function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.tcs-eligibility") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.tcs-eligibility") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.75rem; border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
