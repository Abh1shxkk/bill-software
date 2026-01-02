<!DOCTYPE html>
<html>
<head>
    <title>List of Old Stock - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #333; color: #fff; font-size: 11px; }
        td { font-size: 11px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; font-size: 10px; text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    <div class="header">
        <h3>List of Old Stock</h3>
        <p>Days Old: {{ request('days_old', 90) }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:30px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Batch No</th>
                <th>Last Movement</th>
                <th class="text-end">Days Old</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['batch_no'] ?? '' }}</td>
                <td>{{ $row['last_movement'] ?? '' }}</td>
                <td class="text-end">{{ $row['days_old'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($row['qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Printed on: {{ date('d-m-Y H:i:s') }}</div>
</body>
</html>
