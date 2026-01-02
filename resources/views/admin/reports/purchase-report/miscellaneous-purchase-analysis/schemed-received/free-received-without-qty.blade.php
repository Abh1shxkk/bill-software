@extends('layouts.admin')

@section('title', 'Free Received Without Qty Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold" style="color: #ff6f00;">FREE RECEIVED WITHOUT QTY</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.free-without-qty') }}">
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
                        <label class="small text-muted">Search Item</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Item Name...">
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
                            <th>Date</th>
                            <th>Bill details</th>
                            <th>Supplier Name</th>
                            <th>Item Name</th>
                            <th>Pack</th>
                            <th class="text-end">Pur. Qty</th>
                            <th class="text-end">Free Qty</th>
                             <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                         @for($i=1; $i<=8; $i++)
                        <tr>
                            <td>{{ date('d-m-Y', strtotime("-{$i} days")) }}</td>
                             <td>BILL-{{ 900+$i }}</td>
                            <td>Supplier Z</td>
                            <td>Bonus Item {{ $i }}</td>
                            <td>1x1</td>
                             <td class="text-end text-muted">0</td>
                            <td class="text-end fw-bold text-success">{{ rand(5, 20) }}</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
