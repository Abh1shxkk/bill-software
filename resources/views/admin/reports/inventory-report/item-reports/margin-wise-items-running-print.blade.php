<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Margin Report - Running Items</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Item Margin Report - Running Items</h2>
        <p>Margin Range: {{ request('margin_from', 0) }}% - {{ request('margin_to', 100) }}%</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Packing</th>
                <th class="text-end">Pur. Rate</th>
                <th class="text-end">Sale Rate</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Stock</th>
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
                <td class="text-end">{{ number_format($row['current_stock'], 2) }}</td>
                <td class="text-end {{ $row['margin'] < 10 ? 'text-danger' : ($row['margin'] > 20 ? 'text-success' : '') }}">
                    {{ number_format($row['margin'], 2) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }}</p>
    </div>
</body>
</html>
