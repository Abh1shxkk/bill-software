<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Wise - Area Wise Sale Matrix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background: #e0e0e0; }
        @media print { @page { size: A4 landscape; margin: 5mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>ITEM WISE - AREA WISE SALE (MATRIX)</h2>
        <p>From: {{ $dateFrom ?? date('Y-m-d') }} To: {{ $dateTo ?? date('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Item Name</th>
                @foreach($areas ?? [] as $area)
                <th class="text-right">{{ $area->name ?? $area }}</th>
                @endforeach
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] ?? '-' }}</td>
                @foreach($areas ?? [] as $area)
                <td class="text-right">{{ number_format($row['area_' . ($area->id ?? $area)] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($row['total'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 3 + count($areas ?? []) }}" class="text-center">No data found</td>
            </tr>
            @endforelse
            @if(isset($totals))
            <tr class="total-row">
                <td colspan="2" class="text-right">Total:</td>
                @foreach($areas ?? [] as $area)
                <td class="text-right">{{ number_format($totals['area_' . ($area->id ?? $area)] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($totals['total'] ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
