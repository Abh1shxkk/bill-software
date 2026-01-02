<!DOCTYPE html>
<html>
<head>
    <title>Sales Man - Invoice - Item Wise Sale</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; color: #8B0000; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #f0f0f0; font-weight: bold; text-align: center; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .salesman-header { background: #d0d0d0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Sales Man - Invoice - Item Wise Sale</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Salesman</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Item Name</th>
                @if($withBrExpiry == 'Y')
                <th>Batch</th>
                <th>Expiry</th>
                @endif
                <th>Qty</th>
                <th>Free</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Disc</th>
                <th>Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; @endphp
            @forelse($data->groupBy('salesman_name') as $salesmanName => $salesmanData)
                <tr class="salesman-header">
                    <td colspan="{{ $withBrExpiry == 'Y' ? 14 : 12 }}">Salesman: {{ $salesmanName }}</td>
                </tr>
                @foreach($salesmanData as $row)
                <tr>
                    <td class="text-center">{{ ++$sno }}</td>
                    <td>{{ $row['salesman_name'] }}</td>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d-m-Y') }}</td>
                    <td>{{ $row['customer_name'] }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    @if($withBrExpiry == 'Y')
                    <td>{{ $row['batch_no'] }}</td>
                    <td>{{ $row['expiry_date'] != '-' ? \Carbon\Carbon::parse($row['expiry_date'])->format('m/Y') : '-' }}</td>
                    @endif
                    <td class="text-right">{{ $row['qty'] }}</td>
                    <td class="text-right">{{ $row['free_qty'] }}</td>
                    <td class="text-right">{{ number_format($row['rate'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['discount'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['net_amount'], 2) }}</td>
                </tr>
                @endforeach
            @empty
            <tr><td colspan="{{ $withBrExpiry == 'Y' ? 14 : 12 }}" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ $withBrExpiry == 'Y' ? 8 : 6 }}">Grand Total</td>
                <td class="text-right">{{ $totals['qty'] }}</td>
                <td class="text-right">{{ $totals['free_qty'] }}</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($totals['amount'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['discount'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
