<!DOCTYPE html>
<html>
<head>
    <title>Party Wise All Purchase Details</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #1565c0; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #1565c0; color: white; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e3f2fd; font-weight: bold; }
        .text-danger { color: red; }
        .text-warning { color: orange; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 12px; padding: 3px 6px; background: #f0f0f0; border-radius: 3px; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Party Wise All Purchase Details</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Report Type: {{ ($reportType ?? 'purchase_book') == 'purchase_book' ? 'Purchase Book' : 'With GST Details' }} | With GST: {{ ($withGst ?? false) ? 'Yes' : 'No' }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Suppliers: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Bills: {{ $totals['bills'] ?? 0 }}</span>
        <span class="summary-item">Gross: ₹{{ number_format($totals['gross_amount'] ?? 0, 2) }}</span>
        <span class="summary-item">Net: ₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</span>
        <span class="summary-item">Returns: ₹{{ number_format($totals['returns'] ?? 0, 2) }}</span>
        <span class="summary-item">Net Purchase: ₹{{ number_format($totals['net_purchase'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr.</th>
                <th style="width: 50px;">Code</th>
                <th>Supplier Name</th>
                <th style="width: 70px;">City</th>
                <th class="text-center" style="width: 40px;">Bills</th>
                <th class="text-right" style="width: 70px;">Gross Amt</th>
                <th class="text-right" style="width: 60px;">Discount</th>
                @if($withGst ?? false)
                <th class="text-right" style="width: 55px;">CGST</th>
                <th class="text-right" style="width: 55px;">SGST</th>
                <th class="text-right" style="width: 55px;">IGST</th>
                @else
                <th class="text-right" style="width: 60px;">Tax</th>
                @endif
                <th class="text-right" style="width: 70px;">Net Amount</th>
                <th class="text-right" style="width: 65px;">Returns</th>
                <th class="text-right" style="width: 75px;">Net Purchase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($partyDetails ?? [] as $index => $party)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $party->supplier_code ?? '-' }}</td>
                <td>{{ $party->supplier_name ?? 'N/A' }}</td>
                <td>{{ $party->city ?? '-' }}</td>
                <td class="text-center">{{ $party->bill_count ?? 0 }}</td>
                <td class="text-right">{{ number_format($party->gross_amount ?? 0, 2) }}</td>
                <td class="text-right text-danger">{{ number_format($party->discount ?? 0, 2) }}</td>
                @if($withGst ?? false)
                <td class="text-right">{{ number_format($party->cgst ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->sgst ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->igst ?? 0, 2) }}</td>
                @else
                <td class="text-right">{{ number_format($party->tax_amount ?? 0, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($party->net_amount ?? 0, 2) }}</td>
                <td class="text-right text-warning">{{ number_format($party->returns ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($party->net_purchase ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($withGst ?? false) ? 13 : 11 }}" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">Grand Total</td>
                <td class="text-center">{{ $totals['bills'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                @if($withGst ?? false)
                <td class="text-right">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                @else
                <td class="text-right">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['returns'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_purchase'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
