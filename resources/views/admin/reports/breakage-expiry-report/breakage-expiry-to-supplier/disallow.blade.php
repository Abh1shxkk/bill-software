@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Breakage / Expiry to Supplier Disallow</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <label class="form-label">From</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-1">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier_code" class="form-control form-control-sm text-uppercase" value="{{ request('supplier_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <select name="supplier_id" class="form-select form-select-sm">
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers ?? [] as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-12 d-flex gap-2">
                                <button type="submit" name="ok" value="1" class="btn btn-outline-secondary btn-sm">Ok</button>
                                <button type="submit" name="view" value="1" class="btn btn-outline-secondary btn-sm">View</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportToExcel()">Excel</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close()">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                            <thead style="background-color: #000080; color: white;">
                                <tr>
                                    <th style="width: 40px;">S.NO</th>
                                    <th>DATE</th>
                                    <th>TRN.NO</th>
                                    <th>CODE</th>
                                    <th>SUPPLIER NAME</th>
                                    <th class="text-end">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData ?? [] as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->transaction_date ? $row->transaction_date->format('d/m/Y') : '' }}</td>
                                    <td>{{ $row->trn_no ?? '' }}</td>
                                    <td>{{ $row->supplier_id ?? '' }}</td>
                                    <td>{{ $row->supplier_name ?? '' }}</td>
                                    <td class="text-end">{{ number_format($row->total_inv_amt ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                @for($i = 1; $i <= 17; $i++)
                                <tr>
                                    <td>{{ $i }}.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endfor
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2" style="font-size: 12px;">
                        <span style="color: red; font-style: italic; font-family: 'Times New Roman', Times, serif;">Total Records : {{ count($reportData ?? []) }}</span>
                        <span class="float-end" style="color: red; font-style: italic; font-family: 'Times New Roman', Times, serif;">Total : {{ number_format($totalAmount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    window.location.href = '{{ route("admin.reports.breakage-expiry.to-supplier.disallow") }}?excel=1&' + $('#filterForm').serialize();
}
</script>
@endsection
