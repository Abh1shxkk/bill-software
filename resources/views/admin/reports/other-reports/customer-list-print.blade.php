<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .filters { margin-bottom: 10px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 9px; }
        @media print {
            body { padding: 0; font-size: 9px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>CUSTOMER LIST</h2>
        <div style="font-size: 12px;">Total Records: {{ $reportData->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">S.No</th>
                <th style="width: 50px;">Code</th>
                <th>Name</th>
                @if(in_array('address', $columns)) <th>Address</th> @endif
                @if(in_array('mobile', $columns)) <th style="width: 90px;">Mobile</th> @endif
                @if(in_array('telephone', $columns)) <th style="width: 90px;">Telephone</th> @endif
                @if(in_array('email', $columns)) <th>Email</th> @endif
                @if(in_array('gst_no', $columns)) <th style="width: 130px;">GST No</th> @endif
                @if(in_array('dl_no', $columns)) <th>DL No</th> @endif
                @if(in_array('tin', $columns)) <th>TIN</th> @endif
                @if(in_array('credit_limit', $columns)) <th class="text-end" style="width: 80px;">Credit Limit</th> @endif
                @if(in_array('days', $columns)) <th style="width: 40px;">Days</th> @endif
                @if(in_array('food_license', $columns)) <th>Food Lic.</th> @endif
                @if(in_array('bank', $columns)) <th>Bank</th> @endif
                @if(in_array('state_code', $columns)) <th style="width: 50px;">State</th> @endif
                <th style="width: 70px;">Area</th>
                <th style="width: 70px;">Route</th>
                <th class="text-center" style="width: 40px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}</td>
                @if(in_array('address', $columns)) <td>{{ $record->address }}</td> @endif
                @if(in_array('mobile', $columns)) <td>{{ $record->mobile }}</td> @endif
                @if(in_array('telephone', $columns)) <td>{{ $record->telephone_office }}</td> @endif
                @if(in_array('email', $columns)) <td>{{ $record->email }}</td> @endif
                @if(in_array('gst_no', $columns)) <td>{{ $record->gst_number }}</td> @endif
                @if(in_array('dl_no', $columns)) <td>{{ $record->dl_number }}</td> @endif
                @if(in_array('tin', $columns)) <td>{{ $record->tin_number }}</td> @endif
                @if(in_array('credit_limit', $columns)) <td class="text-end">{{ number_format($record->credit_limit ?? 0, 2) }}</td> @endif
                @if(in_array('days', $columns)) <td>{{ $record->credit_days }}</td> @endif
                @if(in_array('food_license', $columns)) <td>{{ $record->food_license }}</td> @endif
                @if(in_array('bank', $columns)) <td>{{ $record->bank }}</td> @endif
                @if(in_array('state_code', $columns)) <td>{{ $record->state_code }}</td> @endif
                <td>{{ $record->area_name }}</td>
                <td>{{ $record->route_name }}</td>
                <td class="text-center">{{ $record->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
