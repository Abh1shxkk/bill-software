<!DOCTYPE html>
<html>
<head>
    <title>GST Set Off GSTR Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #856404; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0e68c; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-header { background: #daa520; color: #333; font-weight: bold; }
        .total-row { background: #f5f5dc; font-weight: bold; }
        .sub-row td:first-child { padding-left: 20px; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; }
        .badge-success { background: #198754; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>GST - SET OFF GSTR Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Show DN/CN: {{ $showDnCn ?? 'Y' }} | Show Br.Exp: {{ $showBrExp ?? 'Y' }}</p>
    </div>

    <!-- GSTR-3B Summary -->
    <table>
        <thead>
            <tr>
                <th colspan="2">Description</th>
                <th class="text-right">Integrated Tax</th>
                <th class="text-right">Central Tax</th>
                <th class="text-right">State/UT Tax</th>
                <th class="text-right">Cess</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="6">3.1 Details of Outward Supplies</td>
            </tr>
            <tr>
                <td width="25">(a)</td>
                <td>Outward taxable supplies</td>
                <td class="text-right">{{ number_format($gstr['outward_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['outward_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['outward_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['outward_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>(b)</td>
                <td>Zero rated supplies</td>
                <td class="text-right">{{ number_format($gstr['zero_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['zero_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['zero_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['zero_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>(d)</td>
                <td>Inward supplies (RCM)</td>
                <td class="text-right">{{ number_format($gstr['rcm_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['rcm_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['rcm_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['rcm_cess'] ?? 0, 2) }}</td>
            </tr>
            
            <tr class="section-header">
                <td colspan="6">4. Eligible ITC</td>
            </tr>
            <tr class="total-row">
                <td>(A)</td>
                <td>ITC Available</td>
                <td class="text-right">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr class="sub-row">
                <td></td>
                <td>(5) All other ITC</td>
                <td class="text-right">{{ number_format($gstr['other_itc_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['other_itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['other_itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['other_itc_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>(B)</td>
                <td>ITC Reversed</td>
                <td class="text-right text-danger">{{ number_format($gstr['reversed_igst'] ?? 0, 2) }}</td>
                <td class="text-right text-danger">{{ number_format($gstr['reversed_cgst'] ?? 0, 2) }}</td>
                <td class="text-right text-danger">{{ number_format($gstr['reversed_sgst'] ?? 0, 2) }}</td>
                <td class="text-right text-danger">{{ number_format($gstr['reversed_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr class="total-row" style="background: #d4edda;">
                <td>(C)</td>
                <td>Net ITC Available</td>
                <td class="text-right">{{ number_format($gstr['net_itc_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['net_itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['net_itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['net_itc_cess'] ?? 0, 2) }}</td>
            </tr>
            
            <tr class="section-header">
                <td colspan="6">6. Payment of Tax</td>
            </tr>
            <tr class="total-row" style="background: #cfe2ff;">
                <td colspan="2">Tax Payable</td>
                <td class="text-right">{{ number_format($gstr['payable_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['payable_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['payable_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['payable_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">Paid through ITC</td>
                <td class="text-right text-success">{{ number_format($gstr['paid_itc_igst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($gstr['paid_itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($gstr['paid_itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-right text-success">{{ number_format($gstr['paid_itc_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr class="total-row" style="background: #f8d7da;">
                <td colspan="2">Tax paid in Cash</td>
                <td class="text-right">{{ number_format($gstr['cash_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['cash_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['cash_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($gstr['cash_cess'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- ITC Reconciliation -->
    <table>
        <thead>
            <tr>
                <th>ITC Reconciliation</th>
                <th class="text-right">As Per Books</th>
                <th class="text-right">As Per GSTR-2B</th>
                <th class="text-right">Difference</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>IGST</td>
                <td class="text-right">{{ number_format($recon['book_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['gstr_igst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['diff_igst'] ?? 0, 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ ($recon['diff_igst'] ?? 0) == 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ ($recon['diff_igst'] ?? 0) == 0 ? 'Matched' : 'Mismatch' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>CGST</td>
                <td class="text-right">{{ number_format($recon['book_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['gstr_cgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['diff_cgst'] ?? 0, 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ ($recon['diff_cgst'] ?? 0) == 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ ($recon['diff_cgst'] ?? 0) == 0 ? 'Matched' : 'Mismatch' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>SGST</td>
                <td class="text-right">{{ number_format($recon['book_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['gstr_sgst'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['diff_sgst'] ?? 0, 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ ($recon['diff_sgst'] ?? 0) == 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ ($recon['diff_sgst'] ?? 0) == 0 ? 'Matched' : 'Mismatch' }}
                    </span>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td class="text-right">{{ number_format($recon['book_total'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['gstr_total'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($recon['diff_total'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
