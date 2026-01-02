<!DOCTYPE html>
<html>
<head>
    <title>Debit/Credit Note Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #dc3545; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f8d7da; font-weight: bold; text-align: left; color: #721c24; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f5c6cb; font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-dn { background: #dc3545; color: white; }
        .badge-cn { background: #198754; color: white; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 20px; padding: 5px 10px; background: #f0f0f0; border-radius: 3px; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>DEBIT / CREDIT NOTE - REPORT</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>
            Party Type: {{ $partyType == 'A' ? 'All' : ($partyType == 'S' ? 'Sale' : ($partyType == 'P' ? 'Purchase' : 'General')) }} |
            Note Type: {{ $noteType == 'A' ? 'All' : ($noteType == 'D' ? 'Debit Note' : 'Credit Note') }}
        </p>
        @if($customerId ?? false)
            <p>Customer: {{ $customers->firstWhere('id', $customerId)->name ?? 'Selected' }}</p>
        @endif
        @if($supplierId ?? false)
            <p>Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}</p>
        @endif
    </div>

    <div class="summary">
        <span class="summary-item">Total Notes: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">DN: {{ $totals['dn_count'] ?? 0 }} (₹{{ number_format($totals['dn_amount'] ?? 0, 2) }})</span>
        <span class="summary-item">CN: {{ $totals['cn_count'] ?? 0 }} (₹{{ number_format($totals['cn_amount'] ?? 0, 2) }})</span>
        <span class="summary-item">Net: ₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">Sr.</th>
                <th style="width: 45px;">Type</th>
                <th style="width: 80px;">Note No</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 60px;">Party Type</th>
                <th>Party Name</th>
                <th>Reason</th>
                <th class="text-right" style="width: 80px;">Gross Amt</th>
                <th class="text-right" style="width: 60px;">GST</th>
                <th class="text-right" style="width: 90px;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notes ?? [] as $index => $note)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-center">
                    <span class="badge {{ $note->note_type == 'DN' ? 'badge-dn' : 'badge-cn' }}">
                        {{ $note->note_type }}
                    </span>
                </td>
                <td>{{ $note->note_no }}</td>
                <td>{{ $note->note_date->format('d-m-Y') }}</td>
                <td>{{ $note->party_type_label ?? '' }}</td>
                <td>{{ $note->party_name ?? '' }}</td>
                <td>{{ $note->reason ?? '' }}</td>
                <td class="text-right">{{ number_format($note->gross_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($note->total_gst ?? 0, 2) }}</td>
                <td class="text-right {{ $note->note_type == 'DN' ? 'text-danger' : 'text-success' }}">
                    {{ number_format($note->net_amount ?? 0, 2) }}
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7">Grand Total</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td colspan="2" class="text-right">DN Total:</td>
                <td class="text-right text-danger">₹{{ number_format($totals['dn_amount'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td colspan="2" class="text-right">CN Total:</td>
                <td class="text-right text-success">₹{{ number_format($totals['cn_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
