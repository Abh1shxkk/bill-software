<!DOCTYPE html>
<html>
<head>
    <title>Order Form Three Column - Print</title>
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
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Order Form Three Column</h3>
        <p>Date: {{ date('d-m-Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th class="text-center">Pack</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Stock</th>
                <th class="text-center">Order</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td>{{ $row['company_name'] ?? '' }}</td>
                <td class="text-center">{{ $row['pack'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['mrp'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['rate'] ?? 0, 2) }}</td>
                <td class="text-end">{{ $row['stock'] ?? 0 }}</td>
                <td class="text-center"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
