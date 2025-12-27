<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book Extra Charges - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #6f42c1; padding-bottom: 10px; }
        .header h2 { color: #6f42c1; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10px; }
        .filters { background: #e2d5f1; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 3px 5px; text-align: left; }
        th { background: #343a40; color: white; font-size: 9px; }
        td { font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        tfoot tr { background: #343a40; color: white; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <h2>SALE BOOK EXTRA CHARGES</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Format:</strong> {{ $reportFormat == 'D' ? 'Detailed' : 'Summarised' }}</span>
        <span><strong>L/C:</strong> {{ $localCentral == 'B' ? 'Both' : ($localCentral == 'L' ? 'Local' : 'Central') }}</span>
        <span><strong>GSTN:</strong> {{ $gstnFilter == '1' ? 'With GSTN' : ($gstnFilter == '2' ? 'Without GSTN' : 'All') }}</span>
        @if($orderByCustomer ?? false)<span><strong>Order:</strong> By Customer</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">#</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 60px;">Bill No</th>
                <th style="width: 45px;">Code</th>
                <th>Party Name</th>
                <th class="text-end" style="width: 70px;">NT Amt</th>
                <th class="text-end" style="width: 60px;">Disc</th>
                <th class="text-end" style="width: 60px;">Scheme</th>
                <th class="text-end" style="width: 50px;">SC</th>
                <th class="text-end" style="width: 50px;">FT</th>
                <th class="text-end" style="width: 60px;">Tax</th>
                <th class="text-end" style="width: 50px;">TCS</th>
                <th class="text-end" style="width: 75px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales ?? [] as $index => $sale)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td class="fw-bold">{{ $sale->series }}{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 18) }}</td>
                <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->sc_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->ft_amount ?? 0, 2) }}</td>
                <td class="text-end text-info">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->tcs_amount ?? 0, 2) }}</td>
                <td class="text-end fw-bold text-success">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="13" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($sales) && $sales->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['sc_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['ft_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
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
