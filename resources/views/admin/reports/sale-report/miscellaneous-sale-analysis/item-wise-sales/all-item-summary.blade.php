@extends('layouts.admin')

@section('title', 'Item Wise Sale Summary')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Item WIse Sale Summary</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.item-wise-sales.all-item-summary') }}">
            {{-- Date Range --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold">From :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-4">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            {{-- Transaction Type --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Both / 4. Sale Challan :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="transaction_type" class="form-control form-control-sm" value="1" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company & Division Section --}}
            <div class="border p-2 mb-3" style="background: #e8e0e8;">
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Company :</label></div>
                    <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2">
                    <div class="col-auto" style="width: 100px;"><label class="small">Division :</label></div>
                    <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                </div>
            </div>

            {{-- Filters Section --}}
            <div class="border p-2 mb-3" style="background: #e8e0e8;">
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Customer :</label></div>
                    <div class="col-auto"><input type="text" name="customer_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="customer_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($customers ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Sales Man :</label></div>
                    <div class="col-auto"><input type="text" name="salesman_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}">{{ $sm->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Area :</label></div>
                    <div class="col-auto"><input type="text" name="area_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Route :</label></div>
                    <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-auto" style="width: 100px;"><label class="small">State :</label></div>
                    <div class="col-auto"><input type="text" name="state_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="state_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($states ?? [] as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                </div>

                {{-- Order By & No. of Top Items --}}
                <div class="row g-2 mb-1">
                    <div class="col-auto">
                        <label class="small">Order By Q(ty) / V(alue) / N(ame) :</label>
                        <input type="text" name="order_by" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="small">A(sc) / D(esc) :</label>
                        <input type="text" name="order_direction" class="form-control form-control-sm d-inline-block" value="A" style="width: 35px;" maxlength="1">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-auto">
                        <label class="small">No. of Top Items :</label>
                        <input type="text" name="top_items" class="form-control form-control-sm d-inline-block" value="0" style="width: 50px;">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col text-end">
                    <button type="button" class="btn btn-secondary btn-sm border px-3 me-2" id="btnSaleWithTax">Sale With Tax</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-3 me-2">Excel</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    document.getElementById('btnSaleWithTax').addEventListener('click', function() {
        let vi = form.querySelector('input[name="view_type"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'view_type'; form.appendChild(vi); }
        vi.value = 'print'; form.target = '_blank'; form.submit(); form.target = '_self'; vi.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
});
</script>
@endsection
