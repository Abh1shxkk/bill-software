<!DOCTYPE html>
<html>
<head>
    <title>Margin-Wise Items - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #333; font-size: 16px; }
        .header p { margin: 5px 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #333; color: white; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: red; }
        .text-success { color: green; }
        .footer { margin-top: 15px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <div class="header">
        <h2>Margin-Wise Items Report</h2>
        <p>Margin Range: {{ request('margin_from', 0) }}% - {{ request('margin_to', 29) }}%</p>
        <p>Generated on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Packing</th>
                <th class="text-end">Pur. Rate</th>
                <th class="text-end">Sale Rate</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Cost</th>
                <th class="text-end">Margin %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['packing'] }}</td>
                <td class="text-end">{{ number_format($row['pur_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cost'], 2) }}</td>
                <td class="text-end {{ $row['margin'] < 10 ? 'text-danger' : ($row['margin'] > 20 ? 'text-success' : '') }}">
                    {{ number_format($row['margin'], 2) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <strong>Total Records: {{ $reportData->count() }}</strong>
    </div>
</body>
</html>
