<!DOCTYPE html>
<html>
<head>
    <title>Supplier's Pending Orders - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; font-style: italic; font-family: 'Times New Roman', serif; }
        .header p { margin: 3px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .total-row { background-color: #e9ecef; font-weight: bold; }
        @media print {
            body { margin: 0; }
            @page { margin: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Supplier's Pending Orders</h3>
        <p>Period: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-d'))->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">S.No</th>
                <th class="text-center" style="width: 8%;">Date</th>
                <th class="text-center" style="width: 8%;">Order No</th>
                <th style="width: 18%;">Supplier</th>
                <th style="width: 22%;">Item Name</th>
                <th class="text-end" style="width: 8%;">Order Qty</th>
                <th class="text-end" style="width: 8%;">Recd Qty</th>
                <th class="text-end" style="width: 8%;">Pending</th>
                <th class="text-end" style="width: 8%;">Rate</th>
                <th class="text-end" style="width: 8%;">Amount</th>
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
                <td class="text-center">{{ $row['order_date'] }}</td>
                <td class="text-center">{{ $row['order_no'] }}</td>
                <td>{{ $row['supplier_name'] }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td class="text-end">{{ number_format($row['order_qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['received_qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['pending_qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalPending, 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 9px; text-align: right;">
        Printed on: {{ now()->format('d-M-Y h:i A') }} | Total Items: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
