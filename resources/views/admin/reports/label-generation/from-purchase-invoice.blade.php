{{-- From Purchase Invoice - Label Generation --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">Label Generation - From Purchase Invoice</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">From:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="{{ $dateFrom }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">To:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="{{ $dateTo }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Series:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('series', '') }}" maxlength="5" style="width: 60px;" placeholder="All">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Supplier:</label>
                    </div>
                    <div class="col-3">
                        <select name="supplier" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->supplier_id }}" {{ request('supplier') == $sup->supplier_id ? 'selected' : '' }}>
                                    {{ $sup->supplier_id }} - {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                        <button type="button" onclick="printReport()" class="btn btn-secondary btn-sm">Print</button>
                        <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Purchase Invoices - {{ $reportData->count() }} Records</h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm table-striped mb-0" style="font-size: 0.75rem;">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>S.No</th>
                            <th>Pur Inv. No</th>
                            <th>Inv. Date</th>
                            <th>Party Name</th>
                            <th class="text-end">Amount</th>
                            <th>Voucher Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $invoice)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $invoice->bill_no }}</td>
                            <td>{{ $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '' }}</td>
                            <td>{{ $invoice->supplier?->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($invoice->net_amount ?? 0, 2) }}</td>
                            <td>{{ $invoice->voucher_type }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">No records found matching the criteria.</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.label.from-purchase-invoice") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endpush
