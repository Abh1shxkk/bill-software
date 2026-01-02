<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book Report - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            background: #fff;
            color: #000;
        }
        .container {
            max-width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header .company-name {
            font-size: 14px;
            font-weight: bold;
        }
        .header .report-info {
            font-size: 11px;
            margin-top: 5px;
        }
        .filters-info {
            background: #f5f5f5;
            padding: 5px 10px;
            margin-bottom: 10px;
            font-size: 10px;
            border: 1px solid #ddd;
        }
        .filters-info span {
            margin-right: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
        }
        th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .day-total {
            background: #ffffcc;
            font-weight: bold;
        }
        .grand-total {
            background: #cccccc;
            font-weight: bold;
        }
        .summary-box {
            margin-top: 15px;
            border: 2px solid #000;
            padding: 10px;
        }
        .summary-box h3 {
            font-size: 12px;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        .print-btn:hover {
            background: #0056b3;
        }
        .close-btn {
            position: fixed;
            top: 10px;
            right: 100px;
            padding: 10px 20px;
            background: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        @media print {
            .print-btn, .close-btn {
                display: none;
            }
            body {
                font-size: 9px;
            }
            table {
                font-size: 8px;
            }
            th, td {
                padding: 2px 3px;
            }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    <button class="close-btn" onclick="window.close()">✕ Close</button>

    <div class="container">
        <div class="header">
            <div class="company-name">{{ config('app.name', 'PRABHAT MEDICINE COMPANY') }}</div>
            <h1>SALE BOOK</h1>
            <div class="report-info">
                Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
                | Generated: {{ now()->format('d-M-Y h:i A') }}
            </div>
        </div>

        <div class="filters-info">
            <span><strong>Report Type:</strong> 
                @switch($reportType ?? '1')
                    @case('1') Sale @break
                    @case('2') Sale Return @break
                    @case('3') Debit Note @break
                    @case('4') Credit Note @break
                    @case('5') Consolidated @break
                    @case('6') All CN_DN @break
                @endswitch
            </span>
            @if($customerId ?? false)
                <span><strong>Customer:</strong> {{ $customers->firstWhere('id', $customerId)->name ?? 'N/A' }}</span>
            @endif
            @if($salesmanId ?? false)
                <span><strong>Salesman:</strong> {{ $salesmen->firstWhere('id', $salesmanId)->name ?? 'N/A' }}</span>
            @endif
            @if($series ?? false)
                <span><strong>Series:</strong> {{ $series }}</span>
            @endif
            <span><strong>Local/Central:</strong> {{ $localCentral ?? 'Both' }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th style="width: 70px;">Date</th>
                    <th style="width: 60px;">Bill No</th>
                    <th style="width: 50px;">Code</th>
                    <th>Party Name</th>
                    @if($showArea ?? false)
                    <th style="width: 80px;">Area</th>
                    @endif
                    @if($showSalesman ?? false)
                    <th style="width: 80px;">Salesman</th>
                    @endif
                    <th style="width: 80px;" class="text-right">Gross Amt</th>
                    <th style="width: 70px;" class="text-right">Discount</th>
                    <th style="width: 70px;" class="text-right">Sch Amt</th>
                    <th style="width: 70px;" class="text-right">Tax</th>
                    <th style="width: 85px;" class="text-right">Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $currentDate = null; 
                    $dayTotal = 0; 
                    $dayCount = 0;
                    $dayGross = 0;
                    $dayDiscount = 0;
                    $dayScm = 0;
                    $dayTax = 0;
                @endphp
                
                @forelse($sales ?? [] as $index => $sale)
                    @if(($dayWiseTotal ?? 'N') == 'Y' && $currentDate !== null && $currentDate != $sale->sale_date->format('Y-m-d'))
                        <tr class="day-total">
                            <td colspan="{{ 5 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-right">
                                Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-M-Y') }}) - {{ $dayCount }} Bills:
                            </td>
                            <td class="text-right">{{ number_format($dayGross, 2) }}</td>
                            <td class="text-right">{{ number_format($dayDiscount, 2) }}</td>
                            <td class="text-right">{{ number_format($dayScm, 2) }}</td>
                            <td class="text-right">{{ number_format($dayTax, 2) }}</td>
                            <td class="text-right">{{ number_format($dayTotal, 2) }}</td>
                        </tr>
                        @php 
                            $dayTotal = 0; $dayCount = 0; $dayGross = 0; $dayDiscount = 0; $dayScm = 0; $dayTax = 0;
                        @endphp
                    @endif
                    
                    @php 
                        $currentDate = $sale->sale_date->format('Y-m-d'); 
                        $dayTotal += $sale->net_amount;
                        $dayGross += $sale->nt_amount;
                        $dayDiscount += $sale->dis_amount;
                        $dayScm += $sale->scm_amount;
                        $dayTax += $sale->tax_amount;
                        $dayCount++;
                    @endphp
                    
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $sale->sale_date->format('d-M-y') }}</td>
                        <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->customer->code ?? '' }}</td>
                        <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                        @if($showArea ?? false)
                        <td>{{ $sale->customer->area_name ?? '-' }}</td>
                        @endif
                        @if($showSalesman ?? false)
                        <td>{{ $sale->salesman->name ?? '-' }}</td>
                        @endif
                        <td class="text-right">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">No records found for selected criteria</td>
                    </tr>
                @endforelse

                @if(($dayWiseTotal ?? 'N') == 'Y' && isset($sales) && $sales->count() > 0)
                    <tr class="day-total">
                        <td colspan="{{ 5 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-right">
                            Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-M-Y') }}) - {{ $dayCount }} Bills:
                        </td>
                        <td class="text-right">{{ number_format($dayGross, 2) }}</td>
                        <td class="text-right">{{ number_format($dayDiscount, 2) }}</td>
                        <td class="text-right">{{ number_format($dayScm, 2) }}</td>
                        <td class="text-right">{{ number_format($dayTax, 2) }}</td>
                        <td class="text-right">{{ number_format($dayTotal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="grand-total">
                    <td colspan="{{ 5 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-right">
                        <strong>GRAND TOTAL ({{ number_format($totals['count'] ?? 0) }} Bills):</strong>
                    </td>
                    <td class="text-right">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <h3>SUMMARY</h3>
            <table style="width: 400px; border: none;">
                <tr style="border: none;">
                    <td style="border: none; width: 200px;">Total Bills:</td>
                    <td style="border: none;" class="text-right">{{ number_format($totals['count'] ?? 0) }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none;">Gross Amount:</td>
                    <td style="border: none;" class="text-right">₹ {{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none;">Total Discount:</td>
                    <td style="border: none;" class="text-right">₹ {{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none;">Scheme Amount:</td>
                    <td style="border: none;" class="text-right">₹ {{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none;">Tax Amount:</td>
                    <td style="border: none;" class="text-right">₹ {{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                </tr>
                <tr style="border: none; border-top: 1px solid #000;">
                    <td style="border: none;"><strong>Net Amount:</strong></td>
                    <td style="border: none;" class="text-right"><strong>₹ {{ number_format($totals['net_amount'] ?? 0, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 20px; font-size: 9px; text-align: center; color: #666;">
            Report generated on {{ now()->format('d-M-Y h:i:s A') }} | Page 1
        </div>
    </div>
</body>
</html>
