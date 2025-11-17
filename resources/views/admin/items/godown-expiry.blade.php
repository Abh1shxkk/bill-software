@extends('layouts.admin')
@section('title', 'Godown Expiry - ' . $item->name)
@section('content')
    <style>
        .expiry-header {
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .expiry-table {
            font-size: 12px;
            border-collapse: collapse;
        }
        .expiry-table th {
            background: #e9ecef;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-weight: bold;
        }
        .expiry-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        .expiry-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .expired-row {
            background: #f8d7da;
            color: #721c24;
        }
        .expiring-soon {
            background: #fff3cd;
        }
        .active-row {
            background: #d4edda;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .action-buttons button,
        .action-buttons a {
            padding: 6px 12px;
            margin: 0 5px;
            font-size: 12px;
        }
        .form-section {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            border: 1px solid #dee2e6;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="expiry-header">
            <h4 style="margin: 0; color: #0d6efd;">
                <strong>GODOWN EXPIRY</strong>
            </h4>
            <div style="margin-top: 10px; font-size: 13px;">
                <strong>Item:</strong> {{ $item->name }} | 
                <strong>Packing:</strong> {{ $item->packing ?? '1*1' }} | 
                <strong>Qty:</strong> {{ $item->getTotalQuantity() }}
            </div>
        </div>

        <!-- Expiry Records Table -->
        <div style="border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden;">
            <table class="expiry-table" style="width: 100%; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 12%;">Batch Number</th>
                        <th style="width: 12%;">Expiry Date</th>
                        <th style="width: 10%;">Quantity</th>
                        <th style="width: 15%;">Location</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Days Left</th>
                        <th style="width: 16%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($expiryRecords->count() > 0)
                        @foreach($expiryRecords as $record)
                            @php
                                $daysLeft = now()->diffInDays($record->expiry_date, false);
                                $isExpired = $record->expiry_date < now()->toDateString();
                                $expiringsoon = $daysLeft <= 30 && $daysLeft > 0;
                            @endphp
                            <tr class="@if($isExpired) expired-row @elseif($expiringsoon) expiring-soon @else active-row @endif">
                                <!-- Batch Number -->
                                <td>
                                    <strong>{{ $record->batch->batch_number ?? '-' }}</strong>
                                </td>
                                <!-- Expiry Date -->
                                <td style="text-align: center;">{{ $record->expiry_date->format('d-M-y') }}</td>
                                <!-- Quantity -->
                                <td style="text-align: center; font-weight: bold;">{{ $record->quantity }}</td>
                                <!-- Location -->
                                <td>{{ $record->godown_location ?? '-' }}</td>
                                <!-- Status -->
                                <td style="text-align: center;">
                                    @if($record->status === 'expired')
                                        <span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">EXPIRED</span>
                                    @elseif($record->status === 'disposed')
                                        <span style="background: #6c757d; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">DISPOSED</span>
                                    @else
                                        <span style="background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">ACTIVE</span>
                                    @endif
                                </td>
                                <!-- Days Left -->
                                <td style="text-align: center;">
                                    @if($isExpired)
                                        <span style="color: #dc3545; font-weight: bold;">EXPIRED</span>
                                    @else
                                        <span style="font-weight: bold;">{{ $daysLeft }} days</span>
                                    @endif
                                </td>
                                <!-- Actions -->
                                <td style="text-align: center;">
                                    @if($record->status === 'active')
                                        <form method="POST" action="{{ route('admin.items.godown-expiry.mark-expired', [$item, $record]) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm" title="Mark as Expired">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.items.godown-expiry.delete', [$item, $record]) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                No expiry records found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($expiryRecords->hasPages())
            <div style="margin-top: 20px;">
                {{ $expiryRecords->links() }}
            </div>
        @endif

        <!-- Add New Expiry Record Form -->
        <div class="form-section" style="margin-top: 20px;">
            <h5 style="margin-top: 0;">Add New Expiry Record</h5>
            <form method="POST" action="{{ route('admin.items.godown-expiry.store', $item) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Batch Number:</label>
                        <select name="batch_id" required style="width: 100%;">
                            <option value="">-- Select Batch --</option>
                            @foreach($item->batches as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->batch_number }} (Qty: {{ $batch->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Expiry Date:</label>
                        <input type="date" name="expiry_date" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" min="1" required placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Godown Location:</label>
                        <input type="text" name="godown_location" placeholder="e.g., Shelf A1, Rack B2">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Remarks:</label>
                        <input type="text" name="remarks" placeholder="Optional remarks">
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Expiry Record
                    </button>
                </div>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.items.show', $item) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Item
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <style>
        @media print {
            .form-section, .action-buttons, .btn {
                display: none !important;
            }
        }
    </style>
@endsection
