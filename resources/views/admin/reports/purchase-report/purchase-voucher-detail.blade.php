@extends('layouts.admin')

@section('title', 'Purchase Voucher Detail')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff3cd 0%, #fff9e6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold">PURCHASE VOUCHER DETAIL</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
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
                            <span class="input-group-text">Voucher No</span>
                            <input type="text" name="voucher_no" class="form-control" value="{{ $voucherNo ?? '' }}" placeholder="Search...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
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
            <div class="table-responsive" style="max-height: 65vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Voucher No</th>
                            <th>Supplier</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers ?? [] as $index => $voucher)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $voucher->voucher_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $voucher->voucher_no }}</td>
                            <td>{{ $voucher->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $voucher->description ?? '-' }}</td>
                            <td class="text-end">{{ number_format($voucher->amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($voucher->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($voucher->total_amount ?? 0, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $voucher->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($voucher->status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No records found
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
