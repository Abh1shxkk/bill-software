@extends('layouts.admin')

@section('title', 'Debit / Credit Note Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #fce4ec 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">DEBIT / CREDIT NOTE REPORT</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="note_type" id="type_all" value="all" checked>
                    <label class="btn btn-outline-primary" for="type_all">All</label>
                    
                    <input type="radio" class="btn-check" name="note_type" id="type_debit" value="debit">
                    <label class="btn btn-outline-danger" for="type_debit">Debit Note</label>
                    
                    <input type="radio" class="btn-check" name="note_type" id="type_credit" value="credit">
                    <label class="btn btn-outline-success" for="type_credit">Credit Note</label>
                </div>
            </div>
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
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
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
                            <th>Date</th>
                            <th>Note No</th>
                            <th>Type</th>
                            <th>Supplier</th>
                            <th>Against Bill</th>
                            <th>Reason</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notes ?? [] as $index => $note)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $note->note_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $note->note_no }}</td>
                            <td>
                                <span class="badge {{ $note->type == 'debit' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($note->type) }}
                                </span>
                            </td>
                            <td>{{ $note->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $note->against_bill ?? '-' }}</td>
                            <td>{{ $note->reason ?? '-' }}</td>
                            <td class="text-end">{{ number_format($note->amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($note->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($note->total_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
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
