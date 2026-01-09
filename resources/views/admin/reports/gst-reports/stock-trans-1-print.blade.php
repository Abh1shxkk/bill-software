<!DOCTYPE html>
<html>
<head>
    <title>Stock Trans - 1 (GST Trans) - Print</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            margin: 20px;
            font-size: 11px;
        }
        .header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 10px; 
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #999;
        }
        .header h2 {
            margin: 0;
            color: #800000;
        }
        .period {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }
        table th {
            background-color: #cc6666;
            color: white;
            font-weight: bold;
        }
        table tbody tr {
            background-color: #ffffcc;
        }
        table tfoot tr {
            background-color: #d4d4d4;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-row {
            background-color: #ffcc99 !important;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            @page { margin: 0.5cm; size: landscape; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>GST Trans - Stock Trans 1</h2>
    </div>
    
    <div class="period">
        As On: {{ \Carbon\Carbon::parse($asOnDate)->format('d-M-Y') }} | 
        Sale Month: {{ date('F', mktime(0, 0, 0, $saleMonth, 1)) }} {{ $year }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">HSNCode</th>
                <th style="width: 180px;">Item</th>
                <th style="width: 60px;">Pack</th>
                <th class="text-end" style="width: 70px;">Qty.</th>
                <th class="text-end" style="width: 80px;">Cost Rate</th>
                <th class="text-end" style="width: 90px;">Value</th>
                <th class="text-end" style="width: 70px;">CGST</th>
                <th class="text-end" style="width: 70px;">SGST</th>
                <th class="text-end" style="width: 70px;">IGST</th>
                <th class="text-end" style="width: 70px;">Sale Qty.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $item)
            <tr>
                <td>{{ $item['hsn_code'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['pack'] }}</td>
                <td class="text-end">{{ number_format($item['qty'], 0) }}</td>
                <td class="text-end">{{ number_format($item['cost_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($item['value'], 2) }}</td>
                <td class="text-end">{{ number_format($item['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['sale_qty'], 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td colspan="5" class="text-end">Total Stock Value:</td>
                <td class="text-end">{{ number_format($totalStockValue ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format(collect($reportData)->sum('cgst'), 2) }}</td>
                <td class="text-end">{{ number_format(collect($reportData)->sum('sgst'), 2) }}</td>
                <td class="text-end">{{ number_format(collect($reportData)->sum('igst'), 2) }}</td>
                <td class="text-end">{{ number_format(collect($reportData)->sum('sale_qty'), 0) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 10px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y H:i:s') }}
    </div>
</body>
</html>
