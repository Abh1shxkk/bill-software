<!DOCTYPE html>
<html>
<head>
    <title>User Work Summary - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { 
            body { margin: 0; } 
            .header { background-color: #ffc4d0 !important; -webkit-print-color-adjust: exact; } 
            th, .totals { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>USER WORK SUMMARY</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">S.No</th>
                <th>User Name</th>
                <th class="text-center" style="width: 80px;">Sales</th>
                <th class="text-center" style="width: 80px;">Purchases</th>
                <th class="text-center" style="width: 80px;">Sale Returns</th>
                <th class="text-center" style="width: 80px;">Pur. Returns</th>
                <th class="text-center" style="width: 80px;">Receipts</th>
                <th class="text-center" style="width: 80px;">Payments</th>
                <th class="text-center" style="width: 70px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalSales = 0; $totalPurchases = 0; $totalSaleReturns = 0;
                $totalPurchaseReturns = 0; $totalReceipts = 0; $totalPayments = 0; $grandTotal = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalSales += $row['sales'];
                $totalPurchases += $row['purchases'];
                $totalSaleReturns += $row['sale_returns'];
                $totalPurchaseReturns += $row['purchase_returns'];
                $totalReceipts += $row['receipts'];
                $totalPayments += $row['payments'];
                $grandTotal += $row['total'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['user_name'] }}</td>
                <td class="text-center">{{ $row['sales'] }}</td>
                <td class="text-center">{{ $row['purchases'] }}</td>
                <td class="text-center">{{ $row['sale_returns'] }}</td>
                <td class="text-center">{{ $row['purchase_returns'] }}</td>
                <td class="text-center">{{ $row['receipts'] }}</td>
                <td class="text-center">{{ $row['payments'] }}</td>
                <td class="text-center">{{ $row['total'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="2" class="text-end">Totals:</td>
                <td class="text-center">{{ $totalSales }}</td>
                <td class="text-center">{{ $totalPurchases }}</td>
                <td class="text-center">{{ $totalSaleReturns }}</td>
                <td class="text-center">{{ $totalPurchaseReturns }}</td>
                <td class="text-center">{{ $totalReceipts }}</td>
                <td class="text-center">{{ $totalPayments }}</td>
                <td class="text-center">{{ $grandTotal }}</td>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top: 10px; font-size: 10px;">Total Users: {{ count($reportData ?? []) }}</p>
</body>
</html>
