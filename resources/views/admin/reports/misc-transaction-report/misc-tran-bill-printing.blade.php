@extends('layouts.admin')
@section('title', 'Misc. Tran. Bill Printing')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.misc-transaction.bill-printing') }}">
                <div class="row">
                    <!-- Left Column - Transaction Types -->
                    <div class="col-md-5">
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_adjustment" value="1" id="stock_adjustment" {{ request('stock_adjustment') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_adjustment">Stock Adjustment</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sample_issued" value="1" id="sample_issued" {{ request('sample_issued') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sample_issued">Sample Issued</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="replacement_issued" value="1" id="replacement_issued" {{ request('replacement_issued') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="replacement_issued">Replacement Issued</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sample_received" value="1" id="sample_received" {{ request('sample_received') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sample_received">Sample Received</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_transfer_outgoing" value="1" id="stock_transfer_outgoing" {{ request('stock_transfer_outgoing') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_transfer_outgoing">Stock Transfer - Outgoing</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quotation" value="1" id="quotation" {{ request('quotation') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="quotation">Quotation</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_transfer_outgoing_return" value="1" id="stock_transfer_outgoing_return" {{ request('stock_transfer_outgoing_return') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_transfer_outgoing_return">Stock Transfer - Outgoing Return</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="godown_breakage_expiry" value="1" id="godown_breakage_expiry" {{ request('godown_breakage_expiry') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="godown_breakage_expiry">Godown Breakage Expiry Adj.</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_transfer_incomming" value="1" id="stock_transfer_incomming" {{ request('stock_transfer_incomming') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_transfer_incomming">Stock Transfer - Incomming</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="claim_to_supplier" value="1" id="claim_to_supplier" {{ request('claim_to_supplier') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="claim_to_supplier">Claim To Supplier</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_transfer_incomming_return" value="1" id="stock_transfer_incomming_return" {{ request('stock_transfer_incomming_return') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_transfer_incomming_return">Stock Transfer - Incomming Return</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="debit_note" value="1" id="debit_note" {{ request('debit_note') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="debit_note">Debit Note</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sale_return_replacement" value="1" id="sale_return_replacement" {{ request('sale_return_replacement') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sale_return_replacement">Sale Return Replacement</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="credit_note" value="1" id="credit_note" {{ request('credit_note') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="credit_note">Credit Note</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Filters -->
                    <div class="col-md-7">
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto"><label class="fw-bold mb-0">From :</label></div>
                            <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 130px;"></div>
                            <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                            <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 130px;"></div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto"><label class="fw-bold mb-0">Nos. From :</label></div>
                            <div class="col-auto"><input type="text" name="nos_from" class="form-control form-control-sm" value="{{ request('nos_from') }}" style="width: 100px;"></div>
                            <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                            <div class="col-auto"><input type="text" name="nos_to" class="form-control form-control-sm" value="{{ request('nos_to') }}" style="width: 100px;"></div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto"><label class="fw-bold mb-0">Customer :</label></div>
                            <div class="col">
                                <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                                    <option value="">-- All Customers --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->code }} - {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12 text-end">
                                <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm">Ok</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card mt-2">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="resultsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">DATE</th>
                            <th style="width: 100px;">TRN.NO.</th>
                            <th>PARTY NAME</th>
                            <th style="width: 100px;" class="text-end">AMOUNT</th>
                            <th style="width: 50px;" class="text-center">TAG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($reportData) && $reportData->count() > 0)
                            @foreach($reportData as $index => $item)
                            <tr data-id="{{ $item->id ?? $index }}">
                                <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                                <td>{{ $item->trn_no ?? '' }}</td>
                                <td>{{ $item->party_name ?? '' }}</td>
                                <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                                <td class="text-center tag-cell">{{ $item->tag ?? '' }}</td>
                            </tr>
                            @endforeach
                        @else
                            @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer with instructions and buttons -->
    <div class="card mt-2">
        <div class="card-body p-2 d-flex justify-content-between align-items-center">
            <span class="text-danger fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Press - "+" TAG  /  "-" UNTAG</span>
            <div>
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">Print (F7)</button>
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Exit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }
function printReport() { window.open('{{ route("admin.reports.misc-transaction.bill-printing") }}?print=1&' + $('#filterForm').serialize(), '_blank'); }

// Tag/Untag functionality
$(document).on('keydown', function(e) {
    var $focused = $('tr.table-active');
    if (e.key === '+' || e.key === '=') {
        e.preventDefault();
        if ($focused.length) { $focused.find('.tag-cell').text('+'); }
    } else if (e.key === '-') {
        e.preventDefault();
        if ($focused.length) { $focused.find('.tag-cell').text(''); }
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        var $next = $focused.next('tr');
        if ($next.length) { $focused.removeClass('table-active'); $next.addClass('table-active'); }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        var $prev = $focused.prev('tr');
        if ($prev.length) { $focused.removeClass('table-active'); $prev.addClass('table-active'); }
    } else if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
});

$('#resultsTable tbody tr').on('click', function() {
    $('#resultsTable tbody tr').removeClass('table-active');
    $(this).addClass('table-active');
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
.form-check-label { font-size: 0.85rem; }
.table-active { background-color: #cce5ff !important; }
</style>
@endpush