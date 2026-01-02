<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Remarks Report - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; background: #f5f5f5; padding: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background: #333; color: #fff; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .fw-bold { font-weight: bold; }
        .table-warning { background: #fff3cd; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .bg-success { background: #198754; color: #fff; }
        .bg-warning { background: #ffc107; color: #000; }
        tfoot td { background: #333; color: #fff; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print</button>
    
    <div class="header">
        <h2>Sale Remarks Report</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="filters">
        <strong>Filters:</strong> 
        Date: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        @if($series ?? false) | Series: {{ $series }} @endif
        | Status: {{ ($pendingFilter ?? 'A') == 'P' ? 'Pending' : (($pendingFilter ?? 'A') == 'N' ? 'Non-Pending' : 'All') }}
        | Stock: {{ ($stockFilter ?? '3') == '1' ? 'With Stock' : (($stockFilter ?? '3') == '2' ? 'Without Stock' : 'All') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 80px;">Bill No</th>
                <th style="width: 60px;">Code</th>
                <th>Party Name</th>
                <th>Salesman</th>
                <th>Remarks</th>
                <th class="text-end" style="width: 80px;">Amount</th>
                <th class="text-end" style="width: 70px;">Balance</th>
                <th class="text-center" style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($remarks ?? [] as $index => $r)
            <tr class="{{ $r['status'] == 'Pending' ? 'table-warning' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['bill_no'] }}</td>
                <td>{{ $r['party_code'] }}</td>
                <td>{{ $r['party_name'] }}</td>
                <td>{{ $r['salesman'] }}</td>
                <td>{{ $r['remarks'] }}</td>
                <td class="text-end fw-bold">{{ number_format($r['amount'], 2) }}</td>
                <td class="text-end {{ $r['balance'] > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($r['balance'], 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ $r['status'] == 'Pending' ? 'bg-warning' : 'bg-success' }}">
                        {{ $r['status'] }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No sales with remarks found</td>
            </tr>
            @endforelse
        </tbody>
        @if(isset($totals) && ($totals['count'] ?? 0) > 0)
        <tfoot>
            <tr>
                <td colspan="7" class="text-end">Total ({{ $totals['count'] }} bills | Pending: {{ $totals['pending'] }} | Paid: {{ $totals['paid'] }}):</td>
                <td class="text-end">{{ number_format($totals['amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['balance'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        Printed on: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
