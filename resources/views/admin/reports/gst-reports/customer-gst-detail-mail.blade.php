@extends('layouts.admin')

@section('title', 'Customer GST Detail Mail')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="card mb-1" style="background-color: #ffc4d0; border-radius: 0; border: none;">
        <div class="card-body py-1">
            <h5 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">Customer GST Detail Mail</h5>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-1" style="background-color: #d4d4d4; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.customer-gst-detail-mail') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" id="fromDate" class="form-control form-control-sm" 
                               value="{{ $fromDate ?? date('Y-m-01') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" id="toDate" class="form-control form-control-sm" 
                               value="{{ $toDate ?? date('Y-m-d') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto">
                        <select name="gst_filter" class="form-select form-select-sm" style="width: 230px;">
                            <option value="all" {{ ($gstFilter ?? 'all') == 'all' ? 'selected' : '' }}>1.With GSTIN / 2.Without GSTIN / 3.All</option>
                            <option value="with_gstin" {{ ($gstFilter ?? '') == 'with_gstin' ? 'selected' : '' }}>1.With GSTIN</option>
                            <option value="without_gstin" {{ ($gstFilter ?? '') == 'without_gstin' ? 'selected' : '' }}>2.Without GSTIN</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="number" name="number_filter" class="form-control form-control-sm" 
                               placeholder="3" value="{{ $numberFilter ?? '3' }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white small">Show Br. Exp</span>
                            <select name="show_br_exp" class="form-select form-select-sm" style="width: 50px;">
                                <option value="Y" {{ ($showBrExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showBrExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm px-3">
                            <strong>OK</strong>
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm px-3">
                            <strong>Exit</strong>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-1" style="border-radius: 0; border: 1px solid #666;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="reportTable" style="background-color: #ffffcc;">
                    <thead style="background-color: #cc9966; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 100px; border: 1px solid #666;">ALTERCODE</th>
                            <th style="width: 280px; border: 1px solid #666;">PARTY NAME</th>
                            <th style="width: 180px; border: 1px solid #666;">GSTIN No.</th>
                            <th class="text-end" style="width: 120px; border: 1px solid #666;">AMOUNT</th>
                            <th style="width: 80px; border: 1px solid #666;">TAG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($reportData) && count($reportData) > 0)
                            @foreach($reportData as $customer)
                            <tr class="customer-row" data-id="{{ $loop->index }}" data-mobile="{{ $customer['mobile'] ?? '' }}" data-email="{{ $customer['email'] ?? '' }}">
                                <td style="border: 1px solid #999;">{{ $customer['code'] }}</td>
                                <td style="border: 1px solid #999;">{{ $customer['name'] }}</td>
                                <td style="border: 1px solid #999;">{{ $customer['gst_number'] }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($customer['balance'], 2) }}</td>
                                <td style="border: 1px solid #999;">{{ $customer['tag'] }}</td>
                            </tr>
                            @endforeach
                        @else
                            {{-- Show empty rows to match desktop app look --}}
                            @for($i = 0; $i < 12; $i++)
                            <tr>
                                <td style="border: 1px solid #999; height: 24px;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                            </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Section with Summary and Actions -->
    <div class="card shadow-sm" style="background-color: #d4d4d4; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center" id="btnExcel" title="Export to Excel">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAABJ0lEQVR4nGNgGAWjYCCBf///N/z//78ExMbwM/7///+BgP////dD2UD5DiS5/xCxhv//GR4w/P//H0kewMAI5AMdANWMyyBcBiA7AJsB2AzAawC6A4gagOoAsAbIDkB2wP///xsY/v9ngIj9Bxv4/z+Y8///f4b///8zwBQy/P/PABP7D6b8x8kG0gwRQ9IDMwDb0P9oYigOBpsFdACqAygOIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAygxgKAByA6gxACCBiA7gBIDCBqA7ABKDCBoALIDKDGAoAHIDqDEAIIGIDuAEgMIGoDsAEoMIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAyhxAEED/o8C6gMAaI/b+P/hVzwAAAAASUVORK5CYII=" alt="Excel" style="width: 16px; height: 16px;">
                    </button>
                </div>
                <div class="col-auto">
                    <span class="small">Mobile : <span class="text-danger fw-bold" id="mobileCount">{{ $summary['mobile_count'] ?? 0 }}</span></span>
                </div>
                <div class="col-auto">
                    <span class="small">Email ID : <span class="text-primary fw-bold" id="emailCount">{{ $summary['email_count'] ?? 0 }}</span> <span class="text-primary">Mail</span></span>
                </div>
                <div class="col-auto ms-auto">
                    <span class="small">Total : <span class="text-success fw-bold" id="totalAmount">{{ number_format($summary['total_amount'] ?? 0, 2) }}</span></span>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center" id="btnMail" title="Send Mail">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA7UlEQVR4nGNgoAD8//+/gcJ8/P///xtA+P///xiocsCa//9v+P//PwNE8j8DspgNiK9kAAWD4ID///+DJf7/ZwBr+v+fAVnOhgFMIhsAEQNp/s8A0gRR/J8BLPkfAoj/A4T+/wfzwWIgsf8MaAb9RzUASQ5sE9AQqM3/weJImv8jwOD/f4gBIEGI2H8GNGf/R3YAsty/////I2kGuwjJAJAjQZqQ5f///8/w//9/dL0QMX7IBoDsgBiAbPP/P/////8zwDVhi+D5IDL8/4+k5z+I9v+AgYEBbACyA5AFGFAc8J/8bMDwfxQMKQAAn11oqJCBU6QAAAAASUVORK5CYII=" alt="Mail" style="width: 16px; height: 16px;">
                        <span class="ms-1">Mail</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Row selection
    let selectedRow = null;
    
    $('#reportTable tbody').on('click', 'tr.customer-row', function() {
        $('#reportTable tbody tr').removeClass('table-active');
        $(this).addClass('table-active');
        selectedRow = $(this);
    });

    // Mail button
    $('#btnMail').on('click', function() {
        const rowsWithEmail = $('.customer-row').filter(function() {
            return $(this).data('email') && $(this).data('email').trim() !== '';
        });
        
        if (rowsWithEmail.length === 0) {
            alert('No customers with email addresses found.');
            return;
        }
        
        if (confirm('Send GST details to ' + rowsWithEmail.length + ' customers via email?')) {
            $.ajax({
                url: '{{ route("admin.reports.gst.customer-gst-detail-mail.post") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 'send_mail',
                    ...Object.fromEntries(new FormData($('#filterForm')[0]))
                },
                beforeSend: function() {
                    $('#btnMail').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Sending...');
                },
                success: function(response) {
                    alert('Emails sent successfully!');
                    $('#btnMail').prop('disabled', false).html('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA7UlEQVR4nGNgoAD8//+/gcJ8/P///xtA+P///xiocsCa//9v+P//PwNE8j8DspgNiK9kAAWD4ID///+DJf7/ZwBr+v+fAVnOhgFMIhsAEQNp/s8A0gRR/J8BLPkfAoj/A4T+/wfzwWIgsf8MaAb9RzUASQ5sE9AQqM3/weJImv8jwOD/f4gBIEGI2H8GNGf/R3YAsty/////I2kGuwjJAJAjQZqQ5f///8/w//9/dL0QMX7IBoDsgBiAbPP/P/////8zwDVhi+D5IDL8/4+k5z+I9v+AgYEBbACyA5AFGFAc8J/8bMDwfxQMKQAAn11oqJCBU6QAAAAASUVORK5CYII=" alt="Mail" style="width: 16px; height: 16px;"><span class="ms-1">Mail</span>');
                },
                error: function(xhr) {
                    alert('Error sending emails. Please try again.');
                    $('#btnMail').prop('disabled', false).html('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA7UlEQVR4nGNgoAD8//+/gcJ8/P///xtA+P///xiocsCa//9v+P//PwNE8j8DspgNiK9kAAWD4ID///+DJf7/ZwBr+v+fAVnOhgFMIhsAEQNp/s8A0gRR/J8BLPkfAoj/A4T+/wfzwWIgsf8MaAb9RzUASQ5sE9AQqM3/weJImv8jwOD/f4gBIEGI2H8GNGf/R3YAsty/////I2kGuwjJAJAjQZqQ5f///8/w//9/dL0QMX7IBoDsgBiAbPP/P/////8zwDVhi+D5IDL8/4+k5z+I9v+AgYEBbACyA5AFGFAc8J/8bMDwfxQMKQAAn11oqJCBU6QAAAAASUVORK5CYII=" alt="Mail" style="width: 16px; height: 16px;"><span class="ms-1">Mail</span>');
                }
            });
        }
    });

    // Excel export button
    $('#btnExcel').on('click', function() {
        exportToExcel();
    });
    
    function exportToExcel() {
        const table = document.getElementById('reportTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cols = row.querySelectorAll('td, th');
            let rowData = [];
            
            for (let j = 0; j < cols.length; j++) {
                let cellText = cols[j].innerText.replace(/"/g, '""');
                rowData.push('"' + cellText + '"');
            }
            
            csv.push(rowData.join(','));
        }
        
        // Create download
        const csvContent = '\uFEFF' + csv.join('\n'); // BOM for Excel
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'customer_gst_detail_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    }
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // ESC to exit
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
        // Enter to submit
        if (e.key === 'Enter' && !$(e.target).is('button')) {
            e.preventDefault();
            $('#filterForm').submit();
        }
    });
});

function printReport() {
    window.open('{{ route("admin.reports.gst.customer-gst-detail-mail") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { 
    font-size: 0.75rem; 
    border-radius: 0;
}
.form-check-label { 
    font-size: 0.75rem; 
}
.table th, .table td { 
    padding: 0.2rem 0.4rem; 
    font-size: 0.75rem; 
    vertical-align: middle; 
}
.table thead th {
    font-weight: bold;
    text-transform: uppercase;
}
.btn-sm { 
    font-size: 0.7rem; 
    padding: 0.2rem 0.5rem; 
    border-radius: 0;
}
.input-group-text {
    font-size: 0.75rem;
    padding: 0.2rem 0.4rem;
    border-radius: 0;
}
.table-active {
    background-color: #b8daff !important;
}
.customer-row:hover {
    background-color: #e9ecef;
    cursor: pointer;
}
.card {
    border-radius: 0;
}
</style>
@endpush
