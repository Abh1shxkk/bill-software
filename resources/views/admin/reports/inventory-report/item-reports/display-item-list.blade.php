@extends('layouts.admin')

@section('title', 'Display Item List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">-: Display Item List :-</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.display-item-list') }}">
                <!-- Date Range & Item -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from', date('Y-m-d')) }}">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to', date('Y-m-d')) }}">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Item :</label>
                    </div>
                    <div class="col-md-1">
                        <select name="item_id" id="item_id" class="form-select form-select-sm">
                            <option value="">00</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="item_name_display" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border fw-bold shadow-sm">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card mt-2">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm mb-0" style="font-size: 11px;">
                    <thead class="table-danger sticky-top">
                        <tr>
                            <th style="width: 70px;">Date</th>
                            <th style="width: 60px;">Bill No.</th>
                            <th style="width: 50px;">Code</th>
                            <th>Party Name</th>
                            <th>Sales Man</th>
                            <th>Product</th>
                            <th class="text-end" style="width: 70px;">Amount</th>
                            <th class="text-end" style="width: 50px;">Qty.</th>
                            <th style="width: 40px;">Tag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData ?? [] as $row)
                        <tr>
                            <td>{{ isset($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('d-M-y') : '' }}</td>
                            <td>{{ $row['bill_no'] ?? '' }}</td>
                            <td>{{ $row['code'] ?? '' }}</td>
                            <td>{{ $row['party_name'] ?? $row['name'] ?? '' }}</td>
                            <td>{{ $row['salesman'] ?? '' }}</td>
                            <td>{{ $row['product'] ?? $row['name'] ?? '' }}</td>
                            <td class="text-end">{{ isset($row['amount']) ? number_format($row['amount'], 2) : '' }}</td>
                            <td class="text-end">{{ isset($row['qty']) ? number_format($row['qty'], 0) : ($row['current_stock'] ?? '') }}</td>
                            <td>{{ $row['tag'] ?? '' }}</td>
                        </tr>
                        @empty
                        @for($i = 0; $i < 15; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endfor
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Stats -->
    <div class="card mt-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <strong>Total Records : {{ $totals['total_records'] ?? 0 }}</strong>
                </div>
                <div class="col-auto">
                    <strong>Display Issued : {{ $totals['display_issued'] ?? 0 }}</strong>
                </div>
                <div class="col-auto">
                    <strong class="text-danger">Display Pending : {{ $totals['display_pending'] ?? 0 }}</strong>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-light border fw-bold shadow-sm" onclick="closeWindow()">Exit (Esc)</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('item_id').addEventListener('change', function() {
    @if(isset($items))
    var items = @json($items);
    var item = items.find(i => i.id == this.value);
    document.getElementById('item_name_display').value = item ? item.name : '';
    @endif
});

function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") }}';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeWindow();
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.4rem; vertical-align: middle; }
.table-danger th { background-color: #dc3545; color: white; }
</style>
@endpush
