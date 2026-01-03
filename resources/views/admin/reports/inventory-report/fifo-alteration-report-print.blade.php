<!DOCTYPE html>
<html>
<head>
    <title>FIFO Alteration Report - Print</title>
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
        <h3>FIFO Alteration Report</h3>
        <p>From: {{ $request->from_date ?? date('d-m-Y') }} To: {{ $request->to_date ?? date('d-m-Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Item Name</th>
                <th>Batch No</th>
                <th class="text-center">Expiry</th>
                <th class="text-end">Old Qty</th>
                <th class="text-end">New Qty</th>
                <th class="text-end">Difference</th>
                <th class="text-center">Date</th>
                <th>User</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td>{{ $row['batch_no'] ?? '' }}</td>
                <td class="text-center">{{ $row['expiry'] ?? '' }}</td>
                <td class="text-end">{{ $row['old_qty'] ?? 0 }}</td>
                <td class="text-end">{{ $row['new_qty'] ?? 0 }}</td>
                <td class="text-end">{{ $row['difference'] ?? 0 }}</td>
                <td class="text-center">{{ $row['date'] ?? '' }}</td>
                <td>{{ $row['user_name'] ?? '' }}</td>
                <td>{{ $row['remarks'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
