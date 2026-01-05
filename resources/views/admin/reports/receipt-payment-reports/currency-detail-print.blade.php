<!DOCTYPE html>
<html>
<head>
    <title>Currency Detail - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background: linear-gradient(135deg, #e2d5f7 0%, #d4c4f0 100%); padding: 10px; }
        .header h3 { margin: 0; font-size: 18px; color: #6f42c1; font-style: italic; }
        table { width: 100%; border-collapse: collapse; max-width: 500px; margin: 0 auto; }
        th, td { border: 1px solid #000; padding: 6px 10px; }
        th { background-color: #343a40; color: white; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { background-color: #343a40; color: white; font-weight: bold; }
        @media print { 
            th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Currency Detail</h3>
        <p>Date: {{ \Carbon\Carbon::parse($request->report_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Denomination</th>
                <th class="text-center" style="width: 80px;">Count</th>
                <th class="text-end" style="width: 100px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $denominations = [
                    ['value' => 2000, 'label' => '₹2000 Notes'],
                    ['value' => 500, 'label' => '₹500 Notes'],
                    ['value' => 200, 'label' => '₹200 Notes'],
                    ['value' => 100, 'label' => '₹100 Notes'],
                    ['value' => 50, 'label' => '₹50 Notes'],
                    ['value' => 20, 'label' => '₹20 Notes'],
                    ['value' => 10, 'label' => '₹10 Notes'],
                    ['value' => 5, 'label' => '₹5 Coins'],
                    ['value' => 2, 'label' => '₹2 Coins'],
                    ['value' => 1, 'label' => '₹1 Coins'],
                ];
                $totalAmount = 0;
            @endphp
            @foreach($denominations as $index => $denom)
            @php 
                $count = $reportData[$denom['value']] ?? 0;
                $amount = $denom['value'] * $count;
                $totalAmount += $amount;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $denom['label'] }}</td>
                <td class="text-center">{{ $count }}</td>
                <td class="text-end">{{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="3" class="text-end">Grand Total:</td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 10px; text-align: center;">
        Printed on: {{ date('d-M-Y h:i A') }}
    </div>
</body>
</html>
