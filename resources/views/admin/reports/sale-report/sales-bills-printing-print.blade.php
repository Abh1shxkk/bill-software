<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Bills Printing - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .group-header { background-color: #e3f2fd; font-weight: bold; }
        .group-footer { background-color: #f5f5f5; font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>SALES BILLS PRINTING</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Salesman:</strong> {{ $salesmanId ? ($salesmen->firstWhere('id', $salesmanId)->name ?? 'Selected') : 'All' }}</span>
        <span><strong>Format:</strong> {{ $billSalesmanWise == 'S' ? 'Salesman Wise' : 'Bill Wise' }}</span>
        @if($remarks)<span><strong>Remarks:</strong> {{ $remarks }}</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">Bill No</th>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th>Address</th>
                <th style="width: 80px;">Mobile</th>
                <th class="text-end" style="width: 70px;">Discount</th>
                <th class="text-end" style="width: 70px;">Tax</th>
                <th class="text-end" style="width: 80px;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $srNo = 0; @endphp
            @foreach($groupedSales ?? [] as $groupName => $sales)
                @if($billSalesmanWise === 'S')
                <tr class="group-header">
                    <td colspan="10">{{ $groupName }} ({{ $sales->count() }} Bills)</td>
                </tr>
                @endif
                @foreach($sales as $sale)
                @php $srNo++; @endphp
                <tr>
                    <td class="text-center">{{ $srNo }}</td>
                    <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                    <td>{{ $sale->series }}{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->code ?? '' }}</td>
                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($sale->customer->address ?? '', 35) }}</td>
                    <td>{{ $sale->customer->mobile ?? '' }}</td>
                    <td class="text-end">{{ number_format((float)($sale->dis_amount ?? 0), 2) }}</td>
                    <td class="text-end">{{ number_format((float)($sale->tax_amount ?? 0), 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                </tr>
                @endforeach
                @if($billSalesmanWise === 'S')
                <tr class="group-footer">
                    <td colspan="7" class="text-end">{{ $groupName }} Total:</td>
                    <td class="text-end">{{ number_format($sales->sum('dis_amount'), 2) }}</td>
                    <td class="text-end">{{ number_format($sales->sum('tax_amount'), 2) }}</td>
                    <td class="text-end">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
