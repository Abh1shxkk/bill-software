@extends('layouts.admin')

@section('title', 'Claim Items - Sold on Loss')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Claim Items - Sold on Loss</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.gross-profit.claim-items-sold-on-loss') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="company_code" class="form-control form-control-sm text-uppercase" value="{{ request('company_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="company_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Companies --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Supplier :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="supplier_code" class="form-control form-control-sm text-uppercase" value="{{ request('supplier_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="supplier_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Suppliers --</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Min Loss % -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Min Loss % :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="min_loss_percent" class="form-control form-control-sm" value="{{ request('min_loss_percent', '0') }}" style="width: 80px;">
                    </div>
                </div>

                <!-- Sort By & Order -->
                <div class="row g-2 mb-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Sort By :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sort_by" class="form-select form-select-sm" style="width: 150px;">
                            <option value="item_name" {{ request('sort_by', 'item_name') == 'item_name' ? 'selected' : '' }}>Item Name</option>
                            <option value="company" {{ request('sort_by') == 'company' ? 'selected' : '' }}>Company</option>
                            <option value="loss_amount" {{ request('sort_by') == 'loss_amount' ? 'selected' : '' }}>Loss Amount</option>
                            <option value="loss_percent" {{ request('sort_by') == 'loss_percent' ? 'selected' : '' }}>Loss %</option>
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Order :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="asc" value="asc" {{ request('order', 'asc') == 'asc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="asc">Asc</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="desc" value="desc" {{ request('order') == 'desc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="desc">Desc</label>
                        </div>
                    </div>
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
            <span class="fw-bold">Report Results - Items Sold on Loss (Claimable)</span>
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
                            <th class="text-center">Qty</th>
                            <th class="text-end">Sale Amt</th>
                            <th class="text-end">Cost Amt</th>
                            <th class="text-end">Loss Amt</th>
                            <th class="text-end">Loss %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalSale = 0;
                            $totalCost = 0;
                            $totalLoss = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php
                            $totalQty += $row['qty'];
                            $totalSale += $row['sale_amount'];
                            $totalCost += $row['cost_amount'];
                            $totalLoss += $row['loss_amount'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['cost_amount'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row['loss_amount'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row['loss_percent'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCost, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totalLoss, 2) }}</td>
                            <td class="text-end text-danger">{{ $totalSale > 0 ? number_format(abs($totalLoss) / $totalSale * 100, 2) : '0.00' }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No items sold on loss found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.gross-profit.claim-items-sold-on-loss") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
