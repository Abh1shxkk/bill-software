<!DOCTYPE html>
<html>
<head>
    <title>Stock and O/S Report for Bank - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        .header p { margin: 5px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        tfoot th { background-color: #e0e0e0; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>STOCK and O/S REPORT FOR BANK</h3>
        <p>As On: {{ $request->as_on_date ?? date('d-m-Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Particulars</th>
                <th class="text-end">Amount</th>
                <th class="text-end">%</th>
                <th class="text-end">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['particulars'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['percentage'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totals['total_amount'] ?? 0, 2) }}</th>
                <th></th>
                <th class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
