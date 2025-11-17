<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\GodownExpiry;

class BatchObserver
{
    /**
     * Handle the Batch "created" event.
     * Auto-create godown expiry record when batch is created
     */
    public function created(Batch $batch)
    {
        // Auto-create godown expiry record if expiry_date exists
        if ($batch->expiry_date && $batch->quantity > 0) {
            GodownExpiry::create([
                'item_id' => $batch->item_id,
                'batch_id' => $batch->id,
                'expiry_date' => $batch->expiry_date,
                'quantity' => $batch->quantity,
                'godown_location' => $batch->godown ?? 'Default',
                'status' => 'active',
                'remarks' => 'Auto-created from batch',
            ]);
        }
    }

    /**
     * Handle the Batch "updated" event.
     * Sync godown expiry when batch is updated
     */
    public function updated(Batch $batch)
    {
        // Update godown expiry record if expiry_date changed
        if ($batch->isDirty('expiry_date') || $batch->isDirty('quantity')) {
            $godownExpiry = GodownExpiry::where('batch_id', $batch->id)
                ->where('status', 'active')
                ->first();

            if ($godownExpiry) {
                $godownExpiry->update([
                    'expiry_date' => $batch->expiry_date,
                    'quantity' => $batch->quantity,
                    'godown_location' => $batch->godown ?? 'Default',
                ]);
            }
        }
    }

    /**
     * Handle the Batch "deleted" event.
     * Mark godown expiry as disposed when batch is deleted
     */
    public function deleted(Batch $batch)
    {
        // Mark godown expiry as disposed instead of deleting
        GodownExpiry::where('batch_id', $batch->id)
            ->update(['status' => 'disposed']);
    }
}
