@extends('layouts.admin')

@section('title', 'Company Wise Purchase - Party Wise')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">Company Wise Purchase - Party Wise</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.company.party-wise') }}" id="reportForm">
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
                            @foreach($companyList ?? [] as $company)
                                <option value="{{ $company->id }}" data-code="{{ $company->short_name }}"
                                    {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-secondary btn-sm px-4">View</button>
                        <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($parties) && $parties->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">S.No</th>
                            <th>Company</th>
                            <th>Supplier (Party)</th>
                            <th class="text-end">Total Bills</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandBills = 0; $grandQty = 0;
                            $grandAmount = 0; $grandNet = 0;
                        @endphp
                        @foreach($parties as $index => $party)
                        @php
                            $grandBills += $party->total_bills;
                            $grandQty += $party->total_qty;
                            $grandAmount += $party->total_amount;
                            $grandNet += $party->total_net;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $party->company_name ?? 'N/A' }}</td>
                            <td>{{ $party->supplier->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ $party->total_bills }}</td>
                            <td class="text-end">{{ number_format($party->total_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($party->total_amount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($party->total_net, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ $grandBills }}</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandAmount, 2) }}</td>
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
document.getElementById('companySelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('companyCode').value = selected.dataset.code || '00';
});

function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.company.party-wise.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
