<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Levels Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background: #e0e0e0; }
        .group-header { font-weight: bold; background: #d0d0d0; font-size: 12px; }
        @media print { @page { size: A4 landscape; margin: 5mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>MARKETING LEVELS REPORT</h2>
        <p>From: {{ $dateFrom ?? date('Y-m-d') }} To: {{ $dateTo ?? date('Y-m-d') }}</p>
        <p>Level: {{ ucwords(str_replace('_', ' ', $level ?? 'salesman')) }}</p>
    </div>

    <div class="filters">
        @php
            $types = ['1' => 'Sale', '2' => 'Sale Return', '3' => 'Br./Exp.', '4' => 'Consolidated'];
        @endphp
        <span>Transaction Type: {{ $types[request('transaction_type', '4')] ?? 'Consolidated' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Name</th>
                <th>Code</th>
                <th class="text-right">Sale Qty</th>
                <th class="text-right">Sale Amt</th>
                <th class="text-right">Return Qty</th>
                <th class="text-right">Return Amt</th>
                <th class="text-right">Net Qty</th>
                <th class="text-right">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] ?? '-' }}</td>
                <td>{{ $row['code'] ?? '-' }}</td>
                <td class="text-right">{{ number_format($row['sale_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['sale_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['return_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['return_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['net_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['net_amount'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No data found</td>
            </tr>
            @endforelse
            @if(isset($totals))
            <tr class="total-row">
                <td colspan="3" class="text-right">Total:</td>
                <td class="text-right">{{ number_format($totals['sale_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['sale_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['return_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['return_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
