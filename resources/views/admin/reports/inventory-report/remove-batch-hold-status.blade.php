@extends('layouts.admin')

@section('title', 'Remove Batch Hold Status')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Remove Batch Hold Status</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="POST" id="filterForm" action="">
                @csrf
                <!-- From / To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ $toDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Sales Return / Purchase Radio -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="salesReturn" value="SR" {{ ($type ?? 'SR') == 'SR' ? 'checked' : '' }}>
                            <label class="form-check-label text-danger fw-bold" for="salesReturn">Sales Return</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="purchase" value="PB" {{ ($type ?? '') == 'PB' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="purchase">Purchase</label>
                        </div>
                    </div>
                </div>

                <!-- Selective & Number From/To -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="selective" id="selective" {{ ($selective ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="selective">Selective</label>
                        </div>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Number From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="number_from" class="form-control form-control-sm" value="{{ $numberFrom ?? '0' }}">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="number_to" class="form-control form-control-sm" value="{{ $numberTo ?? '0' }}">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>O</u>k</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
</style>
@endpush
