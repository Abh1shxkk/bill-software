@extends('layouts.admin')

@section('title', 'Expiry Date Modification')

@section('content')
<style>
    .expiry-header {
        background: linear-gradient(135deg, #FFA500 0%, #FFD700 100%);
        padding: 15px;
        text-align: center;
        border: 2px solid #000;
        margin-bottom: 20px;
    }
    .expiry-header h2 {
        color: #000080;
        font-weight: bold;
        margin: 0;
        font-size: 28px;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border: 2px solid #000;
        margin-bottom: 20px;
    }
    .filter-label {
        font-weight: 600;
        font-size: 14px;
        margin-right: 10px;
    }
    .filter-input {
        font-size: 13px;
        padding: 4px 8px;
        border: 1px solid #000;
    }
    .expiry-table {
        border: 2px solid #000;
    }
    .expiry-table th {
        background: #FFD700;
        color: #000;
        font-weight: bold;
        padding: 8px;
        border: 1px solid #000;
        font-size: 14px;
        font-style: italic;
    }
    .expiry-table td {
        padding: 8px;
        border: 1px solid #999;
        background: #fff;
    }
    .expiry-table tbody tr:hover {
        background: #f0f0f0;
    }
    .btn-action {
        font-size: 13px;
        padding: 6px 20px;
        font-weight: 600;
    }
    .footer-note {
        color: #dc3545;
        font-weight: 600;
        margin-top: 10px;
        font-size: 14px;
    }
</style>

<div class="container-fluid p-3">
    <!-- Header -->
    <div class="expiry-header">
        <h2>Expiry Date Modification</h2>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <label class="filter-label">From :</label>
                        <input type="date" class="form-control filter-input" value="{{ date('Y-m-d', strtotime('first day of this month')) }}" style="width: 150px;">
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="filter-label">To :</label>
                        <input type="date" class="form-control filter-input" value="{{ date('Y-m-d', strtotime('last day of this month')) }}" style="width: 150px;">
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="filter-label">Party Code :</label>
                        <input type="text" class="form-control filter-input" placeholder="00" style="width: 80px;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="d-flex align-items-center">
                        <label class="filter-label">Trn No.</label>
                        <input type="text" class="form-control filter-input" style="width: 150px;">
                    </div>
                    <button type="button" class="btn btn-secondary btn-action">OK</button>
                    <button type="button" class="btn btn-danger btn-action">Exit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered expiry-table mb-0">
            <thead>
                <tr>
                    <th style="width: 15%;">DATE</th>
                    <th style="width: 15%;">TRN.NO.</th>
                    <th style="width: 45%;">PARTY NAME</th>
                    <th style="width: 25%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 15; $i++)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        Date Modification (F5)
    </div>
</div>
@endsection
