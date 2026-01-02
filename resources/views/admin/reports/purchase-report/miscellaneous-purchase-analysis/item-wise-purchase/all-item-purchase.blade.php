@extends('layouts.admin')

@section('title', 'All Item Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">ALL ITEM PURCHASE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.item.all-item-purchase') }}" id="reportForm">
                <!-- Report Type -->
                <div class="row mb-2">
                    <div class="col-12">
                        <label class="small text-muted fw-bold">1. Purchase / 2. Purchase Return / 3. Both:</label>
                        <select name="report_type" class="form-select form-select-sm d-inline-block" style="width: 60px;">
                            <option value="1" {{ ($reportType ?? '3') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($reportType ?? '3') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($reportType ?? '3') == '3' ? 'selected' : '' }}>3</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="small text-muted">From :</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">To :</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted">Bill No.</label>
                        <input type="number" name="bill_no_from" class="form-control form-control-sm" value="{{ $billNoFrom ?? 0 }}" style="width: 80px;">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted">To :</label>
                        <input type="number" name="bill_no_to" class="form-control form-control-sm" value="{{ $billNoTo ?? 0 }}" style="width: 80px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Companies [ Y / N ] :</label>
                        <input type="text" name="tagged_companies" class="form-control form-control-sm" value="{{ $taggedCompanies ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Company :</label>
                        <input type="text" name="company_code" class="form-control form-control-sm" value="{{ $companyCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-3">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">-- All Companies --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Division :</label>
                        <input type="text" name="division_code" class="form-control form-control-sm" value="{{ $divisionCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-3">
                        <select name="division_id" class="form-select form-select-sm">
                            <option value="">-- All Divisions --</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Item :</label>
                        <input type="text" name="item_code" class="form-control form-control-sm" value="{{ $itemCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="item_name" class="form-control form-control-sm" placeholder="Item Name...">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Categories [ Y / N ] :</label>
                        <input type="text" name="tagged_categories" class="form-control form-control-sm" value="{{ $taggedCategories ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">Remove Tags [ Y / N ] :</label>
                        <input type="text" name="remove_tags" class="form-control form-control-sm" value="{{ $removeTags ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Category :</label>
                        <input type="text" name="category_code" class="form-control form-control-sm" value="{{ $categoryCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">-- All Categories --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted">Range :</label>
                        <input type="text" name="range_yn" class="form-control form-control-sm" value="{{ $rangeYN ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">Value :</label>
                        <input type="number" name="value_from" class="form-control form-control-sm" value="{{ $valueFrom ?? -999999999 }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">To :</label>
                        <input type="number" name="value_to" class="form-control form-control-sm" value="{{ $valueTo ?? 999999999 }}">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">Order By Q(ty)/V(alue) :</label>
                        <input type="text" name="order_by" class="form-control form-control-sm" value="{{ $orderBy ?? 'V' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">A(SC)/D(ESC) :</label>
                        <input type="text" name="sort_order" class="form-control form-control-sm" value="{{ $sortOrder ?? 'D' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">No. of Top Items :</label>
                        <input type="number" name="top_items" class="form-control form-control-sm" value="{{ $topItems ?? 0 }}" style="width: 80px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">Batch Wise</label>
                        <input type="checkbox" name="batch_wise" value="Y" {{ ($batchWise ?? 'N') == 'Y' ? 'checked' : '' }}>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">With Br. / Expiry [ Y / N ] :</label>
                        <input type="text" name="with_br_expiry" class="form-control form-control-sm" value="{{ $withBrExpiry ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">With Return Det. [ Y / N ] :</label>
                        <input type="text" name="with_return_det" class="form-control form-control-sm" value="{{ $withReturnDet ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info btn-sm px-3">Tax Wise</button>
                        <span class="float-end">
                            <button type="submit" class="btn btn-secondary btn-sm px-4">View</button>
                            <button type="button" class="btn btn-primary btn-sm px-4" onclick="openPrintView()">Print</button>
                            <button type="button" class="btn btn-secondary btn-sm px-4" onclick="window.close();">Close</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($items) && $items->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">S.No</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Bonus Qty</th>
                            <th class="text-end">Avg Rate</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandQty = 0; $grandFree = 0; $grandAmount = 0; $grandTax = 0; $grandNet = 0;
                        @endphp
                        @foreach($items as $index => $item)
                        @php
                            $grandQty += $item->total_qty;
                            $grandFree += $item->total_free_qty;
                            $grandAmount += $item->total_amount;
                            $grandTax += $item->total_tax;
                            $grandNet += $item->total_net;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->total_qty, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($item->total_free_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($item->avg_rate, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_tax, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->total_net, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format($grandAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTax, 2) }}</td>
                            <td class="text-end">{{ number_format($grandNet, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('from_date'))
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> No records found.</div>
    @else
    <div class="alert alert-secondary"><i class="bi bi-info-circle"></i> Select date range and click "View".</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function openPrintView() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    window.open("{{ route('admin.reports.purchase.misc.item.all-item-purchase.print') }}?" + params, '_blank');
}
</script>
@endpush
