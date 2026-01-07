<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Wise Discount - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .party-section { margin-bottom: 15px; page-break-inside: avoid; }
        .party-header { background-color: #e0e0e0; padding: 5px 10px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .party-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>Company Wise Discount</h2>
    </div>

    @foreach($reportData as $party)
    <div class="party-section">
        <div class="party-header">
            {{ $party['party_type'] }}: {{ $party['party_code'] }} - {{ $party['party_name'] }}
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Code</th>
                    <th>Company Name</th>
                    <th style="width: 80px;" class="text-end">Dis.Brk</th>
                    <th style="width: 80px;" class="text-end">Dis.Exp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($party['companies'] as $company)
                <tr>
                    <td>{{ $company['company_code'] }}</td>
                    <td>{{ $company['company_name'] }}</td>
                    <td class="text-end">{{ number_format($company['discount_brk'], 2) }}</td>
                    <td class="text-end">{{ number_format($company['discount_exp'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="footer">
        <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
