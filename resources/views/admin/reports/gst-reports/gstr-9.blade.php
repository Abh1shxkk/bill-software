@extends('layouts.admin')

@section('title', 'GSTR-9 Annual Return')

@section('content')
<div class="container-fluid p-0">
    <!-- Main Card -->
    <div class="card shadow-sm" style="max-width: 500px; margin: 50px auto; border: 2px solid #999; border-radius: 0;">
        <!-- Header -->
        <div class="card-header py-1" style="background-color: #c08080; border-bottom: 1px solid #999;">
            <div class="row align-items-center">
                <div class="col">
                    <span class="fw-bold" style="font-size: 1rem;">GSTR9</span>
                </div>
                <div class="col-auto">
                    <a href="#" class="text-primary fst-italic" id="downloadLink">Download File : GSTR9.xls</a>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="card-body py-2" style="background-color: #d4d4d4;">
            <form method="POST" id="filterForm" action="{{ route('admin.reports.gst.gstr-9') }}">
                @csrf
                
                <!-- Year and Date Range Row -->
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto">
                        <label class="small fw-bold" style="background-color: #c08080; padding: 2px 6px;">Year</label>
                    </div>
                    <div class="col-auto">
                        <select name="year" class="form-select form-select-sm" id="yearSelect" style="width: 100px; background-color: #4080c0; color: white; border: none;">
                            @foreach($years ?? [] as $y => $label)
                                <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                            @if(empty($years))
                                @for($i = 0; $i < 5; $i++)
                                    @php $y = date('Y') - $i; @endphp
                                    <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            @endif
                        </select>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="date_range" id="dateRangeCheck" 
                                   {{ ($useDateRange ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="dateRangeCheck">Date Range</label>
                        </div>
                    </div>
                </div>

                <!-- Date From/To Row -->
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto">
                        <label class="small fw-bold" style="background-color: #c08080; padding: 2px 6px;">Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" id="fromDate"
                               value="{{ $fromDate ?? date('Y-04-01') }}" style="width: 120px;" {{ ($useDateRange ?? false) ? '' : 'disabled' }}>
                    </div>
                    <div class="col-auto">
                        <label class="small fw-bold" style="background-color: #ff6666; color: white; padding: 2px 6px;">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" id="toDate"
                               value="{{ $toDate ?? date('Y-03-31', strtotime('+1 year')) }}" style="width: 120px;" {{ ($useDateRange ?? false) ? '' : 'disabled' }}>
                    </div>
                    <div class="col-auto">
                        <label class="small fw-bold" style="background-color: #c08080; padding: 2px 6px;">HSN</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn" class="form-select form-select-sm" style="width: 70px;">
                            <option value="Full" {{ ($hsnType ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Short" {{ ($hsnType ?? '') == 'Short' ? 'selected' : '' }}>Short</option>
                        </select>
                    </div>
                </div>

                <!-- Checkbox Options -->
                <div class="mb-2">
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="reduce_unreg_cndn" id="reduceUnregCndn"
                               {{ ($reduceUnregCndn ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="reduceUnregCndn">Reduce Unregistered CNDN from B2C (Small)</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="reduce_cust_expiry" id="reduceCustExpiry"
                               {{ ($reduceCustExpiry ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="reduceCustExpiry">Reduce Cust. Expiry Same As Sales Return</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="zero_rated_remove" id="zeroRatedRemove"
                               {{ ($zeroRatedRemove ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="zeroRatedRemove">Zero Rated Sale Remove In B2B</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="add_supplier_expiry" id="addSupplierExpiry"
                               {{ ($addSupplierExpiry ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="addSupplierExpiry">Add Supplier Expiry Same As Sales Bill</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="with_unreg_supplier" id="withUnregSupplier"
                               {{ ($withUnregSupplier ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="withUnregSupplier">With Unregister Supplier Purchase</label>
                    </div>
                </div>

                <!-- File Path -->
                <div class="mb-2 p-2" style="background-color: #e8e8e8; border: 1px solid #999;">
                    <span class="small fw-bold">File Path : Documents\GSTR</span>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2 justify-content-end">
                    <div class="col-auto">
                        <button type="submit" name="export" value="1" class="btn btn-sm px-4" style="background-color: #d4d4d4; border: 1px solid #666;">
                            <strong>Export</strong>
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm px-4" style="background-color: #d4d4d4; border: 1px solid #666;">
                            <strong>Exit</strong>
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
$(document).ready(function() {
    // Toggle date range fields
    $('#dateRangeCheck').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('#fromDate, #toDate').prop('disabled', !isChecked);
        
        if (!isChecked) {
            // Set dates based on selected year
            updateDatesFromYear();
        }
    });

    // Update dates when year changes
    $('#yearSelect').on('change', function() {
        if (!$('#dateRangeCheck').is(':checked')) {
            updateDatesFromYear();
        }
    });

    function updateDatesFromYear() {
        const year = parseInt($('#yearSelect').val());
        $('#fromDate').val(year + '-04-01');
        $('#toDate').val((year + 1) + '-03-31');
    }

    // Download link click
    $('#downloadLink').on('click', function(e) {
        e.preventDefault();
        $('#filterForm').find('input[name="export"]').remove();
        $('<input>').attr({
            type: 'hidden',
            name: 'export',
            value: '1'
        }).appendTo('#filterForm');
        $('#filterForm').submit();
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
        if (e.key === 'Enter' && !$(e.target).is('button, a')) {
            e.preventDefault();
            $('button[name="export"]').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { 
    font-size: 0.75rem; 
    border-radius: 0;
    padding: 0.2rem 0.4rem;
}
.form-check-input {
    margin-top: 0.2rem;
}
.form-check-label {
    font-size: 0.75rem;
}
.btn-sm { 
    font-size: 0.75rem; 
    padding: 0.25rem 0.75rem; 
    border-radius: 0;
}
.card {
    border-radius: 0;
}
</style>
@endpush
