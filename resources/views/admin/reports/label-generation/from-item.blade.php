{{-- From Item - Barcode Printing --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Barcode Printing</h4>
        </div>
        <div class="card-body p-3">
            {{-- Item Selection --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Item:</label>
                    <select class="form-control" id="item_id">
                        <option value="">-- Select Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->id }} - {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Qty:</label>
                    <input type="number" class="form-control" id="qty" min="1" value="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="addItem">Add</button>
                </div>
            </div>

            {{-- Items Table --}}
            <div style="overflow-x: auto; overflow-y: hidden; width: 100%;">
                <table class="table table-bordered table-striped mb-0" id="itemsTable" style="table-layout: auto; width: 100%; min-width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">CODE</th>
                            <th style="width: 35%;">ITEM NAME</th>
                            <th style="width: 15%;">PACK</th>
                            <th style="width: 15%;">QTY</th>
                            <th style="width: 20%;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        {{-- Items will be added here dynamically --}}
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
        // Add item to table
        $('#addItem').on('click', function() {
            const itemId = $('#item_id').val();
            const itemText = $('#item_id option:selected').text();
            const qty = $('#qty').val();
            
            if (!itemId) {
                alert('Please select an item');
                return;
            }
            
            if (!qty || qty < 1) {
                alert('Please enter a valid quantity');
                return;
            }
            
            const row = `
                <tr>
                    <td class="text-truncate">${itemId}</td>
                    <td class="text-truncate" title="${itemText}">${itemText}</td>
                    <td class="text-center">1</td>
                    <td class="text-center">${qty}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger delete-row" title="Remove" style="font-size: 18px; line-height: 1; padding: 2px 8px;">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
            
            $('#itemsTableBody').append(row);
            
            // Reset form
            $('#item_id').val('');
            $('#qty').val('1');
        });
        
        // Delete single row
        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
        });
        
        // Delete all rows
        $('#deleteAll').on('click', function() {
            if (confirm('Are you sure you want to delete all items?')) {
                $('#itemsTableBody').empty();
            }
        });
        
        // Print barcode
        $('#printBarcode').on('click', function() {
            const rows = $('#itemsTableBody tr');
            
            if (rows.length === 0) {
                alert('Please add at least one item to print');
                return;
            }
            
            // Build form data
            let params = [];
            rows.each(function() {
                const itemId = $(this).find('td:eq(0)').text().trim();
                const qty = $(this).find('td:eq(3)').text().trim();
                params.push('items[]=' + encodeURIComponent(itemId));
                params.push('quantities[' + itemId + ']=' + encodeURIComponent(qty));
            });
            params.push('print=1');
            
            // Open print window
            window.open('{{ route("admin.reports.label.from-item") }}?' + params.join('&'), '_blank');
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