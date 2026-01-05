<!DOCTYPE html>
<html>
<head>
    <title>Receipt from Customer - Month Wise - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 5px; }
        .header { text-align: center; margin-bottom: 10px; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); padding: 8px; }
        .header h3 { margin: 0; font-size: 16px; color: #0d6efd; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #343a40; color: white; font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { background-color: #343a40; color: white; font-weight: bold; }
        @media print { 
            th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
            @page { size: landscape; margin: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Receipt from Customer - Month Wise</h3>
        <p>Year: {{ $request->from_year ?? date('Y') }} - {{ $request->to_year ?? date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Customer Name</th>
                <th class="text-end" style="width: 60px;">Apr</th>
                <th class="text-end" style="width: 60px;">May</th>
                <th class="text-end" style="width: 60px;">Jun</th>
                <th class="text-end" style="width: 60px;">Jul</th>
                <th class="text-end" style="width: 60px;">Aug</th>
                <th class="text-end" style="width: 60px;">Sep</th>
                <th class="text-end" style="width: 60px;">Oct</th>
                <th class="text-end" style="width: 60px;">Nov</th>
                <th class="text-end" style="width: 60px;">Dec</th>
                <th class="text-end" style="width: 60px;">Jan</th>
                <th class="text-end" style="width: 60px;">Feb</th>
                <th class="text-end" style="width: 60px;">Mar</th>
                <th class="text-end" style="width: 75px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $monthTotals = array_fill(0, 12, 0); @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $rowTotal = array_sum($row['months'] ?? []);
                $grandTotal += $rowTotal;
                foreach($row['months'] ?? [] as $mIdx => $mAmt) {
                    $monthTotals[$mIdx] += $mAmt;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['customer_name'] }}</td>
                @foreach($row['months'] ?? array_fill(0, 12, 0) as $amount)
                <td class="text-end">{{ number_format($amount, 2) }}</td>
                @endforeach
                <td class="text-end" style="font-weight: bold;">{{ number_format($rowTotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="2" class="text-end">Grand Total:</td>
                @foreach($monthTotals as $mTotal)
                <td class="text-end">{{ number_format($mTotal, 2) }}</td>
                @endforeach
                <td class="text-end">{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 9px;">
        Printed on: {{ date('d-M-Y h:i A') }} | Total Customers: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
