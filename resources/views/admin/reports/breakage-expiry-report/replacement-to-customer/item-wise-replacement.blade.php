@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">ITEM WISE REPLACEMENT</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-auto">
                                <label class="form-label mb-0">From:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">To:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Tagged Companies [Y/N]:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="tagged_companies" class="form-control form-control-sm text-uppercase" style="width: 40px;" maxlength="1" value="{{ request('tagged_companies', 'N') }}">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Company:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="company_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('company_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="company_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Customer:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="customer_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('customer_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="customer_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Division:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="division_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('division_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="division_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($divisions ?? [] as $division)
                                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Item:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="item_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('item_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="item_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($items ?? [] as $item)
                                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-3">
                                <label class="form-label mb-0">Category:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="category_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('category_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="category_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm">View</button>
                                <a href="{{ route('admin.reports.breakage-expiry.replacement-to-customer.item-wise') }}" class="btn btn-secondary btn-sm">Close</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                            <thead>
                                <tr style="background-color: #000080; color: white;">
                                    <th>S.No.</th>
                                    <th>Item Name</th>
                                    <th>Batch</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $row->item_name ?? '' }}</td>
                                    <td>{{ $row->batch ?? '' }}</td>
                                    <td>{{ $row->qty ?? 0 }}</td>
                                    <td>{{ number_format($row->amount ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td><strong>{{ $reportData->sum('qty') }}</strong></td>
                                    <td><strong>{{ number_format($reportData->sum('amount'), 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @elseif(request()->has('view'))
            <div class="alert alert-info mt-3">No records found.</div>
            @endif
        </div>
    </div>
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.breakage-expiry.replacement-to-customer.item-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
