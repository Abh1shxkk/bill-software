@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Unused Dump</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-auto">
                                <label class="form-label mb-0"><u>F</u>rom:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">To:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm">View</button>
                                <a href="{{ route('admin.reports.breakage-expiry.unused-dump') }}" class="btn btn-secondary btn-sm">Close</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
