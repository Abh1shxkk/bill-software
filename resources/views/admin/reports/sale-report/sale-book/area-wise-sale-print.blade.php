<!DOCTYPE html>
<html>
<head>
    <title>Area Wise Sale Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #8B0000; }
        .info { margin-bottom: 10px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background: #f0f0f0; text-align: left; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .area-header { background: #e0e0e0; font-weight: bold; }
        .area-total { background: #d0e8ff; }
        .grand-total { background: #333; color: #fff; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Area Wise Sale Report</h3>
        <div class="info">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Area</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedSales as $areaName => $sales)
                <tr class="area-header">
                    <td colspan="5">{{ $areaName }}</td>
                </tr>
                @foreach($sales as $sale)
                <tr>
                    <td></td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}</td>
                    <td>{{ $sale->customer->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($sale->net_amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="area-total">
                    <td colspan="4" class="text-end fw-bold">{{ $areaName }} Total:</td>
                    <td class="text-end fw-bold">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="4" class="text-end">Grand Total:</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
