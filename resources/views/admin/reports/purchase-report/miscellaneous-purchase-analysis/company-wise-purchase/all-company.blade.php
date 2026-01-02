@extends('layouts.admin')

@section('title', 'Company Wise Purchase - All Company')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">Company Wise Purchase - All Company</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.company.all-company') }}" id="reportForm">
                <!-- Report Type Selection -->
                <div class="row mb-2">
                    <div class="col-12">
                        <label class="small text-muted fw-bold">
                            1. Purchase / 2. Purchase Return / 3. Both:
                        </label>
                        <select name="report_type" class="form-select form-select-sm d-inline-block" style="width: 60px;">
                            <option value="1" {{ ($reportType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($reportType ?? '1') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($reportType ?? '1') == '3' ? 'selected' : '' }}>3</option>
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

    @if(isset($companies) && $companies->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th>Company Name</th>
                            <th class="text-end">Total Items</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandItems = 0;
                            $grandQty = 0;
                            $grandFree = 0;
                            $grandAmount = 0;
                            $grandTax = 0;
                            $grandNet = 0;
                        @endphp
                        @foreach($companies as $index => $company)
                        @php
                            $grandItems += $company->total_items;
                            $grandQty += $company->total_qty;
                            $grandFree += $company->total_free_qty;
                            $grandAmount += $company->total_amount;
                            $grandTax += $company->total_tax;
                            $grandNet += $company->total_net;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $company->company_name ?? 'N/A' }}</td>
                            <td class="text-end">{{ $company->total_items }}</td>
                            <td class="text-end">{{ number_format($company->total_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($company->total_free_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($company->total_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($company->total_tax, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($company->total_net, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ $grandItems }}</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 2) }}</td>
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
function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const printUrl = "{{ route('admin.reports.purchase.misc.company.all-company.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
