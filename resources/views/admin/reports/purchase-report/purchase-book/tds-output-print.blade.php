<!DOCTYPE html>
<html>
<head>
    <title>TDS Output Report</title>
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
        .party-total-row { background: #e2e3e5; font-weight: bold; }
        .tds-highlight { color: #dc3545; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>TDS Output Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 75px;">Date</th>
                <th style="width: 90px;">Bill No</th>
                <th style="width: 70px;">Code</th>
                <th>Party Name</th>
                <th style="width: 100px;">Pan</th>
                <th class="text-right" style="width: 90px;">Amount</th>
                <th class="text-right" style="width: 90px;">Taxable</th>
                <th class="text-center" style="width: 60px;">TDS%</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $currentSupplier = null;
                $supplierTotals = ['amount' => 0, 'taxable' => 0, 'count' => 0];
            @endphp
            @forelse($tdsRecords ?? [] as $record)
                @if(($orderBySupplier ?? false) && $currentSupplier !== null && $currentSupplier != $record->supplier_id)
                    <tr class="party-total-row">
                        <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Bills</td>
                        <td class="text-right">{{ number_format($supplierTotals['amount'], 2) }}</td>
                        <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                        <td></td>
                    </tr>
                    @php $supplierTotals = ['amount' => 0, 'taxable' => 0, 'count' => 0]; @endphp
                @endif
                @php 
                    $currentSupplier = $record->supplier_id;
                    $supplierTotals['amount'] += $record->nt_amount ?? 0;
                    $supplierTotals['taxable'] += $record->nt_amount ?? 0;
                    $supplierTotals['count']++;
                @endphp
            <tr>
                <td>{{ $record->bill_date->format('d/m/Y') }}</td>
                <td>{{ $record->voucher_type ?? '' }}{{ $record->bill_no }}</td>
                <td>{{ $record->supplier->code ?? '' }}</td>
                <td>{{ $record->supplier->name ?? 'N/A' }}</td>
                <td style="font-size: 9px;">{{ $record->supplier->pan ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($record->nt_amount ?? 0, 2) }}</td>
                <td class="text-center tds-highlight">{{ number_format($record->tds_rate ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No data found</td></tr>
            @endforelse

            @if(($orderBySupplier ?? false) && isset($tdsRecords) && $tdsRecords->count() > 0)
                <tr class="party-total-row">
                    <td colspan="5" class="text-right">Party Total: {{ $supplierTotals['count'] }} Bills</td>
                    <td class="text-right">{{ number_format($supplierTotals['amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($supplierTotals['taxable'], 2) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">Grand Total: {{ $totals['transactions'] ?? 0 }} Transactions</td>
                <td class="text-right">{{ number_format($totals['gross'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
