@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">List Of Disallowed Items</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-auto">
                                <label class="form-label mb-0">From:</label>
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

                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-3">
                                <label class="form-label mb-0">PartyName:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="party_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('party_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="party_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}" {{ request('party_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" name="ok" value="1" class="btn btn-secondary btn-sm">OK</button>
                                <a href="{{ route('admin.reports.breakage-expiry.list-of-disallowed-items') }}" class="btn btn-secondary btn-sm">Exit</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
