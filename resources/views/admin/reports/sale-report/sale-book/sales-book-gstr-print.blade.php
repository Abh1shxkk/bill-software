<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book GSTR - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 1.2; background: #fff; color: #000; }
        .container { max-width: 100%; padding: 8px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 3px; }
        .header .company-name { font-size: 12px; font-weight: bold; }
        .header .report-info { font-size: 9px; margin-top: 3px; }
        .filters-info { background: #f0f0f0; padding: 4px 8px; margin-bottom: 8px; font-size: 9px; border: 1px solid #ccc; }
        .filters-info span { margin-right: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 2px 4px; text-align: left; }
        th { background: #d0d0d0; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .grand-total { background: #b0b0b0; font-weight: bold; }
        .summary-box { margin-top: 12px; border: 2px solid #000; padding: 8px; width: 450px; }
        .summary-box h3 { font-size: 11px; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 4px; }
        .print-btn, .close-btn { position: fixed; top: 8px; padding: 8px 16px; color: #fff; border: none; cursor: pointer; font-size: 12px; border-radius: 4px; }
        .print-btn { right: 8px; background: #28a745; }
        .close-btn { right: 90px; background: #dc3545; }
        @media print {
            .print-btn, .close-btn { display: none; }
            body { font-size: 8px; }
            table { font-size: 7px; }
            @page { size: A4 landscape; margin: 8mm; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    <button class="close-btn" onclick="window.close()">✕ Close</button>

    <div class="container">
        <div class="header">
            <div class="company-name">{{ config('app.name', 'PRABHAT MEDICINE COMPANY') }}</div>
            <h1>SALE BOOK GSTR</h1>
            <div class="report-info">
                Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
                | Generated: {{ now()->format('d-M-Y h:i A') }}
            </div>
        </div>

        <div class="filters-info">
            <span><strong>Type:</strong> 
                @switch($reportType ?? '8')
                    @case('1') Sale @break
                    @case('2') Sale Return @break
                    @case('3') Debit Note @break
                    @case('4') Credit Note @break
                    @case('5') Consolidated @break
                    @case('6') All CN_DN @break
                    @case('7') Expiry Sale @break
                    @case('8') Voucher Sale @break
                @endswitch
            </span>
            <span><strong>Local/Central:</strong> {{ $localCentral ?? 'Both' }}</span>
            <span><strong>GSTN:</strong> {{ $gstnFilter == '1' ? 'With' : ($gstnFilter == '2' ? 'Without' : 'All') }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th style="width: 65px;">Date</th>
                    <th style="width: 55px;">Bill No</th>
                    <th style="width: 40px;">Code</th>
                    <th>Party Name</th>
                    <th style="width: 110px;">GSTIN</th>
                    <th style="width: 35px;">State</th>
                    @if($showArea ?? false)<th style="width: 70px;">Area</th>@endif
                    @if($showSalesman ?? false)<th style="width: 70px;">Salesman</th>@endif
                    <th style="width: 75px;" class="text-right">Taxable</th>
                    <th style="width: 60px;" class="text-right">CGST</th>
                    <th style="width: 60px;" class="text-right">SGST</th>
                    <th style="width: 60px;" class="text-right">IGST</th>
                    <th style="width: 75px;" class="text-right">Net Amt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales ?? [] as $index => $sale)
                @php
                    $taxable = $sale->items->sum('taxable_amount') ?: $sale->items->sum('amount') ?: $sale->nt_amount;
                    $cgst = $sale->items->sum('cgst_amount') ?: 0;
                    $sgst = $sale->items->sum('sgst_amount') ?: 0;
                    $igst = $sale->items->sum('igst_amount') ?: 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale->sale_date->format('d-M-y') }}</td>
                    <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->code ?? '' }}</td>
                    <td>{{ Str::limit($sale->customer->name ?? 'N/A', 22) }}</td>
                    <td>{{ $sale->customer->gst_number ?? '-' }}</td>
                    <td class="text-center">{{ $sale->customer->state_code ?? '' }}</td>
                    @if($showArea ?? false)<td>{{ $sale->customer->area_name ?? '-' }}</td>@endif
                    @if($showSalesman ?? false)<td>{{ $sale->salesman->name ?? '-' }}</td>@endif
                    <td class="text-right">{{ number_format($taxable, 2) }}</td>
                    <td class="text-right">{{ number_format($cgst, 2) }}</td>
                    <td class="text-right">{{ number_format($sgst, 2) }}</td>
                    <td class="text-right">{{ number_format($igst, 2) }}</td>
                    <td class="text-right">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="14" class="text-center">No records found</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="grand-total">
                    <td colspan="{{ 7 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-right">
                        <strong>GRAND TOTAL ({{ number_format($totals['count'] ?? 0) }} Bills):</strong>
                    </td>
                    <td class="text-right">{{ number_format($totals['taxable_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['cgst_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['sgst_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['igst_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <h3>GST SUMMARY</h3>
            <table style="width: 100%; border: none;">
                <tr style="border: none;"><td style="border: none;">Total Bills:</td><td style="border: none;" class="text-right">{{ number_format($totals['count'] ?? 0) }}</td></tr>
                <tr style="border: none;"><td style="border: none;">Taxable Amount:</td><td style="border: none;" class="text-right">₹ {{ number_format($totals['taxable_amount'] ?? 0, 2) }}</td></tr>
                <tr style="border: none;"><td style="border: none;">CGST:</td><td style="border: none;" class="text-right">₹ {{ number_format($totals['cgst_amount'] ?? 0, 2) }}</td></tr>
                <tr style="border: none;"><td style="border: none;">SGST:</td><td style="border: none;" class="text-right">₹ {{ number_format($totals['sgst_amount'] ?? 0, 2) }}</td></tr>
                <tr style="border: none;"><td style="border: none;">IGST:</td><td style="border: none;" class="text-right">₹ {{ number_format($totals['igst_amount'] ?? 0, 2) }}</td></tr>
                <tr style="border: none;"><td style="border: none;">CESS:</td><td style="border: none;" class="text-right">₹ {{ number_format($totals['cess_amount'] ?? 0, 2) }}</td></tr>
                <tr style="border: none; border-top: 1px solid #000;"><td style="border: none;"><strong>Total Tax:</strong></td><td style="border: none;" class="text-right"><strong>₹ {{ number_format($totals['total_tax'] ?? 0, 2) }}</strong></td></tr>
                <tr style="border: none;"><td style="border: none;"><strong>Net Amount:</strong></td><td style="border: none;" class="text-right"><strong>₹ {{ number_format($totals['net_amount'] ?? 0, 2) }}</strong></td></tr>
            </table>
        </div>

        <div style="margin-top: 15px; font-size: 8px; text-align: center; color: #666;">
            Report generated on {{ now()->format('d-M-Y h:i:s A') }}
        </div>
    </div>
</body>
</html>
