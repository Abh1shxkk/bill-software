@extends('layouts.admin')

@section('title', 'Purchase / Return Book Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e6e6fa 0%, #d8bfd8 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-dark fst-italic fw-bold">Purchase / Return Book Item Wise</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-return-item-wise') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range and Local/Central -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">FROM:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">TO:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal)/C(entral)/B(oth):</span>
                            <select name="local_central" class="form-select" style="max-width: 60px;">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3"></div>

                    <!-- Row 2: Show Value, Group By, Category -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show value:</span>
                            <select name="show_value" class="form-select" style="max-width: 50px;">
                                <option value="Y" {{ ($showValue ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showValue ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">C(ompany)/I(tem) Wise/B(al. Stock):</span>
                            <select name="group_by" class="form-select" style="max-width: 50px;">
                                <option value="C" {{ ($groupBy ?? 'C') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="I" {{ ($groupBy ?? '') == 'I' ? 'selected' : '' }}>I</option>
                                <option value="B" {{ ($groupBy ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Category:</span>
                            <input type="text" name="category_code" class="form-control" value="{{ $categoryCode ?? '' }}" placeholder="00" style="max-width: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-check-lg me-1"></i>Ok
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 120px;">COMPANY</th>
                            <th>ITEM NAME</th>
                            <th style="width: 80px;">PACK</th>
                            <th class="text-center" style="width: 80px;">PURCHASE</th>
                            @if(($showValue ?? 'Y') == 'Y')
                            <th class="text-end" style="width: 100px;">VALUE</th>
                            @endif
                            <th class="text-center" style="width: 80px;">BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $item)
                        <tr>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->packing ?? '-' }}</td>
                            <td class="text-center">{{ number_format($item->purchase_qty ?? 0, 0) }}</td>
                            @if(($showValue ?? 'Y') == 'Y')
                            <td class="text-end">{{ number_format($item->purchase_value ?? 0, 2) }}</td>
                            @endif
                            <td class="text-center">{{ number_format($item->balance_qty ?? 0, 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($showValue ?? 'Y') == 'Y' ? 6 : 5 }}" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select date range and click "Ok" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Footer with totals -->
        <div class="card-footer bg-light py-2">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-journal-text me-1"></i>Stock Ledger
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </button>
                </div>
                <div class="col-md-8 text-end">
                    <span class="text-primary fw-bold fs-6">TOTAL PURCHASE VALUE</span>
                    <span class="text-primary fw-bold fs-5 ms-4">{{ number_format($totals['purchase_value'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View button
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Excel button
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.target = '_self';
        form.submit();
        exportInput.value = '';
    });

    // Print button
    document.getElementById('btnPrint').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) {
            viewTypeInput = document.createElement('input');
            viewTypeInput.type = 'hidden';
            viewTypeInput.name = 'view_type';
            form.appendChild(viewTypeInput);
        }
        viewTypeInput.value = 'print';
        form.target = '_blank';
        form.submit();
        form.target = '_self';
        viewTypeInput.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
