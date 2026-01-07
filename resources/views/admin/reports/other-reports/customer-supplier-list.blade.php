{{-- Customer/Supplier List Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">Customer / Supplier List</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">C(ustomer) / S(upplier):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="list_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('list_type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-3">T(ax) / R(etail):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="tax_retail" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('tax_retail', 'T') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Status:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="status" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('status') }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-2">Flag:</label>
                    </div>
                    <div class="col-3">
                        <input type="text" name="flag" class="form-control form-control-sm" 
                               value="{{ request('flag') }}">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">W(hole Sale) / R(etail) / I(nstitution) / D(ept. Store) / O(thers):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="business_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('business_type') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">1.With Tin / 2. WithOut Sale / 3. All:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="tin_filter" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('tin_filter', '3') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-2">1. Active / 2. Inactive / 3. All:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="active_filter" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('active_filter', '3') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Sales Man:</label>
                    </div>
                    <div class="col-3">
                        <select name="salesman" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" {{ request('salesman') == $sm->id ? 'selected' : '' }}>
                                    {{ $sm->id }} - {{ $sm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Area:</label>
                    </div>
                    <div class="col-3">
                        <select name="area" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                                    {{ $area->id }} - {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Route:</label>
                    </div>
                    <div class="col-3">
                        <select name="route" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                    {{ $route->id }} - {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Day:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="day" class="form-control form-control-sm" 
                               value="{{ request('day') }}" maxlength="10" style="width: 80px;">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">State:</label>
                    </div>
                    <div class="col-3">
                        <select name="state" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ request('state') == $state->id ? 'selected' : '' }}>
                                    {{ $state->id }} - {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" onclick="exportExcel()" class="btn btn-success btn-sm">Excel</button>
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                        <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ request('list_type', 'C') == 'C' ? 'Customer' : 'Supplier' }} List - {{ $reportData->count() }} Records</h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0" style="font-size: 0.75rem;">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>S.No</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>GST No</th>
                            @if(request('list_type', 'C') == 'C')
                            <th>Area</th>
                            <th>Route</th>
                            <th>Salesman</th>
                            @endif
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->address }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ request('list_type', 'C') == 'C' ? $record->gst_number : $record->gst_no }}</td>
                            @if(request('list_type', 'C') == 'C')
                            <td>{{ $record->area_name }}</td>
                            <td>{{ $record->route_name }}</td>
                            <td>{{ $record->sales_man_name }}</td>
                            @endif
                            <td>{{ $record->status == 'A' ? 'Active' : 'Inactive' }}</td>
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
    window.open('{{ route("admin.reports.other.customer-supplier-list") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function exportExcel() {
    // Export functionality can be added here
    alert('Excel export functionality to be implemented');
}
</script>
@endsection
