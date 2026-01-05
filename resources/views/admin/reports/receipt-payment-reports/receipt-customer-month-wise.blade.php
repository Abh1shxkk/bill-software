{{-- Receipt from Customer - Month Wise --}}
@extends('layouts.admin')
@section('title', 'Receipt from Customer - Month Wise')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #d1ecf1;"><div class="card-body py-2 text-center"><h4 class="mb-0 text-info fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Receipt from Customer - Month Wise</h4></div></div>
    <div class="card shadow-sm" style="background-color: #f0f0f0;"><div class="card-body p-3">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.receipt-customer-month-wise') }}">
            <div class="row g-3 align-items-center">
                <div class="col-auto"><label class="fw-bold mb-0">Year :</label></div>
                <div class="col-auto"><select name="year" class="form-select form-select-sm" style="width: 100px;">@for($y = date('Y'); $y >= date('Y') - 5; $y--)<option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor</select></div>
                <div class="col-auto ms-3"><label class="fw-bold mb-0">Customer :</label></div>
                <div class="col-auto"><select name="customer_id" class="form-select form-select-sm" style="width: 250px;"><option value="">-- All Customers --</option>@foreach($customers ?? [] as $customer)<option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>@endforeach</select></div>
            </div>
            <div class="row mt-3"><div class="col-12 text-end">
                <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">View</button>
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.location.href='{{ route('admin.dashboard') }}'">Close</button>
            </div></div>
        </form>
    </div></div>
    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1" style="background-color: #d1ecf1;"><span class="fw-bold">Receipt from Customer - Month Wise ({{ request('year', date('Y')) }})</span></div>
        <div class="card-body p-0"><table class="table table-bordered table-sm mb-0">
            <thead style="background-color: #e0e0e0;"><tr><th>S.No</th><th>Customer Name</th><th class="text-end">Apr</th><th class="text-end">May</th><th class="text-end">Jun</th><th class="text-end">Jul</th><th class="text-end">Aug</th><th class="text-end">Sep</th><th class="text-end">Oct</th><th class="text-end">Nov</th><th class="text-end">Dec</th><th class="text-end">Jan</th><th class="text-end">Feb</th><th class="text-end">Mar</th><th class="text-end">Total</th></tr></thead>
            <tbody>@foreach($reportData as $index => $row)<tr><td>{{ $index + 1 }}</td><td>{{ $row['customer_name'] }}</td><td class="text-end">{{ number_format($row['apr'], 0) }}</td><td class="text-end">{{ number_format($row['may'], 0) }}</td><td class="text-end">{{ number_format($row['jun'], 0) }}</td><td class="text-end">{{ number_format($row['jul'], 0) }}</td><td class="text-end">{{ number_format($row['aug'], 0) }}</td><td class="text-end">{{ number_format($row['sep'], 0) }}</td><td class="text-end">{{ number_format($row['oct'], 0) }}</td><td class="text-end">{{ number_format($row['nov'], 0) }}</td><td class="text-end">{{ number_format($row['dec'], 0) }}</td><td class="text-end">{{ number_format($row['jan'], 0) }}</td><td class="text-end">{{ number_format($row['feb'], 0) }}</td><td class="text-end">{{ number_format($row['mar'], 0) }}</td><td class="text-end fw-bold">{{ number_format($row['total'], 0) }}</td></tr>@endforeach</tbody>
        </table></div>
    </div>
    @elseif(request()->has('view'))<div class="alert alert-info mt-2">No data found.</div>@endif
</div>
@endsection
@push('styles')<style>.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }.card { border-radius: 0; }.btn { border-radius: 0; }.table th, .table td { padding: 0.2rem 0.4rem; font-size: 0.8rem; }</style>@endpush
