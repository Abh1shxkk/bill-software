<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Below Cost</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background: #e0e0e0; }
        .negative { color: red; }
        @media print { @page { size: A4 landscape; margin: 5mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>SALE BELOW COST REPORT</h2>
        <p>From: {{ $dateFrom ?? date('Y-m-d') }} To: {{ $dateTo ?? date('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Party Name</th>
                <th>Item Name</th>
                <th class="text-right" style="width: 60px;">Qty</th>
                <th class="text-right" style="width: 80px;">Sale Rate</th>
                <th class="text-right" style="width: 80px;">Cost Rate</th>
                <th class="text-right" style="width: 80px;">Loss/Unit</th>
                <th class="text-right" style="width: 100px;">Total Loss</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['date'] ?? '-' }}</td>
                <td>{{ $row['bill_no'] ?? '-' }}</td>
                <td>{{ $row['party_name'] ?? '-' }}</td>
                <td>{{ $row['item_name'] ?? '-' }}</td>
                <td class="text-right">{{ number_format($row['qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['sale_rate'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['cost_rate'] ?? 0, 2) }}</td>
                <td class="text-right negative">{{ number_format($row['loss_per_unit'] ?? 0, 2) }}</td>
                <td class="text-right negative">{{ number_format($row['total_loss'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No data found</td>
            </tr>
            @endforelse
            @if(isset($totals))
            <tr class="total-row">
                <td colspan="5" class="text-right">Total:</td>
                <td class="text-right">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td colspan="3"></td>
                <td class="text-right negative">{{ number_format($totals['total_loss'] ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
