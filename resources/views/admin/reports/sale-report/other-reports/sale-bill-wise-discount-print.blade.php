<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount On Sale - Bill Wise</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { color: #0066cc; font-style: italic; margin-bottom: 5px; }
        .header .date-range { font-size: 11px; color: #333; }
        .header .filter-info { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; font-size: 9px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; }
        @media print {
            body { padding: 0; font-size: 9px; }
            .no-print { display: none; }
            th { font-size: 8px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h2>DISCOUNT ON SALE - BILL WISE</h2>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
        </div>
        <div class="filter-info">
            @if(isset($discountOption))
                @if($discountOption == '1') With Discount @elseif($discountOption == '2') Without Discount @else All Bills @endif
            @endif
        </div>
    </div>

    @if(isset($sales) && $sales->count() > 0)
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th class="text-center" style="width: 70px;">Date</th>
                <th class="text-center" style="width: 70px;">Bill No</th>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th>Area</th>
                <th>Salesman</th>
                <th class="text-end" style="width: 80px;">Gross Amt</th>
                <th class="text-end" style="width: 70px;">Discount</th>
                <th class="text-center" style="width: 45px;">Dis%</th>
                <th class="text-end" style="width: 60px;">Scheme</th>
                <th class="text-end" style="width: 60px;">Tax</th>
                <th class="text-end" style="width: 80px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $index => $sale)
            @php
                $disPercent = $sale->nt_amount > 0 ? ($sale->dis_amount / $sale->nt_amount) * 100 : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td class="text-center">{{ ($sale->series ?? '') . $sale->invoice_no }}</td>
                <td>{{ $sale->customer->code ?? '' }}</td>
                <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                <td>{{ $sale->customer->area_name ?? '' }}</td>
                <td>{{ $sale->salesman->name ?? '' }}</td>
                <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                <td class="text-end {{ $sale->dis_amount > 0 ? 'text-danger fw-bold' : '' }}">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($disPercent, 1) }}%</td>
                <td class="text-end">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">TOTAL:</td>
                <td class="text-end">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($totals['dis_percent'] ?? 0, 1) }}%</td>
                <td class="text-end">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Total Bills: {{ $totals['count'] ?? 0 }} | Total Discount: ₹{{ number_format($totals['dis_amount'] ?? 0, 2) }} | Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
    @else
    <p>No records found for the selected filters.</p>
    @endif
</body>
</html>
