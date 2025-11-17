@extends('layouts.admin')
@section('title', 'Due List - Breakage/Expiry - ' . $customer->name)
@section('content')
    <style>
        .page-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #9966cc;
            font-style: italic;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .due-header {
            background: #fff;
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .due-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .due-header-item {
            font-weight: bold;
        }

        .due-header-item span {
            color: #0066cc;
            font-weight: bold;
            margin-left: 10px;
        }

        .due-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .due-table th {
            background: #ffcccc;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        .due-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .due-table tr:first-child {
            background: #ffcccc;
        }

        .due-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .sno-column,
        .days-column,
        .amount-column {
            text-align: center;
        }

        .footer-section {
            background: #fff;
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-item {
            display: flex;
            gap: 10px;
        }

        .footer-item label {
            font-weight: bold;
            min-width: 120px;
        }

        .footer-item span {
            color: #0066cc;
            font-weight: bold;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
            font-size: 11px;
        }

        .info-item {
            display: flex;
            gap: 10px;
        }

        .info-item label {
            font-weight: bold;
            min-width: 100px;
        }

        .info-item span {
            color: #0066cc;
        }

        .action-buttons {
            background: #fff;
            border: 2px solid #000;
            padding: 10px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }

        .action-buttons button,
        .action-buttons a {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .hold-badge {
            background: #ffcccc;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            text-align: center;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>

    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title">Due List - Breakage/Expiry</div>

        <!-- Header -->
        <div class="due-header">
            <div class="due-header-row">
                <div>
                    <div class="due-header-item">Name: <span>{{ $customer->name }}</span></div>
                    <div class="due-header-item">Code: <span>{{ $customer->code ?? '-' }}</span></div>
                </div>
                <div style="text-align: right;">
                    <span style="color: #0066cc; font-size: 14px; font-weight: bold;">Press " " To Hold</span>
                </div>
            </div>
        </div>

        <!-- Due List Table -->
        <div style="border: 2px solid #000; border-radius: 0; overflow: hidden;">
            <table class="due-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Sr.No</th>
                        <th style="width: 10%;">Trans No.</th>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 8%;">Days</th>
                        <th style="width: 10%;">Due Date</th>
                        <th style="width: 8%;">Days</th>
                        <th style="width: 12%;">Trn.Amt.</th>
                        <th style="width: 12%;">Debit</th>
                        <th style="width: 12%;">Credit</th>
                        <th style="width: 8%;">Hold</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10" class="empty-message">
                            No expiry due entries found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Summary -->
        <div class="footer-section">
            <div class="footer-row">
                <div style="flex: 1;"></div>
                <div style="text-align: right;">
                    <span style="font-weight: bold;">0.00</span>
                    <span style="margin-left: 20px; font-weight: bold;">0.00</span>
                </div>
            </div>

            <!-- Info Section -->
            <div class="info-row">
                <div class="info-item">
                    <label>Sales Man :</label>
                    <span>{{ $customer->sales_man ?? 'DIRECT' }}</span>
                </div>
                <div class="info-item">
                    <label>Area :</label>
                    <span>{{ $customer->area ?? 'DIRECT' }}</span>
                </div>
                <div class="info-item">
                    <label>Route :</label>
                    <span>{{ $customer->route ?? 'DIRECT' }}</span>
                </div>
                <div class="info-item">
                    <label>Day :</label>
                    <span>-</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-item" style="grid-column: 1 / 2;">
                    <label>Tag :</label>
                    <span>-</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-outline-info btn-sm">
                <i class="fas fa-exchange"></i> Change Type
            </button>
            <button class="btn btn-outline-info btn-sm">
                <i class="fas fa-list"></i> Adjustment Details (F5)
            </button>
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-print"></i> Print (F7)
            </button>
            <a href="{{ route('admin.customers.dues', $customer) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-times"></i> Exit
            </a>
        </div>
    </div>

    <style>
        @media print {
            .action-buttons, .modal, .btn-group {
                display: none !important;
            }
        }
    </style>
@endsection
