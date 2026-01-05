@extends('layouts.admin')
@section('title', 'Stock Transfer Incoming Item/Party/Bill Wise')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">STOCK TRANSFER INCOMING<br>ITEM / PARTY / BILL WISE</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.misc-transaction.stock-transfer-incoming.item-party-bill-wise') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>F</u>rom :</label></div>
                    <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                    <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                    <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>T</u>(ransfer) / <u>R</u>(eturn) / <u>B</u>(oth) :</label></div>
                    <div class="col-auto">
                        <input type="text" name="tran_type" class="form-control form-control-sm text-uppercase" value="{{ request('tran_type', 'B') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 90px;"><label class="fw-bold mb-0">Supplier :</label></div>
                    <div class="col">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 90px;"><label class="fw-bold mb-0">Company :</label></div>
                    <div class="col">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && $reportData->count() > 0)
    <div class="card mt-2">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr><th class="text-center">S.No</th><th>Item Name</th><th>Party Name</th><th>Date</th><th>Voucher No</th><th class="text-end">Qty</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->item_name ?? '' }}</td>
                            <td>{{ $item->party_name ?? '' }}</td>
                            <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                            <td>{{ $item->voucher_no ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="card mt-2">
        <div class="card-body p-3 text-center">
            <p class="mb-0 text-muted">No records found for the selected criteria.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }
function printReport() { window.open('{{ route("admin.reports.misc-transaction.stock-transfer-incoming.item-party-bill-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank'); }
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush