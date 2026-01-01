@extends('layouts.admin')

@section('title', 'Purchase / Return Book Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">PURCHASE / RETURN BOOK ITEM WISE</h4>
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
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Item</span>
                            <select name="item_id" class="form-select">
                                <option value="">All Items</option>
                                @foreach($items ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company</span>
                            <select name="company_id" class="form-select">
                                <option value="">All</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-center">Pur Qty</th>
                            <th class="text-end">Pur Amt</th>
                            <th class="text-center">Ret Qty</th>
                            <th class="text-end">Ret Amt</th>
                            <th class="text-center">Net Qty</th>
                            <th class="text-end">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemWise ?? [] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row->item_name }}</td>
                            <td>{{ $row->company_name ?? '-' }}</td>
                            <td class="text-center">{{ $row->pur_qty ?? 0 }}</td>
                            <td class="text-end">{{ number_format($row->pur_amount ?? 0, 2) }}</td>
                            <td class="text-center text-danger">{{ $row->ret_qty ?? 0 }}</td>
                            <td class="text-end text-danger">{{ number_format($row->ret_amount ?? 0, 2) }}</td>
                            <td class="text-center fw-bold">{{ ($row->pur_qty ?? 0) - ($row->ret_qty ?? 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format(($row->pur_amount ?? 0) - ($row->ret_amount ?? 0), 2) }}</td>
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
