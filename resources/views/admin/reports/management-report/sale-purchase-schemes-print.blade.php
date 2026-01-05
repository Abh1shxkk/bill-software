<!DOCTYPE html>
<html>
<head>
    <title>Sale /Purchase Scheme - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; font-style: italic; font-family: 'Times New Roman', serif; }
        .header p { margin: 3px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        @media print {
            body { margin: 0; }
            @page { margin: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>{{ ($request->scheme_type ?? 'S') == 'S' ? 'Sale' : 'Purchase' }} Scheme</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">S.No</th>
                <th style="width: 28%;">Item Name</th>
                <th style="width: 18%;">Company</th>
                <th style="width: 10%;">Packing</th>
                <th class="text-center" style="width: 10%;">Scheme (+)</th>
                <th class="text-center" style="width: 10%;">Scheme (-)</th>
                <th class="text-center" style="width: 10%;">From Date</th>
                <th class="text-center" style="width: 10%;">To Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['packing'] }}</td>
                <td class="text-center">{{ $row['scheme_plus'] }}</td>
                <td class="text-center">{{ $row['scheme_minus'] }}</td>
                <td class="text-center">{{ $row['from_date'] }}</td>
                <td class="text-center">{{ $row['to_date'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 9px; text-align: right;">
        Printed on: {{ now()->format('d-M-Y h:i A') }} | Total Items: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
