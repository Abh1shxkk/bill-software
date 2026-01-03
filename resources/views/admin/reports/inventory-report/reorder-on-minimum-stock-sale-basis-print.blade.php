<!DOCTYPE html>
<html>
<head>
    <title>Reorder on Minimum Stock & Sale Basis - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        .header p { margin: 5px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .totals { margin-top: 10px; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Reorder on Minimum Stock & Sale Basis</h3>
        <p>From: {{ $request->from_date ?? date('d-m-Y') }} To: {{ $request->to_date ?? date('d-m-Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th>Company</th>
                <th>Item Name</th>
                <th class="text-center">Pack</th>
                <th class="text-center">Unit</th>
                <th class="text-end">Min</th>
                <th class="text-end">Max</th>
                <th class="text-end">Bal</th>
                <th class="text-end">Sale</th>
                <th class="text-end">Reorder</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['company_name'] ?? '' }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td class="text-center">{{ $row['pack'] ?? '' }}</td>
                <td class="text-center">{{ $row['unit'] ?? '' }}</td>
                <td class="text-end">{{ $row['min_stock'] ?? 0 }}</td>
                <td class="text-end">{{ $row['max_stock'] ?? 0 }}</td>
                <td class="text-end">{{ $row['balance'] ?? 0 }}</td>
                <td class="text-end">{{ $row['sale'] ?? 0 }}</td>
                <td class="text-end">{{ $row['reorder'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <p>Total Items: {{ count($reportData ?? []) }}</p>
    </div>
</body>
</html>
