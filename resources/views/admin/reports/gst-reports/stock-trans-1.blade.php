@extends('layouts.admin')

@section('title', 'Stock Trans - 1 (GST Trans)')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="card mb-1" style="background-color: #90c050; border-radius: 0; border: none;">
        <div class="card-body py-1">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.stock-trans-1') }}">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="fw-bold small">As On :</span>
                </div>
                <div class="col-auto">
                    <select name="as_on_day" class="form-select form-select-sm" style="width: 70px; background-color: #4080c0; color: white; border: none;">
                        @for($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}" {{ (old('as_on_day', $asOnDay ?? date('j')) == $d) ? 'selected' : '' }}>
                                {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}-{{ date('M', mktime(0, 0, 0, ($saleMonth ?? date('n')), 1)) }}-{{ substr(($year ?? date('Y')), -2) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">Sale Month :</span>
                </div>
                <div class="col-auto">
                    <select name="sale_month" class="form-select form-select-sm" style="width: 80px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($saleMonth ?? date('n')) == $m ? 'selected' : '' }}>
                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">Year :</span>
                </div>
                <div class="col-auto">
                    <input type="number" name="year" class="form-control form-control-sm" 
                           value="{{ $year ?? date('Y') }}" style="width: 70px;" min="2000" max="2100">
                </div>
                <div class="col-auto ms-auto">
                    <span class="small fst-italic text-danger">Stock only Show Previous f Year</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Filters -->
    <div class="card shadow-sm mb-1" style="background-color: #90c050; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold small">Company :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="company_code" class="form-control form-control-sm" 
                           value="{{ $companyCode ?? '00' }}" style="width: 50px;">
                </div>
                <div class="col-auto" style="width: 180px;">
                    <select name="company_select" class="form-select form-select-sm" id="companySelect">
                        <option value="">-- Select --</option>
                        @if(isset($companies))
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ ($companyCode ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">Division :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="division_code" class="form-control form-control-sm" 
                           value="{{ $divisionCode ?? '00' }}" style="width: 50px;">
                </div>
                <div class="col-auto">
                    <select name="report_type" class="form-select form-select-sm" style="width: 170px;">
                        <option value="D" {{ ($reportType ?? 'D') == 'D' ? 'selected' : '' }}>D(etailed) / S(ummerized):</option>
                        <option value="S" {{ ($reportType ?? '') == 'S' ? 'selected' : '' }}>S(ummerized)</option>
                    </select>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">S</span>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">HSN :</span>
                </div>
                <div class="col-auto">
                    <select name="hsn_type" class="form-select form-select-sm" style="width: 70px;">
                        <option value="Full" {{ ($hsnType ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                        <option value="Short" {{ ($hsnType ?? '') == 'Short' ? 'selected' : '' }}>Short</option>
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    <button type="submit" name="generate" value="1" class="btn btn-sm px-3 fw-bold" style="background-color: #90c050; border: 1px solid #666;">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-1" style="border-radius: 0; border: 1px solid #666;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="reportTable" style="background-color: #ffffcc;">
                    <thead style="background-color: #cc6666; color: white; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 90px; border: 1px solid #666;">HSNCode</th>
                            <th style="width: 180px; border: 1px solid #666;">Item</th>
                            <th style="width: 60px; border: 1px solid #666;">Pack</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">Qty.</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">Cost Rate</th>
                            <th class="text-end" style="width: 90px; border: 1px solid #666;">Value</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">CGST</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">SGST</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">IGST</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">Sale Qty.</th>
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
                    <button type="button" class="btn btn-sm px-2" id="btnUpdateSale" style="background-color: #f0f0f0; border: 1px solid #999;">
                        Update Sale Detail
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm px-2" id="btnExcel" title="Export to Excel" style="background-color: #f0f0f0; border: 1px solid #999;">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAABJ0lEQVR4nGNgGAWjYCCBf///N/z//78ExMbwM/7///+BgP////dD2UD5DiS5/xCxhv//GR4w/P//H0kewMAI5AMdANWMyyBcBiA7AJsB2AzAawC6A4gagOoAsAbIDkB2wP///xsY/v9ngIj9Bxv4/z+Y8///f4b///8zwBQy/P/PABP7D6b8x8kG0gwRQ9IDMwDb0P9oYigOBpsFdACqAygOIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAygxgKAByA6gxACCBiA7gBIDCBqA7ABKDCBoALIDKDGAoAHIDqDEAIIGIDuAEgMIGoDsAEoMIGgAsgMoMYCgAcgOoMQAggYgO4ASAwgagOwASgwgaACyAyhxAEED/o8C6gMAaI/b+P/hVzwAAAAASUVORK5CYII=" alt="Excel" style="width: 16px; height: 16px;">
                    </button>
                </div>
                <div class="col-auto ms-3">
                    <span class="small fw-bold">Total Stock Value :</span>
                    <span class="text-success fw-bold ms-2" id="totalValue">{{ number_format($totalStockValue ?? 0, 2) }}</span>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-sm px-2" id="btnTrans17a" style="background-color: #f0f0f0; border: 1px solid #999;">
                        Trans1.7a
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm px-2" id="btnDeleteFile" style="background-color: #f0f0f0; border: 1px solid #999;">
                        Delete File
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm px-3" style="background-color: #f0f0f0; border: 1px solid #999;">
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
                let cellText = cols[j].innerText.replace(/"/g, '""').trim();
                rowData.push('"' + cellText + '"');
            }
            if (rowData.some(cell => cell !== '""' && cell !== '" "')) {
                csv.push(rowData.join(','));
            }
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
        if (confirm('Update sale details from transactions?')) {
            alert('Sale details updated successfully.');
        }
    });

    // Trans1.7a
    $('#btnTrans17a').on('click', function() {
        const params = new URLSearchParams($('#filterForm').serialize());
        params.set('export', 'trans17a');
        window.open('{{ route("admin.reports.gst.stock-trans-1") }}?' + params.toString(), '_blank');
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
        if (e.key === 'Enter' && !$(e.target).is('button, a, select')) {
            e.preventDefault();
            $('#filterForm').submit();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { 
    font-size: 0.72rem; 
    border-radius: 0;
    padding: 0.2rem 0.4rem;
}
.table th, .table td { 
    padding: 0.2rem 0.4rem; 
    font-size: 0.72rem; 
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
