<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book With TCS - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #856404; padding-bottom: 10px; }
        .header h2 { color: #856404; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10px; }
        .filters { background: #fff3cd; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 4px 6px; text-align: left; }
        th { background: #343a40; color: white; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        tfoot tr { background: #343a40; color: white; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <h2>SALE BOOK WITH TCS</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Format:</strong> {{ $reportFormat == 'D' ? 'Detailed' : 'Summarised' }}</span>
        <span><strong>TCS Filter:</strong> {{ $tcsFilter == 'T' ? 'With TCS' : ($tcsFilter == 'W' ? 'Without TCS' : 'All') }}</span>
        <span><strong>From:</strong> {{ $fromSource == 'T' ? 'Transaction' : 'Master' }}</span>
        <span><strong>Sale Type:</strong> {{ $saleType == 'S' ? 'Sale' : ($saleType == 'R' ? 'Return' : 'Both') }}</span>
        <span><strong>L/C:</strong> {{ $localCentral == 'B' ? 'Both' : ($localCentral == 'L' ? 'Local' : 'Central') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 75px;">Date</th>
                <th style="width: 70px;">Trn No</th>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th style="width: 100px;">PAN No</th>
                <th class="text-end" style="width: 85px;">Taxable</th>
                <th class="text-end" style="width: 70px;">Tax Amt</th>
                <th class="text-center" style="width: 45px;">TCS%</th>
                <th class="text-end" style="width: 70px;">TCS Amt</th>
                <th class="text-end" style="width: 85px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales ?? [] as $index => $sale)
            @php
                $taxableAmount = $sale->nt_amount - ($sale->dis_amount ?? 0);
                $tcsPercent = ($fromSource ?? 'T') == 'T' 
                    ? ($sale->tcs_amount > 0 ? round(($sale->tcs_amount / $taxableAmount) * 100, 2) : 0)
                    : ($sale->calculated_tcs_percent ?? 0);
                $tcsAmount = ($fromSource ?? 'T') == 'T' 
                    ? ($sale->tcs_amount ?? 0)
                    : ($sale->calculated_tcs_amount ?? 0);
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td class="fw-bold">{{ $sale->series }}{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 22) }}</td>
                <td>{{ $sale->customer->pan_number ?? '-' }}</td>
                <td class="text-end">{{ number_format($taxableAmount, 2) }}</td>
                <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($tcsPercent, 2) }}%</td>
                <td class="text-end text-danger fw-bold">{{ number_format($tcsAmount, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($sales) && $sales->count() > 0)
        <tfoot>
            <tr>
                <td colspan="6" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                <td class="text-end">{{ number_format($totals['taxable_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: center;">
        Generated on {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
