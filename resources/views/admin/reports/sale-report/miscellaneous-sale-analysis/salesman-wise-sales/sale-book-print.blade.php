<!DOCTYPE html>
<html>
<head>
    <title>Sales Man Wise - Sale Book</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; color: #8B0000; font-style: italic; font-size: 14px; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #f0f0f0; font-weight: bold; text-align: center; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .salesman-header { background: #d0d0d0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 8mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>SALES MAN WISE - SALE BOOK</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Type: @if($transactionType == '1') Sale @elseif($transactionType == '2') Sale Return @elseif($transactionType == '3') Debit Note @else Credit Note @endif</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-right">Gross Amt</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; @endphp
            @forelse($data->groupBy('salesman_name') as $salesmanName => $salesmanData)
                <tr class="salesman-header">
                    <td colspan="7">Salesman: {{ $salesmanName }}</td>
                </tr>
                @php $smanTotal = ['gross' => 0, 'discount' => 0, 'net' => 0]; @endphp
                @foreach($salesmanData as $row)
                <tr>
                    <td class="text-center">{{ ++$sno }}</td>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d-m-Y') }}</td>
                    <td>{{ $row['customer_name'] }}</td>
                    <td class="text-right">{{ number_format($row['gross_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['discount'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['net_amount'], 2) }}</td>
                </tr>
                @php 
                    $smanTotal['gross'] += $row['gross_amount'];
                    $smanTotal['discount'] += $row['discount'];
                    $smanTotal['net'] += $row['net_amount'];
                @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right">Salesman Total ({{ $salesmanData->count() }} Bills):</td>
                    <td class="text-right">{{ number_format($smanTotal['gross'], 2) }}</td>
                    <td class="text-right">{{ number_format($smanTotal['discount'], 2) }}</td>
                    <td class="text-right">{{ number_format($smanTotal['net'], 2) }}</td>
                </tr>
            @empty
            <tr><td colspan="7" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Grand Total ({{ $totals['bills'] }} Bills):</td>
                <td class="text-right">{{ number_format($totals['gross'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['discount'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
