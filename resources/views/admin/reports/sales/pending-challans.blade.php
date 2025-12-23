@extends('layouts.admin')

@section('title', 'Pending Challans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Pending Challans</h5>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Days Old (Min)</label>
                        <input type="number" name="days_old" class="form-control form-control-sm" value="{{ $daysOld }}" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-2 mb-3">
        <div class="col-md-4"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center">
            <small>Pending Challans</small><h5 class="mb-0">{{ number_format($totals['count']) }}</h5>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Amount</small><h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Items</small><h5 class="mb-0">{{ number_format($totals['items_count']) }}</h5>
        </div></div></div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Challan No</th>
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Salesman</th>
                            <th class="text-end">Items</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans as $index => $challan)
                        @php $daysOldVal = $challan->challan_date->diffInDays(now()); @endphp
                        <tr class="{{ $daysOldVal > 7 ? 'table-warning' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $challan->challan_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale-challan.show', $challan->id) }}">{{ $challan->challan_no }}</a></td>
                            <td>{{ $challan->customer->name ?? 'N/A' }}</td>
                            <td>{{ $challan->customer->mobile ?? '-' }}</td>
                            <td>{{ $challan->salesman->name ?? '-' }}</td>
                            <td class="text-end">{{ $challan->items->sum('qty') }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($challan->net_amount, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $daysOldVal > 7 ? 'bg-danger' : ($daysOldVal > 3 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $daysOldVal }} days
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.sale-challan.show', $challan->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No pending challans found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
