@extends('layouts.admin')

@section('title', 'Half Scheme Received Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #c2185b;">HALF SCHEME RECEIVED</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.half-schemed') }}" id="reportForm">
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
                    <!-- Company Wise -->
                    <div class="col-md-1">
                        <label class="small text-muted">Company Wise :</label>
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
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <!-- Division -->
                    <div class="col-md-1">
                        <label class="small text-muted">Division :</label>
                        <input type="text" name="division_code" class="form-control form-control-sm" 
                               value="{{ $divisionCode ?? '00' }}" placeholder="00">
                    </div>
                    <div class="col-md-3">
                        <select name="division_id" class="form-select form-select-sm" id="divisionSelect">
                            <option value="">All Divisions</option>
                            @foreach($divisions ?? [] as $division)
                                <option value="{{ $division->id }}" {{ ($divisionId ?? '') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
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
                            <th class="text-end">Full Scheme</th>
                            <th class="text-end">Half Scheme</th>
                            <th class="text-end">Qty Recd</th>
                            <th class="text-end">Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0;
                            $grandFree = 0;
                            $grandDiff = 0;
                        @endphp
                        @foreach($items as $index => $item)
                        @php
                            $grandQty += $item->qty ?? 0;
                            $grandFree += $item->free_qty ?? 0;
                            $grandDiff += $item->difference ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->bill_no ?? '-' }}</td>
                            <td>{{ $item->supplier_name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td class="text-end">{{ $item->full_scheme ?? '-' }}</td>
                            <td class="text-end fw-bold text-primary">{{ $item->half_scheme ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 0) }}</td>
                            <td class="text-end {{ $item->difference < 0 ? 'text-danger' : '' }}">{{ $item->difference ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="8" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 0) }}</td>
                            <td class="text-end">{{ number_format($grandDiff, 0) }}</td>
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
    const printUrl = "{{ route('admin.reports.purchase.misc.schemed.half-schemed.print') }}?" + params;
    window.open(printUrl, '_blank');
}
</script>
@endpush
