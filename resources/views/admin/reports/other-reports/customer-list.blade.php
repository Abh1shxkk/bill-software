{{-- Customer List Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">CUSTOMER LIST</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">C(ustomer):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="list_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('list_type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">T(ax) / R(etail):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="tax_retail" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('tax_retail') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Status:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="status" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('status') }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Flag:</label>
                    </div>
                    <div class="col-2">
                        <input type="text" name="flag" class="form-control form-control-sm" 
                               value="{{ request('flag') }}">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">W(hole Sale) / R(etail) / I(nstitution) / D(ept. Store) / O(ther):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="business_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('business_type') }}" maxlength="1" style="width: 40px;">
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
                    <div class="col-6"></div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">1. With GSTIN / 2. WithOut GSTIN / 3. All:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="gstin_filter" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('gstin_filter', '3') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Customer:</label>
                    </div>
                    <div class="col-3">
                        <select name="customer" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ request('customer') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->id }} - {{ $cust->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Sales Man:</label>
                    </div>
                    <div class="col-3">
                        <select name="salesman" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
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
                            <option value="00">00 - All</option>
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
                            <option value="00">00 - All</option>
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
                        <label class="col-form-label col-form-label-sm">State:</label>
                    </div>
                    <div class="col-3">
                        <select name="state" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ request('state') == $state->id ? 'selected' : '' }}>
                                    {{ $state->id }} - {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-4">Day:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="day" class="form-control form-control-sm" 
                               value="{{ request('day') }}" maxlength="10" style="width: 60px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Party Name / Sales Man / Area / Route:</label>
                    </div>
                    <div class="col-3">
                        <select name="sort_by" class="form-select form-select-sm">
                            <option value="PARTYNAME" {{ request('sort_by', 'PARTYNAME') == 'PARTYNAME' ? 'selected' : '' }}>PARTYNAME</option>
                            <option value="SALESMAN" {{ request('sort_by') == 'SALESMAN' ? 'selected' : '' }}>SALESMAN</option>
                            <option value="AREA" {{ request('sort_by') == 'AREA' ? 'selected' : '' }}>AREA</option>
                            <option value="ROUTE" {{ request('sort_by') == 'ROUTE' ? 'selected' : '' }}>ROUTE</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="col-form-label col-form-label-sm">Select Columns to Display:</label>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="address" {{ in_array('address', request('columns', [])) ? 'checked' : '' }}> Address</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="telephone" {{ in_array('telephone', request('columns', [])) ? 'checked' : '' }}> Telephone</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="mobile" {{ in_array('mobile', request('columns', [])) ? 'checked' : '' }}> Mobile</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="email" {{ in_array('email', request('columns', [])) ? 'checked' : '' }}> Email</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="expiry" {{ in_array('expiry', request('columns', [])) ? 'checked' : '' }}> Expiry</label></div>
                        </div>
                        <div class="row">
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="dl_no" {{ in_array('dl_no', request('columns', [])) ? 'checked' : '' }}> DL.NO</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="dis" {{ in_array('dis', request('columns', [])) ? 'checked' : '' }}> Dis</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="bank" {{ in_array('bank', request('columns', [])) ? 'checked' : '' }}> Bank</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="days" {{ in_array('days', request('columns', [])) ? 'checked' : '' }}> Days</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="max_os_amt" {{ in_array('max_os_amt', request('columns', [])) ? 'checked' : '' }}> Max O/S Amt</label></div>
                        </div>
                        <div class="row">
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="food_license" {{ in_array('food_license', request('columns', [])) ? 'checked' : '' }}> Food License No.</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="exp_dis" {{ in_array('exp_dis', request('columns', [])) ? 'checked' : '' }}> Exp Dis</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="dis_on_excise" {{ in_array('dis_on_excise', request('columns', [])) ? 'checked' : '' }}> DIS ON EXCISE</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="state_code" {{ in_array('state_code', request('columns', [])) ? 'checked' : '' }}> State Code</label></div>
                        </div>
                        <div class="row">
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="credit_limit" {{ in_array('credit_limit', request('columns', [])) ? 'checked' : '' }}> Locks Credit Limit</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="due_date_limit" {{ in_array('due_date_limit', request('columns', [])) ? 'checked' : '' }}> Due Date Limit</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="gst_no" {{ in_array('gst_no', request('columns', [])) ? 'checked' : '' }}> GST NO</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="gst_regd_dt" {{ in_array('gst_regd_dt', request('columns', [])) ? 'checked' : '' }}> GST Regd. Dt.</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="tin" {{ in_array('tin', request('columns', [])) ? 'checked' : '' }}> Tin</label></div>
                        </div>
                        <div class="row">
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="breakage_dis" {{ in_array('breakage_dis', request('columns', [])) ? 'checked' : '' }}> Breakage Dis</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="cn_settlement" {{ in_array('cn_settlement', request('columns', [])) ? 'checked' : '' }}> CNSettlement</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="notebook" {{ in_array('notebook', request('columns', [])) ? 'checked' : '' }}> Notebook</label></div>
                            <div class="col-2"><label><input type="checkbox" name="columns[]" value="remarks" {{ in_array('remarks', request('columns', [])) ? 'checked' : '' }}> Remarks</label></div>
                        </div>
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
                <h6 class="mb-0">Customer List - {{ $reportData->count() }} Records</h6>
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
                            @if(in_array('address', request('columns', []))) <th>Address</th> @endif
                            @if(in_array('mobile', request('columns', []))) <th>Mobile</th> @endif
                            @if(in_array('telephone', request('columns', []))) <th>Telephone</th> @endif
                            @if(in_array('email', request('columns', []))) <th>Email</th> @endif
                            @if(in_array('gst_no', request('columns', []))) <th>GST No</th> @endif
                            @if(in_array('dl_no', request('columns', []))) <th>DL No</th> @endif
                            @if(in_array('tin', request('columns', []))) <th>TIN</th> @endif
                            @if(in_array('credit_limit', request('columns', []))) <th>Credit Limit</th> @endif
                            @if(in_array('days', request('columns', []))) <th>Days</th> @endif
                            <th>Area</th>
                            <th>Route</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->code }}</td>
                            <td>{{ $record->name }}</td>
                            @if(in_array('address', request('columns', []))) <td>{{ $record->address }}</td> @endif
                            @if(in_array('mobile', request('columns', []))) <td>{{ $record->mobile }}</td> @endif
                            @if(in_array('telephone', request('columns', []))) <td>{{ $record->telephone_office }}</td> @endif
                            @if(in_array('email', request('columns', []))) <td>{{ $record->email }}</td> @endif
                            @if(in_array('gst_no', request('columns', []))) <td>{{ $record->gst_number }}</td> @endif
                            @if(in_array('dl_no', request('columns', []))) <td>{{ $record->dl_number }}</td> @endif
                            @if(in_array('tin', request('columns', []))) <td>{{ $record->tin_number }}</td> @endif
                            @if(in_array('credit_limit', request('columns', []))) <td class="text-end">{{ number_format($record->credit_limit ?? 0, 2) }}</td> @endif
                            @if(in_array('days', request('columns', []))) <td>{{ $record->credit_days }}</td> @endif
                            <td>{{ $record->area_name }}</td>
                            <td>{{ $record->route_name }}</td>
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
    window.open('{{ route("admin.reports.other.customer-list") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function exportExcel() {
    alert('Excel export functionality to be implemented');
}
</script>
@endsection
