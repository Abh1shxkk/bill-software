@extends('layouts.admin')

@section('title', 'Sale /Purchase Scheme')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Sale /Purchase Scheme</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.sale-purchase-schemes') }}">
                <!-- Sale/Purchase Selection -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">S(ale) / P(urchase) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="scheme_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('scheme_type', 'S') }}" maxlength="1" style="width: 40px; background-color: #add8e6;">
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
            <span class="fw-bold">{{ request('scheme_type', 'S') == 'S' ? 'Sale' : 'Purchase' }} Schemes ({{ count($reportData) }} items)</span>
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
                            <th>Packing</th>
                            <th class="text-center">Scheme (+)</th>
                            <th class="text-center">Scheme (-)</th>
                            <th class="text-center">From Date</th>
                            <th class="text-center">To Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
                            <td class="text-center">{{ $row['scheme_plus'] }}</td>
                            <td class="text-center">{{ $row['scheme_minus'] }}</td>
                            <td class="text-center">{{ $row['from_date'] }}</td>
                            <td class="text-center">{{ $row['to_date'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No schemes found.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.sale-purchase-schemes") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
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
