@extends('layouts.admin')

@section('title', 'Slow Moving Items')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SLOW MOVING ITEMS</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.slow-moving-items') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 80px;">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Sale Stock Ratio Below -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 180px;">
                        <label class="fw-bold mb-0">Sale Stock Ratio Below :</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" step="0.01" name="ratio_below" class="form-control form-control-sm text-end" value="{{ request('ratio_below', '0.00') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">%</label>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">With Batch Detail</label>
                    </div>
                    <div class="col-auto">
                        <input type="checkbox" name="with_batch_detail" value="1" class="form-check-input" {{ request('with_batch_detail') ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px; margin-top: 30px;">
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Slow Moving Items ({{ count($reportData) }} items)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Stock Qty</th>
                            <th class="text-end">Sale Qty</th>
                            <th class="text-end">Ratio %</th>
                            <th class="text-center">Last Sale Date</th>
                            <th class="text-end">Stock Value</th>
                            @if(request('with_batch_detail'))
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalStock = 0; $totalSale = 0; $totalValue = 0; @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalStock += $row['stock_qty']; 
                            $totalSale += $row['sale_qty'];
                            $totalValue += $row['stock_value'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td class="text-end">{{ number_format($row['stock_qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['ratio'], 2) }}%</td>
                            <td class="text-center">{{ $row['last_sale_date'] }}</td>
                            <td class="text-end">{{ number_format($row['stock_value'], 2) }}</td>
                            @if(request('with_batch_detail'))
                            <td>{{ $row['batch_no'] ?? '' }}</td>
                            <td class="text-center">{{ $row['expiry_date'] ?? '' }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalStock, 2) }}</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td class="text-end">{{ number_format($totalValue, 2) }}</td>
                            @if(request('with_batch_detail'))
                            <td colspan="2"></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No slow moving items found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.slow-moving-items") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        closeWindow();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
