<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCS Eligibility Report - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; }
        .header h2 { color: #17a2b8; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10px; }
        .info-box { background: #d1ecf1; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-size: 10px; color: #0c5460; }
        .filters { background: #f8f9fa; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 4px 6px; text-align: left; }
        th { background: #343a40; color: white; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-success { background: #28a745; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        tfoot tr { background: #343a40; color: white; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <h2>TCS ELIGIBILITY REPORT</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="info-box">
        TCS (Tax Collected at Source) @ 0.1% is applicable on sales exceeding ₹50 Lakhs to a single party in a financial year.
        <strong>Threshold Amount: ₹{{ number_format($amountThreshold ?? 5000000) }}</strong>
    </div>

    <div class="filters">
        <span><strong>Party Type:</strong> {{ $partyType == 'C' ? 'Customer' : 'Supplier' }}</span>
        <span><strong>L/C:</strong> {{ $localCentral == 'B' ? 'Both' : ($localCentral == 'L' ? 'Local' : 'Central') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 60px;">Party Code</th>
                <th>Party Name</th>
                <th style="width: 130px;">GST No</th>
                <th style="width: 100px;">PAN No</th>
                <th class="text-end" style="width: 110px;">Amount</th>
                <th class="text-center" style="width: 50px;">TCS%</th>
                <th class="text-end" style="width: 90px;">TCS Amt</th>
                <th class="text-center" style="width: 70px;">TCS Appl.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parties ?? [] as $index => $party)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $party->code ?? '' }}</td>
                <td>{{ Str::limit($party->name ?? 'N/A', 30) }}</td>
                <td>{{ $party->gst_number ?? '-' }}</td>
                <td>{{ $party->pan_number ?? '-' }}</td>
                <td class="text-end fw-bold">{{ number_format($party->total_amount ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($party->tcs_rate ?? 0, 2) }}%</td>
                <td class="text-end text-danger fw-bold">{{ number_format($party->tcs_amount ?? 0, 2) }}</td>
                <td class="text-center">
                    @if($party->tcs_applicable)
                        <span class="badge badge-success">Yes</span>
                    @else
                        <span class="badge badge-secondary">No</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">No parties found exceeding threshold</td></tr>
            @endforelse
        </tbody>
        @if(isset($parties) && $parties->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Parties):</td>
                <td class="text-end">{{ number_format($totals['total_amount'] ?? 0, 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: center;">
        Generated on {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
