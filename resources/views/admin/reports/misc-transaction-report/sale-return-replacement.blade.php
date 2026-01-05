@extends('layouts.admin')
@section('title', 'Sale Return Replacement Report')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Sale Return Replacement Report</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.misc-transaction.sale-return-replacement') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>F</u>rom :</label></div>
                    <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                    <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                    <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">With Item Details [ <u>Y</u> / <u>N</u> ] :</label></div>
                    <div class="col-auto">
                        <input type="text" name="with_item_details" class="form-control form-control-sm text-uppercase" value="{{ request('with_item_details', 'N') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 90px;"><label class="fw-bold mb-0">Customer :</label></div>
                    <div class="col">
                        <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                            <option value="">-- All Customers --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->code }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="printReport()"><u>P</u>rint</button>
                    </div>
                    <div class="col-md-8 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view'))
        @if(isset($reportData) && $reportData->count() > 0)
        <div class="card mt-2">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">S.No</th>
                                <th>Date</th>
                                <th>Voucher No</th>
                                <th>Customer</th>
                                @if(request('with_item_details') == 'Y')
                                <th>Item Name</th>
                                <th class="text-end">Return Qty</th>
                                <th class="text-end">Replace Qty</th>
                                @endif
                                <th class="text-end">Net Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sno = 1; @endphp
                            @foreach($reportData as $transaction)
                                @if(request('with_item_details') == 'Y')
                                    @foreach($transaction->items as $item)
                                    <tr>
                                        <td class="text-center">{{ $sno++ }}</td>
                                        <td>{{ $transaction->trn_date->format('d-M-y') }}</td>
                                        <td>{{ $transaction->trn_no }}</td>
                                        <td>{{ $transaction->customer->name ?? $transaction->customer_name }}</td>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->free_qty ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="text-center">{{ $sno++ }}</td>
                                    <td>{{ $transaction->trn_date->format('d-M-y') }}</td>
                                    <td>{{ $transaction->trn_no }}</td>
                                    <td>{{ $transaction->customer->name ?? $transaction->customer_name }}</td>
                                    <td class="text-end">{{ number_format($transaction->net_amt, 2) }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card mt-2">
            <div class="card-body p-3 text-center">
                <p class="mb-0 text-muted">No records found for the selected criteria.</p>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }
function printReport() { window.open('{{ route("admin.reports.misc-transaction.sale-return-replacement") }}?print=1&' + $('#filterForm').serialize(), '_blank'); }

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key === 'f') { e.preventDefault(); $('input[name="from_date"]').focus(); }
    if (e.altKey && e.key === 'y') { e.preventDefault(); $('input[name="with_item_details"]').val('Y').focus(); }
    if (e.altKey && e.key === 'n') { e.preventDefault(); $('input[name="with_item_details"]').val('N').focus(); }
    if (e.altKey && e.key === 'p') { e.preventDefault(); printReport(); }
    if (e.altKey && e.key === 'v') { e.preventDefault(); $('#filterForm').submit(); }
    if (e.altKey && e.key === 'c') { e.preventDefault(); closeWindow(); }
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