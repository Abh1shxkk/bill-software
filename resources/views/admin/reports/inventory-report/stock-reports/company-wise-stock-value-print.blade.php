<!DOCTYPE html>
<html>
<head>
    <title>Company Wise Stock Value - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; text-decoration: underline; }
        .sub-header { text-align: center; margin-bottom: 10px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #e0e0e0; font-size: 11px; }
        td { font-size: 11px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; font-size: 10px; text-align: right; }
        tfoot tr { background-color: #f0f0f0; font-weight: bold; color: #cc00cc; }
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
        <h3>Company Wise Stock Value</h3>
    </div>
    
    <div class="sub-header">
        <span>From: {{ request('from_date', date('d-m-Y')) }}</span> | 
        <span>To: {{ request('to_date', date('d-m-Y')) }}</span> |
        <span>Date: {{ date('d-m-Y') }}</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 250px;">Company</th>
                <th class="text-end" style="width: 130px;">Opening</th>
                <th class="text-end" style="width: 130px;">Purchase</th>
                <th class="text-end" style="width: 130px;">Sale</th>
                <th class="text-end" style="width: 130px;">Closing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $row)
            <tr>
                <td>{{ $row['company_name'] }}</td>
                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                <td class="text-end">{{ number_format($row['purchase'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sale'], 2) }}</td>
                <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($totals))
        <tfoot>
            <tr>
                <td>TOTAL :</td>
                <td class="text-end">{{ number_format($totals['total_opening'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_purchase'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_sale'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_closing'] ?? 0, 2) }}</td>
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
