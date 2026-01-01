@extends('layouts.admin')

@section('title', 'Short Expiry Received')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #fce4ec 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">SHORT EXPIRY RECEIVED</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Expiry Within</span>
                            <select name="expiry_months" class="form-select">
                                <option value="3" {{ ($expiryMonths ?? 3) == 3 ? 'selected' : '' }}>3 Months</option>
                                <option value="6" {{ ($expiryMonths ?? '') == 6 ? 'selected' : '' }}>6 Months</option>
                                <option value="9" {{ ($expiryMonths ?? '') == 9 ? 'selected' : '' }}>9 Months</option>
                                <option value="12" {{ ($expiryMonths ?? '') == 12 ? 'selected' : '' }}>12 Months</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Recv Date</th>
                            <th>Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th class="text-center">Days Left</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortExpiry ?? [] as $index => $item)
                        <tr class="{{ $item->days_left <= 30 ? 'table-danger' : ($item->days_left <= 90 ? 'table-warning' : '') }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->received_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $item->bill_no }}</td>
                            <td>{{ $item->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->batch_no ?? '-' }}</td>
                            <td>{{ $item->expiry_date->format('M-Y') ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $item->days_left <= 30 ? 'bg-danger' : ($item->days_left <= 90 ? 'bg-warning' : 'bg-info') }}">
                                    {{ $item->days_left }} days
                                </span>
                            </td>
                            <td class="text-center">{{ $item->qty ?? 0 }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No short expiry items received
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
