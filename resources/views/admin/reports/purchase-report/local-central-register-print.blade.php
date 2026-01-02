<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book Local Central</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #dc3545; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f8d7da; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f5c6cb; font-weight: bold; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-local { background: #0d6efd; color: white; }
        .badge-central { background: #198754; color: white; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 15px; padding: 4px 8px; background: #f0f0f0; border-radius: 3px; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>PURCHASE BOOK LOCAL CENTRAL</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Report Type: {{ $reportType == '5' ? 'Consolidated' : ($reportType == '1' ? 'Purchase' : ($reportType == '2' ? 'Return' : ($reportType == '3' ? 'Debit Note' : 'Credit Note'))) }} |
           Local/Central: {{ $localCentral == 'B' ? 'Both' : ($localCentral == 'L' ? 'Local' : 'Central') }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Total Bills: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Local: {{ $totals['local_count'] ?? 0 }}</span>
        <span class="summary-item">Central: {{ $totals['central_count'] ?? 0 }}</span>
        <span class="summary-item">Taxable: ₹{{ number_format($totals['taxable'] ?? 0, 2) }}</span>
        <span class="summary-item">Total: ₹{{ number_format($totals['total'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr.</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">Bill No</th>
                <th>Supplier</th>
                <th style="width: 100px;">GSTN</th>
                <th class="text-center" style="width: 50px;">Type</th>
                <th class="text-right" style="width: 80px;">Taxable</th>
                <th class="text-right" style="width: 65px;">CGST</th>
                <th class="text-right" style="width: 65px;">SGST</th>
                <th class="text-right" style="width: 65px;">IGST</th>
                <th class="text-right" style="width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases ?? [] as $index => $purchase)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                <td>{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td>{{ $purchase->supplier->gst_no ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $purchase->is_local ? 'badge-local' : 'badge-central' }}">
                        {{ $purchase->is_local ? 'L' : 'C' }}
                    </span>
                </td>
                <td class="text-right">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6">Grand Total</td>
                <td class="text-right">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['total'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
