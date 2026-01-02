<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book GSTR Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #198754; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #d4edda; font-weight: bold; text-align: left; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #c3e6cb; font-weight: bold; }
        .party-total-row { background: #fff3cd; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>
            @if(($reportType ?? '1') == '1')
                Purchase Book GSTR Report
            @elseif(($reportType ?? '1') == '2')
                Purchase Return GSTR Report
            @elseif(($reportType ?? '1') == '3')
                Debit Note GSTR Report
            @elseif(($reportType ?? '1') == '4')
                Credit Note GSTR Report
            @elseif(($reportType ?? '1') == '5')
                Consolidated Purchase GSTR Report
            @elseif(($reportType ?? '1') == '6')
                All CN/DN GSTR Report
            @elseif(($reportType ?? '1') == '7')
                Voucher Purchase GSTR Report
            @elseif(($reportType ?? '1') == '8')
                Customer Expense GSTR Report
            @endif
        </h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
        <p>
            @if(($localCentral ?? 'B') != 'B')
                Type: {{ $localCentral == 'L' ? 'Local' : 'Central' }} |
            @endif
            @if(($gstnFilter ?? '3') != '3')
                GSTN: {{ $gstnFilter == '1' ? 'With GSTN' : 'Without GSTN' }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th style="width: 130px;">GSTN</th>
                <th>Supplier Name</th>
                <th style="width: 90px;">Invoice No</th>
                <th style="width: 75px;">Date</th>
                <th class="text-right" style="width: 85px;">Taxable</th>
                <th class="text-right" style="width: 75px;">CGST</th>
                <th class="text-right" style="width: 75px;">SGST</th>
                <th class="text-right" style="width: 75px;">IGST</th>
                <th class="text-right" style="width: 90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0; 
                $currentSupplier = null;
                $supplierTotals = ['taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0, 'total' => 0, 'count' => 0];
            @endphp
            @forelse($purchases ?? [] as $purchase)
                @if(($partyWiseTotal ?? false) && $currentSupplier !== null && $currentSupplier != $purchase->supplier_id)
                    <tr class="party-total-row">
                        <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Invoices</td>
                        <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['cgst'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['sgst'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['igst'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['total'], 2) }}</td>
                    </tr>
                    @php $supplierTotals = ['taxable' => 0, 'cgst' => 0, 'sgst' => 0, 'igst' => 0, 'total' => 0, 'count' => 0]; @endphp
                @endif
                @php 
                    $currentSupplier = $purchase->supplier_id;
                    $supplierTotals['taxable'] += $purchase->nt_amount ?? 0;
                    $supplierTotals['cgst'] += $purchase->cgst_amount ?? 0;
                    $supplierTotals['sgst'] += $purchase->sgst_amount ?? 0;
                    $supplierTotals['igst'] += $purchase->igst_amount ?? 0;
                    $supplierTotals['total'] += $purchase->net_amount ?? 0;
                    $supplierTotals['count']++;
                @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td style="font-size: 9px;">{{ $purchase->supplier->gst_no ?? '-' }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->bill_date->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No data found</td></tr>
            @endforelse

            @if(($partyWiseTotal ?? false) && isset($purchases) && $purchases->count() > 0)
                <tr class="party-total-row">
                    <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Invoices</td>
                    <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['cgst'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['sgst'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['igst'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['total'], 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">Grand Total: {{ $totals['invoices'] ?? 0 }} Invoices</td>
                <td class="text-right">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
