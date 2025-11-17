@extends('layouts.admin')

@section('title', 'Batch Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-box-seam me-2"></i> 
            @if(isset($viewType) && $viewType === 'all')
                All Batches
            @else
                Available Batches
            @endif
        </h4>
        <div class="text-muted small">
            @if(isset($viewType) && $viewType === 'all')
                View all batches including positive, negative, and zero quantities for this item
            @else
                View batches with non-zero quantity (positive or negative only) for this item
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.items.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
        @php
            $currentItemId = $itemId ?? request('item_id');
        @endphp
        @if(isset($viewType) && $viewType === 'all')
            <a href="{{ route('admin.batches.index', ['item_id' => $currentItemId]) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-check-circle me-1"></i> Available Batches
            </a>
        @else
            <a href="{{ route('admin.batches.all', ['item_id' => $currentItemId]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> All Batches
            </a>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                @php
                    $currentItem = isset($itemId) ? \App\Models\Item::find($itemId) : null;
                @endphp
                @if($currentItem)
                    <div class="fw-semibold">{{ $currentItem->name }}</div>
                    <div class="text-muted small">Code: {{ $currentItem->bar_code ?? 'N/A' }}</div>
                @else
                    <div class="text-muted small">Item details not available.</div>
                @endif
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        @if($groupedBatches->count() > 0)
            @foreach($groupedBatches as $itemId => $batches)
                @php
                    $item = \App\Models\Item::find($itemId);
                    
                    // Skip if item not found
                    if (!$item) {
                        continue;
                    }
                    
                    // Get first batch for item name (only if batches exist)
                    $firstBatch = $batches->isNotEmpty() ? $batches->first() : null;
                    
                    // Filter out batches with null batch_no
                    $validBatches = $batches->filter(function($batch) {
                        return !empty($batch->batch_no);
                    });
                @endphp
                
                @if($validBatches->isNotEmpty() || $item)
                    <div class="mb-3 p-2 bg-light rounded">
                        <strong>{{ $firstBatch->item_name ?? $item->name ?? 'N/A' }}</strong>
                        <span class="text-muted ms-2">(Packing: {{ $item->packing ?? '1*10' }})</span>
                    </div>
                    
                    @if($validBatches->isNotEmpty())
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr.</th>
                                    <th>Batch</th>
                                    <th>Exp.</th>
                                    <th>Qty.</th>
                                    <th>S.Rate</th>
                                    <th>F.T.Rate</th>
                                    <th>P.Rate</th>
                                    <th>MRP</th>
                                    <th>WS.Rate</th>
                                    <th>Spl.Rate</th>
                                    <th>Scm.</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validBatches as $index => $batch)
                                    @php
                                        // Calculate values from batch data
                                        $totalQty = $batch->total_qty ?? 0;
                                        $avgRate = $batch->avg_pur_rate ?? 0;
                                        $avgMrp = $batch->avg_mrp ?? 0;
                                        $maxRate = $batch->max_rate ?? 0;
                                        $avgSRate = $batch->avg_s_rate ?? 0;
                                        $avgWsRate = $batch->avg_ws_rate ?? 0;
                                        $avgSplRate = $batch->avg_spl_rate ?? 0;
                                        $avgCgst = $batch->avg_cgst_percent ?? 0;
                                        $avgSgst = $batch->avg_sgst_percent ?? 0;
                                        
                                        $totalGstPercent = $avgCgst + $avgSgst;
                                        
                                        // Calculate F.T.Rate: S.Rate × (1 + GST/100)
                                        $ftRate = $avgSRate > 0 ? ($avgSRate * (1 + ($totalGstPercent / 100))) : 0;
                                        
                                        // Format expiry date as MM/YY
                                        $expiryDisplay = $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '---';
                                        
                                        // Check if expired
                                        $isExpired = $batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->isPast();
                                        
                                        // Check if quantity is zero
                                        $isZero = $totalQty == 0;
                                        
                                        // Check if quantity is negative
                                        $isNegative = $totalQty < 0;
                                        
                                        // Get first batch ID for edit link
                                        $firstBatchId = $batch->first_batch_id ?? null;
                                        
                                        // Determine row class based on status
                                        $rowClass = '';
                                        if ($isExpired) {
                                            $rowClass = 'table-danger'; // Red for expired
                                        } elseif ($isNegative) {
                                            $rowClass = 'table-warning'; // Yellow for negative
                                        } elseif ($isZero) {
                                            $rowClass = 'table-secondary'; // Gray for zero
                                        }
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $batch->batch_no }}
                                            @if($isExpired)
                                                <span class="badge bg-danger ms-1">EXPIRED</span>
                                            @endif
                                        </td>
                                        <td class="{{ $isExpired ? 'text-danger fw-bold' : '' }}">{{ $expiryDisplay }}</td>
                                        <td class="{{ $isNegative ? 'text-danger fw-bold' : ($isZero ? 'text-muted' : '') }}">
                                            {{ number_format($totalQty, 0) }}
                                            @if($isNegative)
                                                <span class="badge bg-danger ms-1">NEGATIVE</span>
                                            @elseif($isZero)
                                                <span class="badge bg-secondary ms-1">ZERO</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($avgSRate, 2) }}</td>
                                        <td>{{ number_format($ftRate, 2) }}</td>
                                        <td>{{ number_format($avgRate, 2) }}</td>
                                        <td>{{ number_format($avgMrp, 2) }}</td>
                                        <td>{{ number_format($avgWsRate, 2) }}</td>
                                        <td>{{ number_format($avgSplRate, 2) }}</td>
                                        <td></td>
                                        <td class="text-end">
                                            @if($firstBatchId)
                                                <a href="{{ route('admin.batches.show', $firstBatchId) }}" 
                                                   class="btn btn-sm btn-outline-info me-1" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.batches.edit', $firstBatchId) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Batch">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-3">
                            <p>No batches found for this item.</p>
                        </div>
                    @endif
                    <hr class="my-3">
                @endif
            @endforeach
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3">No batches found</p>
                <p class="small">Batches will appear here once purchase transactions are created with batch numbers.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Any additional JavaScript can go here
});
</script>
@endpush

