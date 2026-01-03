<!DOCTYPE html>
<html>
<head>
    <title>Stock Register for IT Return - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; text-decoration: underline; }
        .sub-header { text-align: center; margin-bottom: 10px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #e0e0e0; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; font-size: 9px; text-align: right; }
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
        <h3>Stock Register for IT Return</h3>
    </div>
    
    <div class="sub-header">
        <span>From: {{ request('from_date', date('d-m-Y')) }}</span> | 
        <span>To: {{ request('to_date', date('d-m-Y')) }}</span> |
        <span>Date: {{ date('d-m-Y') }}</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Item</th>
                <th>Company</th>
                <th class="text-end" style="width: 80px;">Opening</th>
                <th class="text-end" style="width: 80px;">Purchase</th>
                <th class="text-end" style="width: 80px;">Sale</th>
                <th class="text-end" style="width: 80px;">Shortage</th>
                <th class="text-end" style="width: 80px;">Closing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                <td class="text-end">{{ number_format($row['purchase'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sale'], 2) }}</td>
                <td class="text-end">{{ number_format($row['shortage'], 2) }}</td>
                <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($totals))
        <tfoot>
            <tr>
                <td colspan="3">TOTAL :</td>
                <td class="text-end">{{ number_format($totals['total_opening'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_purchase'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_sale'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_shortage'] ?? 0, 2) }}</td>
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
