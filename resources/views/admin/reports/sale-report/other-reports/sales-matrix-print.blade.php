<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Matrix Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { color: #0066cc; font-style: italic; margin-bottom: 5px; font-size: 16px; }
        .header .date-range { font-size: 11px; color: #333; }
        .header .filter-info { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 2px 4px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; font-size: 8px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #999; }
        .row-header { background-color: #f0f0f0; font-weight: bold; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
        .grand-total { background-color: #198754; color: white; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; }
        @media print {
            body { padding: 0; font-size: 8px; }
            .no-print { display: none; }
            th { font-size: 7px; }
            @page { size: landscape; margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h2>SALES MATRIX</h2>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
        </div>
        <div class="filter-info">
            {{ $showFor ?? 'Party' }} vs Items | {{ $valueOn ?? 'Net Sale' }} Rate
            @if($matrixType == '1') | X->Party Y->Item @else | X->Item Y->Party @endif
        </div>
    </div>

    @if(isset($matrixData) && count($matrixData) > 0)
    <table>
        <thead>
            <tr>
                <th style="min-width: 120px;">
                    @if($matrixType == '1') {{ $showFor ?? 'Party' }} @else Item @endif
                </th>
                @if($matrixType == '1')
                    @foreach($itemsList as $itemId => $itemName)
                    <th class="text-center" style="min-width: 70px;">{{ Str::limit($itemName, 12) }}</th>
                    @endforeach
                @else
                    @foreach($entitiesList as $entityId => $entityName)
                    <th class="text-center" style="min-width: 70px;">{{ Str::limit($entityName, 12) }}</th>
                    @endforeach
                @endif
                <th class="text-end" style="min-width: 80px; background-color: #ffc107; color: #000;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($matrixType == '1')
                @foreach($entitiesList as $entityId => $entityName)
                <tr>
                    <td class="row-header">{{ Str::limit($entityName, 20) }}</td>
                    @php $rowTotal = 0; @endphp
                    @foreach($itemsList as $itemId => $itemName)
                        @php 
                            $value = $matrixData[$entityId][$itemId] ?? 0;
                            $rowTotal += $value;
                        @endphp
                    <td class="text-end {{ $value > 0 ? '' : 'text-muted' }}">
                        {{ $value > 0 ? number_format($value, 0) : '-' }}
                    </td>
                    @endforeach
                    <td class="text-end total-row">{{ number_format($rowTotal, 0) }}</td>
                </tr>
                @endforeach
            @else
                @foreach($itemsList as $itemId => $itemName)
                <tr>
                    <td class="row-header">{{ Str::limit($itemName, 20) }}</td>
                    @php $rowTotal = 0; @endphp
                    @foreach($entitiesList as $entityId => $entityName)
                        @php 
                            $value = $matrixData[$itemId][$entityId] ?? 0;
                            $rowTotal += $value;
                        @endphp
                    <td class="text-end {{ $value > 0 ? '' : 'text-muted' }}">
                        {{ $value > 0 ? number_format($value, 0) : '-' }}
                    </td>
                    @endforeach
                    <td class="text-end total-row">{{ number_format($rowTotal, 0) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="fw-bold">TOTAL</td>
                @if($matrixType == '1')
                    @foreach($itemsList as $itemId => $itemName)
                    <td class="text-end">{{ number_format($totals['col_totals'][$itemId] ?? 0, 0) }}</td>
                    @endforeach
                @else
                    @foreach($entitiesList as $entityId => $entityName)
                    <td class="text-end">{{ number_format($totals['col_totals'][$entityId] ?? 0, 0) }}</td>
                    @endforeach
                @endif
                <td class="text-end grand-total">{{ number_format($totals['grand_total'] ?? 0, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Items: {{ $totals['items_count'] ?? 0 }} | 
        {{ $showFor ?? 'Parties' }}: {{ $totals['entities_count'] ?? 0 }} | 
        Grand Total: ₹{{ number_format($totals['grand_total'] ?? 0, 2) }} | 
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
    @else
    <p>No records found for the selected filters.</p>
    @endif
</body>
</html>
