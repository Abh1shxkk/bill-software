<!DOCTYPE html>
<html>
<head>
    <title>Customer's Pending Order - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Customer's Pending Orders</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th class="text-center">Date</th>
                <th class="text-center">Order No</th>
                <th>Customer Name</th>
                <th>Item Name</th>
                <th class="text-end">Order Qty</th>
                <th class="text-end">Delivered Qty</th>
                <th class="text-end">Pending Qty</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPending = 0; $totalAmount = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalPending += $row['pending_qty']; 
                $totalAmount += $row['amount'];
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row['order_date'] ?? '' }}</td>
                <td class="text-center">{{ $row['order_no'] ?? '' }}</td>
                <td>{{ $row['customer_name'] ?? '' }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['order_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['delivered_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['pending_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['rate'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['amount'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="7" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalPending, 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

