<!DOCTYPE html>
<html>
<head>
    <title>Reorder on Minimum Stock Basis - Print</title>
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
        <h3>Reorder on Minimum Stock Basis</h3>
        <p>Date: {{ date('d-m-Y') }}</p>
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
                <th class="text-end">O.Ord</th>
                <th class="text-end">P.Ord</th>
                <th class="text-end">Scm</th>
                <th class="text-end">Qty</th>
                <th class="text-end">F.Qty</th>
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
                <td class="text-end">{{ $row['o_ord'] ?? 0 }}</td>
                <td class="text-end">{{ $row['p_ord'] ?? 0 }}</td>
                <td class="text-end">{{ $row['scm'] ?? 0 }}</td>
                <td class="text-end">{{ $row['qty'] ?? 0 }}</td>
                <td class="text-end">{{ $row['f_qty'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <p>SALE: {{ number_format($totals['total_sale'] ?? 0, 0) }} | CLOSING: {{ number_format($totals['total_closing'] ?? 0, 0) }} | RE-ORDER: {{ number_format($totals['total_reorder'] ?? 0, 0) }}</p>
    </div>
</body>
</html>
