<!DOCTYPE html>
<html>
<head>
    <title>Performance Report - Print</title>
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
        <h3>Performance Report</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th>Name</th>
                <th class="text-end">Sale Amount</th>
                <th class="text-end">Return Amount</th>
                <th class="text-end">Net Amount</th>
                <th class="text-end">Target</th>
                <th class="text-end">Achievement %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['sale_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['return_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['net_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['target'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['achievement'] ?? 0, 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
