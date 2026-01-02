<!DOCTYPE html>
<html>
<head>
    <title>Minimum / Maximum Level Items - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px 8px; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: left; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: red; }
        .text-warning { color: orange; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <div class="header">
        <h2>Minimum / Maximum Level Items Report</h2>
        <p>Generated on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th>Packing</th>
                <th class="text-end">Min Level</th>
                <th class="text-end">Max Level</th>
                <th class="text-end">Current Stock</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['packing'] }}</td>
                <td class="text-end">{{ number_format($row['min_level'], 0) }}</td>
                <td class="text-end">{{ number_format($row['max_level'], 0) }}</td>
                <td class="text-end {{ $row['current_stock'] < $row['min_level'] ? 'text-danger' : '' }}">
                    {{ number_format($row['current_stock'], 0) }}
                </td>
                <td class="text-center">
                    @if($row['current_stock'] < $row['min_level'])
                        Below Min
                    @elseif($row['current_stock'] > $row['max_level'] && $row['max_level'] > 0)
                        Above Max
                    @else
                        OK
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Total Records: {{ $reportData->count() }}
    </div>
</body>
</html>
