<!DOCTYPE html>
<html>
<head>
    <title>GST Set Off Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #856404; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 5px 8px; }
        th { background: #f0e68c; font-weight: bold; text-align: left; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-header { background: #daa520; color: white; font-weight: bold; padding: 8px; margin-top: 10px; }
        .total-row { background: #f5f5dc; font-weight: bold; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .summary { margin-bottom: 15px; display: flex; gap: 20px; }
        .summary-box { padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; }
        .two-col { display: flex; gap: 15px; }
        .two-col > div { flex: 1; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>GST - SET OFF Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Show DN/CN: {{ $showDnCn ?? 'Y' }} | Show Br.Exp: {{ $showBrExp ?? 'Y' }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">Input ITC: ₹{{ number_format($totals['input_gst'] ?? 0, 2) }}</div>
        <div class="summary-box">Output Liability: ₹{{ number_format($totals['output_gst'] ?? 0, 2) }}</div>
        <div class="summary-box">ITC Set Off: ₹{{ number_format($totals['set_off'] ?? 0, 2) }}</div>
        <div class="summary-box">Net {{ ($totals['net'] ?? 0) >= 0 ? 'Payable' : 'Refundable' }}: ₹{{ number_format(abs($totals['net'] ?? 0), 2) }}</div>
    </div>

    <div class="two-col">
        <!-- INPUT GST -->
        <div>
            <div class="section-header">INPUT GST (ITC Available)</div>
            <table>
                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th class="text-right">CGST</th>
                        <th class="text-right">SGST</th>
                        <th class="text-right">IGST</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Purchase B2B</td>
                        <td class="text-right">{{ number_format($input['purchase_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['purchase_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['purchase_igst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['purchase_total'] ?? 0, 2) }}</td>
                    </tr>
                    @if(($showDnCn ?? 'Y') == 'Y')
                    <tr>
                        <td>Add: Debit Note</td>
                        <td class="text-right text-success">{{ number_format($input['dn_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($input['dn_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($input['dn_igst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($input['dn_total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Less: Credit Note</td>
                        <td class="text-right text-danger">{{ number_format($input['cn_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($input['cn_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($input['cn_igst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($input['cn_total'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Net Input ITC</td>
                        <td class="text-right">{{ number_format($input['net_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['net_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['net_igst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($input['net_total'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- OUTPUT GST -->
        <div>
            <div class="section-header">OUTPUT GST (Liability)</div>
            <table>
                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th class="text-right">CGST</th>
                        <th class="text-right">SGST</th>
                        <th class="text-right">IGST</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sales B2B/B2C</td>
                        <td class="text-right">{{ number_format($output['sales_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['sales_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['sales_igst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['sales_total'] ?? 0, 2) }}</td>
                    </tr>
                    @if(($showDnCn ?? 'Y') == 'Y')
                    <tr>
                        <td>Add: Credit Note</td>
                        <td class="text-right text-success">{{ number_format($output['cn_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($output['cn_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($output['cn_igst'] ?? 0, 2) }}</td>
                        <td class="text-right text-success">{{ number_format($output['cn_total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Less: Debit Note</td>
                        <td class="text-right text-danger">{{ number_format($output['dn_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($output['dn_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($output['dn_igst'] ?? 0, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($output['dn_total'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Net Output Liability</td>
                        <td class="text-right">{{ number_format($output['net_cgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['net_sgst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['net_igst'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($output['net_total'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- GST SET OFF COMPUTATION -->
    <div class="section-header" style="background: #333;">GST SET OFF COMPUTATION</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">CGST</th>
                <th class="text-right">SGST</th>
                <th class="text-right">IGST</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Output Liability (A)</td>
                <td class="text-right">{{ number_format($setoff['liability_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($setoff['liability_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($setoff['liability_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($setoff['liability_total'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Less: Input ITC Set Off (B)</td>
                <td class="text-right text-success">{{ number_format($setoff['itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($setoff['itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($setoff['itc_igst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($setoff['itc_total'] ?? 0, 2) }}</td>
            </tr>
            <tr class="total-row" style="background: {{ ($setoff['net_total'] ?? 0) >= 0 ? '#f8d7da' : '#d4edda' }};">
                <td>Net GST {{ ($setoff['net_total'] ?? 0) >= 0 ? 'Payable' : 'Refundable' }} (A - B)</td>
                <td class="text-right">{{ number_format($setoff['net_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($setoff['net_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($setoff['net_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format(abs($setoff['net_total'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
