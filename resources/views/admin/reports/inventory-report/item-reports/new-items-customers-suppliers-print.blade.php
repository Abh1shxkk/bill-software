<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New {{ ucfirst($reportType ?? 'Items') }} Report</title>
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
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>New {{ ucfirst($reportType ?? 'Items') }} Report</h2>
        <p>From: {{ request('date_from') }} To: {{ request('date_to') }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Name</th>
                @if(($reportType ?? 'items') == 'items')
                <th>Company</th>
                <th>Packing</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Sale Rate</th>
                @else
                <th>Address</th>
                <th>Phone</th>
                @endif
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                @if(($reportType ?? 'items') == 'items')
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['packing'] }}</td>
                <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
                <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                @else
                <td>{{ $row['address'] }}</td>
                <td>{{ $row['phone'] }}</td>
                @endif
                <td>{{ $row['created_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }}</p>
    </div>
</body>
</html>
