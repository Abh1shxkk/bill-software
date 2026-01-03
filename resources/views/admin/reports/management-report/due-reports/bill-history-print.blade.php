<!DOCTYPE html>
<html>
<head>
    <title>Bill History - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot th { background-color: #e0e0e0; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>BILL HISTORY</h2>
        <p>Series: {{ request('series', 'SB') }} | Bill No: {{ request('bill_no', '') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Bill No</th>
                <th>Date</th>
                <th>Party Name</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; $totalPaid = 0; $totalBalance = 0; @endphp
            @forelse($reportData as $index => $item)
            @php 
                $amount = $item->net_amount ?? 0;
                $paid = $item->paid_amount ?? 0;
                $balance = $amount - $paid;
                $totalAmount += $amount;
                $totalPaid += $paid;
                $totalBalance += $balance;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->invoice_no ?? '' }}</td>
                <td>{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                <td>{{ $item->customer->name ?? '' }}</td>
                <td class="text-end">{{ number_format($amount, 2) }}</td>
                <td class="text-end">{{ number_format($paid, 2) }}</td>
                <td class="text-end">{{ number_format($balance, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totalAmount, 2) }}</th>
                <th class="text-end">{{ number_format($totalPaid, 2) }}</th>
                <th class="text-end">{{ number_format($totalBalance, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }} | Printed on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
</body>
</html>
