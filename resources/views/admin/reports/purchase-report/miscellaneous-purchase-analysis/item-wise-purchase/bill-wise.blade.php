@extends('layouts.admin')

@section('title', 'Item Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #721c24;">Item Wise Purchase</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.item.bill-wise') }}" id="reportForm">
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
                    <div class="col-md-2">
                        <label class="small text-muted">Selective Item [ Y / N ] :</label>
                        <input type="text" name="selective_item" class="form-control form-control-sm" value="{{ $selectiveItem ?? 'Y' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted">Status :</label>
                        <input type="text" name="status" class="form-control form-control-sm" placeholder="Status...">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">Tagged Items [ Y / N ] :</label>
                        <input type="text" name="tagged_items" class="form-control form-control-sm" value="{{ $taggedItems ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">Remove Tags [ Y / N ] :</label>
                        <input type="text" name="remove_tags" class="form-control form-control-sm" value="{{ $removeTags ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-4">
                        <label class="small text-muted text-primary fw-bold">Item :</label>
                        <input type="text" name="item_name" class="form-control form-control-sm" placeholder="Search Item...">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Division :</label>
                        <input type="text" name="division_code" class="form-control form-control-sm" value="{{ $divisionCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">Batch :</label>
                        <input type="text" name="batch_no" class="form-control form-control-sm" value="{{ $batchNo ?? '' }}" placeholder="Batch No...">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-1">
                        <label class="small text-muted fw-bold">Party Code :</label>
                        <input type="text" name="party_code" class="form-control form-control-sm" value="{{ $partyCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">-- All Suppliers --</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <fieldset class="border p-2 mt-2">
                    <legend class="small text-muted w-auto px-2">Filters</legend>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-1">
                            <label class="small text-muted">Category :</label>
                            <input type="text" name="category_code" class="form-control form-control-sm" value="00" style="width: 60px;">
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
                </fieldset>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-md-2">
                        <label class="small text-muted">With Br. / Expiry [ Y / N ] :</label>
                        <input type="text" name="with_br_expiry" class="form-control form-control-sm" value="{{ $withBrExpiry ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-2">
                        <label class="small text-muted">With Address [ Y / N ] :</label>
                        <input type="text" name="with_address" class="form-control form-control-sm" value="{{ $withAddress ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">With Value [ Y / N ] :</label>
                        <input type="text" name="with_value" class="form-control form-control-sm" value="{{ $withValue ?? 'N' }}" maxlength="1" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success btn-sm px-3">Excel</button>
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
                            <th>Bill Date</th>
                            <th>Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandQty = 0; $grandFree = 0; $grandAmount = 0; @endphp
                        @foreach($items as $index => $item)
                        @php $grandQty += $item->qty; $grandFree += $item->free_qty; $grandAmount += $item->amount; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->transaction->bill_date ? $item->transaction->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->transaction->bill_no ?? '-' }}</td>
                            <td>{{ $item->transaction->supplier->name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->batch_no ?? '-' }}</td>
                            <td>{{ $item->expiry_date ? $item->expiry_date->format('m/y') : '-' }}</td>
                            <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($item->free_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($item->pur_rate, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandQty, 2) }}</td>
                            <td class="text-end">{{ number_format($grandFree, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format($grandAmount, 2) }}</td>
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
    window.open("{{ route('admin.reports.purchase.misc.item.bill-wise.print') }}?" + params, '_blank');
}
</script>
@endpush
