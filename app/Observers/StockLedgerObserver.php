<?php

namespace App\Observers;

use App\Models\StockLedger;
use App\Models\Batch;
use App\Models\ExpiryLedger;

class StockLedgerObserver
{
    /**
     * Handle the StockLedger "created" event.
     * Auto-update batch quantity when stock ledger entry is created
     */
    public function created(StockLedger $stockLedger)
    {
        // Update batch quantity based on transaction type
        if ($stockLedger->batch_id) {
            $batch = Batch::find($stockLedger->batch_id);
            
            if ($batch) {
                // Total quantity including free quantity
                $totalQty = ($stockLedger->quantity ?? 0) + ($stockLedger->free_quantity ?? 0);
                
                if (in_array($stockLedger->transaction_type, ['IN', 'RETURN'])) {
                    // Increase quantity for IN and RETURN
                    $batch->increment('qty', $totalQty);
                } elseif (in_array($stockLedger->transaction_type, ['OUT', 'ADJUSTMENT'])) {
                    // Decrease quantity for OUT and ADJUSTMENT
                    $batch->decrement('qty', $totalQty);
                }
            }
        }

        // Auto-create expiry ledger entry
        if ($stockLedger->batch_id) {
            $batch = Batch::find($stockLedger->batch_id);
            
            if ($batch && $batch->expiry_date) {
                // Calculate running balance from batch qty
                $runningBalance = $stockLedger->running_balance ?? $batch->qty ?? 0;
                
                ExpiryLedger::create([
                    'item_id' => $stockLedger->item_id,
                    'batch_id' => $stockLedger->batch_id,
                    'customer_id' => $stockLedger->customer_id,
                    'supplier_id' => $stockLedger->supplier_id,
                    'transaction_date' => $stockLedger->transaction_date,
                    'trans_no' => $stockLedger->trans_no,
                    'transaction_type' => $stockLedger->transaction_type,
                    'party_name' => $stockLedger->party_name ?? 'N/A',
                    'quantity' => $stockLedger->quantity,
                    'free_quantity' => $stockLedger->free_quantity ?? 0,
                    'running_balance' => $runningBalance,
                    'expiry_date' => $batch->expiry_date,
                    'remarks' => $stockLedger->remarks,
                ]);
            }
        }
    }

    /**
     * Handle the StockLedger "updated" event.
     */
    public function updated(StockLedger $stockLedger)
    {
        // Handle updates if needed
    }

    /**
     * Handle the StockLedger "deleted" event.
     * Reverse the batch quantity update when stock ledger entry is deleted
     */
    public function deleted(StockLedger $stockLedger)
    {
        if ($stockLedger->batch_id) {
            $batch = Batch::find($stockLedger->batch_id);
            
            if ($batch) {
                // Total quantity including free quantity
                $totalQty = ($stockLedger->quantity ?? 0) + ($stockLedger->free_quantity ?? 0);
                
                if (in_array($stockLedger->transaction_type, ['IN', 'RETURN'])) {
                    // Decrease quantity (reverse of created)
                    $batch->decrement('qty', $totalQty);
                } elseif (in_array($stockLedger->transaction_type, ['OUT', 'ADJUSTMENT'])) {
                    // Increase quantity (reverse of created)
                    $batch->increment('qty', $totalQty);
                }
            }
        }

        // Delete corresponding expiry ledger entry
        ExpiryLedger::where('trans_no', $stockLedger->trans_no)
            ->where('item_id', $stockLedger->item_id)
            ->delete();
    }
}
