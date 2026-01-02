@extends('layouts.admin')

@section('title', 'Supplier Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">Supplier Wise Purchase</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.all-supplier') }}" id="reportForm">
                <!-- Report Type Selection -->
                <div class="row mb-2">
                    <div class="col-12">
                        <label class="small text-muted fw-bold">
                            1. Purchase / 2. Purchase Return / 3. Debit Note / 4. Credit Note / 5. Consolidated Purchase Book :
                        </label>
                        <select name="report_type" class="form-select form-select-sm d-inline-block" style="width: 60px;">
                            <option value="1" {{ ($reportType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($reportType ?? '1') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($reportType ?? '1') == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ ($reportType ?? '1') == '4' ? 'selected' : '' }}>4</option>
                            <option value="5" {{ ($reportType ?? '1') == '5' ? 'selected' : '' }}>5</option>
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
                    <!-- Order By -->
                    <div class="col-md-2">
                        <label class="small text-muted">Order By N(ame) / V(alue) :</label>
                        <input type="text" name="order_by" class="form-control form-control-sm" 
                               value="{{ $orderBy ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-2">
                        <label class="small text-muted">A(scending) / D(escending) :</label>
                        <input type="text" name="sort_order" class="form-control form-control-sm" 
                               value="{{ $sortOrder ?? 'A' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- With Br. / Expiry -->
                    <div class="col-md-2">
                        <label class="small text-muted">With Br. / Expiry [ Y / N ] :</label>
                        <input type="text" name="with_br_expiry" class="form-control form-control-sm" 
                               value="{{ $withBrExpiry ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-4 text-end">
                        <button type="submit" class="btn btn-secondary btn-sm px-4">View</button>
                        <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($suppliers) && $suppliers->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 60px;">S.No</th>
                            <th>Supplier Name</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th class="text-end">Total Bills</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Tax Amount</th>
                            <th class="text-end">Net Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotalBills = 0;
                            $grandTotalAmount = 0;
                            $grandTaxAmount = 0;
                            $grandNetPayable = 0;
                        @endphp
                        @foreach($suppliers as $index => $data)
                        @php
                            $grandTotalBills += $data->total_bills;
                            $grandTotalAmount += $data->total_amount;
                            $grandTaxAmount += $data->tax_amount;
                            $grandNetPayable += $data->net_payable;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $data->supplier->address ?? '-' }}</td>
                            <td>{{ $data->supplier->mobile ?? '-' }}</td>
                            <td class="text-end">{{ $data->total_bills }}</td>
                            <td class="text-end">{{ number_format($data->total_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($data->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($data->net_payable, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ $grandTotalBills }}</td>
                            <td class="text-end">{{ number_format($grandTotalAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTaxAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($grandNetPayable, 2) }}</td>
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
function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.supplier.all-supplier.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush