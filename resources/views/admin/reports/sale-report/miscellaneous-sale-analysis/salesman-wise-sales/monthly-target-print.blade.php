<!DOCTYPE html>
<html>
<head>
    <title>Monthly Target Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; color: #8B0000; font-style: italic; font-size: 14px; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #f0f0f0; font-weight: bold; text-align: center; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .salesman-row { background: #f5f5f5; }
        .positive { color: green; }
        .negative { color: red; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>MONTHLY TARGET REPORT</h3>
        <p>Period: {{ \Carbon\Carbon::parse($monthFrom . '-01')->format('M-Y') }} to {{ \Carbon\Carbon::parse($monthTo . '-01')->format('M-Y') }}</p>
        @if($salesmanId)
            @php $selectedSalesman = $salesmen->firstWhere('id', $salesmanId); @endphp
            <p>Salesman: {{ $selectedSalesman->name ?? 'All' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Code</th>
                <th>Salesman Name</th>
                @foreach($months ?? [] as $month)
                    <th class="text-right">{{ \Carbon\Carbon::parse($month . '-01')->format('M-y') }}</th>
                @endforeach
                <th class="text-right">Total Target</th>
                <th class="text-right">Total Actual</th>
                <th class="text-right">Difference</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; @endphp
            @forelse($data as $row)
            <tr class="salesman-row">
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $row['salesman_code'] }}</td>
                <td>{{ $row['salesman_name'] }}</td>
                @foreach($row['months'] as $monthData)
                    <td class="text-right">{{ number_format($monthData['actual'], 0) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($row['total_target'], 0) }}</td>
                <td class="text-right">{{ number_format($row['total_actual'], 0) }}</td>
                <td class="text-right {{ $row['total_difference'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($row['total_difference'], 0) }}
                </td>
            </tr>
            @empty
            <tr><td colspan="{{ 6 + count($months ?? []) }}" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Grand Total:</td>
                @foreach($months ?? [] as $index => $month)
                    <td class="text-right">
                        {{ number_format($data->sum(function($row) use ($index) { return $row['months'][$index]['actual'] ?? 0; }), 0) }}
                    </td>
                @endforeach
                <td class="text-right">{{ number_format($totals['target'], 0) }}</td>
                <td class="text-right">{{ number_format($totals['actual'], 0) }}</td>
                <td class="text-right {{ $totals['difference'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($totals['difference'], 0) }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
