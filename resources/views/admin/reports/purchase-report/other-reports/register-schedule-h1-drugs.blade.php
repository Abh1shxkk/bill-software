@extends('layouts.admin')

@section('title', 'Register of Schedule H1 Drugs')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">REGISTER OF SCHEDULE H1 DRUGS</h4>
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
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Drug</span>
                            <input type="text" name="drug_name" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="alert alert-danger mb-2 py-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Schedule H1 Drugs:</strong> These are drugs that require special record-keeping as per Drug and Cosmetics Act.
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-danger sticky-top">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Name of Drug</th>
                            <th>Batch No</th>
                            <th>Expiry</th>
                            <th class="text-end">Qty Received</th>
                            <th>Manufacturer</th>
                            <th>Supplier Name</th>
                            <th>D.L. No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drugs ?? [] as $index => $drug)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $drug->bill_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $drug->bill_no ?? '-' }}</td>
                            <td class="fw-bold text-danger">{{ $drug->drug_name ?? '-' }}</td>
                            <td>{{ $drug->batch_no ?? '-' }}</td>
                            <td>{{ $drug->expiry_date ?? '-' }}</td>
                            <td class="text-end">{{ number_format($drug->quantity ?? 0) }}</td>
                            <td class="small">{{ $drug->manufacturer ?? '-' }}</td>
                            <td>{{ $drug->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $drug->supplier->dl_no ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Schedule H1 drug records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="card mt-2">
        <div class="card-body py-2 small text-muted">
            <strong>Note:</strong> This register is maintained as per Rule 65(10) of Drugs and Cosmetics Rules, 1945 for Schedule H1 drugs including Antibiotics, Anti-TB drugs, Habit forming drugs, etc.
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
