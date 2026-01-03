<!DOCTYPE html>
<html>
<head>
    <title>Bill Tagging - Print</title>
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
        <h2>BILL TAGGING</h2>
        <p>Tag No: {{ request('tag_no', 'All') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Bill No</th>
                <th>Date</th>
                <th>Code</th>
                <th>Party</th>
                <th>Ref</th>
                <th class="text-end">O/S Amt</th>
                <th>Tag No</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @forelse($bills as $index => $bill)
            @php $osAmt = ($bill->net_amount ?? 0) - ($bill->paid_amount ?? 0); $totalAmount += $osAmt; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $bill->invoice_no }}</td>
                <td>{{ $bill->sale_date ? date('d-M-y', strtotime($bill->sale_date)) : '' }}</td>
                <td>{{ $bill->customer->code ?? $bill->customer_id }}</td>
                <td>{{ $bill->customer->name ?? '' }}</td>
                <td>{{ $bill->reference ?? '' }}</td>
                <td class="text-end">{{ number_format($osAmt, 2) }}</td>
                <td>{{ $bill->tag_no ?? '' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totalAmount, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total Invoices: {{ $bills->count() }} | Printed on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
</body>
</html>
