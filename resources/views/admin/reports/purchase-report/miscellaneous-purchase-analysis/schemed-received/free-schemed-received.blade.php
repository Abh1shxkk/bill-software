@extends('layouts.admin')

@section('title', 'Free Schemed Received Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold" style="color: #c2185b;">FREE SCHEMED RECEIVED</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.free-schemed') }}">
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
                            <option value="">All Suppliers</option>
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
                            <th>Bill Date</th>
                            <th>Bill No</th>
                            <th>Supplier Name</th>
                            <th>Item Name</th>
                            <th class="text-end">Pur. Qty</th>
                            <th class="text-end">Free Qty</th>
                            <th class="text-end">Scheme %</th>
                            <th class="text-end">Free Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=15; $i++)
                        <tr>
                            <td>{{ date('d-m-Y', strtotime("-{$i} days")) }}</td>
                            <td>INV-{{ 202400+$i }}</td>
                            <td>Supplier {{ chr(65 + ($i%3)) }}</td>
                            <td>Product {{ $i }}</td>
                            <td class="text-end">{{ rand(10, 100) }}</td>
                            <td class="text-end fw-bold text-success">{{ rand(1, 10) }}</td>
                            <td class="text-end">10+1</td>
                            <td class="text-end fw-bold">{{ number_format(rand(100, 1000), 2) }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
