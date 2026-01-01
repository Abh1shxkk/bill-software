@extends('layouts.admin')

@section('title', 'Supplier Bill Wise Purchase')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-dark fst-italic fw-bold" style="color: #6a1b9a;">SUPPLIER BILL WISE PURCHASE</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.supplier.bill-wise') }}">
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
                        <label class="small text-muted">Select Supplier</label>
                         <select name="supplier" class="form-select form-select-sm">
                            <option value="">All Suppliers View</option>
                            <option value="1">Supplier A</option>
                            <option value="2">Supplier B</option>
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
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Supplier Name</th>
                            <th>Type</th>
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Dis%</th>
                            <th class="text-end">GST Amt</th>
                            <th class="text-end">Net Amount</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=15; $i++)
                        <tr>
                            <td>{{ date('d-m-Y', strtotime("-{$i} days")) }}</td>
                            <td>BILL-{{ 202400 + $i }}</td>
                            <td>Supplier {{ chr(65 + ($i % 5)) }}</td>
                            <td>Credit</td>
                            <td class="text-end">{{ number_format(rand(5000, 20000), 2) }}</td>
                            <td class="text-end">5.00</td>
                            <td class="text-end">{{ number_format(rand(200, 1000), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(rand(5500, 22000), 2) }}</td>
                            <td>{{ date('d-m-Y', strtotime("+30 days")) }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
