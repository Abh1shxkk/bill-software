@extends('layouts.admin')

@section('title', 'Ledger Printing')

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.ledger-printing') }}">
                <div class="row g-2 align-items-center">
                    <!-- Date From -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 140px;">
                    </div>

                    <!-- Date To -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 140px;">
                    </div>

                    <!-- Customer/Supplier -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">(C)ustomer / (S)upplier</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="ledger_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $ledgerType }}" style="width: 40px;" maxlength="1">
                    </div>

                    <!-- View Mode -->
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_mode" id="viewAll" value="all" {{ $viewMode == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="viewAll">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_mode" id="viewSelective" value="selective" {{ $viewMode == 'selective' ? 'checked' : '' }}>
                            <label class="form-check-label" for="viewSelective">Selective</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- Sales Man -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">SALES MAN</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="salesman" class="form-control form-control-sm" 
                               value="{{ $salesmanCode }}" style="width: 50px;">
                    </div>
                    <div class="col-2">
                        <select class="form-select form-select-sm" onchange="document.querySelector('input[name=salesman]').value = this.value">
                            <option value="00">All</option>
                            @foreach($salesmen as $sm)
                            <option value="{{ $sm->id }}" {{ $salesmanCode == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">AREA :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="area" class="form-control form-control-sm" 
                               value="{{ $areaCode }}" style="width: 50px;">
                    </div>
                    <div class="col-2">
                        <select class="form-select form-select-sm" onchange="document.querySelector('input[name=area]').value = this.value">
                            <option value="00">All</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ $areaCode == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- Route -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">ROUTE :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="route" class="form-control form-control-sm" 
                               value="{{ $routeCode }}" style="width: 50px;">
                    </div>
                    <div class="col-2">
                        <select class="form-select form-select-sm" onchange="document.querySelector('input[name=route]').value = this.value">
                            <option value="00">All</option>
                            @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ $routeCode == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name Starting With -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Name starting with</label>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="name_from" class="form-control form-control-sm text-uppercase" 
                               value="{{ $nameFrom }}" style="width: 40px;" maxlength="1">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="name_to" class="form-control form-control-sm text-uppercase" 
                               value="{{ $nameTo }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- With Account Name -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">With Account Name on every voucher [ Y / N ]</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="show_account_name" class="form-control form-control-sm text-uppercase" 
                               value="{{ $showAccountName ? 'Y' : 'N' }}" style="width: 40px;" maxlength="1">
                    </div>

                    <!-- TIN Option -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">1. With Tin / 2. WithOut Tin / 3.All</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tin_option" class="form-control form-control-sm" 
                               value="{{ $tinOption }}" style="width: 40px;" maxlength="1">
                    </div>

                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Ok
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="background-color: #e8e8e8;">
                    <thead>
                        <tr style="background-color: #d0d0d0;">
                            <th style="width: 50px;">S.No</th>
                            <th style="width: 100px;">Code</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}.</td>
                            <td>{{ $item['code'] }}</td>
                            <td>{{ $item['name'] }}</td>
                        </tr>
                        @empty
                        @for($i = 1; $i <= 16; $i++)
                        <tr>
                            <td>{{ $i }}.</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endforelse
                        @if($selectedItems->count() > 0 && $selectedItems->count() < 16)
                        @for($i = $selectedItems->count() + 1; $i <= 16; $i++)
                        <tr>
                            <td>{{ $i }}.</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($selectedItems->count() > 0)
    <div class="mt-3 text-center">
        <button type="button" class="btn btn-secondary" onclick="printReport()">
            <i class="bi bi-printer me-1"></i>Print Ledgers
        </button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.financial.ledger-printing") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Uppercase for single char inputs
    document.querySelectorAll('.text-uppercase').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.table th, .table td { 
    padding: 0.3rem 0.5rem; 
    font-size: 0.85rem; 
    vertical-align: middle; 
    border-color: #999;
}
</style>
@endpush
