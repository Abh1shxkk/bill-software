@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Godown Brk/Expiry Item Wise - Pending</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-auto">
                                <label class="form-label mb-0"><u>C</u>(ompany)/<u>A</u>(ll):</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="filter_type" class="form-control form-control-sm text-uppercase" style="width: 30px;" maxlength="1" value="{{ request('filter_type', 'A') }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">Company:</label>
                            </div>
                            <div class="col-md-2">
                                <select name="company_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 align-items-end mt-1">
                            <div class="col-auto">
                                <label class="form-label mb-0">Value on - <u>P</u>(ur.Rate)/<u>S</u>(ale Rate)/<u>M</u>(rp):</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="value_on" class="form-control form-control-sm text-uppercase" style="width: 30px;" maxlength="1" value="{{ request('value_on', 'M') }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">Loose Units Only [Y/N]:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="loose_units" class="form-control form-control-sm text-uppercase" style="width: 30px;" maxlength="1" value="{{ request('loose_units', 'N') }}">
                            </div>
                            <div class="col-auto">
                                <select name="loose_value" class="form-select form-select-sm" style="width: 60px;">
                                    <option value="00" {{ request('loose_value') == '00' ? 'selected' : '' }}>00</option>
                                    <option value="01" {{ request('loose_value') == '01' ? 'selected' : '' }}>01</option>
                                    <option value="02" {{ request('loose_value') == '02' ? 'selected' : '' }}>02</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="ok" value="1" class="btn btn-secondary btn-sm">Ok</button>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm">View</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="showHistory()">History</button>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.reports.breakage-expiry.godown-item-wise.pending') }}" class="btn btn-secondary btn-sm">Close</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                            <thead>
                                <tr style="background-color: #ffc4d0;">
                                    <th style="width: 50px;">S.No.</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Pack</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Mrp</th>
                                    <th>Qty.Recd.</th>
                                    <th>Qty.Pend.</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData ?? [] as $index => $row)
                                <tr style="{{ $index == 0 ? 'background-color: #ffc4d0;' : '' }}">
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $row->company_name ?? '' }}</td>
                                    <td>{{ $row->item_name ?? '' }}</td>
                                    <td>{{ $row->pack ?? '' }}</td>
                                    <td>{{ $row->batch ?? '' }}</td>
                                    <td>{{ $row->expiry ?? '' }}</td>
                                    <td>{{ number_format($row->mrp ?? 0, 2) }}</td>
                                    <td>{{ $row->qty_recd ?? 0 }}</td>
                                    <td>{{ $row->qty_pend ?? 0 }}</td>
                                    <td>{{ number_format($row->amount ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                @for($i = 1; $i <= 19; $i++)
                                <tr style="{{ $i == 1 ? 'background-color: #ffc4d0;' : '' }}">
                                    <td>{{ $i }}.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endfor
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between p-2" style="font-family: 'Times New Roman', Times, serif; font-style: italic;">
                        <div><strong>Total Records :</strong> <span style="color: red;">{{ isset($reportData) ? count($reportData) : 0 }}</span></div>
                        <div><strong>Total Value :</strong> <span style="color: red;">{{ number_format($totalValue ?? 0, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showHistory() {
    alert('History feature coming soon');
}

function printReport() {
    window.open('{{ route("admin.reports.breakage-expiry.godown-item-wise.pending") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
