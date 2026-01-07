@extends('layouts.admin')

@section('title', 'Voucher Printing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800080;">-: Voucher Printing :-</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.voucher-printing') }}">
                <div class="row g-3 align-items-center">
                    <!-- Date From -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Date From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 150px;">
                    </div>

                    <!-- Date To -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 150px;">
                    </div>
                </div>

                <div class="row g-3 align-items-center mt-2">
                    <!-- Voucher Type -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Voucher Type :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="voucher_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $voucherType }}" style="width: 60px;" maxlength="2" 
                               placeholder="00" title="00=All, RE=Receipt, PA=Payment, CO=Contra, JO=Journal">
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">(00=All)</small>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-primary px-4" onclick="printReport()">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4 ms-2">
                            Exit (Esc)
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.financial.voucher-printing") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Uppercase for voucher type input
    document.querySelector('input[name="voucher_type"]').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Handle Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-body hr {
    border-color: #000;
}
</style>
@endpush
