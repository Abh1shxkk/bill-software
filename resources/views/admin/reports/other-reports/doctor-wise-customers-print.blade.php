<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Wise Patient - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .doctor-section { margin-bottom: 20px; page-break-inside: avoid; }
        .doctor-header { background-color: #e0e0e0; padding: 5px 10px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .doctor-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>Doctor Wise Patient List</h2>
    </div>

    @foreach($reportData as $doctorData)
    <div class="doctor-section">
        <div class="doctor-header">
            Doctor: {{ $doctorData['doctor_name'] }} ({{ count($doctorData['patients']) }} Patients)
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">S.No</th>
                    <th>Patient Name</th>
                    <th>Customer Name</th>
                    <th style="width: 60px;">Code</th>
                    <th style="width: 100px;">Mobile</th>
                    <th>Address</th>
                    <th style="width: 80px;">Presc. Date</th>
                    <th style="width: 80px;">Validity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctorData['patients'] as $index => $patient)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $patient['patient_name'] }}</td>
                    <td>{{ $patient['customer_name'] }}</td>
                    <td>{{ $patient['customer_code'] }}</td>
                    <td>{{ $patient['customer_mobile'] }}</td>
                    <td>{{ $patient['customer_address'] }}</td>
                    <td>{{ $patient['prescription_date'] ? $patient['prescription_date']->format('d-m-Y') : '' }}</td>
                    <td>{{ $patient['validity_date'] ? $patient['validity_date']->format('d-m-Y') : '' }}</td>
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
