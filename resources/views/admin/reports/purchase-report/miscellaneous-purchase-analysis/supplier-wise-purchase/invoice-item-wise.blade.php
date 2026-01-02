@extends('layouts.admin')

@section('title', 'Supplier Wise - Invoice Item Wise')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">SUPPLIER WISE - INVOICE ITEM WISE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.invoice-item-wise') }}" id="reportForm">
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

                    <!-- Division Select -->
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
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success btn-sm px-4">Excel</button>
                        <span class="float-end">
                            <button type="submit" class="btn btn-secondary btn-sm px-4">View</button>
                            <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
                            <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Close</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($invoices) && $invoices->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Bill No / Date</th>
                            <th>Supplier</th>
                            <th>Item Details</th>
                            <th>Batch / Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0;
                            $grandFree = 0;
                            $grandAmount = 0;
                        @endphp
                        @foreach($invoices as $invoice)
                        @php
                            $invoiceTotal = $invoice->items->sum('amount');
                            $invoiceQty = $invoice->items->sum('qty');
                            $invoiceFree = $invoice->items->sum('free_qty');
                            $grandQty += $invoiceQty;
                            $grandFree += $invoiceFree;
                            $grandAmount += $invoiceTotal;
                        @endphp
                        <tr class="table-warning fw-bold">
                            <td colspan="4">
                                Bill No: {{ $invoice->bill_no ?? '-' }} | 
                                Date: {{ $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '-' }} |
                                Supplier: {{ $invoice->supplier->name ?? '-' }}
                            </td>
                            <td class="text-end">{{ number_format($invoiceQty, 2) }}</td>
                            <td class="text-end">{{ number_format($invoiceFree, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">₹{{ number_format($invoiceTotal, 2) }}</td>
                        </tr>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->batch_no ?? '-' }} / {{ $item->expiry_date ? $item->expiry_date->format('m/y') : '-' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->free_qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">₹{{ number_format($grandAmount, 2) }}</td>
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
    const printUrl = "{{ route('admin.reports.purchase.misc.supplier.invoice-item-wise.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
