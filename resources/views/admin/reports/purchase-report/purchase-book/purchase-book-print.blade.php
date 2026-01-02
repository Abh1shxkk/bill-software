<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #8B0000; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .day-total-row { background: #fff3cd; font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-info { color: #0dcaf0; }
        .text-success { color: #198754; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>
            @if(($reportType ?? '1') == '1')
                Purchase Book Report
            @elseif(($reportType ?? '1') == '2')
                Purchase Return Book Report
            @elseif(($reportType ?? '1') == '3')
                Debit Note Report
            @elseif(($reportType ?? '1') == '4')
                Credit Note Report
            @elseif(($reportType ?? '1') == '5')
                Consolidated Purchase Book Report
            @elseif(($reportType ?? '1') == '6')
                All CN/DN Report
            @endif
        </h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th style="width: 75px;">Date</th>
                <th style="width: 90px;">Bill No</th>
                <th>Supplier Name</th>
                @if($showArea ?? false)
                <th>Area</th>
                @endif
                <th class="text-right" style="width: 90px;">Gross Amt</th>
                <th class="text-right" style="width: 80px;">Discount</th>
                <th class="text-right" style="width: 80px;">Tax</th>
                <th class="text-right" style="width: 95px;">Net Amount</th>
                @if($withAddress ?? false)
                <th>Address</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0; 
                $currentDate = null; 
                $dayTotal = 0; 
                $dayCount = 0;
                $dayGross = 0;
                $dayDiscount = 0;
                $dayTax = 0;
            @endphp
            @forelse($purchases ?? [] as $purchase)
                @if(($dayWiseTotal ?? 'N') == 'Y' && $currentDate !== null && $currentDate != $purchase->bill_date->format('Y-m-d'))
                    <tr class="day-total-row">
                        <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}" class="text-right">
                            Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d/m/Y') }}): {{ $dayCount }} Bills
                        </td>
                        <td class="text-right">{{ number_format($dayGross, 2) }}</td>
                        <td class="text-right">{{ number_format($dayDiscount, 2) }}</td>
                        <td class="text-right">{{ number_format($dayTax, 2) }}</td>
                        <td class="text-right">{{ number_format($dayTotal, 2) }}</td>
                        @if($withAddress ?? false)<td></td>@endif
                    </tr>
                    @php $dayTotal = 0; $dayCount = 0; $dayGross = 0; $dayDiscount = 0; $dayTax = 0; @endphp
                @endif
                @php 
                    $currentDate = $purchase->bill_date->format('Y-m-d'); 
                    $dayTotal += $purchase->net_amount ?? 0;
                    $dayGross += $purchase->nt_amount ?? 0;
                    $dayDiscount += $purchase->dis_amount ?? 0;
                    $dayTax += $purchase->tax_amount ?? 0;
                    $dayCount++;
                @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $purchase->bill_date->format('d/m/Y') }}</td>
                <td>
                    @if(in_array($reportType ?? '1', ['1', '5']))
                        {{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}
                    @elseif(($reportType ?? '1') == '2')
                        <span class="text-danger">{{ $purchase->bill_no }}</span>
                    @elseif(($reportType ?? '1') == '3')
                        <span class="text-info">DN-{{ $purchase->bill_no }}</span>
                    @elseif(($reportType ?? '1') == '4')
                        <span class="text-success">CN-{{ $purchase->bill_no }}</span>
                    @elseif(($reportType ?? '1') == '6')
                        {{ $purchase->note_type ?? '' }}-{{ $purchase->bill_no }}
                    @else
                        {{ $purchase->bill_no }}
                    @endif
                </td>
                <td>
                    <small>{{ $purchase->supplier->code ?? '' }}</small>
                    {{ $purchase->supplier->name ?? $purchase->debit_party_name ?? $purchase->credit_party_name ?? '-' }}
                </td>
                @if($showArea ?? false)
                <td>{{ $purchase->supplier->area_name ?? '-' }}</td>
                @endif
                <td class="text-right">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->dis_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                @if($withAddress ?? false)
                <td style="font-size: 9px;">{{ $purchase->supplier->address ?? '' }}</td>
                @endif
            </tr>
            @empty
            <tr><td colspan="{{ 9 + (($showArea ?? false) ? 1 : 0) + (($withAddress ?? false) ? 1 : 0) }}" class="text-center">No data found</td></tr>
            @endforelse

            @if(($dayWiseTotal ?? 'N') == 'Y' && isset($purchases) && $purchases->count() > 0)
                <tr class="day-total-row">
                    <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}" class="text-right">
                        Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d/m/Y') }}): {{ $dayCount }} Bills
                    </td>
                    <td class="text-right">{{ number_format($dayGross, 2) }}</td>
                    <td class="text-right">{{ number_format($dayDiscount, 2) }}</td>
                    <td class="text-right">{{ number_format($dayTax, 2) }}</td>
                    <td class="text-right">{{ number_format($dayTotal, 2) }}</td>
                    @if($withAddress ?? false)<td></td>@endif
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}">Total: {{ $totals['count'] ?? 0 }} Bills</td>
                <td class="text-right">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                @if($withAddress ?? false)<td></td>@endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
