@extends('layouts.admin')

@section('title', 'Stock Trans - 2')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="card mb-1" style="background-color: #e07020; border-radius: 0; border: none;">
        <div class="card-body py-1">
            <div class="row align-items-center">
                <div class="col-auto">
                    <h5 class="mb-0 fst-italic text-white" style="font-family: 'Times New Roman', serif;">Stock Trans - 2</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-1" style="background-color: #c4a060; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.stock-trans-2') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Month</label>
                    </div>
                    <div class="col-auto">
                        <select name="sale_month" class="form-select form-select-sm" style="width: 100px; background-color: #4080c0; color: white;">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($saleMonth ?? date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">Year</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" name="year" class="form-control form-control-sm" 
                               value="{{ $year ?? date('Y') }}" style="width: 80px;" min="2000" max="2100">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold small">HSN</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn_type" class="form-select form-select-sm" style="width: 80px;">
                            <option value="Full" {{ ($hsnType ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Short" {{ ($hsnType ?? '') == 'Short' ? 'selected' : '' }}>Short</option>
                        </select>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="generate" value="1" class="btn btn-warning btn-sm px-3 fw-bold" style="background-color: #e07020; border-color: #c06010;">
                            OK
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm px-3">
                            Exit
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Header Labels -->
    <div class="card shadow-sm mb-0" style="background-color: #c4a060; border-radius: 0; border: 1px solid #999; border-bottom: none;">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-4 text-center">
                    <span class="fw-bold small">Opening Stock For The TAX Period</span>
                </div>
                <div class="col-5 text-center">
                    <span class="fw-bold small">Outward Supply Made</span>
                </div>
                <div class="col-3 text-center">
                    <span class="fw-bold small">Balance Closing</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-1" style="border-radius: 0; border: 1px solid #666;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="reportTable" style="background-color: #ffffcc;">
                    <thead style="background-color: #c4a060; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 80px; border: 1px solid #666;">HSN</th>
                            <th style="width: 180px; border: 1px solid #666;">Description</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">Qty.</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">Qty.</th>
                            <th class="text-end" style="width: 90px; border: 1px solid #666;">Value</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">CGST</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">SGST</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">IGST</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">ITC allowed</th>
                            <th class="text-end" style="width: 70px; border: 1px solid #666;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($reportData) && count($reportData) > 0)
                            @foreach($reportData as $item)
                            <tr class="item-row">
                                <td style="border: 1px solid #999;">{{ $item['hsn_code'] }}</td>
                                <td style="border: 1px solid #999;">{{ $item['item_name'] }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['opening_qty'] ?? 0, 0) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['qty'] ?? 0, 0) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['value'] ?? 0, 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['cgst'] ?? 0, 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['sgst'] ?? 0, 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['igst'] ?? 0, 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['itc_allowed'] ?? 0, 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($item['closing_qty'] ?? 0, 0) }}</td>
                            </tr>
                            @endforeach
                        @else
                            @for($i = 0; $i < 8; $i++)
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

    <!-- Footer Section with Totals -->
    <div class="card shadow-sm" style="background-color: #c4a060; border-radius: 0; border: 1px solid #999;">
        <div class="card-body py-1">
            <!-- Row 1: Excel and Trans-2 buttons with Total Qty -->
            <div class="row align-items-center mb-1" style="border-bottom: 1px solid #999; padding-bottom: 4px;">
                <div class="col-auto">
                    <button type="button" class="btn btn-sm px-3" id="btnExcel" style="background-color: #f0f0f0; border: 1px solid #999;">
                        <strong>Excel</strong>
                    </button>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">Total Qty :</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small" id="totalOpeningQty">{{ number_format($totals['opening_qty'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small" id="totalQty">{{ number_format($totals['qty'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto ms-auto">
                    <span class="text-primary small" id="totalClosingQty">{{ number_format($totals['closing_qty'] ?? 0, 2) }}</span>
                </div>
            </div>
            
            <!-- Row 2: Trans-2 button with Total Value -->
            <div class="row align-items-center">
                <div class="col-auto">
                    <button type="button" class="btn btn-sm px-3" id="btnTrans2" style="background-color: #f0f0f0; border: 1px solid #999;">
                        <strong>Trans - 2</strong>
                    </button>
                </div>
                <div class="col-auto">
                    <span class="fw-bold small">Total Value :</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['value'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['value2'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['cgst'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['sgst'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['igst'] ?? 0, 2) }}</span>
                </div>
                <div class="col-auto">
                    <span class="text-primary small">{{ number_format($totals['itc_allowed'] ?? 0, 2) }}</span>
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
        
        // Add headers
        csv.push('"HSN","Description","Opening Qty","Qty","Value","CGST","SGST","IGST","ITC Allowed","Closing Qty"');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cols = row.querySelectorAll('td');
            if (cols.length > 0) {
                let rowData = [];
                for (let j = 0; j < cols.length; j++) {
                    let cellText = cols[j].innerText.trim().replace(/"/g, '""');
                    rowData.push('"' + cellText + '"');
                }
                csv.push(rowData.join(','));
            }
        }
        
        const csvContent = '\uFEFF' + csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'stock_trans_2_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    }

    // Trans-2 button
    $('#btnTrans2').on('click', function() {
        alert('Trans-2 export functionality will be implemented.');
    });
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
        if (e.key === 'Enter' && !$(e.target).is('button, a')) {
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
    font-size: 0.75rem; 
    border-radius: 0;
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
