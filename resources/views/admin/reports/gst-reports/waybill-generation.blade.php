@extends('layouts.admin')

@section('title', 'E-Way Bill Generation')

@section('content')
<div class="container-fluid p-0">
    <!-- Filter Form - Row 1 -->
    <div class="card mb-0" style="background-color: #90a890; border-radius: 0; border: 1px solid #666;">
        <div class="card-body py-1">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.waybill-generation') }}">
                <div class="row g-1 align-items-center mb-1">
                    <div class="col-auto">
                        <select name="document_type" class="form-select form-select-sm" style="width: 280px; font-size: 0.7rem;">
                            <option value="1" {{ ($documentType ?? '1') == '1' ? 'selected' : '' }}>1.Challan / 2.Bill / 3.BT Trf. / 4.Exp.Sale / 5.Pur.Ret. / 6.Pur. Exp.</option>
                            <option value="2" {{ ($documentType ?? '') == '2' ? 'selected' : '' }}>2.Bill</option>
                            <option value="3" {{ ($documentType ?? '') == '3' ? 'selected' : '' }}>3.BT Transfer</option>
                            <option value="4" {{ ($documentType ?? '') == '4' ? 'selected' : '' }}>4.Expiry Sale</option>
                            <option value="5" {{ ($documentType ?? '') == '5' ? 'selected' : '' }}>5.Purchase Return</option>
                            <option value="6" {{ ($documentType ?? '') == '6' ? 'selected' : '' }}>6.Purchase Expiry</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="small fw-bold">1</span>
                    </div>
                    <div class="col-auto">
                        <label class="small">Bill Amt. >=</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" name="bill_amt" class="form-control form-control-sm" 
                               value="{{ $billAmtThreshold ?? 50000.00 }}" style="width: 90px;" step="0.01">
                    </div>
                    <div class="col-auto">
                        <label class="small">SMan</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="sman" class="form-control form-control-sm" 
                               value="{{ $salesmanCode ?? '00' }}" style="width: 40px;">
                    </div>
                </div>
                
                <div class="row g-1 align-items-center mb-1">
                    <div class="col-auto">
                        <label class="small">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate ?? date('Y-m-d') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <label class="small">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate ?? date('Y-m-d') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <select name="transaction_type" class="form-select form-select-sm" style="width: 150px;">
                            <option value="1" {{ ($transactionType ?? '') == '1' ? 'selected' : '' }}>1.Local</option>
                            <option value="2" {{ ($transactionType ?? '') == '2' ? 'selected' : '' }}>2.Inter</option>
                            <option value="3" {{ ($transactionType ?? '3') == '3' ? 'selected' : '' }}>1.Local / 2.Inter / 3.Both</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="small fw-bold">3</span>
                    </div>
                    <div class="col-auto">
                        <label class="small">Series</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm" 
                               value="{{ $seriesCode ?? '00' }}" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="small">Area</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="area" class="form-control form-control-sm" 
                               value="{{ $areaCode ?? '00' }}" style="width: 40px;">
                    </div>
                </div>
                
                <div class="row g-1 align-items-center">
                    <div class="col-auto">
                        <label class="small">Party Code</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="party_code" class="form-control form-control-sm" 
                               value="{{ $partyCode ?? '00' }}" style="width: 60px;">
                    </div>
                    <div class="col-auto">
                        <label class="small">HSN:</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn" class="form-select form-select-sm" style="width: 70px;">
                            <option value="Full" {{ ($hsnType ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Short" {{ ($hsnType ?? '') == 'Short' ? 'selected' : '' }}>Short</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="small">Route</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="route" class="form-control form-control-sm" 
                               value="{{ $routeCode ?? '00' }}" style="width: 40px;">
                    </div>
                </div>
                
                <div class="row g-1 align-items-center mt-1">
                    <div class="col-auto">
                        <select name="gst_filter" class="form-select form-select-sm" style="width: 230px;">
                            <option value="1" {{ ($gstFilter ?? '') == '1' ? 'selected' : '' }}>1.With GSTIN</option>
                            <option value="2" {{ ($gstFilter ?? '') == '2' ? 'selected' : '' }}>2.Without GSTIN</option>
                            <option value="3" {{ ($gstFilter ?? '3') == '3' ? 'selected' : '' }}>1.With GSTIN / 2.Without GSTIN / 3.All</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="small fw-bold">3</span>
                    </div>
                    <div class="col-auto">
                        <select name="order_by" class="form-select form-select-sm" style="width: 200px;">
                            <option value="1" {{ ($orderBy ?? '1') == '1' ? 'selected' : '' }}>Order By 1.VNO / 2.Party / 3.GSTIN</option>
                            <option value="2" {{ ($orderBy ?? '') == '2' ? 'selected' : '' }}>2.Party</option>
                            <option value="3" {{ ($orderBy ?? '') == '3' ? 'selected' : '' }}>3.GSTIN</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="small fw-bold">1</span>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="ok" value="1" class="btn btn-warning btn-sm px-3">
                            <strong>OK</strong>
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm px-2">
                            Close
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Download File Section -->
    <div class="card mb-0" style="background-color: #90a890; border-radius: 0; border: 1px solid #666; border-top: none;">
        <div class="card-body py-1">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="#" class="text-primary fst-italic fw-bold" id="downloadLink">Download File : GSTEWaybills.xls</a>
                </div>
                <div class="col-auto ms-5">
                    <label class="small fw-bold">Trn No.:</label>
                </div>
                <div class="col-auto">
                    <input type="number" name="trn_no" class="form-control form-control-sm" 
                           value="{{ $trnNo ?? 1 }}" style="width: 50px; background-color: #ff6666; color: white;" form="filterForm">
                </div>
                <div class="col-auto">
                    <input type="date" name="trn_date" class="form-control form-control-sm" 
                           value="{{ $trnDate ?? date('Y-m-d') }}" style="width: 120px;" form="filterForm">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnShowAllTags">
                        Show All Tags
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-0" style="border-radius: 0; border: 1px solid #666;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="reportTable" style="background-color: #c8d8c8;">
                    <thead style="background-color: #90a890; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 120px; border: 1px solid #666;">GST NO.</th>
                            <th style="width: 60px; border: 1px solid #666;">CODE</th>
                            <th style="width: 180px; border: 1px solid #666;">PARTY NAME</th>
                            <th style="width: 80px; border: 1px solid #666;">BILLNO</th>
                            <th style="width: 90px; border: 1px solid #666;">DATE</th>
                            <th class="text-end" style="width: 100px; border: 1px solid #666;">AMOUNT</th>
                            <th class="text-end" style="width: 100px; border: 1px solid #666;">TAXABLE</th>
                            <th class="text-end" style="width: 80px; border: 1px solid #666;">TAX</th>
                            <th style="width: 50px; border: 1px solid #666;">TAG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($reportData) && count($reportData) > 0)
                            @foreach($reportData as $row)
                            <tr class="data-row" data-id="{{ $row['id'] }}" 
                                data-customer="{{ json_encode($row['customer'] ?? []) }}"
                                data-gst="{{ $row['gst_no'] }}">
                                <td style="border: 1px solid #999;">{{ $row['gst_no'] }}</td>
                                <td style="border: 1px solid #999;">{{ $row['code'] }}</td>
                                <td style="border: 1px solid #999;">{{ $row['party_name'] }}</td>
                                <td style="border: 1px solid #999;">{{ $row['bill_no'] }}</td>
                                <td style="border: 1px solid #999;">{{ is_string($row['date']) ? $row['date'] : $row['date']->format('d-M-y') }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($row['amount'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($row['taxable'], 2) }}</td>
                                <td class="text-end" style="border: 1px solid #999;">{{ number_format($row['tax'], 2) }}</td>
                                <td style="border: 1px solid #999;">{{ $row['tag'] }}</td>
                            </tr>
                            @endforeach
                        @else
                            @for($i = 0; $i < 5; $i++)
                            <tr>
                                <td style="border: 1px solid #999; height: 22px;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                                <td style="border: 1px solid #999;">&nbsp;</td>
                            </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- E-Way Bill Details Section -->
    <div class="card mb-0" style="background-color: #c8d8c8; border-radius: 0; border: 1px solid #666; border-top: none;">
        <div class="card-body py-2">
            <div class="row">
                <!-- eWayBill From -->
                <div class="col-6">
                    <div class="fw-bold small mb-1">eWayBill From</div>
                    <div class="row g-1 mb-1">
                        <div class="col-3">
                            <label class="small">PinCode</label>
                            <input type="text" class="form-control form-control-sm" id="fromPinCode" 
                                   value="{{ $ewayBillFrom['pincode'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <label class="small">GSTIN</label>
                            <input type="text" class="form-control form-control-sm" id="fromGstin" 
                                   value="{{ $ewayBillFrom['gstin'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1 mb-1">
                        <div class="col-5">
                            <label class="small">Place</label>
                            <input type="text" class="form-control form-control-sm" id="fromPlace" 
                                   value="{{ $ewayBillFrom['place'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <label class="small">State</label>
                            <input type="text" class="form-control form-control-sm" id="fromState" 
                                   value="{{ $ewayBillFrom['state'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="small">Address 1</label>
                            <input type="text" class="form-control form-control-sm" id="fromAddress1" 
                                   value="{{ $ewayBillFrom['address1'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="small">Address 2</label>
                            <input type="text" class="form-control form-control-sm" id="fromAddress2" 
                                   value="{{ $ewayBillFrom['address2'] ?? '' }}">
                        </div>
                    </div>
                </div>

                <!-- eWayBill To -->
                <div class="col-6">
                    <div class="fw-bold small mb-1">eWayBill To</div>
                    <div class="row g-1 mb-1">
                        <div class="col-3">
                            <label class="small">PinCode</label>
                            <input type="text" class="form-control form-control-sm" id="toPinCode" 
                                   value="{{ $ewayBillTo['pincode'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <label class="small">GSTIN</label>
                            <input type="text" class="form-control form-control-sm" id="toGstin" 
                                   value="{{ $ewayBillTo['gstin'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1 mb-1">
                        <div class="col-5">
                            <label class="small">Place</label>
                            <input type="text" class="form-control form-control-sm" id="toPlace" 
                                   value="{{ $ewayBillTo['place'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <label class="small">State</label>
                            <input type="text" class="form-control form-control-sm" id="toState" 
                                   value="{{ $ewayBillTo['state'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="small">Address 1</label>
                            <input type="text" class="form-control form-control-sm" id="toAddress1" 
                                   value="{{ $ewayBillTo['address1'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="small">Address 2</label>
                            <input type="text" class="form-control form-control-sm" id="toAddress2" 
                                   value="{{ $ewayBillTo['address2'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="card shadow-sm" style="background-color: #90a890; border-radius: 0; border: 1px solid #666; border-top: none;">
        <div class="card-body py-1">
            <div class="row align-items-center">
                <div class="col-auto">
                    <button type="button" class="btn btn-light btn-sm" id="btnExportEwayPrev" style="font-size: 0.65rem;">
                        Export<br>EwayBill Prev.
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-light btn-sm" id="btnExportEway" style="font-size: 0.65rem;">
                        Export<br>EwayBill
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-light btn-sm" id="btnExportJson" style="font-size: 0.65rem;">
                        Export<br>JSON
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-success btn-sm" id="btnTag">TAG</button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger btn-sm" id="btnUntag">UNTAG</button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-light btn-sm" id="btnFind">
                        Find [F1]<br><span style="font-size: 0.6rem;">Find Next</span>
                    </button>
                </div>
                <div class="col-auto">
                    <span class="small">No.Of Records : <strong id="recordCount">{{ $recordCount ?? 0 }}</strong></span>
                </div>
                <div class="col-auto">
                    <span class="small text-muted">Press Enter To Modify</span>
                </div>
                <div class="col-auto">
                    <span class="small text-danger fst-italic">File Save on : C:\EsTemp\EWayBill</span>
                </div>
                <div class="col-auto ms-auto">
                    <button type="submit" form="filterForm" name="generate" value="1" class="btn btn-warning btn-sm px-3">
                        <strong>Generate</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Row selection and populate eWayBill To details
    let selectedRow = null;
    $('#reportTable tbody').on('click', 'tr.data-row', function() {
        $('#reportTable tbody tr').removeClass('table-active');
        $(this).addClass('table-active');
        selectedRow = $(this);
        
        // Populate eWayBill To from customer data
        try {
            const customer = JSON.parse($(this).data('customer') || '{}');
            if (customer) {
                $('#toGstin').val(customer.gst_number || '');
                $('#toPinCode').val(customer.pin_code || '');
                $('#toPlace').val(customer.city || '');
                $('#toState').val(customer.state_name || '');
                $('#toAddress1').val(customer.address || '');
                $('#toAddress2').val(customer.address_line2 || '');
            }
        } catch (e) {
            console.error('Error parsing customer data:', e);
        }
    });

    // TAG button
    $('#btnTag').on('click', function() {
        if (!selectedRow) {
            alert('Please select a row first.');
            return;
        }
        const id = selectedRow.data('id');
        // TODO: Implement TAG functionality via AJAX
        alert('TAG functionality for record ID: ' + id);
    });

    // UNTAG button
    $('#btnUntag').on('click', function() {
        if (!selectedRow) {
            alert('Please select a row first.');
            return;
        }
        const id = selectedRow.data('id');
        // TODO: Implement UNTAG functionality via AJAX
        alert('UNTAG functionality for record ID: ' + id);
    });

    // Export EwayBill
    $('#btnExportEway').on('click', function() {
        exportToExcel();
    });

    // Export JSON
    $('#btnExportJson').on('click', function() {
        exportToJson();
    });

    function exportToExcel() {
        const table = document.getElementById('reportTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cols = row.querySelectorAll('td, th');
            let rowData = [];
            for (let j = 0; j < cols.length; j++) {
                let cellText = cols[j].innerText.trim().replace(/"/g, '""');
                rowData.push('"' + cellText + '"');
            }
            csv.push(rowData.join(','));
        }
        
        const csvContent = '\uFEFF' + csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'GSTEWaybills_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    }

    function exportToJson() {
        const rows = $('.data-row');
        let data = [];
        rows.each(function() {
            data.push({
                gst_no: $(this).find('td:eq(0)').text().trim(),
                code: $(this).find('td:eq(1)').text().trim(),
                party_name: $(this).find('td:eq(2)').text().trim(),
                bill_no: $(this).find('td:eq(3)').text().trim(),
                date: $(this).find('td:eq(4)').text().trim(),
                amount: $(this).find('td:eq(5)').text().trim(),
                taxable: $(this).find('td:eq(6)').text().trim(),
                tax: $(this).find('td:eq(7)').text().trim(),
            });
        });
        
        const jsonContent = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonContent], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'GSTEWaybills_' + new Date().toISOString().slice(0,10) + '.json';
        link.click();
    }

    // Find functionality
    $('#btnFind').on('click', function() {
        const searchTerm = prompt('Enter search term:');
        if (searchTerm) {
            $('.data-row').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(searchTerm.toLowerCase())) {
                    $(this).addClass('table-warning');
                } else {
                    $(this).removeClass('table-warning');
                }
            });
        }
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
        if (e.key === 'F1') {
            e.preventDefault();
            $('#btnFind').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { 
    font-size: 0.7rem; 
    border-radius: 0;
    padding: 0.15rem 0.3rem;
}
.table th, .table td { 
    padding: 0.15rem 0.3rem; 
    font-size: 0.7rem; 
    vertical-align: middle; 
}
.table thead th {
    font-weight: bold;
}
.btn-sm { 
    font-size: 0.65rem; 
    padding: 0.15rem 0.4rem; 
    border-radius: 0;
}
.table-active {
    background-color: #4080c0 !important;
    color: white;
}
.data-row:hover {
    background-color: #b8c8b8;
    cursor: pointer;
}
.card {
    border-radius: 0;
}
label.small {
    font-size: 0.7rem;
    margin-bottom: 0;
}
</style>
@endpush
