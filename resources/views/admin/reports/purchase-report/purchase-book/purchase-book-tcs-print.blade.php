<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book With TCS Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #856404; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #fff3cd; font-weight: bold; text-align: left; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #ffeeba; font-weight: bold; }
        .party-total-row { background: #e2e3e5; font-weight: bold; }
        .tcs-highlight { color: #856404; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Purchase Book With TCS Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
        <p>
            TCS Filter: {{ ($tcsFilter ?? '1') == '1' ? 'With TCS' : (($tcsFilter ?? '') == '2' ? 'Without TCS' : 'All') }}
            @if($panNo ?? false) | PAN: {{ $panNo }} @endif
            @if($gstNo ?? false) | GST: {{ $gstNo }} @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 75px;">Date</th>
                <th style="width: 90px;">Trn No</th>
                <th style="width: 70px;">Party Code</th>
                <th>Party Name</th>
                <th style="width: 100px;">Pan No.</th>
                <th class="text-right" style="width: 90px;">Taxable</th>
                <th class="text-right" style="width: 80px;">Tax Amt</th>
                <th class="text-center" style="width: 60px;">TCS %</th>
                <th class="text-right" style="width: 80px;">TCS Amt</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $currentSupplier = null;
                $supplierTotals = ['taxable' => 0, 'tax' => 0, 'tcs' => 0, 'count' => 0];
            @endphp
            @forelse($purchases ?? [] as $purchase)
                @if(($orderBy ?? 'bill_wise') == 'supplier_wise' && $currentSupplier !== null && $currentSupplier != $purchase->supplier_id)
                    <tr class="party-total-row">
                        <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Bills</td>
                        <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['tax'], 2) }}</td>
                        <td></td>
                        <td class="text-right">{{ number_format($supplierTotals['tcs'], 2) }}</td>
                    </tr>
                    @php $supplierTotals = ['taxable' => 0, 'tax' => 0, 'tcs' => 0, 'count' => 0]; @endphp
                @endif
                @php 
                    $currentSupplier = $purchase->supplier_id;
                    $supplierTotals['taxable'] += $purchase->nt_amount ?? 0;
                    $supplierTotals['tax'] += $purchase->tax_amount ?? 0;
                    $supplierTotals['tcs'] += $purchase->tcs_amount ?? 0;
                    $supplierTotals['count']++;
                @endphp
            <tr>
                <td>{{ $purchase->bill_date->format('d/m/Y') }}</td>
                <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->supplier->code ?? '' }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td style="font-size: 9px;">{{ $purchase->supplier->pan ?? '-' }}</td>
                <td class="text-right">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($purchase->tcs_rate ?? 0, 3) }}</td>
                <td class="text-right tcs-highlight">{{ number_format($purchase->tcs_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">No data found</td></tr>
            @endforelse

            @if(($orderBy ?? 'bill_wise') == 'supplier_wise' && isset($purchases) && $purchases->count() > 0)
                <tr class="party-total-row">
                    <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Bills</td>
                    <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['tax'], 2) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($supplierTotals['tcs'], 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">Grand Total: {{ $totals['bills'] ?? 0 }} Bills</td>
                <td class="text-right">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td></td>
                <td class="text-right">{{ number_format($totals['tcs'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
