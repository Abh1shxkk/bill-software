@extends('layouts.admin')

@section('title', 'List of Expired Items')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">List of Expired Items</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.list-of-expired-items') }}">
                <!-- Action Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1. show &nbsp; 2. Mark Expiry &nbsp; 3. Un-Mark &nbsp; 4. Trf. to Godown Expiry :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="action_type" class="form-control form-control-sm text-center" value="{{ request('action_type', '1') }}" maxlength="1" style="width: 40px; background-color: #0000ff; color: #fff;">
                    </div>
                </div>

                <!-- From & To Date (MM/YY format) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="from_expiry" class="form-control form-control-sm text-center" value="{{ request('from_expiry', '01/90') }}" placeholder="MM/YY" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="to_expiry" class="form-control form-control-sm text-center" value="{{ request('to_expiry', date('m/y')) }}" placeholder="MM/YY" style="width: 80px;">
                    </div>
                </div>

                <!-- Remove Min/Max Limit -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto ms-auto">
                        <label class="fw-bold mb-0">Remove Min./ Max. Limit from Item :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="remove_limit" class="form-control form-control-sm text-center text-uppercase" value="{{ request('remove_limit', 'N') }}" maxlength="1" style="width: 40px;">
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
                                <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Division & Location -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division" class="form-control form-control-sm" value="{{ request('division') }}" style="width: 150px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Location :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="location" class="form-control form-control-sm" value="{{ request('location') }}" style="width: 150px;">
                    </div>
                </div>

                <!-- Value on & Hide Inactive -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Value on S(rate)/P(rate) / C(ost) / M(rp) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center text-uppercase" value="{{ request('value_on', 'S') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hide_inactive" id="hideInactive" value="Y" {{ request('hide_inactive') == 'Y' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="hideInactive">Hide Inactive Items</label>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-auto">
                        <button type="submit" name="excel" value="1" class="btn btn-light border px-4 fw-bold shadow-sm">E<u>x</u>cel</button>
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
            <span class="fw-bold">Expired Items ({{ count($reportData) }} items)</span>
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
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalValue = 0; $totalQty = 0; @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalValue += $row['value']; 
                            $totalQty += $row['qty'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['batch_no'] }}</td>
                            <td class="text-center">{{ $row['expiry_date'] }}</td>
                            <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['value'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalQty, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totalValue, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No expired items found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.list-of-expired-items") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_expiry"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        closeWindow();
    }
    if (e.altKey && e.key.toLowerCase() === 'x') {
        e.preventDefault();
        $('button[name="excel"]').click();
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
