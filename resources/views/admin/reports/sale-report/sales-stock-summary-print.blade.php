<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Sale Summary - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
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
        .group-header { background-color: #fff3cd; font-weight: bold; }
        .group-footer { background-color: #f5f5f5; font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>-: Stock Sale Summary :-</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Type:</strong> {{ $reportType == 'S' ? 'Sale' : ($reportType == 'R' ? 'Return' : 'Challan') }}</span>
        <span><strong>Group By:</strong> {{ $groupBy == 'S' ? 'Salesman' : ($groupBy == 'A' ? 'Area' : 'Route') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">TRN. No.</th>
                <th>Party Name</th>
                <th>Sales Man</th>
                <th class="text-end" style="width: 90px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedSales ?? [] as $groupName => $sales)
                <tr class="group-header">
                    <td colspan="5">{{ $groupName }} ({{ $sales->count() }} Bills)</td>
                </tr>
                @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                    <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                    <td>{{ $sale->salesman->name ?? '' }}</td>
                    <td class="text-end">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-footer">
                    <td colspan="4" class="text-end">{{ $groupName }} Total:</td>
                    <td class="text-end">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="4" class="text-end">Grand Total ({{ $totals['count'] ?? 0 }} Bills):</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
