<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Collection Transfer Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { color: #0066cc; font-style: italic; margin-bottom: 5px; }
        .header .date-range { font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h2>CASH COLLECTION TRANSFER</h2>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
        </div>
    </div>

    @if(isset($sales) && $sales->count() > 0)
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th class="text-center" style="width: 80px;">Date</th>
                <th class="text-center" style="width: 80px;">Bill No</th>
                <th style="width: 60px;">Code</th>
                <th>Party Name</th>
                <th>Salesman</th>
                <th class="text-end" style="width: 90px;">Net Amount</th>
                <th class="text-end" style="width: 90px;">Paid Amt</th>
                <th class="text-end" style="width: 90px;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $index => $sale)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td class="text-center">{{ ($sale->series ?? '') . $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                <td>{{ $sale->salesman->name ?? '' }}</td>
                <td class="text-end">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->balance_amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">TOTAL:</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['paid_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['balance_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Total Records: {{ $totals['count'] ?? 0 }} | Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
    @else
    <p>No records found for the selected date range.</p>
    @endif
</body>
</html>
