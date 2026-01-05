{{-- Currency Detail --}}
@extends('layouts.admin')
@section('title', 'Currency Detail')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #fff3cd;"><div class="card-body py-2 text-center"><h4 class="mb-0 text-dark fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Currency Detail</h4></div></div>
    <div class="card shadow-sm" style="background-color: #f0f0f0;"><div class="card-body p-3">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.currency-detail') }}">
            <div class="row g-3 align-items-center">
                <div class="col-auto"><label class="fw-bold mb-0">From :</label></div>
                <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                <div class="col-auto ms-3"><label class="fw-bold mb-0">To :</label></div>
                <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;"></div>
            </div>
            <div class="row mt-3"><div class="col-12 text-end">
                <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">View</button>
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.location.href='{{ route('admin.dashboard') }}'">Close</button>
            </div></div>
        </form>
    </div></div>
    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1" style="background-color: #fff3cd;"><span class="fw-bold">Currency Detail - {{ count($reportData) }} records</span></div>
        <div class="card-body p-0"><table class="table table-bordered table-sm mb-0">
            <thead style="background-color: #e0e0e0;"><tr><th>S.No</th><th>Date</th><th>Transaction</th><th class="text-end">₹2000</th><th class="text-end">₹500</th><th class="text-end">₹200</th><th class="text-end">₹100</th><th class="text-end">₹50</th><th class="text-end">₹20</th><th class="text-end">₹10</th><th class="text-end">Coins</th><th class="text-end">Total</th></tr></thead>
            <tbody>@foreach($reportData as $index => $row)<tr><td>{{ $index + 1 }}</td><td>{{ $row['date'] }}</td><td>{{ $row['transaction'] }}</td><td class="text-end">{{ $row['n2000'] }}</td><td class="text-end">{{ $row['n500'] }}</td><td class="text-end">{{ $row['n200'] }}</td><td class="text-end">{{ $row['n100'] }}</td><td class="text-end">{{ $row['n50'] }}</td><td class="text-end">{{ $row['n20'] }}</td><td class="text-end">{{ $row['n10'] }}</td><td class="text-end">{{ $row['coins'] }}</td><td class="text-end fw-bold">{{ number_format($row['total'], 2) }}</td></tr>@endforeach</tbody>
        </table></div>
    </div>
    @elseif(request()->has('view'))<div class="alert alert-info mt-2">No currency details found.</div>@endif
</div>
@endsection
@push('styles')<style>.form-control-sm { border: 1px solid #aaa; border-radius: 0; }.card { border-radius: 0; }.btn { border-radius: 0; }.table th, .table td { padding: 0.3rem 0.5rem; font-size: 0.85rem; }</style>@endpush
