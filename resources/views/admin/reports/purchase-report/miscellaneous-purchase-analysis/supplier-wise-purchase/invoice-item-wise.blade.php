@extends('layouts.admin')

@section('title', 'Supplier Invoice - Item Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #ede7f6 0%, #d1c4e9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="color: #4527a0;">SUPPLIER INVOICE - ITEM WISE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.invoice-item-wise') }}">
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
                         <label class="small text-muted">Search Invoice / Supplier</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Bill No or Supplier...">
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
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Bill No / Date</th>
                             <th>Supplier</th>
                            <th>Item Details</th>
                            <th>Batch / Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                         @for($i=1; $i<=5; $i++)
                        <tr class="table-light fw-bold">
                            <td colspan="7">Bill No: SUP-{{ 500+$i }} | Date: {{ date('d-m-Y') }} | Total: ₹{{ number_format(rand(5000, 10000), 2) }}</td>
                        </tr>
                            @for($j=1; $j<=3; $j++)
                            <tr>
                                <td></td>
                                <td>Supplier Name X</td>
                                <td>Medicine Product Item {{ $j }}</td>
                                <td>B-{{ 100+$j }} / {{ date('m/y', strtotime("+1 year")) }}</td>
                                <td class="text-end">{{ rand(10, 50) }}</td>
                                <td class="text-end">{{ number_format(rand(50, 200), 2) }}</td>
                                <td class="text-end">{{ number_format(rand(500, 5000), 2) }}</td>
                            </tr>
                            @endfor
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
