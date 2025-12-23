@extends('layouts.admin')

@section('title', 'Sales Bills Printing')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-printer me-2"></i>Sales Bills Printing</h5>
        <button type="button" class="btn btn-primary btn-sm" onclick="printSelected()">
            <i class="bi bi-printer me-1"></i>Print Selected
        </button>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Invoice From</label>
                        <input type="text" name="invoice_from" class="form-control form-control-sm" value="{{ $invoiceFrom }}" placeholder="From">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Invoice To</label>
                        <input type="text" name="invoice_to" class="form-control form-control-sm" value="{{ $invoiceTo }}" placeholder="To">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bills List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
                <label class="form-check-label" for="selectAll">Select All ({{ $sales->count() }} bills)</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th width="40"><input type="checkbox" class="form-check-input" id="headerCheck" onchange="toggleAll(this)"></th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th class="text-end">Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td><input type="checkbox" class="form-check-input bill-check" value="{{ $sale->id }}"></td>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->items->count() }} items</td>
                            <td class="text-end fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No bills found</td></tr>
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
function toggleAll(checkbox) {
    document.querySelectorAll('.bill-check').forEach(cb => cb.checked = checkbox.checked);
}

function printSelected() {
    const selected = Array.from(document.querySelectorAll('.bill-check:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select at least one bill to print');
        return;
    }
    // Open print preview for selected bills
    selected.forEach(id => {
        window.open('{{ url("admin/sale") }}/' + id + '?print=1', '_blank');
    });
}
</script>
@endpush
