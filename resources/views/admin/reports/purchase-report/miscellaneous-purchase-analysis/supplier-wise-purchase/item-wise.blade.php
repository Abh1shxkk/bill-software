@extends('layouts.admin')

@section('title', 'Supplier Item Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold" style="color: #00695c;">SUPPLIER ITEM WISE PURCHASE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.item-wise') }}">
                <div class="row g-2 align-items-end">
                     <div class="col-md-2">
                        <label class="small text-muted">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted">Supplier</label>
                        <select name="supplier" class="form-select form-select-sm">
                            <option value="">Select Supplier</option>
                             <option value="1">Supplier A</option>
                        </select>
                    </div>
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> View</button>
                    </div>
                     <div class="col-md-2">
                        <button type="button" class="btn btn-success btn-sm w-100"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
         <div class="card-body p-0">
             <div class="table-responsive" style="height: 60vh">
                <table class="table table-sm table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th class="text-end">Total Qty Received</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Avg Rate</th>
                            <th class="text-end">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                         @for($i=1; $i<=10; $i++)
                        <tr>
                            <td>Item Name {{ $i }}</td>
                            <td>General</td>
                             <td>Supplier A</td>
                            <td class="text-end">{{ rand(50, 500) }}</td>
                            <td class="text-end">{{ rand(0, 50) }}</td>
                            <td class="text-end">{{ number_format(rand(100, 500), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(rand(5000, 50000), 2) }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
