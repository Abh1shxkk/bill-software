@extends('layouts.admin')

@section('title', 'Reorder on Minimum Stock Basis')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">REORDER ON MINIMUM STOCK BASIS</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- C(ompany)/S(upplier) -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-4 text-end pe-2">
                                <label class="fw-bold mb-0"><u>C</u>(ompany)/<u>S</u>(upplier) :</label>
                            </div>
                            <div class="col-md-6">
                                <select name="cs_type" id="cs_type" class="form-select form-select-sm">
                                    <option value="C" {{ ($csType ?? 'C') == 'C' ? 'selected' : '' }}>C(ompany)</option>
                                    <option value="S" {{ ($csType ?? '') == 'S' ? 'selected' : '' }}>S(upplier)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Company -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-4 text-end pe-2">
                                <label class="fw-bold mb-0">Company :</label>
                            </div>
                            <div class="col-md-6">
                                <select name="company_id" id="company_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->id }} - {{ Str::limit($company->name, 20) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Division -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-4 text-end pe-2">
                                <label class="fw-bold mb-0">Division :</label>
                            </div>
                            <div class="col-md-6">
                                <select name="division_id" id="division_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($divisions ?? [] as $division)
                                        <option value="{{ $division->id }}" {{ ($divisionId ?? '') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- All Items Y/N/O -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-4 text-end pe-2">
                                <label class="fw-bold mb-0">All Items <u>Y</u>/<u>N</u>/<u>O</u> :</label>
                            </div>
                            <div class="col-md-6">
                                <select name="all_items" id="all_items" class="form-select form-select-sm">
                                    <option value="Y" {{ ($allItems ?? 'Y') == 'Y' ? 'selected' : '' }}>Y(es)</option>
                                    <option value="N" {{ ($allItems ?? '') == 'N' ? 'selected' : '' }}>N(o)</option>
                                    <option value="O" {{ ($allItems ?? '') == 'O' ? 'selected' : '' }}>O(nly Selected)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-4 text-end pe-2">
                                <label class="fw-bold mb-0">Category :</label>
                            </div>
                            <div class="col-md-6">
                                <select name="category_id" id="category_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Empty for this report (no date range) -->
                    <div class="col-md-6">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Tables Section -->
    @if(isset($reportData) && count($reportData) > 0)
    <div class="row mt-2">
        <!-- Left Table - Purchase Details -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-secondary sticky-top">
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Bill No</th>
                                    <th>Party</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Free</th>
                                    <th class="text-end">P.Rate</th>
                                    <th class="text-end">Dis.%</th>
                                    <th class="text-end">S.Rate</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">MRP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseDetails ?? [] as $pd)
                                <tr>
                                    <td class="text-center">{{ $pd['date'] ?? '' }}</td>
                                    <td class="text-center">{{ $pd['bill_no'] ?? '' }}</td>
                                    <td>{{ Str::limit($pd['party'] ?? '', 15) }}</td>
                                    <td class="text-end">{{ $pd['qty'] ?? 0 }}</td>
                                    <td class="text-end">{{ $pd['free'] ?? 0 }}</td>
                                    <td class="text-end">{{ number_format($pd['p_rate'] ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($pd['dis_per'] ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($pd['s_rate'] ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($pd['cost'] ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($pd['mrp'] ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Table - Stock Details -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-secondary sticky-top">
                                <tr>
                                    <th>Company</th>
                                    <th>Item Name</th>
                                    <th class="text-center">Pack</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-end">Min</th>
                                    <th class="text-end">Max</th>
                                    <th class="text-end">Bal</th>
                                    <th class="text-end">O.Ord</th>
                                    <th class="text-end">P.Ord</th>
                                    <th class="text-end">Scm</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">F.Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData ?? [] as $row)
                                <tr>
                                    <td>{{ Str::limit($row['company_name'] ?? '', 10) }}</td>
                                    <td>{{ Str::limit($row['item_name'] ?? '', 20) }}</td>
                                    <td class="text-center">{{ $row['pack'] ?? '' }}</td>
                                    <td class="text-center">{{ $row['unit'] ?? '' }}</td>
                                    <td class="text-end">{{ $row['min_stock'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['max_stock'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['balance'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['o_ord'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['p_ord'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['scm'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['qty'] ?? 0 }}</td>
                                    <td class="text-end">{{ $row['f_qty'] ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Totals -->
    <div class="card mt-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-4">
                    <span class="fw-bold">SALE : </span>
                    <span class="text-primary fw-bold">{{ number_format($totals['total_sale'] ?? 0, 0) }}</span>
                </div>
                <div class="col-md-4">
                    <span class="fw-bold">CLOSING : </span>
                    <span class="text-primary fw-bold">{{ number_format($totals['total_closing'] ?? 0, 0) }}</span>
                </div>
                <div class="col-md-4">
                    <span class="fw-bold">RE-ORDER : </span>
                    <span class="text-danger fw-bold">{{ number_format($totals['total_reorder'] ?? 0, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="card mt-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="printReport()">Print(<u>F7</u>)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="showCase()">Case</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="showBox()">Box</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="exportToExcel()"><u>E</u>xcel</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="saveReport()">Save</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="closeWindow()">Exit</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('print', '1');
    window.open('{{ url()->current() }}?' + params.toString(), '_blank');
}

function showCase() {
    alert('Case view functionality');
}

function showBox() {
    alert('Box view functionality');
}

function exportToExcel() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('export', 'excel');
    window.location.href = '{{ url()->current() }}?' + params.toString();
}

function saveReport() {
    alert('Save functionality');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
}

// Keyboard shortcut for Print
document.addEventListener('keydown', function(e) {
    if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.3rem; font-size: 0.75rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 1; }
</style>
@endpush
