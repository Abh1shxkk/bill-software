@extends('layouts.admin')

@section('title', 'Rate Change Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">RATE CHANGE REPORT</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.rate-difference') }}">
                <!-- Date Filters -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Item -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Item :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="item_id" id="item_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->id }} - {{ Str::limit($item->name, 20) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>

                <!-- Company -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->id }} - {{ Str::limit($company->name, 15) }}</option>
                            @endforeach
                        </select>
                    </div>
                   
                </div>

                <!-- Party -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Party :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>{{ $customer->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>

                <!-- Rate Type Option -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-10">
                        <div class="d-flex align-items-center">
                            <label class="fw-bold mb-0 me-2" style="font-size: 0.85rem;">From P(urchase Rate) / S(ale Rate) / R(ate Diff.) / C(ost) :</label>
                            <input type="text" name="rate_type" id="rate_type" class="form-control form-control-sm text-center" style="width: 40px;" value="{{ $rateType ?? 'R' }}" maxlength="1">
                        </div>
                    </div>
                </div>

                <!-- Group By Option -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center">
                            <label class="fw-bold mb-0 me-2" style="font-size: 0.85rem;">I(tem Wise) / B(ill Wise) / P(arty Wise) :</label>
                            <input type="text" name="group_by" id="group_by" class="form-control form-control-sm text-center me-3" style="width: 40px;" value="{{ $groupBy ?? 'I' }}" maxlength="1">
                            
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" name="with_vat" id="withVat" {{ ($withVat ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="withVat">With VAT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="with_sc" id="withSc" {{ ($withSc ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="withSc">With SC</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">Show</button>
                        <button type="submit" form="filterForm" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Rate Change Report ({{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }})</strong>
            <span class="float-end">@if($groupBy == 'I') Item Wise @elseif($groupBy == 'B') Bill Wise @else Party Wise @endif</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($groupBy == 'I')
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end" style="width: 70px;">Qty</th>
                            <th class="text-end" style="width: 90px;">Pur. Rate</th>
                            <th class="text-end" style="width: 90px;">Sale Rate</th>
                            <th class="text-end" style="width: 90px;">Rate Diff</th>
                            <th class="text-end" style="width: 100px;">Diff Amt</th>
                            <th class="text-end" style="width: 110px;">Total Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td class="text-end">{{ number_format($row['qty'], 0) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_rate'], 2) }}</td>
                            <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['rate_diff'], 2) }}</td>
                            <td class="text-end {{ $row['diff_amount'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['diff_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['total_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                            <td colspan="3"></td>
                            <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_amount'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @elseif($groupBy == 'B')
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th class="text-center" style="width: 90px;">Date</th>
                            <th style="width: 90px;">Bill No</th>
                            <th>Party</th>
                            <th>Item Name</th>
                            <th class="text-end" style="width: 60px;">Qty</th>
                            <th class="text-end" style="width: 80px;">Pur. Rate</th>
                            <th class="text-end" style="width: 80px;">Sale Rate</th>
                            <th class="text-end" style="width: 80px;">Rate Diff</th>
                            <th class="text-end" style="width: 90px;">Diff Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                            <td>{{ $row['bill_no'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td class="text-end">{{ number_format($row['qty'], 0) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_rate'], 2) }}</td>
                            <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['rate_diff'], 2) }}</td>
                            <td class="text-end {{ $row['diff_amount'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['diff_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                            <td colspan="3"></td>
                            <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th style="width: 80px;">Party Code</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 80px;">Total Qty</th>
                            <th class="text-end" style="width: 120px;">Purchase Value</th>
                            <th class="text-end" style="width: 120px;">Sale Value</th>
                            <th class="text-end" style="width: 120px;">Rate Diff</th>
                            <th class="text-end" style="width: 80px;">Diff %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['party_code'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td class="text-end">{{ number_format($row['total_qty'], 0) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_value'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_value'], 2) }}</td>
                            <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['rate_diff'], 2) }}</td>
                            <td class="text-end {{ $row['diff_percent'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['diff_percent'], 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                            <td colspan="2"></td>
                            <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $totals['count'] ?? 0 }} | Total Difference: <span class="{{ ($totals['total_diff_amount'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-bold">₹{{ number_format($totals['total_diff_amount'] ?? 0, 2) }}</span></small>
        </div>
    </div>
    @elseif(request()->has('date_from'))
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found for the selected filters.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Update name display when dropdown changes
document.getElementById('item_id').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    document.getElementById('item_name_display').value = selected.value ? selected.text.split(' - ').slice(1).join(' - ') : '';
});

document.getElementById('company_id').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    document.getElementById('company_name_display').value = selected.value ? selected.text.split(' - ').slice(1).join(' - ') : '';
});

document.getElementById('customer_id').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var customerId = selected.value;
    if (customerId) {
        @if(isset($customers))
        var customers = @json($customers);
        var customer = customers.find(c => c.id == customerId);
        document.getElementById('party_name_display').value = customer ? customer.name : '';
        @endif
    } else {
        document.getElementById('party_name_display').value = '';
    }
});

function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.sales.other.rate-difference") }}?' + params.toString();
}

function viewReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.rate-difference") }}?' + params.toString(), 'RateDifference', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.sales") }}';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'e' || e.key === 'E') { if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) exportToExcel(); }
    if (e.key === 'v' || e.key === 'V') { if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) viewReport(); }
    if (e.key === 'Escape') closeWindow();
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.4rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
