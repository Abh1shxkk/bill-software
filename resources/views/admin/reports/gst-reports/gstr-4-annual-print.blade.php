<!DOCTYPE html>
<html>
<head>
    <title>GSTR-4 Annual Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; font-family: 'Times New Roman', serif; color: #800000; }
        .period { text-align: center; margin-bottom: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #cc9966; font-weight: bold; text-align: center; }
        td.text-end { text-align: right; }
        td.text-center { text-align: center; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
        @media print {
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>GSTR-4 (Annual) - Composition Scheme</h2>
    </div>

    <div class="period">
        Period: {{ $reportData['period'] ?? 'N/A' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 200px;">Particulars</th>
                <th style="width: 70px;">Count</th>
                <th style="width: 110px;">Taxable</th>
                <th style="width: 90px;">IGST</th>
                <th style="width: 90px;">CGST</th>
                <th style="width: 90px;">SGST</th>
                <th style="width: 70px;">CESS</th>
                <th style="width: 110px;">Total Tax</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">Annual Turnover</td>
                <td class="text-center">{{ $reportData['annual_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['annual_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['annual_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['annual_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['annual_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['annual_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['annual_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Outward Supplies (Taxable)</td>
                <td class="text-center">{{ $reportData['outward_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['outward_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['outward_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['outward_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['outward_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['outward_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['outward_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Inward Supplies (RCM)</td>
                <td class="text-center">{{ $reportData['inward_rcm_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['inward_rcm_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Exempt Supplies</td>
                <td class="text-center">{{ $reportData['exempt_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['exempt_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td>Nil Rated Supplies</td>
                <td class="text-center">{{ $reportData['nil_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['nil_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr class="total-row">
                <td style="font-weight: bold;">Total Tax Payable</td>
                <td class="text-center">-</td>
                <td class="text-end">{{ number_format($reportData['total_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['total_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['total_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['total_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['total_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['total_tax'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 20px; font-size: 14px;">Print</button>
        <button onclick="window.close()" style="padding: 8px 20px; font-size: 14px; margin-left: 10px;">Close</button>
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        };
    </script>
</body>
</html>
