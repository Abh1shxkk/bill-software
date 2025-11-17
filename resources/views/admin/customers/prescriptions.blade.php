@extends('layouts.admin')
@section('title', 'Prescription List - ' . $customer->name)
@section('content')
    <style>
        .prescription-container {
            background: #fff;
            border: 2px solid #999;
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
        }

        .prescription-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .prescription-header {
            margin-bottom: 15px;
            font-size: 13px;
        }

        .prescription-header label {
            font-weight: bold;
        }

        .prescription-header span {
            color: #0066cc;
            font-weight: bold;
            margin-left: 5px;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #999;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .prescription-table th {
            background: #4169e1;
            border: 1px solid #999;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: white;
        }

        .prescription-table td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
            height: 25px;
        }

        .prescription-table tr:nth-child(even) {
            background: #f0f0f0;
        }

        .code-column,
        .pack-column,
        .qty-column,
        .dose-column,
        .location-column {
            text-align: center;
        }

        .prescription-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #999;
        }

        .prescription-footer button,
        .prescription-footer a {
            padding: 8px 16px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #999;
            background: #fff;
            border-radius: 3px;
        }

        .prescription-footer button:hover,
        .prescription-footer a:hover {
            background: #f0f0f0;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
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

        .add-prescription-section {
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
        .form-group select,
        .form-group textarea {
            padding: 6px 8px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }

        .active-badge {
            background: #ccffcc;
            color: #00cc00;
        }

        .expired-badge {
            background: #ffcccc;
            color: #cc0000;
        }

        .cancelled-badge {
            background: #cccccc;
            color: #666666;
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

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .action-buttons button,
        .action-buttons a {
            padding: 4px 8px;
            font-size: 11px;
        }

        .prescription-details {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 3px;
            margin-top: 5px;
            font-size: 11px;
            line-height: 1.5;
        }

        .prescription-details strong {
            color: #0066cc;
        }
    </style>

    <!-- Prescription Container -->
    <div class="prescription-container">
        <div class="prescription-title">Prescription List</div>
        
        <div class="prescription-header">
            <label>Name :</label>
            <span>{{ $customer->name }}</span>
        </div>

        <!-- Prescription Table -->
        <table class="prescription-table">
            <thead>
                <tr>
                    <th class="code-column" style="width: 8%;">CODE</th>
                    <th style="width: 25%;">ITEM NAME</th>
                    <th class="pack-column" style="width: 10%;">PACK</th>
                    <th class="qty-column" style="width: 8%;">QTY</th>
                    <th class="dose-column" style="width: 8%;">DOSE</th>
                    <th style="width: 12%;">DUE DATE</th>
                    <th class="dose-column" style="width: 8%;">Dose</th>
                    <th class="location-column" style="width: 15%;">Location</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Rows -->
                @php $prescriptionRows = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; @endphp
                @foreach($prescriptionRows as $row)
                    <tr>
                        <td class="code-column"></td>
                        <td></td>
                        <td class="pack-column"></td>
                        <td class="qty-column"></td>
                        <td class="dose-column"></td>
                        <td></td>
                        <td class="dose-column"></td>
                        <td class="location-column"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Buttons -->
        <div class="prescription-footer">
            <button onclick="alert('Delete functionality')">Delete</button>
            <button onclick="window.print()">Print</button>
            <button onclick="window.history.back()">Close</button>
        </div>
    </div>

    @endsection
