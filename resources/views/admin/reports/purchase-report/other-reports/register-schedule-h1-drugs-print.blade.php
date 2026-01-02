<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register of Schedule H1 Drugs - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #dc3545; padding-bottom: 10px; }
        .header h2 { color: #dc3545; font-size: 18px; margin-bottom: 5px; }
        .header .date-range { font-size: 12px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background-color: #dc3545; color: white; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        tfoot tr { background-color: #ffebee; font-weight: bold; }
        .footer-note { margin-top: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107; font-size: 10px; }
        .print-info { text-align: right; font-size: 9px; color: #999; margin-top: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 10mm; size: landscape; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #dc3545; color: white; border: none; cursor: pointer; border-radius: 4px;">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 5px;">
            ✕ Close
        </button>
    </div>

    <div class="header">
        <h2>REGISTER OF SCHEDULE H1 DRUGS</h2>
        <div class="date-range">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        </div>
    </div>

    <div class="filters">
        <strong>Filters:</strong>
        Item Type: {{ $itemType ?? 'H1' }} |
        Supplier Flag: {{ $supplierFlag == '5' ? 'ALL' : $supplierFlag }} |
        Item Status: {{ $itemStatus == 'A' ? 'Active' : ($itemStatus == 'D' ? 'Discontinued' : 'Both') }}
        @if($supplierId)
            | Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 80px;">Invoice No</th>
                <th>Name of Drug</th>
                <th style="width: 80px;">Batch No</th>
                <th style="width: 60px;">Expiry</th>
                <th class="text-end" style="width: 60px;">Qty</th>
                <th>Manufacturer</th>
                <th>Supplier Name</th>
                <th style="width: 100px;">D.L. No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($drugs ?? [] as $index => $drug)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $drug->bill_date ? $drug->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $drug->bill_no ?? '-' }}</td>
                <td class="fw-bold text-danger">{{ $drug->drug_name ?? '-' }}</td>
                <td>{{ $drug->batch_no ?? '-' }}</td>
                <td>{{ $drug->expiry_date ?? '-' }}</td>
                <td class="text-end">{{ number_format($drug->quantity ?? 0) }}</td>
                <td>{{ $drug->manufacturer ?? '-' }}</td>
                <td>{{ $drug->supplier->name ?? 'N/A' }}</td>
                <td>{{ $drug->supplier->dl_no ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px;">No Schedule H1 drug records found</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($drugs ?? []) > 0)
        <tfoot>
            <tr>
                <td colspan="6" class="text-end fw-bold">Total:</td>
                <td class="text-end fw-bold">{{ number_format($totals['total_qty'] ?? 0) }}</td>
                <td colspan="3">{{ $totals['count'] ?? 0 }} Records</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer-note">
        <strong>Note:</strong> This register is maintained as per Rule 65(10) of Drugs and Cosmetics Rules, 1945 for Schedule H1 drugs including Antibiotics, Anti-TB drugs, Habit forming drugs, etc.
    </div>

    <div class="print-info">
        Printed on: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
