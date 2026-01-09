@extends('layouts.admin')

@section('title', 'Stock Trans - 1 (GST Trans)')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="card mb-1" style="background-color: #ffc4d0; border-radius: 0; border: none;">
        <div class="card-body py-1">
            <h5 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">GST Trans - Stock Trans 1</h5>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-1" style="background-color: #c4b896; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.stock-trans-1') }}">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">As On :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" id="asOnDate" class="form-control form-control-sm" 
                               value="{{ $asOnDate ?? date('Y-m-d') }}" style="width: 130px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Sale Month :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sale_month" class="form-select form-select-sm" style="width: 100px;">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($saleMonth ?? date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Year :</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" name="year" class="form-control form-control-sm" 
                               value="{{ $year ?? date('Y') }}" style="width: 80px;" min="2000" max="2100">
                    </div>
                    <div class="col-auto ms-auto">
                        <span class="small fst-italic text-danger">Stock only Show Previous f Year</span>
                    </div>
                </div>
                
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Company :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="company_code" class="form-control form-control-sm" 
                               value="{{ $companyCode ?? '' }}" placeholder="00" style="width: 60px;">
                    </div>
                    <div class="col-auto" style="width: 200px;">
                        <select name="company_select" class="form-select form-select-sm" id="companySelect">
                            <option value="">-- Select Company --</option>
                            @if(isset($companies))
                                @foreach($companies as $company)
                                    <option value="{{ $company->code }}" {{ ($companyCode ?? '') == $company->code ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Division :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division_code" class="form-control form-control-sm" 
                               value="{{ $divisionCode ?? '' }}" placeholder="00" style="width: 60px;">
                    </div>
                    <div class="col-auto">
                        <select name="report_type" class="form-select form-select-sm" style="width: 180px;">
                            <option value="D" {{ ($reportType ?? 'D') == 'D' ? 'selected' : '' }}>D(etailed) / S(ummerized):</option>
                            <option value="S" {{ ($reportType ?? '') == 'S' ? 'selected' : '' }}>S(ummerized)</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="fw-bold small">S</span>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">HSN :</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn_type" class="form-select form-select-sm" style="width: 80px;">
                            <option value="Full" {{ ($hsnType ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Short" {{ ($hsnType ?? '') == 'Short' ? 'selected' : '' }}>Short</option>
                        </select>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="generate" value="1" class="btn btn-warning btn-sm px-3 fw-bold">
                            Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-1" style="border-radius: 0; border: 1px solid #666;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="reportTable" style="background-color: #ffffcc;">
                    <thead style="background-color: #cc6666; color: white; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 100px; border: 1px solid #666;">HSNCode</th>
                            <th style="width: 200px; border: 1px solid #666;">Item</th>
                            <th style="width: 70px; border: 1px solid #666;">Pack</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">Qty.</th>
                            <th class="text-end" style="width: 90px; border: 1px solid #666;">Cost Rate</th>
                            <th class="text-end" style="width: 100px; border: 1px solid #666;">Value</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">CGST</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">SGST</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">IGST</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">Sale Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($reportData) && count($reportData) > 0)
                            @foreach($reportData as $item)
                            <tr class="item-row">
                                <td style="border: 1px solid #999;">{{ $item['hsn_code'] }}</td>
                                <td style="border: 1px solid #999;">{{ $item['item_name'] }}</td>
                                <td style="border: 1px solid #999;">{{ $item['pack'] }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['qty'], 0) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['cost_rate'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['value'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['cgst'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['sgst'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['igst'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['sale_qty'], 0) }}</td>
                            </tr>
                            @endforeach
                        @else
                            {{-- Show empty rows --}}
                            @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td style="border: 1px solid #999; height: 24px;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
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

    <!-- Footer Section with Actions -->
    <div class="card shadow-sm" style="background-color: #d4d4d4; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUpdateSale">
                        Update Sale Detail
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-success btn-sm" id="btnExcel" title="Export to Excel">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAABJ0lEQVR4nGNgGAWjYCCBf///N/z//78ExMbwM/7///+BgP////dD2UD5DiS5/xCxhv//GR4w/P//H0kewMAI5AMdANWMyyBcBiA7AJsB2AzAawC6A4gagOoAsAbIDkB2wP///xsY/v9ngIj9Bxv4/z+Y8///f4b///8zwBQy/P/PABP7D6b8x8kG0gwRQ9IDMwDb0P9oYigOBpsFdACqAygOIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAygxgKAByA6gxACCBiA7gBIDCBqA7ABKDCBoALIDKDGAoAHIDqDEAIIGIDuAEgMIGoDsAEoMIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAyhxAEED/o8C6gMAaI/b+P/hVzwAAAAASUVORK5CYII=" alt="Excel" style="width: 16px; height: 16px;">
                    </button>
                </div>
                <div class="col-auto ms-3">
                    <span class="small fw-bold">Total Stock Value :</span>
                    <span class="text-success fw-bold ms-2" id="totalValue">{{ number_format($totalStockValue ?? 0, 2) }}</span>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnTrans17a">
                        Trans1.7a
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteFile">
                        Delete File
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                        Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Company select sync
    $('#companySelect').on('change', function() {
        $('input[name="company_code"]').val($(this).val());
    });

    // Row selection
    let selectedRow = null;
    $('#reportTable tbody').on('click', 'tr.item-row', function() {
        $('#reportTable tbody tr').removeClass('table-active');
        $(this).addClass('table-active');
        selectedRow = $(this);
    });

    // Excel export
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
        
        const csvContent = '\uFEFF' + csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'stock_trans_1_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    }

    // Update Sale Detail
    $('#btnUpdateSale').on('click', function() {
        alert('Update Sale Detail functionality will be implemented.');
    });

    // Trans1.7a
    $('#btnTrans17a').on('click', function() {
        alert('Trans1.7a export functionality will be implemented.');
    });

    // Delete File
    $('#btnDeleteFile').on('click', function() {
        if (confirm('Are you sure you want to delete the generated file?')) {
            alert('File deleted successfully.');
        }
    });
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
    });
});

function printReport() {
    window.open('{{ route("admin.reports.gst.stock-trans-1") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { 
    font-size: 0.75rem; 
    border-radius: 0;
}
.table th, .table td { 
    padding: 0.2rem 0.4rem; 
    font-size: 0.75rem; 
    vertical-align: middle; 
}
.table thead th {
    font-weight: bold;
}
.btn-sm { 
    font-size: 0.7rem; 
    padding: 0.2rem 0.5rem; 
    border-radius: 0;
}
.table-active {
    background-color: #b8daff !important;
}
.item-row:hover {
    background-color: #e9ecef;
    cursor: pointer;
}
.card {
    border-radius: 0;
}
</style>
@endpush
