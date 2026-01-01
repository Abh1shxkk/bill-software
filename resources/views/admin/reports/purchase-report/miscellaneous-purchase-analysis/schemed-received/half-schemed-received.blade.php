@extends('layouts.admin')

@section('title', 'Half Schemed Received Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="color: #0277bd;">HALF SCHEMED RECEIVED</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
             <form method="GET" action="{{ route('admin.reports.purchase.misc.schemed.half-schemed') }}">
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
                            <option value="">All Companies</option>
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
                            <th>Item Name</th>
                            <th>Company</th>
                             <th class="text-end">Full Scheme</th>
                            <th class="text-end">Half Scheme</th>
                            <th class="text-end">Qty Recd</th>
                            <th class="text-end">Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=10; $i++)
                        <tr>
                            <td>{{ date('d-m-Y', strtotime("-{$i} days")) }}</td>
                             <td>INV-{{ 5000+$i }}</td>
                            <td>Medicine Item {{ $i }}</td>
                            <td>Company X</td>
                            <td class="text-end">10+2</td>
                            <td class="text-end fw-bold text-primary">10+1</td>
                             <td class="text-end">50</td>
                            <td class="text-end text-danger">-5 (Pending)</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
