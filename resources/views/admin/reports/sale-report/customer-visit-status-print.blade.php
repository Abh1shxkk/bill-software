<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Visit Status - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; color: #8B4513; }
        .header p { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        .not-visited { background-color: #fff3cd; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>-: Customer Visit Status :-</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Salesman:</strong> {{ $salesmanId ? ($salesmen->firstWhere('id', $salesmanId)->name ?? 'Selected') : 'All' }}</span>
        <span><strong>Filter:</strong> {{ $visitFilter == 'V' ? 'Visited' : ($visitFilter == 'N' ? 'Not Visited' : 'All') }}</span>
        <span><strong>Group By:</strong> {{ $groupBy == 'S' ? 'Salesman' : ($groupBy == 'A' ? 'Area' : ($groupBy == 'R' ? 'Route' : 'All')) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Code</th>
                <th>Party Name</th>
                <th>Sales Man</th>
                <th class="text-end" style="width: 80px;">No.of Bills</th>
                <th class="text-end" style="width: 100px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report ?? [] as $item)
            <tr class="{{ $item['visit_count'] == 0 ? 'not-visited' : '' }}">
                <td>{{ $item['code'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['salesman'] ?? '' }}</td>
                <td class="text-end">{{ number_format($item['visit_count']) }}</td>
                <td class="text-end fw-bold">{{ number_format($item['total_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-end">Total ({{ $totals['total_customers'] ?? 0 }} Customers):</td>
                <td class="text-end">{{ number_format($totals['total_bills'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['total_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 10px;">
        <strong>Summary:</strong> Visited: {{ $totals['visited'] ?? 0 }} | Not Visited: {{ $totals['not_visited'] ?? 0 }}
    </div>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
