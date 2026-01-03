<!DOCTYPE html>
<html>
<head>
    <title>Supplier's Pending Order - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Supplier's Pending Order</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th>Supplier Name</th>
                <th>Item Name</th>
                <th class="text-center">Order Date</th>
                <th class="text-end">Order Qty</th>
                <th class="text-end">Received Qty</th>
                <th class="text-end">Pending Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['supplier_name'] ?? '' }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td class="text-center">{{ $row['order_date'] ?? '' }}</td>
                <td class="text-end">{{ $row['order_qty'] ?? 0 }}</td>
                <td class="text-end">{{ $row['received_qty'] ?? 0 }}</td>
                <td class="text-end">{{ $row['pending_qty'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
