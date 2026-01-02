@extends('layouts.admin')

@section('title', 'Supplier - Bill Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">Supplier - Bill Wise Purchase</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.bill-wise') }}" id="reportForm">
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

                    <!-- Tagged Parties -->
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Parties [ Y / N ] :</label>
                        <input type="text" name="tagged_parties" class="form-control form-control-sm" 
                               value="{{ $taggedParties ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Remove Tags -->
                    <div class="col-md-2">
                        <label class="small text-muted">Remove Tags [ Y / N ] :</label>
                        <input type="text" name="remove_tags" class="form-control form-control-sm" 
                               value="{{ $removeTags ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <!-- Supplier Code -->
                    <div class="col-md-1">
                        <label class="small text-muted text-primary fw-bold">Supplier :</label>
                        <input type="text" name="supplier_code" id="supplierCode" class="form-control form-control-sm" 
                               value="{{ $supplierCode ?? '00' }}" style="width: 60px;">
                    </div>

                    <!-- Supplier Select -->
                    <div class="col-md-3">
                        <label class="small text-muted">&nbsp;</label>
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

                    <!-- With Br. / Expiry -->
                    <div class="col-md-2">
                        <label class="small text-muted">With Br. / Expiry [ Y / N ] :</label>
                        <input type="text" name="with_br_expiry" class="form-control form-control-sm" 
                               value="{{ $withBrExpiry ?? 'Y' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Amount Type -->
                    <div class="col-md-2">
                        <label class="small text-muted">1. Taxable / 2. Bill Amt. :</label>
                        <input type="text" name="amount_type" class="form-control form-control-sm" 
                               value="{{ $amountType ?? '1' }}" maxlength="1" style="width: 50px;">
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

    @if(isset($bills) && $bills->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">S.No</th>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Supplier Name</th>
                            <th>Type</th>
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax Amt</th>
                            <th class="text-end">Net Amount</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandGrossAmt = 0;
                            $grandDiscount = 0;
                            $grandTaxAmt = 0;
                            $grandNetAmt = 0;
                        @endphp
                        @foreach($bills as $index => $bill)
                        @php
                            $grandGrossAmt += $bill->nt_amount ?? 0;
                            $grandDiscount += $bill->dis_amount ?? 0;
                            $grandTaxAmt += $bill->tax_amount ?? 0;
                            $grandNetAmt += $bill->net_amount ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $bill->bill_date ? $bill->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $bill->bill_no ?? '-' }}</td>
                            <td>{{ $bill->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $bill->cash_flag == 'Y' ? 'Cash' : 'Credit' }}</td>
                            <td class="text-end">{{ number_format($bill->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($bill->dis_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($bill->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($bill->net_amount ?? 0, 2) }}</td>
                            <td>{{ $bill->due_date ? $bill->due_date->format('d-m-Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandGrossAmt, 2) }}</td>
                            <td class="text-end">{{ number_format($grandDiscount, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTaxAmt, 2) }}</td>
                            <td class="text-end">{{ number_format($grandNetAmt, 2) }}</td>
                            <td></td>
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

function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.supplier.bill-wise.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
