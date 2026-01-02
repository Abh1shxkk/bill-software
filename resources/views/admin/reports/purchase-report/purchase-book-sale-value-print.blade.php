<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book With Sale Value Report</title>
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
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Purchase Book With Sale Value Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
        <p>
            Tagged Parties: {{ $taggedParties ?? 'N' }} | Remove Tags: {{ $removeTags ?? 'N' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th style="width: 75px;">Date</th>
                <th style="width: 90px;">Bill No</th>
                <th style="width: 60px;">Code</th>
                <th>Supplier Name</th>
                <th class="text-right" style="width: 90px;">Purchase Amt</th>
                <th class="text-right" style="width: 90px;">Sale Value</th>
                <th class="text-right" style="width: 80px;">Margin</th>
                <th class="text-right" style="width: 65px;">Margin %</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; @endphp
            @forelse($purchases ?? [] as $purchase)
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $purchase->bill_date->format('d/m/Y') }}</td>
                <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->supplier->code ?? '' }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->sale_value ?? 0, 2) }}</td>
                <td class="text-right {{ ($purchase->margin ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($purchase->margin ?? 0, 2) }}
                </td>
                <td class="text-right {{ ($purchase->margin_percent ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($purchase->margin_percent ?? 0, 2) }}%
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">Grand Total: {{ $totals['count'] ?? 0 }} Bills</td>
                <td class="text-right">{{ number_format($totals['purchase_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['sale_value'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['margin'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
