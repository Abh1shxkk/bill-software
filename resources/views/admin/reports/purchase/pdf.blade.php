<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Report - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .summary-box { background: #f8f9fa; padding: 10px; border-radius: 5px; text-align: center; }
        .summary-box h3 { margin: 0; color: #333; }
        .summary-box p { margin: 5px 0 0; font-size: 18px; font-weight: bold; color: #198754; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-end { text-align: right; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h1>Purchase Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
        <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <div style="display: table; width: 100%; margin-bottom: 20px;">
        <div style="display: table-cell; width: 25%; padding: 5px;">
            <div class="summary-box">
                <h3>Total Purchases</h3>
                <p>₹{{ number_format($totalPurchases, 2) }}</p>
            </div>
        </div>
        <div style="display: table-cell; width: 25%; padding: 5px;">
            <div class="summary-box">
                <h3>Total Bills</h3>
                <p>{{ $purchases->count() }}</p>
            </div>
        </div>
        <div style="display: table-cell; width: 25%; padding: 5px;">
            <div class="summary-box">
                <h3>Total Tax</h3>
                <p>₹{{ number_format($totalTax, 2) }}</p>
            </div>
        </div>
        <div style="display: table-cell; width: 25%; padding: 5px;">
            <div class="summary-box">
                <h3>Avg Purchase</h3>
                <p>₹{{ $purchases->count() > 0 ? number_format($totalPurchases / $purchases->count(), 2) : '0.00' }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Supplier</th>
                <th class="text-end">NT Amount</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax</th>
                <th class="text-end">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $index => $purchase)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                <td>{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td class="text-end">₹{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                <td class="text-end">₹{{ number_format($purchase->dis_amount ?? 0, 2) }}</td>
                <td class="text-end">₹{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                <td class="text-end">₹{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8f9fa;">
                <td colspan="4">Total</td>
                <td class="text-end">₹{{ number_format($purchases->sum('nt_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($purchases->sum('dis_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($totalTax, 2) }}</td>
                <td class="text-end">₹{{ number_format($totalPurchases, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
