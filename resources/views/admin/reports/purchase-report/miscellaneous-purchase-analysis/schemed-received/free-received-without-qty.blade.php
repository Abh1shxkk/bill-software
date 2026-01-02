@extends('layouts.admin')

@section('title', 'Free Issues Without Qty Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #c2185b;">Free Issues Without Qty.</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.free-without-qty') }}" id="reportForm">
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

                    <!-- Bill Date / Sys Date -->
                    <div class="col-md-2">
                        <label class="small text-muted">B(ill Date)/ S(ys Date) :</label>
                        <select name="date_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="B" {{ ($dateType ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="S" {{ ($dateType ?? 'B') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>

                    <!-- Report Type -->
                    <div class="col-md-3">
                        <label class="small text-muted d-block">Report Type :</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="report_type" id="detailed" value="D" {{ ($reportType ?? 'D') == 'D' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="detailed">Detailed</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="report_type" id="summarised" value="S" {{ ($reportType ?? 'D') == 'S' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="summarised">Summarised</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Company -->
                    <div class="col-md-1">
                        <label class="small text-muted">Company :</label>
                        <input type="text" name="company_code" class="form-control form-control-sm" 
                               value="{{ $companyCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-3">
                        <select name="company_id" class="form-select form-select-sm" id="companySelect">
                            <option value="">All Companies</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer -->
                    <div class="col-md-1">
                        <label class="small text-muted">Customer :</label>
                        <input type="text" name="customer_code" class="form-control form-control-sm" 
                               value="{{ $customerCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-3">
                        <select name="customer_id" class="form-select form-select-sm" id="customerSelect">
                            <option value="">All Customers</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Exit</button>
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
                            <th>Date</th>
                            <th>Bill Details</th>
                            <th>Supplier Name</th>
                            <th>Item Name</th>
                            <th>Pack</th>
                            <th class="text-end">Pur. Qty</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0;
                            $grandFree = 0;
                            $grandAmount = 0;
                        @endphp
                        @foreach($items as $index => $item)
                        @php
                            $grandQty += $item->qty ?? 0;
                            $grandFree += $item->free_qty ?? 0;
                            $grandAmount += $item->amount ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->bill_no ?? '-' }}</td>
                            <td>{{ $item->supplier_name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->packing ?? '-' }}</td>
                            <td class="text-end text-muted">{{ number_format($item->qty ?? 0, 0) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($item->free_qty ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 0) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 0) }}</td>
                            <td class="text-end">{{ number_format($grandAmount, 2) }}</td>
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
        <i class="bi bi-info-circle"></i> Select date range and click "Print" to generate the report.
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
    const printUrl = "{{ route('admin.reports.purchase.misc.schemed.free-without-qty.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
