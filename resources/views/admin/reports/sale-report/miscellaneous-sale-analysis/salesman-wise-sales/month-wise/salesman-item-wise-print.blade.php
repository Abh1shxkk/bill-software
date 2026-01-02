<!DOCTYPE html>
<html>
<head>
    <title>Sales Man / Item - Month wise</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; margin: 5px; }
        .header { text-align: center; margin-bottom: 8px; }
        .header h3 { margin: 0; color: #8B0000; font-style: italic; font-size: 13px; }
        .header p { margin: 2px 0; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 2px 3px; }
        th { background: #f0f0f0; font-weight: bold; text-align: center; font-size: 7px; }
        td { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .salesman-header { background: #d0d0d0; font-weight: bold; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 5mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Sales Man / Item - Month wise</h3>
        <p>Year: {{ $yearFrom }} to {{ $yearTo }} | 
        Sales in: @if($salesIn == '1') Thousands @elseif($salesIn == '2') Ten Thousands @elseif($salesIn == '3') Lacs @else Actual @endif</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Apr</th>
                <th>May</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Aug</th>
                <th>Sep</th>
                <th>Oct</th>
                <th>Nov</th>
                <th>Dec</th>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $fyOrder = [4,5,6,7,8,9,10,11,12,1,2,3];
                $grandTotal = array_fill(1, 12, 0);
                $grandSum = 0;
            @endphp
            @forelse($data as $salesmanData)
                <tr class="salesman-header">
                    <td colspan="16" class="text-left">Salesman: {{ $salesmanData['salesman_name'] }}</td>
                </tr>
                @php $sno = 0; $smanTotal = array_fill(1, 12, 0); $smanSum = 0; @endphp
                @foreach($salesmanData['items'] as $item)
                <tr>
                    <td class="text-center">{{ ++$sno }}</td>
                    <td class="text-left">{{ $item['item_name'] }}</td>
                    <td class="text-left">{{ $item['company_name'] }}</td>
                    @php $rowTotal = 0; @endphp
                    @foreach($fyOrder as $m)
                        <td>{{ number_format($item['monthly'][$m] ?? 0, 2) }}</td>
                        @php 
                            $smanTotal[$m] += ($item['monthly'][$m] ?? 0);
                            $grandTotal[$m] += ($item['monthly'][$m] ?? 0);
                            $rowTotal += ($item['monthly'][$m] ?? 0);
                        @endphp
                    @endforeach
                    <td class="total-row">{{ number_format($rowTotal, 2) }}</td>
                    @php $smanSum += $rowTotal; $grandSum += $rowTotal; @endphp
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-left">Salesman Total</td>
                    @foreach($fyOrder as $m)
                        <td>{{ number_format($smanTotal[$m], 2) }}</td>
                    @endforeach
                    <td>{{ number_format($smanSum, 2) }}</td>
                </tr>
            @empty
            <tr><td colspan="16" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-left">Grand Total</td>
                @foreach($fyOrder as $m)
                    <td>{{ number_format($grandTotal[$m], 2) }}</td>
                @endforeach
                <td>{{ number_format($grandSum, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
