@extends('layouts.admin')

@section('title', 'Company Customer Item Invoice Wise')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #d8e8d0, #c8d8c0); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #006600;">Company Customer Item Invoice Wise</h4>
    </div>

    <div class="border p-3" style="background: #d8e8d0;">
        <form method="GET" id="filterForm">
            {{-- Row 1: From, To, Transaction Type, Series, With Sale Challan --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small fw-bold">From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 130px;">
                </div>
                <div class="col-auto">
                    <label class="small fw-bold">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 130px;">
                </div>
                <div class="col-auto">
                    <label class="small">1. Sale / 2. Sale Return / 3. Both</label>
                    <input type="text" name="transaction_type" class="form-control form-control-sm d-inline-block" value="3" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto">
                    <label class="small">Series :</label>
                    <input type="text" name="series" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-auto">
                    <label class="small">With Sale Challan</label>
                    <input type="checkbox" name="with_sale_challan" class="form-check-input ms-2">
                </div>
            </div>

            {{-- Row 2: Tagged Companies, Remove Tags, Sales Man --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small">Tagged Companies [ Y / N ] :</label>
                    <input type="text" name="tagged_companies" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto">
                    <label class="small">Sales Man</label>
                    <input type="text" name="salesman_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-md-3"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}">{{ $sm->name }}</option>@endforeach</select></div>
            </div>

            {{-- Row 3: Company, Area --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small">Company</label>
                    <input type="text" name="company_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-md-3"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div class="col-auto">
                    <label class="small">Area</label>
                    <input type="text" name="area_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-md-3"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
            </div>

            {{-- Row 4: Customer, Route --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small">Customer</label>
                    <input type="text" name="customer_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-md-3"><select name="customer_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($customers ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div class="col-auto">
                    <label class="small">Route</label>
                    <input type="text" name="route_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-md-3"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
            </div>

            {{-- Row 5: Division, W/R/I/D/O --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small">Division</label>
                    <input type="text" name="division_code" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-auto ms-3">
                    <label class="small">W(holeSale) / R(etail) / I(nstitution) / D(ept.Store) / O(thers)</label>
                    <input type="text" name="sale_type" class="form-control form-control-sm d-inline-block" value="" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Checkboxes --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <input type="checkbox" name="order_no" class="form-check-input" id="orderNo">
                    <label class="form-check-label small" for="orderNo">Order No.</label>
                </div>
                <div class="col-auto ms-3">
                    <input type="checkbox" name="pts_dis" class="form-check-input" id="ptsDis">
                    <label class="form-check-label small" for="ptsDis">PTS+Dis</label>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-secondary btn-sm border px-4 me-2" id="btnView">OK</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>

        {{-- Data Table --}}
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-sm" style="font-size: 11px;">
                <thead style="background: #c8d8c0; position: sticky; top: 0;">
                    <tr>
                        <th>Company</th>
                        <th>Code</th>
                        <th>Customer</th>
                        <th>Item Code</th>
                        <th>Product</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data ?? [] as $row)
                    <tr>
                        <td>{{ $row['company_name'] ?? '-' }}</td>
                        <td>{{ $row['code'] ?? '-' }}</td>
                        <td>{{ $row['customer_name'] ?? '-' }}</td>
                        <td>{{ $row['item_code'] ?? '-' }}</td>
                        <td>{{ $row['product'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="row mt-2 pt-2 border-top">
            <div class="col">
                <button type="button" class="btn btn-success btn-sm border px-2"><i class="bi bi-file-excel"></i> SUN Pharma Format</button>
            </div>
            <div class="col text-end">
                <span class="small fw-bold">Total : <span class="text-primary">{{ number_format($totals['amount'] ?? 0, 2) }}</span></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    document.getElementById('btnView').addEventListener('click', function() {
        let vi = form.querySelector('input[name="view_type"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'view_type'; form.appendChild(vi); }
        vi.value = 'print'; form.target = '_blank'; form.submit(); form.target = '_self'; vi.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
});
</script>
@endsection
