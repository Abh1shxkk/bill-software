@extends('layouts.admin')

@section('title', 'Supplier Wise - Item Wise')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">SUPPLIER WISE - ITEM WISE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.item-wise') }}" id="reportForm">
                <!-- Report Type Selection -->
                <div class="row mb-2">
                    <div class="col-12">
                        <label class="small text-muted fw-bold">
                            1. Purchase / 2. Purchase Return / 3. Both:
                        </label>
                        <select name="report_type" class="form-select form-select-sm d-inline-block" style="width: 60px;">
                            <option value="1" {{ ($reportType ?? '3') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($reportType ?? '3') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($reportType ?? '3') == '3' ? 'selected' : '' }}>3</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end">
                    <!-- From Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">From :</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">To :</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Supplier Code -->
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Supplier :</label>
                        <input type="text" name="supplier_code" id="supplierCode" class="form-control form-control-sm" 
                               value="{{ $supplierCode ?? '00' }}" style="width: 60px;">
                    </div>

                    <!-- Supplier Select -->
                    <div class="col-md-3">
                        <select name="supplier_id" id="supplierSelect" class="form-select form-select-sm">
                            <option value="">-- All Suppliers --</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" data-code="{{ $supplier->code }}"
                                    {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Group By -->
                    <div class="col-md-2">
                        <label class="small text-muted">(C)ompany / (I)tem :</label>
                        <input type="text" name="group_by" class="form-control form-control-sm" 
                               value="{{ $groupBy ?? 'C' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Tagged Companies -->
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Companies [ Y / N ] :</label>
                        <input type="text" name="tagged_companies" class="form-control form-control-sm" 
                               value="{{ $taggedCompanies ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Remove Tags -->
                    <div class="col-md-2">
                        <label class="small text-muted">Remove Tags [ Y / N ] :</label>
                        <input type="text" name="remove_tags" class="form-control form-control-sm" 
                               value="{{ $removeTags ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Company Code -->
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Company :</label>
                        <input type="text" name="company_code" id="companyCode" class="form-control form-control-sm" 
                               value="{{ $companyCode ?? '00' }}" style="width: 60px;">
                    </div>

                    <!-- Company Select -->
                    <div class="col-md-3">
                        <select name="company_id" id="companySelect" class="form-select form-select-sm">
                            <option value="">-- All Companies --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" data-code="{{ $company->short_name }}"
                                    {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Division Code -->
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Division :</label>
                        <input type="text" name="division_code" id="divisionCode" class="form-control form-control-sm" 
                               value="{{ $divisionCode ?? '00' }}" style="width: 60px;">
                    </div>

                    <!-- Division Select (placeholder) -->
                    <div class="col-md-3">
                        <select name="division_id" id="divisionSelect" class="form-select form-select-sm">
                            <option value="">-- All Divisions --</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Item Category Code -->
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Item Category :</label>
                        <input type="text" name="category_code" id="categoryCode" class="form-control form-control-sm" 
                               value="{{ $categoryCode ?? '00' }}" style="width: 60px;">
                    </div>

                    <!-- Category Select -->
                    <div class="col-md-3">
                        <select name="category_id" id="categorySelect" class="form-select form-select-sm">
                            <option value="">-- All Categories --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}"
                                    {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Buttons -->
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-secondary btn-sm px-4">View</button>
                        <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
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
                            <th class="text-center" style="width: 40px;">S.No</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Avg Rate</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0;
                            $grandFreeQty = 0;
                            $grandAmount = 0;
                            $grandTax = 0;
                            $grandNet = 0;
                        @endphp
                        @foreach($items as $index => $item)
                        @php
                            $grandQty += $item->total_qty ?? 0;
                            $grandFreeQty += $item->total_free_qty ?? 0;
                            $grandAmount += $item->total_amount ?? 0;
                            $grandTax += $item->total_tax ?? 0;
                            $grandNet += $item->total_net ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->total_qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_free_qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->avg_rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_tax ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->total_net ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandFreeQty, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format($grandAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTax, 2) }}</td>
                            <td class="text-end">{{ number_format($grandNet, 2) }}</td>
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
// Sync supplier code with select
document.getElementById('supplierSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('supplierCode').value = selected.dataset.code || '00';
});

// Sync company code with select
document.getElementById('companySelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('companyCode').value = selected.dataset.code || '00';
});

function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.supplier.item-wise.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
