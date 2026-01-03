@extends('layouts.admin')

@section('title', 'FiFo Ledger')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">FiFo Ledger</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- Selective Item -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Selective Item [Y/N] :</label>
                    </div>
                    <div class="col-auto">
                        <select name="selective_item" id="selective_item" class="form-select form-select-sm" style="width: 60px;">
                            <option value="Y" {{ ($selectiveItem ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                            <option value="N" {{ ($selectiveItem ?? '') == 'N' ? 'selected' : '' }}>N</option>
                        </select>
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="company_code" class="form-control form-control-sm" value="{{ $companyCode ?? '' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Item -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Item :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="item_code" class="form-control form-control-sm" value="{{ $itemCode ?? '' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="item_id" id="item_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Sr.</th>
                            <th>Item Name</th>
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Purchase</th>
                            <th class="text-end">Sale</th>
                            <th class="text-end">Closing</th>
                            <th class="text-end">MRP</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td>{{ $row['batch_no'] ?? '' }}</td>
                            <td class="text-center">{{ $row['expiry'] ?? '' }}</td>
                            <td class="text-end">{{ $row['opening'] ?? 0 }}</td>
                            <td class="text-end">{{ $row['purchase'] ?? 0 }}</td>
                            <td class="text-end">{{ $row['sale'] ?? 0 }}</td>
                            <td class="text-end">{{ $row['closing'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($row['mrp'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.8rem; }
</style>
@endpush
