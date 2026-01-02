<!DOCTYPE html>
<html>
<head>
    <title>Party Wise Sale Report</title>
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
        .party-header { background: #d4edda; font-weight: bold; }
        .bill-row { font-size: 11px; color: #666; }
        .total-row { background: #333; color: #fff; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Party Wise Sale</h3>
        <div class="info">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Party Name</th>
                <th>Area</th>
                <th class="text-center">Bills</th>
                <th class="text-end">NT Amount</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax</th>
                <th class="text-end">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $srNo = 0; @endphp
            @foreach($groupedSales as $customerId => $customerSales)
                @php
                    $srNo++;
                    $firstSale = $customerSales->first();
                    $customerTotal = [
                        'nt_amount' => $customerSales->sum('nt_amount'),
                        'dis_amount' => $customerSales->sum('dis_amount'),
                        'tax_amount' => $customerSales->sum('tax_amount'),
                        'net_amount' => $customerSales->sum('net_amount'),
                    ];
                @endphp
                <tr class="party-header">
                    <td>{{ $srNo }}</td>
                    <td>{{ $firstSale->customer->code ?? '' }}</td>
                    <td>{{ $firstSale->customer->name ?? 'N/A' }}</td>
                    <td>{{ $firstSale->customer->area_name ?? '-' }}</td>
                    <td class="text-center">{{ $customerSales->count() }}</td>
                    <td class="text-end">{{ number_format($customerTotal['nt_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($customerTotal['dis_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($customerTotal['tax_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($customerTotal['net_amount'], 2) }}</td>
                </tr>
                @if($billWise === 'Y')
                    @foreach($customerSales as $sale)
                    <tr class="bill-row">
                        <td></td>
                        <td>{{ $sale->sale_date->format('d-m') }}</td>
                        <td colspan="2">{{ $sale->series }}{{ $sale->invoice_no }}</td>
                        <td></td>
                        <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-end">Grand Total:</td>
                <td class="text-center">{{ $totals['count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
