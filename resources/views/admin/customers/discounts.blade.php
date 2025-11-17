@extends('layouts.admin')
@section('title', 'Discounts - ' . $customer->name)
@section('content')
    <style>
        .discount-container {
            background: #fff;
            border: 2px solid #999;
            border-radius: 6px;
            padding: 0;
            margin: 20px auto;
            max-width: 900px;
        }

        .discount-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #8b4513;
            font-style: italic;
            padding: 15px;
            background: #ffe8cc;
            border-bottom: 1px solid #999;
        }

        .discount-customer {
            padding: 12px 15px;
            font-size: 12px;
            background: #ffe8cc;
            border-bottom: 1px solid #999;
        }

        .discount-customer label {
            font-weight: bold;
        }

        .discount-customer span {
            color: #000;
            font-weight: bold;
            margin-left: 5px;
        }

        .discount-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            font-size: 12px;
            margin-bottom: 0;
        }

        .discount-table th {
            background: #ffcc99;
            border: 1px solid #999;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }

        .discount-table td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }

        .discount-table tr:nth-child(even) {
            background: #ffffff;
        }

        .discount-table tr:nth-child(odd) {
            background: #fffaf0;
        }

        .code-column {
            text-align: center;
            width: 10%;
        }

        .company-column {
            width: 60%;
        }

        .discount-value {
            text-align: right;
            font-weight: bold;
            width: 15%;
        }
        }

        .filter-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .filter-group label {
            font-weight: bold;
            font-size: 12px;
        }

        .filter-group select {
            padding: 6px 8px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .tab-button {
            padding: 8px 16px;
            font-weight: bold;
            border: 2px solid #999;
            background: #f0f0f0;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .tab-button.active {
            background: #ffcc99;
            border-color: #cc6600;
        }

        .tab-button:hover {
            background: #ffe6cc;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .nav-buttons button,
        .nav-buttons a {
            padding: 8px 16px;
            font-weight: bold;
            font-size: 12px;
        }

        .add-discount-section {
            background: #fff;
            border: 2px solid #999;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .form-group input,
        .form-group select {
            padding: 6px 8px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .discount-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }

        .breakage-badge {
            background: #ffcccc;
            color: #cc0000;
        }

        .expiry-badge {
            background: #ccffcc;
            color: #00cc00;
        }

        .status-active {
            color: #00cc00;
            font-weight: bold;
        }

        .status-inactive {
            color: #cc0000;
            font-weight: bold;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .pagination-section {
            text-align: center;
            margin-top: 20px;
        }
    </style>

    <!-- Discount Container -->
    <div class="discount-container">
        <div class="discount-title">-: Company Wise Discount :-</div>
        
        <div class="discount-customer">
            <label>Customer :</label>
            <span>{{ $customer->name }}</span>
        </div>

        <!-- Company Wise Discount Table -->
        <table class="discount-table">
            <thead>
                <tr>
                    <th class="code-column">Code</th>
                    <th class="company-column">Company Name</th>
                    <th class="discount-value">Dis.Brk</th>
                    <th class="discount-value">Dis.Exp</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Rows -->
                @php $discountRows = [
                    ['code' => '---', 'company' => 'ADSILA ORGANICS PVT. LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'ALKEM LABORATORIES LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'ALTUS BIOGENICS PVT LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'AMRA REMEDIES', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'APRICA PHARMACEUTICALS PVT LTD', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'ARINNA LIFE SCIENCES PVT.LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'BELOORBAYIR BIOTECH LIMITED', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'BIOTRIC SPECILITIES', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'C.M.R. LIFE SCIENCES PVT. LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'COMED CHEMICALS', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'CONOR COSMO INDIA PVT LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'DR.MOREPEN', 'brk' => '0.00', 'exp' => '0.00'],
                    ['code' => '---', 'company' => 'GENESCIENCES LIFECARE PVT.LTD.', 'brk' => '0.00', 'exp' => '0.00'],
                ] @endphp
                @foreach($discountRows as $row)
                    <tr>
                        <td class="code-column">{{ $row['code'] }}</td>
                        <td class="company-column">{{ $row['company'] }}</td>
                        <td class="discount-value">{{ $row['brk'] }}</td>
                        <td class="discount-value">{{ $row['exp'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Back Button -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('admin.customers.show', $customer) }}" style="padding: 8px 16px; font-weight: bold; border: 1px solid #999; background: #fff; border-radius: 3px; text-decoration: none; color: #000; display: inline-block;">
            ← Back
        </a>
    </div>

    @endsection
