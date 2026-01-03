<!DOCTYPE html>
<html>
<head>
    <title>List of Expired Items - Print</title>
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
        <h3>List of Expired Items</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Batch No</th>
                <th class="text-center">Expiry Date</th>
                <th class="text-end">Qty</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td>{{ $row['company_name'] ?? '' }}</td>
                <td>{{ $row['batch_no'] ?? '' }}</td>
                <td class="text-center">{{ $row['expiry_date'] ?? '' }}</td>
                <td class="text-end">{{ $row['qty'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($row['mrp'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
