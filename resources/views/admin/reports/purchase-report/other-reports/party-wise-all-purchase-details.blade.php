@extends('layouts.admin')

@section('title', 'Party Wise All Purchase Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffcdd2 0%, #f8bbd9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1565c0;">Party Wise All Purchase Details</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.other.party-wise-all-purchase-details') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 2: Report Type Radio Buttons -->
                    <div class="col-md-6">
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="radio" name="report_type" class="form-check-input" id="purchaseBook" value="purchase_book" {{ ($reportType ?? 'purchase_book') == 'purchase_book' ? 'checked' : '' }}>
                                <label class="form-check-label" for="purchaseBook">Purchase Book</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="report_type" class="form-check-input" id="withGstDetails" value="with_gst_details" {{ ($reportType ?? '') == 'with_gst_details' ? 'checked' : '' }}>
                                <label class="form-check-label" for="withGstDetails">With GST Details</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 3: With GST Checkbox and Buttons -->
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="with_gst" class="form-check-input" id="withGst" value="1" {{ ($withGst ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="withGst">With GST</label>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
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

    <!-- Summary Cards -->
    @if(isset($partyDetails) && count($partyDetails) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Suppliers</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['bills'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Gross Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['gross_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Returns</small>
                    <h6 class="mb-0">₹{{ number_format($totals['returns'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Purchase</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_purchase'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 40px;">Sr.</th>
                            <th style="width: 70px;">Code</th>
                            <th>Supplier Name</th>
                            <th style="width: 100px;">City</th>
                            <th class="text-center" style="width: 50px;">Bills</th>
                            <th class="text-end" style="width: 100px;">Gross Amt</th>
                            <th class="text-end" style="width: 80px;">Discount</th>
                            @if($withGst ?? false)
                            <th class="text-end" style="width: 80px;">CGST</th>
                            <th class="text-end" style="width: 80px;">SGST</th>
                            <th class="text-end" style="width: 80px;">IGST</th>
                            @else
                            <th class="text-end" style="width: 80px;">Tax</th>
                            @endif
                            <th class="text-end" style="width: 100px;">Net Amount</th>
                            <th class="text-end" style="width: 90px;">Returns</th>
                            <th class="text-end" style="width: 100px;">Net Purchase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partyDetails ?? [] as $index => $party)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $party->supplier_code ?? '-' }}</td>
                            <td>{{ $party->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $party->city ?? '-' }}</td>
                            <td class="text-center">{{ number_format($party->bill_count ?? 0) }}</td>
                            <td class="text-end">{{ number_format($party->gross_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($party->discount ?? 0, 2) }}</td>
                            @if($withGst ?? false)
                            <td class="text-end">{{ number_format($party->cgst ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->sgst ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->igst ?? 0, 2) }}</td>
                            @else
                            <td class="text-end">{{ number_format($party->tax_amount ?? 0, 2) }}</td>
                            @endif
                            <td class="text-end">{{ number_format($party->net_amount ?? 0, 2) }}</td>
                            <td class="text-end text-warning">{{ number_format($party->returns ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($party->net_purchase ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($withGst ?? false) ? 13 : 11 }}" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($partyDetails) && count($partyDetails) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="4">Grand Total</td>
                            <td class="text-center">{{ number_format($totals['bills'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                            @if($withGst ?? false)
                            <td class="text-end">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                            @else
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            @endif
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['returns'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_purchase'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
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
        viewTypeInput.value = '';
        form.target = '_self';
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
