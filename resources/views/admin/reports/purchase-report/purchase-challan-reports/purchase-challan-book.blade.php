@extends('layouts.admin')

@section('title', 'Purchase Challan Book')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff3cd 0%, #fff9e6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold">PURCHASE CHALLAN BOOK</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
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
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                        <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 65vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Challan No</th>
                            <th>Supplier</th>
                            <th>Items</th>
                            <th class="text-center">Status</th>
                            <th>Bill No</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans ?? [] as $index => $challan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $challan->challan_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $challan->challan_no }}</td>
                            <td>{{ $challan->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $challan->items_count ?? 0 }} items</td>
                            <td class="text-center">
                                <span class="badge {{ $challan->is_billed ? 'bg-success' : 'bg-warning' }}">
                                    {{ $challan->is_billed ? 'Billed' : 'Pending' }}
                                </span>
                            </td>
                            <td>{{ $challan->bill_no ?? '-' }}</td>
                            <td class="text-end">{{ number_format($challan->amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found
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
