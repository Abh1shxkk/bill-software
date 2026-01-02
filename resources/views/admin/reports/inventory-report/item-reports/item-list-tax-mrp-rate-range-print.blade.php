<!DOCTYPE html>
<html>
<head>
    <title>Tax / MRP / Rate Range - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #333; font-size: 16px; }
        .header p { margin: 5px 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #333; color: white; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 15px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <div class="header">
        <h2>Item List - Tax / MRP / Rate Range</h2>
        @php
            $rateTypes = ['1' => 'Sale Rate', '2' => 'MRP', '3' => 'Pur.Rate', '4' => 'Cost', '5' => 'TAX'];
            $rangeTypes = ['1' => '>=', '2' => '<=', '3' => '=', '4' => 'Range'];
        @endphp
        <p>Filter: {{ $rateTypes[request('rate_type', '1')] ?? 'Sale Rate' }} {{ $rangeTypes[request('range_type', '4')] ?? 'Range' }} {{ request('enter_value', '') }}</p>
        <p>Generated on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Packing</th>
                <th class="text-end">Sale Rate</th>
                <th class="text-end">MRP</th>
                <th class="text-end">Pur. Rate</th>
                <th class="text-end">Cost</th>
                <th class="text-end">VAT %</th>
                @if(request('with_stock') == 'Y')
                <th class="text-end">Stock</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['packing'] }}</td>
                <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
                <td class="text-end">{{ number_format($row['pur_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cost'], 2) }}</td>
                <td class="text-end">{{ number_format($row['vat_percent'], 2) }}%</td>
                @if(request('with_stock') == 'Y')
                <td class="text-end">{{ number_format($row['current_stock'] ?? 0, 0) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <strong>Total Records: {{ $reportData->count() }}</strong>
    </div>
</body>
</html>
