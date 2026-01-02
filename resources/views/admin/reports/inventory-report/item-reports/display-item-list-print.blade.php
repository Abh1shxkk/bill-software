<!DOCTYPE html>
<html>
<head>
    <title>Display Item List - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #333; font-size: 16px; }
        .header p { margin: 5px 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #dc3545; color: white; font-weight: bold; }
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
        <h2>-: Display Item List :-</h2>
        <p>From: {{ request('date_from', date('d-M-Y')) }} To: {{ request('date_to', date('d-M-Y')) }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Bill No.</th>
                <th>Code</th>
                <th>Party Name</th>
                <th>Sales Man</th>
                <th>Product</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Qty.</th>
                <th>Tag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            <tr>
                <td>{{ isset($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('d-M-y') : '' }}</td>
                <td>{{ $row['bill_no'] ?? '' }}</td>
                <td>{{ $row['code'] ?? $row['id'] ?? '' }}</td>
                <td>{{ $row['party_name'] ?? $row['name'] ?? '' }}</td>
                <td>{{ $row['salesman'] ?? '' }}</td>
                <td>{{ $row['product'] ?? $row['name'] ?? '' }}</td>
                <td class="text-end">{{ isset($row['amount']) ? number_format($row['amount'], 2) : '' }}</td>
                <td class="text-end">{{ $row['qty'] ?? $row['current_stock'] ?? '' }}</td>
                <td>{{ $row['tag'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <strong>Total Records: {{ $totals['total_records'] ?? $reportData->count() }}</strong> |
        Display Issued: {{ $totals['display_issued'] ?? 0 }} |
        Display Pending: {{ $totals['display_pending'] ?? 0 }}
    </div>
</body>
</html>
