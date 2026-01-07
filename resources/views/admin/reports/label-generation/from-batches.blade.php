{{-- From Batches - Barcode Printing --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Barcode Printing - From Batches</h4>
        </div>
        <div class="card-body p-3">
            {{-- Batch Selection --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Item:</label>
                    <select class="form-control" id="item_id">
                        <option value="">-- Select Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->id }} - {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch:</label>
                    <select class="form-control" id="batch_id" disabled>
                        <option value="">-- Select Batch --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Qty:</label>
                    <input type="number" class="form-control" id="qty" min="1" value="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="addBatch">Add</button>
                </div>
            </div>

            {{-- Batches Table --}}
            <div style="overflow-x: auto; overflow-y: hidden; width: 100%;">
                <table class="table table-bordered table-striped mb-0" id="batchesTable" style="table-layout: auto; width: 100%; min-width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">BATCH ID</th>
                            <th style="width: 25%;">ITEM NAME</th>
                            <th style="width: 15%;">BATCH NO</th>
                            <th style="width: 12%;">EXPIRY</th>
                            <th style="width: 10%;">MRP</th>
                            <th style="width: 10%;">QTY</th>
                            <th style="width: 18%;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="batchesTableBody">
                        {{-- Batches will be added here dynamically --}}
                    </tbody>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-danger" id="deleteAll">Delete</button>
                <button class="btn btn-success" id="printBarcode">Print (F7)</button>
                <button class="btn btn-secondary" id="closeForm">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load batches when item is selected
        $('#item_id').on('change', function() {
            const itemId = $(this).val();
            const batchSelect = $('#batch_id');
            
            batchSelect.html('<option value="">-- Select Batch --</option>');
            
            if (!itemId) {
                batchSelect.prop('disabled', true);
                return;
            }
            
            // Fetch batches for selected item
            $.ajax({
                url: '{{ route("admin.reports.label.get-batches") }}',
                type: 'GET',
                data: { item_id: itemId },
                success: function(response) {
                    if (response.batches && response.batches.length > 0) {
                        response.batches.forEach(function(batch) {
                            const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB') : 'N/A';
                            batchSelect.append(`<option value="${batch.id}" 
                                data-item-name="${batch.item_name}" 
                                data-batch-no="${batch.batch_no || 'N/A'}" 
                                data-expiry="${expiry}"
                                data-mrp="${batch.mrp || 0}"
                                data-packing="${batch.packing || ''}"
                                data-barcode="${batch.bc || ''}">
                                ${batch.batch_no || 'N/A'} - Exp: ${expiry} - MRP: ₹${parseFloat(batch.mrp || 0).toFixed(2)}
                            </option>`);
                        });
                        batchSelect.prop('disabled', false);
                    } else {
                        batchSelect.html('<option value="">No batches found</option>');
                        batchSelect.prop('disabled', true);
                    }
                },
                error: function() {
                    alert('Error loading batches');
                }
            });
        });

        // Add batch to table
        $('#addBatch').on('click', function() {
            const batchId = $('#batch_id').val();
            const batchOption = $('#batch_id option:selected');
            const qty = $('#qty').val();
            
            if (!batchId) {
                alert('Please select a batch');
                return;
            }
            
            if (!qty || qty < 1) {
                alert('Please enter a valid quantity');
                return;
            }
            
            // Check if batch already added
            if ($(`#batchesTableBody tr[data-batch-id="${batchId}"]`).length > 0) {
                alert('This batch is already added');
                return;
            }
            
            const itemName = batchOption.data('item-name');
            const batchNo = batchOption.data('batch-no');
            const expiry = batchOption.data('expiry');
            const mrp = parseFloat(batchOption.data('mrp') || 0).toFixed(2);
            
            const row = `
                <tr data-batch-id="${batchId}">
                    <td class="text-center">${batchId}</td>
                    <td class="text-truncate" title="${itemName}">${itemName}</td>
                    <td class="text-center">${batchNo}</td>
                    <td class="text-center">${expiry}</td>
                    <td class="text-center">₹${mrp}</td>
                    <td class="text-center">${qty}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger delete-row" title="Remove" style="font-size: 18px; line-height: 1; padding: 2px 8px;">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
            
            $('#batchesTableBody').append(row);
            
            // Reset form
            $('#batch_id').val('');
            $('#qty').val('1');
        });
        
        // Delete single row
        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
        });
        
        // Delete all rows
        $('#deleteAll').on('click', function() {
            if (confirm('Are you sure you want to delete all batches?')) {
                $('#batchesTableBody').empty();
            }
        });
        
        // Print barcode
        $('#printBarcode').on('click', function() {
            const rows = $('#batchesTableBody tr');
            
            if (rows.length === 0) {
                alert('Please add at least one batch to print');
                return;
            }
            
            // Build form data
            let params = [];
            rows.each(function() {
                const batchId = $(this).data('batch-id');
                const qty = $(this).find('td:eq(5)').text().trim();
                params.push('batches[]=' + encodeURIComponent(batchId));
                params.push('quantities[' + batchId + ']=' + encodeURIComponent(qty));
            });
            params.push('print=1');
            
            // Open print window
            window.open('{{ route("admin.reports.label.from-batches") }}?' + params.join('&'), '_blank');
        });
        
        // F7 keyboard shortcut for print
        $(document).on('keydown', function(e) {
            if (e.key === 'F7') {
                e.preventDefault();
                $('#printBarcode').click();
            }
        });
        
        // Close form
        $('#closeForm').on('click', function() {
            window.history.back();
        });
    });
</script>
@endpush
