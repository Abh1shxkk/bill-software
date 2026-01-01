@extends('layouts.admin')

@section('title', 'All Company Purchase Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="color: #3949ab;">ALL COMPANY PURCHASE SUMMARY</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reports.purchase.misc.company.all-company') }}">
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
                        <label class="small text-muted">Filter</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="all">All Companies</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
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
                            <th>Company Name</th>
                            <th>Division</th>
                             <th class="text-end">Total Items</th>
                            <th class="text-end">Total Purchase Qty</th>
                            <th class="text-end">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=10; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>Company Name {{ chr(64+$i) }}</td>
                            <td>General</td>
                            <td class="text-end">{{ rand(10, 100) }}</td>
                            <td class="text-end">{{ rand(100, 5000) }}</td>
                            <td class="text-end fw-bold">{{ number_format(rand(50000, 500000), 2) }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
