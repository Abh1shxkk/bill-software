@extends('layouts.admin')

@section('title', 'Stock and O/S Report for Bank')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">STOCK and O/S REPORT FOR BANK</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- CLOSING STOCK (%) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="fw-bold mb-0">CLOSING STOCK (%) :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="closing_stock_per" id="closing_stock_per" class="form-control form-control-sm text-end" value="{{ $closingStockPer ?? '0.00' }}" step="0.01">
                    </div>
                </div>

                <!-- D(UE LIST) / L(EDGER) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="fw-bold mb-0"><u>D</u>(UE LIST) / <u>L</u>(EDGER) :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="dl_type" id="dl_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="D" {{ ($dlType ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                            <option value="L" {{ ($dlType ?? '') == 'L' ? 'selected' : '' }}>L</option>
                        </select>
                    </div>
                </div>

                <!-- DUE LIST (%) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="fw-bold mb-0">DUE LIST (%) :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="due_list_per" id="due_list_per" class="form-control form-control-sm text-end" value="{{ $dueListPer ?? '0.00' }}" step="0.01">
                    </div>
                </div>

                <!-- POST DATED CHQ. (%) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="fw-bold mb-0">POST DATED CHQ. (%) :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="pdc_per" id="pdc_per" class="form-control form-control-sm text-end" value="{{ $pdcPer ?? '0.00' }}" step="0.01">
                    </div>
                </div>

                <!-- As On -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="fw-bold mb-0">As On :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="as_on_date" id="as_on_date" class="form-control form-control-sm" value="{{ $asOnDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- With Creditors -->
                <div class="row g-2 mb-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="with_creditors" id="with_creditors" {{ ($withCreditors ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="with_creditors">With Creditors</label>
                        </div>
                    </div>
                    <div class="col-md-8 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Sr.</th>
                            <th>Particulars</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">%</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['particulars'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['percentage'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="2" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($totals['total_amount'] ?? 0, 2) }}</th>
                            <th></th>
                            <th class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.8rem; }
</style>
@endpush
