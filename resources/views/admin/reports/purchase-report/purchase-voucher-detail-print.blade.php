<!DOCTYPE html>
<html>
<head>
    <title>Purchase Voucher Detail</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #4169e1; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #6495ed; color: white; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #87ceeb; font-weight: bold; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-local { background: #0d6efd; color: white; }
        .badge-inter { background: #198754; color: white; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 15px; padding: 4px 8px; background: #f0f0f0; border-radius: 3px; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Purchase Voucher Detail</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Local/Inter: {{ $localInterState == 'B' ? 'Both' : ($localInterState == 'L' ? 'Local' : 'Inter State') }} |
           RCM: {{ $rcm ?? 'N' }} | HSN: {{ $hsnCode == '1' ? 'With' : 'Without' }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Total Vouchers: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Amount: ₹{{ number_format($totals['amount'] ?? 0, 2) }}</span>
        <span class="summary-item">Tax: ₹{{ number_format($totals['tax'] ?? 0, 2) }}</span>
        <span class="summary-item">Total: ₹{{ number_format($totals['total'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr.</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">Voucher No</th>
                <th style="width: 70px;">Bill No</th>
                <th>Supplier</th>
                <th style="width: 100px;">GSTN</th>
                <th class="text-center" style="width: 45px;">Type</th>
                <th class="text-center" style="width: 45px;">Items</th>
                <th class="text-right" style="width: 80px;">Amount</th>
                <th class="text-right" style="width: 65px;">Tax</th>
                <th class="text-right" style="width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers ?? [] as $index => $voucher)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $voucher->bill_date->format('d-m-Y') }}</td>
                <td>{{ $voucher->trn_no ?? '-' }}</td>
                <td>{{ $voucher->bill_no ?? '-' }}</td>
                <td>{{ $voucher->supplier->name ?? 'N/A' }}</td>
                <td>{{ $voucher->supplier->gst_no ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $voucher->is_local ? 'badge-local' : 'badge-inter' }}">
                        {{ $voucher->is_local ? 'L' : 'I' }}
                    </span>
                </td>
                <td class="text-center">{{ $voucher->item_count ?? 0 }}</td>
                <td class="text-right">{{ number_format($voucher->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($voucher->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($voucher->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8">Grand Total</td>
                <td class="text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['total'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
