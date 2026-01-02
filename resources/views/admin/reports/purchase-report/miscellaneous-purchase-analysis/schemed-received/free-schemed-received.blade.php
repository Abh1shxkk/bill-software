@extends('layouts.admin')

@section('title', 'Free Scheme Received Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #c2185b;">FREE SCHEME RECEIVED</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.free-schemed') }}" id="reportForm">
                <div class="row g-2 align-items-end">
                    <!-- From Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">From :</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $dateFrom ?? date('Y-m-01') }}">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">To :</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>

                    <!-- Bill Date / Received Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">B(ill Date) / R(eceived Date) :</label>
                        <select name="date_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="B" {{ ($dateType ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="R" {{ ($dateType ?? 'B') == 'R' ? 'selected' : '' }}>R</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Show Return -->
                    <div class="col-md-2">
                        <label class="small text-muted">Show Return [Y / N] :</label>
                        <select name="show_return" class="form-select form-select-sm" style="width: 60px;">
                            <option value="N" {{ ($showReturn ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($showReturn ?? 'N') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>

                    <!-- D(etailed) / S(ummary) -->
                    <div class="col-md-2">
                        <label class="small text-muted">D(etailed)/ S(ummary) :</label>
                        <select name="report_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="D" {{ ($reportType ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                            <option value="S" {{ ($reportType ?? 'D') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Company Wise -->
                    <div class="col-md-1">
                        <label class="small text-muted">Company Wise :</label>
                        <input type="text" name="company_code" class="form-control form-control-sm" 
                               value="{{ $companyCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-5">
                        <select name="company_id" class="form-select form-select-sm" id="companySelect">
                            <option value="">All Companies</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Division -->
                    <div class="col-md-1">
                        <label class="small text-muted">Division :</label>
                        <input type="text" name="division_code" class="form-control form-control-sm" 
                               value="{{ $divisionCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-2">
                        <select name="division_id" class="form-select form-select-sm" id="divisionSelect">
                            <option value="">All Divisions</option>
                            @foreach($divisions ?? [] as $division)
                                <option value="{{ $division->id }}" {{ ($divisionId ?? '') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- VAT -->
                    <div class="col-md-2">
                        <label class="small text-muted">VAT [Y\N] :</label>
                        <select name="vat" class="form-select form-select-sm" style="width: 60px;">
                            <option value="N" {{ ($vat ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($vat ?? 'N') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>

                    <!-- S(rate) / P(rate) -->
                    <div class="col-md-2">
                        <label class="small text-muted">S(rate) / P(rate) :</label>
                        <select name="rate_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="P" {{ ($rateType ?? 'P') == 'P' ? 'selected' : '' }}>P</option>
                            <option value="S" {{ ($rateType ?? 'P') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Tagged Categories -->
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Categories [ Y / N ] :</label>
                        <select name="tagged_categories" class="form-select form-select-sm" style="width: 60px;">
                            <option value="N" {{ ($taggedCategories ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($taggedCategories ?? 'N') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>

                    <!-- Remove Tags -->
                    <div class="col-md-2">
                        <label class="small text-muted">Remove Tags [ Y / N ] :</label>
                        <select name="remove_tags" class="form-select form-select-sm" style="width: 60px;" {{ ($taggedCategories ?? 'N') == 'N' ? 'disabled' : '' }}>
                            <option value="N" {{ ($removeTags ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($removeTags ?? 'N') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Item Category -->
                    <div class="col-md-1">
                        <label class="small text-muted">Item Category :</label>
                        <input type="text" name="category_code" class="form-control form-control-sm" 
                               value="{{ $categoryCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-5">
                        <select name="category_id" class="form-select form-select-sm" id="categorySelect">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Supplier -->
                    <div class="col-md-1">
                        <label class="small text-muted">Supplier :</label>
                        <input type="text" name="supplier_code" class="form-control form-control-sm" 
                               value="{{ $supplierCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-5">
                        <select name="supplier_id" class="form-select form-select-sm" id="supplierSelect">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-success btn-sm px-4" onclick="exportExcel()">Excel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">View</button>
                        <button type="button" class="btn btn-info btn-sm px-4" onclick="openPrintView()">Print</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($items) && $items->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th>Bill Date</th>
                            <th>Bill No</th>
                            <th>Supplier Name</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Pur. Qty</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Scheme %</th>
                            <th class="text-end">Free Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0;
                            $grandFree = 0;
                            $grandValue = 0;
                        @endphp
                        @foreach($items as $index => $item)
                        @php
                            $grandQty += $item->qty ?? 0;
                            $grandFree += $item->free_qty ?? 0;
                            $grandValue += $item->free_value ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->bill_no ?? '-' }}</td>
                            <td>{{ $item->supplier_name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 0) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($item->free_qty ?? 0, 0) }}</td>
                            <td class="text-end">{{ $item->scheme_percent ?? '-' }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->free_value ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 0) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 0) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format($grandValue, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('from_date'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No records found for the selected criteria.
    </div>
    @else
    <div class="alert alert-secondary">
        <i class="bi bi-info-circle"></i> Select date range and click "View" to generate the report.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportExcel() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    formData.append('export', 'excel');
    const params = new URLSearchParams(formData).toString();
    window.location.href = "{{ route('admin.reports.purchase.misc.schemed.free-schemed') }}?" + params;
}

function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.schemed.free-schemed.print') }}?" + params;
    window.open(printUrl, '_blank');
}

// Toggle Remove Tags based on Tagged Categories
document.querySelector('select[name="tagged_categories"]').addEventListener('change', function() {
    const removeTagsSelect = document.querySelector('select[name="remove_tags"]');
    removeTagsSelect.disabled = this.value === 'N';
    if (this.value === 'N') {
        removeTagsSelect.value = 'N';
    }
});
</script>
@endpush
