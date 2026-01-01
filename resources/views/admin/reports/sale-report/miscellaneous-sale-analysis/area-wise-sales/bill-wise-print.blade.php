<!DOCTYPE html>
<html>
<head>
    <title>Area Wise Sales - Bill Wise Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #8B0000; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .area-header { background: #d0d0d0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Area Wise Sales - Bill Wise Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Date</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th class="text-right">Gross Amt</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; $currentArea = null; @endphp
            @forelse($data ?? [] as $row)
                @if($currentArea !== $row['area_name'])
                    @php $currentArea = $row['area_name']; @endphp
                    <tr class="area-header">
                        <td colspan="7">{{ $row['area_name'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ ++$sno }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['invoice_no'] ?? '-' }}</td>
                    <td>{{ $row['customer_name'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row['gross_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($row['discount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($row['net_amount'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td class="text-right">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
