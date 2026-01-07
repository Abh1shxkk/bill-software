{{-- Company Wise Discount Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">Company Wise Discount</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Customer / Supplier:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="list_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('list_type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Tagged Parties [ Y / N ]:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="tagged_parties" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('tagged_parties', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm ms-4">Remove Tags [ Y / N ]:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="remove_tags" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('remove_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Customer:</label>
                    </div>
                    <div class="col-4">
                        <select name="customer" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ request('customer') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->code ?? $cust->id }} - {{ $cust->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Company:</label>
                    </div>
                    <div class="col-4">
                        <select name="company" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}" {{ request('company') == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->code ?? $comp->id }} - {{ $comp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Item:</label>
                    </div>
                    <div class="col-4">
                        <select name="item" class="form-select form-select-sm">
                            <option value="00">00 - All</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item') == $item->id ? 'selected' : '' }}>
                                    {{ $item->code ?? $item->id }} - {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                        <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Company Wise Discount - {{ $reportData->count() }} {{ request('list_type', 'C') == 'C' ? 'Customers' : 'Suppliers' }}</h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print</button>
            </div>
        </div>
        <div class="card-body p-0">
            @foreach($reportData as $party)
            <div class="mb-3">
                <div class="bg-light p-2 border-bottom">
                    <strong>{{ $party['party_type'] }}: {{ $party['party_code'] }} - {{ $party['party_name'] }}</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th>Company Name</th>
                                <th style="width: 80px;" class="text-end">Dis.Brk</th>
                                <th style="width: 80px;" class="text-end">Dis.Exp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($party['companies'] as $company)
                            <tr>
                                <td>{{ $company['company_code'] }}</td>
                                <td>{{ $company['company_name'] }}</td>
                                <td class="text-end">{{ number_format($company['discount_brk'], 2) }}</td>
                                <td class="text-end">{{ number_format($company['discount_exp'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">No records found matching the criteria.</div>
    @endif
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.other.company-wise-discount") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
