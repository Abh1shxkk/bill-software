<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company wise Due List - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .header p { font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        td { vertical-align: middle; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 15px; text-align: right; font-weight: bold; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 20px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 8px 20px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h2>COMPANY WISE DUE LIST</h2>
        <p>From: {{ request('from_date', '01-Apr-2000') }} To: {{ request('to_date', date('d-M-Y')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">S.No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 90px;">Bill No</th>
                <th>Customer Name</th>
                <th>Company</th>
                <th style="width: 100px;">Bill Amt</th>
                <th style="width: 100px;">Due Amt</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBill = 0; $totalDue = 0; @endphp
            @forelse($reportData as $index => $item)
            @php 
                $billAmt = $item->net_amount ?? 0;
                $dueAmt = $billAmt - ($item->paid_amount ?? 0);
                $totalBill += $billAmt;
                $totalDue += $dueAmt;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                <td>{{ $item->invoice_no ?? '' }}</td>
                <td>{{ $item->customer->name ?? '' }}</td>
                <td>{{ $item->items->first()->item->company->name ?? '' }}</td>
                <td class="text-end">{{ number_format($billAmt, 2) }}</td>
                <td class="text-end">{{ number_format($dueAmt, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalBill, 2) }}</td>
                <td class="text-end">{{ number_format($totalDue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Total Records: {{ $reportData->count() }}
    </div>
</body>
</html>
