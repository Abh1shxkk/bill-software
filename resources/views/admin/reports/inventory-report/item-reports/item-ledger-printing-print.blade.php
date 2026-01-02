<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Ledger Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        .item-info { margin-bottom: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Item Ledger Report</h2>
        <p>From: {{ request('date_from') }} To: {{ request('date_to') }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    @if(isset($item))
    <div class="item-info">
        <strong>Item:</strong> {{ $item->name }} | 
        <strong>Company:</strong> {{ $item->company->name ?? 'N/A' }} | 
        <strong>Packing:</strong> {{ $item->packing }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Date</th>
                <th>Type</th>
                <th>Voucher No</th>
                <th>Party</th>
                <th class="text-end">Qty In</th>
                <th class="text-end">Qty Out</th>
                <th class="text-end">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ date('d-m-Y', strtotime($row['date'])) }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['voucher_no'] }}</td>
                <td>{{ $row['party'] }}</td>
                <td class="text-end text-success">{{ $row['qty_in'] > 0 ? number_format($row['qty_in'], 2) : '' }}</td>
                <td class="text-end text-danger">{{ $row['qty_out'] > 0 ? number_format($row['qty_out'], 2) : '' }}</td>
                <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }}</p>
    </div>
</body>
</html>
