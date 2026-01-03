@extends('layouts.admin')

@section('title', "Supplier's Pending Order")

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Supplier's Pending Order</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- From Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">From Date :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">To Date :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Supplier :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="supplier_id" id="supplier_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($supplierId ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="submit" name="print" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Sr.</th>
                            <th>Supplier Name</th>
                            <th>Item Name</th>
                            <th class="text-center">Order Date</th>
                            <th class="text-end">Order Qty</th>
                            <th class="text-end">Received Qty</th>
                            <th class="text-end">Pending Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['supplier_name'] ?? '' }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td class="text-center">{{ $row['order_date'] ?? '' }}</td>
                            <td class="text-end">{{ $row['order_qty'] ?? 0 }}</td>
                            <td class="text-end">{{ $row['received_qty'] ?? 0 }}</td>
                            <td class="text-end">{{ $row['pending_qty'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.8rem; }
</style>
@endpush
