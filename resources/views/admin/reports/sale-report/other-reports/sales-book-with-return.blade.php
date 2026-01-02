@extends('layouts.admin')

@section('title', 'Sales Book With Return')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">SALE BOOK WITH SALE RETURN</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.sales-book-with-return') }}">
                <!-- Date Filters -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Customer Filter -->
                <div class="row g-0 mb-4 align-items-center">
                    <div class="col-md-1">
                        <label class="fw-bold mb-0">Customer:</label>
                    </div>
                    <div class="col-md-5">
                        <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                            <option value="">-- All Customers --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->code }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()">
                            <span class="text-decoration-underline">E</span>xcel
                        </button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">
                            Show
                        </button>
                        <button type="submit" form="filterForm" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <span class="text-decoration-underline">V</span>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Data Table -->
    @if(isset($combinedData) && $combinedData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Sale Book With Sale Return ({{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }})</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th class="text-center" style="width: 90px;">Date</th>
                            <th class="text-center" style="width: 70px;">Type</th>
                            <th class="text-center" style="width: 100px;">Doc No</th>
                            <th style="width: 70px;">Code</th>
                            <th>Party Name</th>
                            <th>Area</th>
                            <th class="text-end" style="width: 110px;">Gross Amt</th>
                            <th class="text-end" style="width: 90px;">Discount</th>
                            <th class="text-end" style="width: 90px;">Tax</th>
                            <th class="text-end" style="width: 110px;">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($combinedData as $index => $row)
                        <tr class="{{ $row['is_return'] ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                            <td class="text-center">
                                @if($row['is_return'])
                                    <span class="badge bg-danger">Return</span>
                                @else
                                    <span class="badge bg-success">Sale</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row['doc_no'] }}</td>
                            <td>{{ $row['customer_code'] }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td>{{ $row['area'] }}</td>
                            <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['gross_amount'], 2) }}</td>
                            <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['dis_amount'], 2) }}</td>
                            <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['tax_amount'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['net_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-success fw-bold">
                            <td colspan="7" class="text-end">Sale Total:</td>
                            <td class="text-end" colspan="3">{{ $totals['sale_count'] }} Bills</td>
                            <td class="text-end">{{ number_format($totals['sale_amount'], 2) }}</td>
                        </tr>
                        <tr class="table-danger fw-bold">
                            <td colspan="7" class="text-end">Return Total:</td>
                            <td class="text-end" colspan="3">{{ $totals['return_count'] }} Bills</td>
                            <td class="text-end">{{ number_format($totals['return_amount'], 2) }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td colspan="7" class="text-end">NET TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['gross_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-4">
                    <small class="text-success"><strong>Sales:</strong> {{ $totals['sale_count'] }} bills = ₹{{ number_format($totals['sale_amount'], 2) }}</small>
                </div>
                <div class="col-md-4">
                    <small class="text-danger"><strong>Returns:</strong> {{ $totals['return_count'] }} bills = ₹{{ number_format($totals['return_amount'], 2) }}</small>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-primary"><strong>Net:</strong> ₹{{ number_format($totals['net_amount'], 2) }}</small>
                </div>
            </div>
        </div>
    </div>
    @elseif(request()->has('date_from'))
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> No records found for the selected date range and customer.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.sales.other.sales-book-with-return") }}?' + params.toString();
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.sales-book-with-return") }}?' + params.toString(), 'SalesBookWithReturn', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.sales") }}';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'e' || e.key === 'E') {
        if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) exportToExcel();
    }
    if (e.key === 'v' || e.key === 'V') {
        if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) viewReport();
    }
    if (e.key === 'Escape') closeWindow();
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td {
    padding: 0.35rem 0.4rem;
    font-size: 0.8rem;
    vertical-align: middle;
}
</style>
@endpush
