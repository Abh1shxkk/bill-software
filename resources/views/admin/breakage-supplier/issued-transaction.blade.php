@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Transaction')

@section('content')
<div class="container-fluid p-3">
    <form method="POST" action="#">
        @csrf
        
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="row mb-2">
                    <label class="col-4 col-form-label fw-bold">Date :</label>
                    <div class="col-8">
                        <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        <small class="text-muted">{{ date('l') }}</small>
                    </div>
                </div>
                <div class="row">
                    <label class="col-4 col-form-label fw-bold">Trn. No. :</label>
                    <div class="col-8">
                        <input type="text" class="form-control form-control-sm" value="465" readonly>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row mb-2">
                    <label class="col-1 col-form-label fw-bold">Supplier :</label>
                    <div class="col-11">
                        <input type="text" class="form-control form-control-sm" value="AADISFEE MEDICAL PVT. LTD.">
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="col-2 col-form-label fw-bold">R ( epl. ) / C ( redit ) Note:</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="C">
                    </div>
                    <label class="col-2 col-form-label fw-bold text-end">Tax [Y/N] :</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <label class="col-1 col-form-label fw-bold text-end">Inc.</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                <div class="row">
                    <label class="col-1 col-form-label fw-bold">GST Vno.:</label>
                    <div class="col-2">
                        <input type="text" class="form-control form-control-sm">
                    </div>
                    <label class="col-1 col-form-label fw-bold">Dis :</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <label class="col-1 col-form-label fw-bold">Rpl :</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <label class="col-1 col-form-label fw-bold">Brk. :</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <label class="col-1 col-form-label fw-bold">Exp :</label>
                    <div class="col-1">
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive mb-2" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-bordered table-sm mb-0">
                <thead style="background: linear-gradient(to bottom, #8B4513 0%, #654321 100%); position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th class="text-white" style="width: 80px;">Item Code</th>
                        <th class="text-white" style="width: 220px;">Item Name</th>
                        <th class="text-white" style="width: 100px;">Batch</th>
                        <th class="text-white" style="width: 70px;">Exp.</th>
                        <th class="text-white" style="width: 60px;">Qty.</th>
                        <th class="text-white" style="width: 60px;">F.Qty.</th>
                        <th class="text-white" style="width: 80px;">Rate</th>
                        <th class="text-white" style="width: 60px;">Dis.%</th>
                        <th class="text-white" style="width: 60px;">Scm.%</th>
                        <th class="text-white" style="width: 60px;">Br/Ex</th>
                        <th class="text-white" style="width: 100px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Empty rows for data entry -->
                    @for($i = 0; $i < 8; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Summary Row -->
        <div class="row mb-2" style="background-color: #ffcccc; padding: 6px; border: 1px solid #999;">
            <div class="col-md-12 d-flex align-items-center">
                <span class="fw-bold me-2">N.T AMT</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 100px;" readonly>
                
                <span class="me-2">SC</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 80px;" readonly>
                
                <span class="me-2">DIS. AMT</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 100px;" readonly>
                
                <span class="me-2">Scm. AMT</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 100px;" readonly>
                
                <span class="me-2">Half Scm.</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 100px;" readonly>
                
                <span class="me-2">Tax</span>
                <input type="text" class="form-control form-control-sm me-3" value="" style="width: 80px;" readonly>
                
                <span class="fw-bold me-2">INV. AMT</span>
                <input type="text" class="form-control form-control-sm" value="" style="width: 100px;" readonly>
            </div>
        </div>

        <!-- Selected Item Details Table -->
        <div class="table-responsive mb-2">
            <table class="table table-bordered table-sm mb-0">
                <thead style="background: linear-gradient(to bottom, #6495ED 0%, #4169E1 100%);">
                    <tr>
                        <th class="text-white" style="width: 80px;">Item Code</th>
                        <th class="text-white" style="width: 220px;">Item Name</th>
                        <th class="text-white" style="width: 100px;">Batch</th>
                        <th class="text-white" style="width: 70px;">Exp.</th>
                        <th class="text-white" style="width: 60px;">Qty.</th>
                        <th class="text-white" style="width: 60px;">F.Qty.</th>
                        <th class="text-white" style="width: 80px;">Rate</th>
                        <th class="text-white" style="width: 60px;">Dis.%</th>
                        <th class="text-white" style="width: 60px;">Scm.%</th>
                        <th class="text-white" style="width: 60px;">Br/Ex</th>
                        <th class="text-white" style="width: 100px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Current selected item row - empty for input -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bottom Section -->
        <div class="row">
            <!-- Left Side - Tax and Pack Info -->
            <div class="col-md-5">
                <div class="row mb-2">
                    <div class="col-3">
                        <label class="form-label fw-bold small">SC %</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-3">
                        <label class="form-label fw-bold small">EXCISE</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-3">
                        <label class="form-label fw-bold small">TAX %</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-3">
                        <label class="form-label small">Pack</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Unit</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Comp :</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-2">
                        <label class="form-label small">Bal.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-6">
                        <div class="p-2" style="background-color: #ffe6e6; border: 1px solid #ccc;">
                            <span class="text-danger fw-bold small">CGST(%) :</span>
                            <input type="text" class="form-control form-control-sm d-inline-block" value="" style="width: 60px;">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2" style="background-color: #ffe6e6; border: 1px solid #ccc;">
                            <span class="text-danger fw-bold small">SGST(%) :</span>
                            <input type="text" class="form-control form-control-sm d-inline-block" value="" style="width: 60px;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-4">
                        <label class="form-label small">Srlno.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
            </div>

            <!-- Right Side - Amount Details -->
            <div class="col-md-7">
                <div class="row mb-2">
                    <div class="col-3">
                        <label class="form-label small">Pack</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Disallow</label>
                        <input type="text" class="form-control form-control-sm" value="N">
                    </div>
                    <div class="col-3">
                        <label class="form-label small">MRP</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4">
                        <label class="form-label small">P.RATE</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">S.RATE</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4">
                        <label class="form-label small">N.T Amt.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">DIS. Amt.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Net Amt.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4">
                        <label class="form-label small">Half Scm.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Scm.Amt.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Tax Amt.</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-4">
                        <label class="form-label small">P.Scm.</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm" value="">
                            <button type="button" class="btn btn-outline-secondary">+</button>
                            <button type="button" class="btn btn-outline-secondary">-</button>
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label small">S.Scm.</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm" value="">
                            <button type="button" class="btn btn-outline-secondary">+</button>
                            <button type="button" class="btn btn-outline-secondary">-</button>
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label small">&nbsp;</label>
                        <input type="text" class="form-control form-control-sm" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-secondary btn-sm">Save (End)</button>
                <button type="button" class="btn btn-danger btn-sm ms-2">Delete Item</button>
                <button type="button" class="btn btn-secondary btn-sm ms-2">View On Screen</button>
                <button type="button" class="btn btn-secondary btn-sm ms-2 float-end">Cancel</button>
            </div>
        </div>
    </form>
</div>

<style>
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
        padding: 3px 4px !important;
        font-size: 12px;
        vertical-align: middle;
    }
    
    .table thead th {
        font-weight: bold;
        text-align: center;
    }
    
    .form-control-sm {
        font-size: 12px;
        padding: 2px 5px;
        height: 26px;
    }
    
    .form-label {
        font-size: 12px;
        margin-bottom: 2px;
        font-weight: 500;
    }
    
    .small {
        font-size: 11px !important;
    }
    
    .col-form-label {
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .input-group-sm .btn {
        padding: 2px 6px;
        font-size: 11px;
    }

    /* Scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection