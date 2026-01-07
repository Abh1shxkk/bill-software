{{-- List of Masters Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">List of Masters</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Select Master:</label>
                    </div>
                    <div class="col-3">
                        <select name="master_type" id="masterType" class="form-select form-select-sm">
                            @foreach($masterTypes as $key => $label)
                                <option value="{{ $key }}" {{ $selectedMaster == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Print Address [ Y / N ]:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="print_address" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('print_address', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-3">D(irect) / I(ndirect):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="direct_indirect" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('direct_indirect') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Status:</label>
                    </div>
                    <div class="col-2">
                        <input type="text" name="status" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('status') }}" maxlength="1" placeholder="A/I">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Commodity Code:</label>
                    </div>
                    <div class="col-3">
                        <select name="company_code" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_code') == $company->id ? 'selected' : '' }}>
                                    {{ $company->id }} - {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12">
                        <button type="button" onclick="exportExcel()" class="btn btn-success btn-sm">Excel</button>
                        <span class="float-end">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                            <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $masterTypes[$selectedMaster] ?? 'Master' }} List - {{ $reportData->count() }} Records</h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0" style="font-size: 0.75rem;">
                    <thead class="table-light sticky-top">
                        @if($selectedMaster == 'COMPANY')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Short Name</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'CUSTOMER')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Area</th><th>Route</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'SUPPLIER')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>D/I</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'ITEM')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Company</th><th>Pack</th><th>MRP</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'SALESMAN')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Status</th></tr>
                        @elseif(in_array($selectedMaster, ['AREA', 'ROUTE', 'STATE']))
                        <tr><th>S.No</th><th>ID</th><th>Name</th><th>Alter Code</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'HSN')
                        <tr><th>S.No</th><th>HSN Code</th><th>Description</th><th>GST %</th></tr>
                        @elseif($selectedMaster == 'GENERAL_LEDGER')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Group</th><th>Status</th></tr>
                        @elseif($selectedMaster == 'TRANSPORT')
                        <tr><th>S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Status</th></tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($selectedMaster == 'COMPANY')
                            <td>{{ $record->code ?? $record->id }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->short_name ?? '' }}</td>
                            <td>{{ $record->status }}</td>
                            @elseif($selectedMaster == 'CUSTOMER')
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->area_name }}</td>
                            <td>{{ $record->route_name }}</td>
                            <td>{{ $record->status }}</td>
                            @elseif($selectedMaster == 'SUPPLIER')
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->direct_indirect }}</td>
                            <td>{{ $record->status }}</td>
                            @elseif($selectedMaster == 'ITEM')
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->company_name ?? '' }}</td>
                            <td>{{ $record->pack ?? '' }}</td>
                            <td>{{ number_format($record->mrp ?? 0, 2) }}</td>
                            <td>{{ $record->status }}</td>
                            @elseif($selectedMaster == 'SALESMAN')
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->status }}</td>
                            @elseif(in_array($selectedMaster, ['AREA', 'ROUTE', 'STATE']))
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->alter_code ?? '' }}</td>
                            <td>{{ $record->status ?? 'A' }}</td>
                            @elseif($selectedMaster == 'HSN')
                            <td>{{ $record->hsn_code }}</td>
                            <td>{{ $record->description ?? '' }}</td>
                            <td>{{ $record->gst_rate ?? '' }}%</td>
                            @elseif($selectedMaster == 'GENERAL_LEDGER')
                            <td>{{ $record->account_code }}</td>
                            <td>{{ $record->account_name }}</td>
                            <td>{{ $record->under ?? '' }}</td>
                            <td>{{ $record->flag ?? 'A' }}</td>
                            @elseif($selectedMaster == 'TRANSPORT')
                            <td>{{ $record->code ?? $record->id }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->mobile ?? '' }}</td>
                            <td>{{ $record->status }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">No records found matching the criteria.</div>
    @endif
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.other.list-of-masters") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function exportExcel() {
    alert('Excel export functionality to be implemented');
}
</script>
@endsection
