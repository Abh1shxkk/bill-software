@extends('layouts.admin')

@section('title', 'Area Wise Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Area Wise Sale</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-01') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-5">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 80px;"><label class="small">Area :</label></div>
                <div class="col-md-5">
                    <select name="area_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($areas ?? [] as $a)
                            <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col text-end">
                    <button type="button" class="btn btn-light btn-sm border px-4 me-2" id="btnView"><u>V</u>iew</button>
                    <button type="button" class="btn btn-light btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($groupedSales) && $groupedSales->count() > 0)
    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Area</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedSales as $areaName => $sales)
                            <tr class="table-secondary">
                                <td colspan="5" class="fw-bold">{{ $areaName }}</td>
                            </tr>
                            @foreach($sales as $sale)
                            <tr>
                                <td></td>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}</td>
                                <td>{{ $sale->customer->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-info">
                                <td colspan="4" class="text-end fw-bold">{{ $areaName }} Total:</td>
                                <td class="text-end fw-bold">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                            <td class="text-end fw-bold">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    
    // View button - open in new window
    document.getElementById('btnView').addEventListener('click', function() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('view_type', 'print');
        window.open('{{ route("admin.reports.sales.area-wise-sale") }}?' + params.toString(), 'AreaWiseSale', 'width=1000,height=800,scrollbars=yes,resizable=yes');
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); }
    });
});
</script>
@endsection
