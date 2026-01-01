@extends('layouts.admin')

@section('title', 'Company Item Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-info fst-italic fw-bold" style="color: #00838f;">COMPANY ITEM WISE PURCHASE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.reports.purchase.misc.company.item-wise') }}">
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
                         <label class="small text-muted">Select Company</label>
                        <select name="company" class="form-select form-select-sm">
                            <option value="">Select Company</option>
                            <option value="1">Company A</option>
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
                            <th>Company</th>
                            <th>Item Name</th>
                            <th>Pack</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                             <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                         @for($i=1; $i<=15; $i++)
                        <tr>
                            <td>{{ date('d-m-Y', strtotime("-{$i} days")) }}</td>
                             <td>BILL-{{ 1000+$i }}</td>
                            <td>Company A</td>
                            <td>Product Item {{ $i }}</td>
                            <td>10x10</td>
                            <td class="text-end">{{ rand(10, 100) }}</td>
                            <td class="text-end text-success">{{ rand(0, 50) }}</td>
                             <td class="text-end">{{ number_format(rand(50, 200), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(rand(500, 2000), 2) }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
