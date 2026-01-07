@extends('layouts.admin')

@section('title', 'Sale Challan Book')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALE CHALLAN BOOK (CHALLAN LIST)</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sale-challan-book') }}">
                <input type="hidden" name="tagged_ids" id="taggedIds" value="{{ $taggedIds }}">
                
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Salesman</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                    <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route</span>
                            <select name="route_id" class="form-select">
                                <option value="">All</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: More Filters -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area</span>
                            <select name="area_id" class="form-select">
                                <option value="">All</option>
                                @foreach($areas ?? [] as $area)
                                    <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag</span>
                            <select name="flag" class="form-select text-uppercase">
                                <option value="">All</option>
                                <option value="C" {{ ($flag ?? '') == 'C' ? 'selected' : '' }}>Cash</option>
                                <option value="R" {{ ($flag ?? '') == 'R' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">(D)/(S)</span>
                            <select name="ds_format" class="form-select text-uppercase" style="width: 50px;">
                                <option value="D" {{ ($dsFormat ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="S" {{ ($dsFormat ?? '') == 'S' ? 'selected' : '' }}>S</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Day</span>
                            <select name="day" class="form-select">
                                <option value="">All</option>
                                <option value="Monday" {{ ($day ?? '') == 'Monday' ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ ($day ?? '') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ ($day ?? '') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ ($day ?? '') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ ($day ?? '') == 'Friday' ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ ($day ?? '') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ ($day ?? '') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Order By</span>
                            <select name="order_by" class="form-select">
                                <option value="date" {{ ($orderBy ?? 'date') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="name" {{ ($orderBy ?? '') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="challan_no" {{ ($orderBy ?? '') == 'challan_no' ? 'selected' : '' }}>Challan No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="hold_only" value="1" id="holdOnly" {{ ($holdOnly ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="holdOnly">Hold Challans Only</label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($challans) && $challans->count() > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Challans</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Tagged</small>
                    <h6 class="mb-0 tagged-count">{{ number_format($totals['tagged_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Tagged Amount</small>
                    <h6 class="mb-0 tagged-amount">₹{{ number_format($totals['tagged_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 30px;">TAG</th>
                            <th style="width: 80px;">DATE</th>
                            <th style="width: 80px;">TRN.No</th>
                            <th style="width: 60px;">CODE</th>
                            <th>PARTY NAME</th>
                            <th class="text-end" style="width: 100px;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans ?? [] as $index => $challan)
                        @php $isTagged = in_array($challan->id, $taggedArray ?? []); @endphp
                        <tr class="{{ $isTagged ? 'table-warning' : '' }}" data-id="{{ $challan->id }}">
                            <td class="text-center">
                                <button type="button" class="btn btn-sm {{ $isTagged ? 'btn-danger' : 'btn-success' }} tag-btn py-0 px-1" 
                                        onclick="toggleTag({{ $challan->id }})" title="{{ $isTagged ? 'Untag (-)' : 'Tag (+)' }}">
                                    {{ $isTagged ? '-' : '+' }}
                                </button>
                            </td>
                            <td>{{ $challan->challan_date ? $challan->challan_date->format('d-m-Y') : '' }}</td>
                            <td>
                                <a href="{{ route('admin.sale-challan.show', $challan->id) }}" class="text-primary">
                                    {{ $challan->challan_no }}
                                </a>
                            </td>
                            <td>{{ $challan->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($challan->customer->name ?? 'N/A', 35) }}</td>
                            <td class="text-end fw-bold">{{ number_format($challan->net_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No challans found for selected criteria
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total ({{ number_format($totals['count'] ?? 0) }} Challans):</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let taggedIds = '{{ $taggedIds }}'.split(',').filter(id => id);

function toggleTag(id) {
    const idx = taggedIds.indexOf(String(id));
    if (idx > -1) {
        taggedIds.splice(idx, 1);
    } else {
        taggedIds.push(String(id));
    }
    document.getElementById('taggedIds').value = taggedIds.join(',');
    
    // Update UI
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const btn = row.querySelector('.tag-btn');
    if (taggedIds.includes(String(id))) {
        row.classList.add('table-warning');
        btn.classList.remove('btn-success');
        btn.classList.add('btn-danger');
        btn.textContent = '-';
        btn.title = 'Untag (-)';
    } else {
        row.classList.remove('table-warning');
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-success');
        btn.textContent = '+';
        btn.title = 'Tag (+)';
    }
    
    // Update summary
    updateTaggedSummary();
}

function updateTaggedSummary() {
    // Recalculate tagged amount
    let taggedAmount = 0;
    taggedIds.forEach(id => {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            const amountCell = row.querySelector('td:last-child');
            taggedAmount += parseFloat(amountCell.textContent.replace(/,/g, '')) || 0;
        }
    });
    
    // Update cards
    document.querySelector('.tagged-count').textContent = taggedIds.length;
    document.querySelector('.tagged-amount').textContent = '₹' + taggedAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sale-challan-book") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sale-challan-book") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
    if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
.tag-btn { font-size: 0.8rem; font-weight: bold; line-height: 1; border-radius: 0; }
</style>
@endpush
