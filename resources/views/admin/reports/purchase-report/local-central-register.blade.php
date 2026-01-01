@extends('layouts.admin')

@section('title', 'Local / Central Purchase Register')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d1ecf1 0%, #e8f4f8 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-info fst-italic fw-bold">LOCAL / CENTRAL PURCHASE REGISTER</h4>
        </div>
    </div>

    <!-- Type Selection -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Purchase Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="purchase_type" id="type_local" value="L" {{ ($purchaseType ?? 'L') == 'L' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_local">Local (Intra-State)</label>
                    
                    <input type="radio" class="btn-check" name="purchase_type" id="type_central" value="C" {{ ($purchaseType ?? '') == 'C' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success" for="type_central">Central (Inter-State)</label>
                    
                    <input type="radio" class="btn-check" name="purchase_type" id="type_both" value="B" {{ ($purchaseType ?? '') == 'B' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="type_both">Both</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <input type="hidden" name="purchase_type" id="hidden_purchase_type" value="{{ $purchaseType ?? 'L' }}">
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
                            <span class="input-group-text">State</span>
                            <select name="state_id" class="form-select">
                                <option value="">All States</option>
                                @foreach($states ?? [] as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
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
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Supplier</th>
                            <th>State</th>
                            <th>GSTN</th>
                            <th class="text-center">Type</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">CGST</th>
                            <th class="text-end">SGST</th>
                            <th class="text-end">IGST</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->supplier->state ?? '-' }}</td>
                            <td class="small">{{ $purchase->supplier->gstn ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $purchase->is_interstate ? 'bg-success' : 'bg-primary' }}">
                                    {{ $purchase->is_interstate ? 'Central' : 'Local' }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($purchase->taxable_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
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

@push('scripts')
<script>
document.querySelectorAll('input[name="purchase_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_purchase_type').value = this.value;
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
