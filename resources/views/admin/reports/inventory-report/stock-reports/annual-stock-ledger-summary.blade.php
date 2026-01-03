@extends('layouts.admin')

@section('title', 'Annual Stock Ledger Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Annual Stock Ledger Summary</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.annual-stock-ledger-summary') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Regenerate :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="regenerate" class="form-select form-select-sm">
                            <option value="N" {{ request('regenerate') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ request('regenerate') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Closing Stock Year :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="closing_year" class="form-select form-select-sm">
                            <option value="2026" {{ request('closing_year') == '2026' ? 'selected' : '' }}>2026</option>
                            <option value="2025" {{ request('closing_year', '2025') == '2025' ? 'selected' : '' }}>2025</option>
                            <option value="2024" {{ request('closing_year') == '2024' ? 'selected' : '' }}>2024</option>
                            <option value="2023" {{ request('closing_year') == '2023' ? 'selected' : '' }}>2023</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Show Godown Expiry :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="show_godown_expiry" class="form-select form-select-sm">
                            <option value="N" {{ request('show_godown_expiry') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ request('show_godown_expiry') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">With Value :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="with_value" class="form-select form-select-sm">
                            <option value="Y" {{ request('with_value', 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                            <option value="N" {{ request('with_value') == 'N' ? 'selected' : '' }}>N</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Total Opening Qty :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="total_opening_qty" class="form-control form-control-sm text-end" value="{{ request('total_opening_qty', '0.00') }}" readonly>
                    </div>
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Value :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="total_opening_value" class="form-control form-control-sm text-end" value="{{ request('total_opening_value', '0.00') }}" readonly>
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 offset-md-6 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>Annual Stock Ledger Summary</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                <button type="button" class="btn btn-sm btn-success ms-1" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Opening Qty</th>
                            <th class="text-end">Opening Value</th>
                            <th class="text-end">Purchase Qty</th>
                            <th class="text-end">Purchase Value</th>
                            <th class="text-end">Sale Qty</th>
                            <th class="text-end">Sale Value</th>
                            <th class="text-end">Closing Qty</th>
                            <th class="text-end">Closing Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td>{{ $row['company_name'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['opening_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['opening_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['closing_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['closing_value'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['total_opening_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_opening_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_purchase_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_purchase_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_sale_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_sale_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_closing_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_closing_value'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $reportData->count() }}</small>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.stock.annual-stock-ledger-summary") }}?' + params.toString();
}
function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.annual-stock-ledger-summary") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.4rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
