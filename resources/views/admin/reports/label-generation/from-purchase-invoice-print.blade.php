<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Generation - From Purchase Invoice - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
        @media print {
            body { padding: 0; }
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
        <h2>Label Generation - From Purchase Invoice</h2>
    </div>

    <div class="filters">
        <strong>Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        | <strong>Total Records:</strong> {{ $reportData->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th>Pur Inv. No</th>
                <th>Inv. Date</th>
                <th>Party Name</th>
                <th class="text-end">Amount</th>
                <th>Voucher Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $invoice)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $invoice->bill_no }}</td>
                <td>{{ $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '' }}</td>
                <td>{{ $invoice->supplier?->name ?? '' }}</td>
                <td class="text-end">{{ number_format($invoice->net_amount ?? 0, 2) }}</td>
                <td>{{ $invoice->voucher_type }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
