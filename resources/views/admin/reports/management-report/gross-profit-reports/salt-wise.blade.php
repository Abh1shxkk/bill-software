@extends('layouts.admin')

@section('title', 'Salt wise Gross Profit')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Salt wise Gross Profit</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.gross-profit.salt-wise') }}">
                <!-- From & To Date, Negative -->
                <div class="row g-2 mb-3 align-items-center">
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
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">Negative [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="negative" class="form-control form-control-sm text-center text-uppercase" value="{{ request('negative', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Salt Selection (4 inputs in 2x2 grid) -->
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">1.</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salt_code_1" class="form-control form-control-sm text-uppercase" value="{{ request('salt_code_1', '00') }}" style="width: 50px;">
                            </div>
                            <div class="col">
                                <input type="text" name="salt_name_1" class="form-control form-control-sm" value="{{ request('salt_name_1') }}" placeholder="Salt name...">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">2.</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salt_code_2" class="form-control form-control-sm text-uppercase" value="{{ request('salt_code_2', '00') }}" style="width: 50px;">
                            </div>
                            <div class="col">
                                <input type="text" name="salt_name_2" class="form-control form-control-sm" value="{{ request('salt_name_2') }}" placeholder="Salt name...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">3.</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salt_code_3" class="form-control form-control-sm text-uppercase" value="{{ request('salt_code_3', '00') }}" style="width: 50px;">
                            </div>
                            <div class="col">
                                <input type="text" name="salt_name_3" class="form-control form-control-sm" value="{{ request('salt_name_3') }}" placeholder="Salt name...">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">4.</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salt_code_4" class="form-control form-control-sm text-uppercase" value="{{ request('salt_code_4', '00') }}" style="width: 50px;">
                            </div>
                            <div class="col">
                                <input type="text" name="salt_name_4" class="form-control form-control-sm" value="{{ request('salt_name_4') }}" placeholder="Salt name...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
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
            <span class="fw-bold">Report Results</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Salt Name</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Sale Amt</th>
                            <th class="text-end">Pur Amt</th>
                            <th class="text-end">GP Amt</th>
                            <th class="text-end">GP %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalSale = 0;
                            $totalPurchase = 0;
                            $totalGP = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php
                            $totalQty += $row['qty'];
                            $totalSale += $row['sale_amount'];
                            $totalPurchase += $row['purchase_amount'];
                            $totalGP += $row['gp_amount'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['salt_name'] }}</td>
                            <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_amount'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_percent'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_percent'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td class="text-end">{{ number_format($totalPurchase, 2) }}</td>
                            <td class="text-end {{ $totalGP < 0 ? 'text-danger' : '' }}">{{ number_format($totalGP, 2) }}</td>
                            <td class="text-end">{{ $totalSale > 0 ? number_format($totalGP / $totalSale * 100, 2) : '0.00' }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No records found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.gross-profit.salt-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
