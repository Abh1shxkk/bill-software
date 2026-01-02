@extends('layouts.admin')
@section('title', 'Item Wise Discount')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">-: Item Wise Discount :-</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2" style="background-color: #e9ecef;">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.item-wise-discount') }}">
                <div class="row g-2 align-items-end mb-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">I/C</span>
                            <select name="report_type" class="form-select">
                                <option value="I" {{ ($reportType ?? 'I') == 'I' ? 'selected' : '' }}>Item Wise</option>
                                <option value="C" {{ ($reportType ?? '') == 'C' ? 'selected' : '' }}>Company Wise</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sel.Co</span>
                            <select name="selective_company" class="form-select">
                                <option value="N" {{ ($selectiveCompany ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($selectiveCompany ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Item</span>
                            <select name="item_wise" class="form-select">
                                <option value="Y" {{ ($itemWise ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($itemWise ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <select name="series" class="form-select form-select-sm">
                            <option value="">Series</option>
                            @foreach($seriesList ?? [] as $s)
                            <option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-end mb-2">
                    <div class="col-md-2">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">-- Company --</option>
                            @foreach($companies ?? [] as $c)
                            <option value="{{ $c->id }}" {{ ($companyId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tag.Cat</span>
                            <select name="tagged_categories" class="form-select">
                                <option value="N" {{ ($taggedCategories ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($taggedCategories ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rem.Tag</span>
                            <select name="remove_tags" class="form-select">
                                <option value="N" {{ ($removeTags ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($removeTags ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">-- Item Category --</option>
                            @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">-- Sales Man --</option>
                            @foreach($salesmen ?? [] as $sm)
                            <option value="{{ $sm->id }}" {{ ($salesmanId ?? '') == $sm->id ? 'selected' : '' }}>{{ $sm->code }} - {{ $sm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">-- Area --</option>
                            @foreach($areas ?? [] as $a)
                            <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">-- Route --</option>
                            @foreach($routes ?? [] as $r)
                            <option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">-- Customer --</option>
                            @foreach($customers ?? [] as $cust)
                            <option value="{{ $cust->id }}" {{ ($customerId ?? '') == $cust->id ? 'selected' : '' }}>{{ $cust->code }} - {{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="day" class="form-select form-select-sm">
                            <option value="">-- Day --</option>
                            <option value="Monday" {{ ($day ?? '') == 'Monday' ? 'selected' : '' }}>Monday</option>
                            <option value="Tuesday" {{ ($day ?? '') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="Wednesday" {{ ($day ?? '') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="Thursday" {{ ($day ?? '') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="Friday" {{ ($day ?? '') == 'Friday' ? 'selected' : '' }}>Friday</option>
                            <option value="Saturday" {{ ($day ?? '') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ ($day ?? '') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>Ok
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            @if(($reportType ?? 'I') == 'I')
                            <th style="width: 90px;">Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            @else
                            <th>Company Name</th>
                            @endif
                            <th class="text-end" style="width: 80px;">Qty</th>
                            <th class="text-end" style="width: 100px;">Gross Amt</th>
                            <th class="text-end" style="width: 70px;">Disc %</th>
                            <th class="text-end" style="width: 100px;">Disc Amt</th>
                            <th class="text-end" style="width: 100px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(($reportType ?? 'I') == 'I')
                            <td>{{ $item['item_code'] ?? '' }}</td>
                            <td>{{ $item['item_name'] ?? '' }}</td>
                            <td>{{ $item['company_name'] ?? '' }}</td>
                            @else
                            <td>{{ $item['company_name'] ?? '' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($item['qty'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($item['gross'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item['disc_percent'] ?? 0, 2) }}%</td>
                            <td class="text-end text-danger">{{ number_format($item['disc_amount'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($reportType ?? 'I') == 'I' ? 9 : 6 }}" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "Ok"
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            @if(($reportType ?? 'I') == 'I')
                            <td colspan="4" class="text-end">Total ({{ $totals['count'] }} items):</td>
                            @else
                            <td colspan="2" class="text-end">Total ({{ $totals['count'] }} companies):</td>
                            @endif
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($totals['gross'] ?? 0, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['disc_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel me-1"></i>Excel
                </button>
                <div class="d-flex gap-2">
                    <button type="submit" form="filterForm" class="btn btn-info btn-sm">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.other.item-wise-discount") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.item-wise-discount") }}?' + params.toString(), 'ItemWiseDiscount', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }
.btn-sm { font-size: 0.75rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
