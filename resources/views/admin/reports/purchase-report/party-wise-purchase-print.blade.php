<!DOCTYPE html>
<html>
<head>
    <title>Party Wise Purchase Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #dc3545; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f8d7da; font-weight: bold; text-align: left; color: #721c24; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f5c6cb; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Party Wise Purchase Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
        <p>
            Sort By: {{ $sortBy == 'P' ? 'Party' : 'Amount' }} ({{ $sortOrder == 'A' ? 'Ascending' : 'Descending' }})
            @if($amountGreater) | Amount > {{ number_format($amountGreater, 2) }} @endif
            @if($amountLessEqual) | Amount <= {{ number_format($amountLessEqual, 2) }} @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th style="width: 60px;">Code</th>
                <th>Supplier Name</th>
                @if(($printAddress ?? 'N') == 'Y')
                <th>Address</th>
                @endif
                @if(($printStaxNo ?? 'N') == 'Y')
                <th style="width: 120px;">GST No</th>
                @endif
                <th style="width: 90px;">Mobile</th>
                <th class="text-center" style="width: 50px;">Bills</th>
                <th class="text-right" style="width: 85px;">Gross Amt</th>
                <th class="text-right" style="width: 75px;">Discount</th>
                <th class="text-right" style="width: 75px;">Tax</th>
                <th class="text-right" style="width: 90px;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; @endphp
            @forelse($partyWise ?? [] as $party)
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $party->code ?? '-' }}</td>
                <td>{{ $party->name ?? 'N/A' }}</td>
                @if(($printAddress ?? 'N') == 'Y')
                <td style="font-size: 9px;">{{ $party->address ?? '' }}</td>
                @endif
                @if(($printStaxNo ?? 'N') == 'Y')
                <td style="font-size: 9px;">{{ $party->gst_no ?? '' }}</td>
                @endif
                <td>{{ $party->mobile ?? '-' }}</td>
                <td class="text-center">{{ $party->bill_count ?? 0 }}</td>
                <td class="text-right">{{ number_format($party->gross_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->discount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ 11 + (($printAddress ?? 'N') == 'Y' ? 1 : 0) + (($printStaxNo ?? 'N') == 'Y' ? 1 : 0) }}" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ 5 + (($printAddress ?? 'N') == 'Y' ? 1 : 0) + (($printStaxNo ?? 'N') == 'Y' ? 1 : 0) }}">
                    Grand Total: {{ $totals['count'] ?? 0 }} Parties
                </td>
                <td class="text-center">{{ $totals['bills'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
