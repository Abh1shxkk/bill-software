<!DOCTYPE html>
<html>
<head>
    <title>Day Check List - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffffcc; padding: 10px; }
        .header h3 { margin: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #ffffcc; font-weight: bold; }
        .text-end { text-align: right; }
        .category { background-color: #3399ff; color: #fff; font-weight: bold; }
        .sub-item { background-color: #f0f0f0; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Day Check List</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Transaction</th>
                <th class="text-end" style="width: 25%;">Value</th>
                <th class="text-end" style="width: 25%;">No. Of Transactions</th>
            </tr>
        </thead>
        <tbody>
            @php $totalValue = 0; $totalCount = 0; @endphp
            @foreach($reportData ?? [] as $row)
            @php 
                $totalValue += $row['value']; 
                $totalCount += $row['count'];
            @endphp
            <tr class="{{ ($row['is_header'] ?? false) ? 'category' : 'sub-item' }}">
                <td>{{ $row['transaction'] }}</td>
                <td class="text-end">{{ number_format($row['value'], 2) }}</td>
                <td class="text-end">{{ $row['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td>Grand Total</td>
                <td class="text-end">{{ number_format($totalValue, 2) }}</td>
                <td class="text-end">{{ $totalCount }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
