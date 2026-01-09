<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSTR-2 Report - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background: #ffc4d0; }
        .header h2 { font-family: 'Times New Roman', serif; font-style: italic; color: #800000; margin: 0; }
        .header p { margin: 5px 0 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #cc9966; font-weight: bold; text-align: center; }
        td { background: #ffffcc; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .section-title { background: #e0e0e0; font-weight: bold; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>G S T R - 2 Report</h2>
        <p>Inward Supplies | Period: {{ $reportData['period'] ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 200px;">Particulars</th>
                <th style="width: 60px;">Count</th>
                <th style="width: 100px;">Taxable</th>
                <th style="width: 80px;">IGST</th>
                <th style="width: 80px;">CGST</th>
                <th style="width: 80px;">SGST</th>
                <th style="width: 60px;">CESS</th>
                <th style="width: 100px;">Total Tax</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold">B2B (Registered Suppliers)</td>
                <td class="text-center">{{ $reportData['b2b_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>B2B (Unregistered Suppliers)</td>
                <td class="text-center">{{ $reportData['b2b_unreg_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_unreg_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Credit/Debit Notes (Registered)</td>
                <td class="text-center">{{ $reportData['cdn_reg_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['cdn_reg_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Credit/Debit Notes (Unregistered)</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>Imports</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>Nil Rated/Exempted</td>
                <td class="text-center">{{ $reportData['nil_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['nil_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td>HSN Summary</td>
                <td class="text-center">{{ $hsnSummary->count() }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('taxable_value'), 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('igst'), 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('cgst'), 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('sgst'), 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('cess'), 2) }}</td>
                <td class="text-end">-</td>
            </tr>
        </tbody>
    </table>

    @if($b2bData->count() > 0)
    <h4 style="margin: 15px 0 5px;">B2B Invoices (Registered Suppliers)</h4>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>GSTIN</th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>Total Tax</th>
            </tr>
        </thead>
        <tbody>
            @foreach($b2bData as $row)
            <tr>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d-m-Y') }}</td>
                <td>{{ $row['supplier_name'] }}</td>
                <td>{{ $row['gstin'] }}</td>
                <td class="text-end">{{ number_format($row['taxable_value'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['total_tax'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
