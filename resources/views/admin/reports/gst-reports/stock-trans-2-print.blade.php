<!DOCTYPE html>
<html>
<head>
    <title>Stock Trans - 2 - Print</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            margin: 20px;
            font-size: 11px;
        }
        .header { 
            background-color: #e07020; 
            font-style: italic; 
            padding: 10px; 
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #999;
            color: white;
        }
        .header h2 {
            margin: 0;
        }
        .period {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .section-headers {
            background-color: #c4a060;
            padding: 5px;
            margin-bottom: 0;
            border: 1px solid #666;
            border-bottom: none;
        }
        .section-headers table {
            width: 100%;
            border: none;
        }
        .section-headers td {
            text-align: center;
            font-weight: bold;
            border: none;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #c4a060;
            font-weight: bold;
        }
        table.data-table tbody tr {
            background-color: #ffffcc;
        }
        table.data-table tfoot tr {
            background-color: #c4a060;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            @page { margin: 0.5cm; size: landscape; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Stock Trans - 2</h2>
    </div>
    
    <div class="period">
        Month: {{ date('F', mktime(0, 0, 0, $saleMonth, 1)) }} {{ $year }} | HSN: {{ $hsnType }}
    </div>

    <div class="section-headers">
        <table>
            <tr>
                <td style="width: 35%;">Opening Stock For The TAX Period</td>
                <td style="width: 45%;">Outward Supply Made</td>
                <td style="width: 20%;">Balance Closing</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">HSN</th>
                <th style="width: 150px;">Description</th>
                <th class="text-end" style="width: 60px;">Qty.</th>
                <th class="text-end" style="width: 60px;">Qty.</th>
                <th class="text-end" style="width: 80px;">Value</th>
                <th class="text-end" style="width: 60px;">CGST</th>
                <th class="text-end" style="width: 60px;">SGST</th>
                <th class="text-end" style="width: 60px;">IGST</th>
                <th class="text-end" style="width: 70px;">ITC allowed</th>
                <th class="text-end" style="width: 60px;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $item)
            <tr>
                <td>{{ $item['hsn_code'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td class="text-end">{{ number_format($item['opening_qty'], 0) }}</td>
                <td class="text-end">{{ number_format($item['qty'], 0) }}</td>
                <td class="text-end">{{ number_format($item['value'], 2) }}</td>
                <td class="text-end">{{ number_format($item['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['itc_allowed'], 2) }}</td>
                <td class="text-end">{{ number_format($item['closing_qty'], 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-end">Total Qty:</td>
                <td class="text-end">{{ number_format($totals['opening_qty'] ?? 0, 0) }}</td>
                <td class="text-end">{{ number_format($totals['qty'] ?? 0, 0) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end">{{ number_format($totals['closing_qty'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-end">Total Value:</td>
                <td></td>
                <td></td>
                <td class="text-end">{{ number_format($totals['value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['itc_allowed'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 10px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y H:i:s') }}
    </div>
</body>
</html>
