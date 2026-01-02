<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book Local Central - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; color: #0066cc; }
        .header p { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 9px; }
        td { font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .local-header { background-color: #d4edda; font-weight: bold; }
        .central-header { background-color: #cce5ff; font-weight: bold; }
        .group-footer { background-color: #f5f5f5; font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>SALE BOOK LOCAL CENTRAL</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">#</th>
                <th style="width: 65px;">Date</th>
                <th style="width: 60px;">Bill No</th>
                <th style="width: 40px;">Code</th>
                <th>Party Name</th>
                <th style="width: 30px;">L/C</th>
                <th style="width: 90px;">GSTN</th>
                <th class="text-end" style="width: 70px;">NT Amt</th>
                <th class="text-end" style="width: 60px;">CGST</th>
                <th class="text-end" style="width: 60px;">SGST</th>
                <th class="text-end" style="width: 60px;">IGST</th>
                <th class="text-end" style="width: 80px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @php $srNo = 0; @endphp
            @if(isset($localSales) && $localSales->count() > 0)
            <tr class="local-header">
                <td colspan="12">LOCAL SALES ({{ $localSales->count() }} Bills)</td>
            </tr>
            @foreach($localSales as $sale)
            @php $srNo++; @endphp
            <tr>
                <td class="text-center">{{ $srNo }}</td>
                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 30) }}</td>
                <td class="text-center">L</td>
                <td>{{ $sale->customer->gst_number ?? '-' }}</td>
                <td class="text-end">{{ number_format((float)($sale->nt_amount ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($sale->cgst_amount ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($sale->sgst_amount ?? 0), 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
            </tr>
            @endforeach
            <tr class="group-footer">
                <td colspan="7" class="text-end">Local Total:</td>
                <td class="text-end">{{ number_format($totals['local']['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['local']['cgst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['local']['sgst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format($totals['local']['net_amount'] ?? 0, 2) }}</td>
            </tr>
            @endif

            @if(isset($centralSales) && $centralSales->count() > 0)
            <tr class="central-header">
                <td colspan="12">CENTRAL SALES ({{ $centralSales->count() }} Bills)</td>
            </tr>
            @foreach($centralSales as $sale)
            @php $srNo++; @endphp
            <tr>
                <td class="text-center">{{ $srNo }}</td>
                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 30) }}</td>
                <td class="text-center">C</td>
                <td>{{ $sale->customer->gst_number ?? '-' }}</td>
                <td class="text-end">{{ number_format((float)($sale->nt_amount ?? 0), 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format((float)($sale->igst_amount ?? 0), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
            </tr>
            @endforeach
            <tr class="group-footer">
                <td colspan="7" class="text-end">Central Total:</td>
                <td class="text-end">{{ number_format($totals['central']['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format($totals['central']['igst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['central']['net_amount'] ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="text-end">Grand Total ({{ $totals['total']['count'] ?? 0 }} Bills):</td>
                <td class="text-end">{{ number_format($totals['total']['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['local']['cgst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['local']['sgst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['central']['igst_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total']['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
