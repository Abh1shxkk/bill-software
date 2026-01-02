<!DOCTYPE html>
<html>
<head>
    <title>Sale / Return Book Item Wise</title>
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
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .total-row { background: #333; color: #fff; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Sale / Return Book Item Wise</h3>
        <div class="info">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Company</th>
                <th class="text-end text-success">Sale Qty</th>
                <th class="text-end text-success">Sale Amt</th>
                <th class="text-end text-danger">Ret Qty</th>
                <th class="text-end text-danger">Ret Amt</th>
                <th class="text-end">Net Qty</th>
                <th class="text-end">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['item_code'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['company_name'] }}</td>
                <td class="text-end text-success">{{ number_format($item['sale_qty']) }}</td>
                <td class="text-end text-success">{{ number_format($item['sale_amount'], 2) }}</td>
                <td class="text-end text-danger">{{ number_format($item['return_qty']) }}</td>
                <td class="text-end text-danger">{{ number_format($item['return_amount'], 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($item['net_qty']) }}</td>
                <td class="text-end fw-bold">₹{{ number_format($item['net_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totals['sale_qty']) }}</td>
                <td class="text-end">{{ number_format($totals['sale_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['return_qty']) }}</td>
                <td class="text-end">{{ number_format($totals['return_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_qty']) }}</td>
                <td class="text-end">₹{{ number_format($totals['net_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
