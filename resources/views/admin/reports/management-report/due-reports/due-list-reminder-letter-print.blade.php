<!DOCTYPE html>
<html>
<head>
    <title>Due List Reminder Letter - Print</title>
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
        <h2>DUE LIST REMINDER LETTER</h2>
        <p>From: {{ request('from_date', '2000-04-01') }} | As On: {{ request('as_on_date', date('Y-m-d')) }}</p>
        <p>Reminder Letter Date: {{ request('reminder_date', date('Y-m-d')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Inv No.</th>
                <th>Party Name</th>
                <th class="text-end">Inv Amount</th>
                <th class="text-end">Due Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalInvAmount = 0; $totalDueAmount = 0; @endphp
            @forelse($reportData as $item)
            @php 
                $invAmount = $item->net_amount ?? 0;
                $dueAmount = $invAmount - ($item->paid_amount ?? 0);
                $totalInvAmount += $invAmount;
                $totalDueAmount += $dueAmount;
            @endphp
            <tr>
                <td>{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                <td>{{ $item->invoice_no ?? '' }}</td>
                <td>{{ $item->customer->name ?? '' }}</td>
                <td class="text-end">{{ number_format($invAmount, 2) }}</td>
                <td class="text-end">{{ number_format($dueAmount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totalInvAmount, 2) }}</th>
                <th class="text-end">{{ number_format($totalDueAmount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }} | Printed on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
</body>
</html>
