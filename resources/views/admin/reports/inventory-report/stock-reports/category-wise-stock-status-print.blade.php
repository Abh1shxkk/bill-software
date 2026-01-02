<!DOCTYPE html>
<html>
<head>
    <title>Category Wise Stock Status - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; text-decoration: underline; }
        .sub-header { text-align: center; margin-bottom: 10px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #ccccff; color: #000; font-size: 11px; }
        th.sr-col { background-color: #90EE90; }
        td { font-size: 11px; }
        td.sr-col { background-color: #90EE90; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; font-size: 10px; text-align: right; }
        tfoot tr { background-color: #ffff99; font-weight: bold; }
        @media print { 
            .no-print { display: none; } 
            body { margin: 0; }
        }
        .btn { padding: 5px 15px; margin-right: 5px; cursor: pointer; }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 10px; padding: 10px; background: #f0f0f0;">
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <button class="btn" onclick="window.close()">✖ Close</button>
    </div>
    
    <div class="header">
        <h3>Category Wise Stock Status</h3>
    </div>
    
    <div class="sub-header">
        <span>Company: {{ request('company_id') ? 'Selected' : 'All' }}</span> | 
        <span>Value On: {{ ucfirst(request('value_on', 'Cost')) }} Rate</span> |
        <span>Date: {{ date('d-m-Y') }}</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center sr-col" style="width:40px;">Sr.</th>
                <th>Category Name</th>
                <th class="text-end" style="width:100px;">Qty</th>
                <th class="text-end" style="width:140px;">Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center sr-col">{{ $index + 1 }}</td>
                <td>{{ $row['category_name'] }}</td>
                <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['value'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($totals) && isset($reportData) && $reportData->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totals['total_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    <div class="footer">
        Total Records: {{ isset($reportData) ? $reportData->count() : 0 }} | 
        Printed on: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
