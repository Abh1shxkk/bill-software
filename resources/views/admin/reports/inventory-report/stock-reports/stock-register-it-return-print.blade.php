<!DOCTYPE html>
<html>
<head>
    <title>Stock Register for IT Return - Print</title>
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
        <h3>Stock Register for IT Return</h3>
        <p>Financial Year: {{ request('financial_year', '2025-26') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:30px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th class="text-end">Opening Qty</th>
                <th class="text-end">Opening Value</th>
                <th class="text-end">Closing Qty</th>
                <th class="text-end">Closing Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td class="text-end">{{ number_format($row['opening_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['opening_value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['closing_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['closing_value'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Printed on: {{ date('d-m-Y H:i:s') }}</div>
</body>
</html>
