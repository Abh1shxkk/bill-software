@extends('layouts.admin')

@section('title', 'All Item Purchase Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold" style="color: #e65100;">ALL ITEM PURCHASE SUMMARY</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.reports.purchase.misc.item.all-item-purchase') }}">
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
                        <label class="small text-muted">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <option value="1">Medicine</option>
                             <option value="2">FMCG</option>
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
                            <th>S.No</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th>Category</th>
                             <th class="text-end">Total Qty In</th>
                            <th class="text-end">Bonus Qty</th>
                            <th class="text-end">Avg Rate</th>
                            <th class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=15; $i++)
                        <tr>
                             <td>{{ $i }}</td>
                            <td>Product Item {{ $i }}</td>
                            <td>Company {{ chr(65 + ($i%3)) }}</td>
                            <td>General</td>
                            <td class="text-end">{{ rand(100, 1000) }}</td>
                            <td class="text-end text-success">{{ rand(0, 50) }}</td>
                             <td class="text-end">{{ number_format(rand(50, 500), 2) }}</td>
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
