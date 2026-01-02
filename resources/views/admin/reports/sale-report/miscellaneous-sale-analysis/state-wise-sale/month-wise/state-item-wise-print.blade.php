<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>State / Item - Month Wise Sale Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .container { padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #8B0000; padding-bottom: 5px; }
        .header h2 { color: #8B0000; font-style: italic; margin-bottom: 3px; }
        .header p { font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .state-header { background-color: #d0d8ff; font-weight: bold; }
        @media print { body { font-size: 9px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>State / Item - Month Wise Sale Report</h2>
            <p>Year: {{ $yearFrom }} - {{ $yearTo }} | Sales In: {{ $salesInLabel ?? 'Thousand' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>State / Item</th>
                    <th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th>
                    <th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th><th>Mar</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $currentState = ''; @endphp
                @foreach($data as $row)
                    @if($currentState != $row['state_name'])
                        @php $currentState = $row['state_name']; @endphp
                        <tr class="state-header">
                            <td class="text-left" colspan="14">{{ $currentState }}</td>
                        </tr>
                    @endif
                <tr>
                    <td class="text-left" style="padding-left: 15px;">{{ $row['item_name'] }}</td>
                    <td>{{ number_format($row['apr'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['may'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['jun'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['jul'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['aug'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['sep'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['oct'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['nov'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['dec'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['jan'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['feb'] ?? 0, 0) }}</td>
                    <td>{{ number_format($row['mar'] ?? 0, 0) }}</td>
                    <td class="text-right">{{ number_format($row['total'] ?? 0, 0) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-left">Grand Total</td>
                    <td>{{ number_format($totals['apr'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['may'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['jun'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['jul'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['aug'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['sep'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['oct'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['nov'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['dec'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['jan'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['feb'] ?? 0, 0) }}</td>
                    <td>{{ number_format($totals['mar'] ?? 0, 0) }}</td>
                    <td class="text-right">{{ number_format($totals['total'] ?? 0, 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
