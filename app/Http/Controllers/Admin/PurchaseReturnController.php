<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Batch;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseReturnTransactionItem;
use App\Models\PurchaseReturnAdjustment;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseReturnController extends Controller
{
    /**
     * Display purchase return transaction form
     */
    public function transaction()
    {
        // Get all active suppliers (not deleted)
        $suppliers = Supplier::where('is_deleted', 0)
            ->orderBy('name')
            ->get();

        return view('admin.purchase-return.transaction', compact('suppliers'));
    }

    /**
     * Display purchase return modification form
     */
    public function modification()
    {
        // Get all active suppliers (not deleted)
        $suppliers = Supplier::where('is_deleted', 0)
            ->orderBy('name')
            ->get();

        return view('admin.purchase-return.modification', compact('suppliers'));
    }

    /**
     * Get next transaction number
     */
    public function getNextTransactionNumber()
    {
        // Check if purchase_return_transactions table exists
        if (!DB::getSchemaBuilder()->hasTable('purchase_return_transactions')) {
            return response()->json(['next_trn_no' => 1]);
        }

        // Get the last transaction from purchase_return_transactions table
        $lastTransaction = PurchaseReturnTransaction::orderBy('id', 'desc')->first();

        if ($lastTransaction && $lastTransaction->pr_no) {
            // Extract number from PR0001, PR0002 format
            $lastPrNo = $lastTransaction->pr_no;
            $lastNumber = (int) substr($lastPrNo, 2); // Remove 'PR' prefix
            $nextNumber = $lastNumber + 1;
            $nextTrnNo = 'PR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // Format: PR0003
        } else {
            $nextTrnNo = 'PR0001';
        }

        return response()->json(['next_trn_no' => $nextTrnNo]);
    }

    /**
     * Verify if batch belongs to current supplier
     */
    public function verifyBatchSupplier(Request $request)
    {
        try {
            $purchaseTransactionId = $request->get('purchase_transaction_id');
            $currentSupplierId = $request->get('supplier_id');

            if (!$purchaseTransactionId || !$currentSupplierId) {
                return response()->json([
                    'success' => false,
                    'is_same_supplier' => false,
                    'message' => 'Missing required parameters'
                ]);
            }

            // Get purchase transaction to check supplier
            $purchaseTransaction = PurchaseTransaction::find($purchaseTransactionId);
            
            if (!$purchaseTransaction) {
                return response()->json([
                    'success' => false,
                    'is_same_supplier' => false,
                    'message' => 'Purchase transaction not found'
                ]);
            }

            $batchSupplierId = $purchaseTransaction->supplier_id;
            $isSameSupplier = ($batchSupplierId == $currentSupplierId);

            // Get batch supplier name for warning message
            $batchSupplierName = 'Unknown Supplier';
            if ($purchaseTransaction->supplier) {
                $batchSupplierName = $purchaseTransaction->supplier->name;
            }

            return response()->json([
                'success' => true,
                'is_same_supplier' => $isSameSupplier,
                'batch_supplier_id' => $batchSupplierId,
                'batch_supplier_name' => $batchSupplierName,
                'current_supplier_id' => $currentSupplierId
            ]);

        } catch (\Exception $e) {
            \Log::error('Verify batch supplier error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'is_same_supplier' => false,
                'message' => 'Error verifying supplier'
            ], 500);
        }
    }

    /**
     * Get batches for a specific supplier and item
     * This will fetch all batches from past purchase transactions
     */
    public function getBatches(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        $itemId = $request->input('item_id');

        if (!$supplierId || !$itemId) {
            return response()->json(['batches' => []]);
        }

        // Get item details for packing, company, hsn_code, s_rate etc.
        $item = Item::with('company:id,name')->find($itemId);
        
        // Calculate total closing qty across all batches for this item
        $totalClQty = Batch::where('item_id', $itemId)
            ->where('is_deleted', 0)
            ->sum('qty');

        // Get all purchase transactions for this supplier
        $purchaseTransactionIds = PurchaseTransaction::where('supplier_id', $supplierId)
            ->pluck('id');

        // Get all batches for this item from those purchase transactions
        // Only show batches that are not deleted and have qty > 0
        $batches = Batch::where('item_id', $itemId)
            ->whereIn('purchase_transaction_id', $purchaseTransactionIds)
            ->where('is_deleted', 0)
            ->where('qty', '>', 0)
            ->with(['purchaseTransaction' => function($query) {
                $query->select('id', 'bill_no', 'bill_date');
            }, 'transactionItem' => function($query) {
                $query->select('id', 'purchase_transaction_id', 'item_id', 'qty', 'free_qty');
            }])
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function($batch) use ($item, $totalClQty) {
                // Get original purchase quantity from transaction item
                $originalPurchaseQty = 0;
                if ($batch->transactionItem) {
                    $originalPurchaseQty = ($batch->transactionItem->qty ?? 0) + ($batch->transactionItem->free_qty ?? 0);
                }
                
                // Available quantity = Current batch qty (already updated after sales/returns)
                // Batch table ki qty field me hi current available qty hai
                $availableQty = $batch->qty ?? 0;
                
                return [
                    'batch_id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'expiry' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '',
                    'qty' => $originalPurchaseQty, // Original purchase quantity
                    'available_qty' => max(0, $availableQty), // Current available quantity
                    'mrp' => $batch->mrp ?? 0,
                    'purchase_rate' => $batch->pur_rate ?? 0,
                    // Batch specific rates - from selected batch
                    'sale_rate' => $batch->s_rate ?? 0,
                    's_rate' => $batch->s_rate ?? 0,
                    'ws_rate' => $batch->ws_rate ?? 0,
                    'spl_rate' => $batch->spl_rate ?? 0,
                    'purchase_transaction_id' => $batch->purchase_transaction_id,
                    'bill_no' => $batch->purchaseTransaction ? $batch->purchaseTransaction->bill_no : '',
                    'bill_date' => $batch->purchaseTransaction && $batch->purchaseTransaction->bill_date 
                        ? $batch->purchaseTransaction->bill_date->format('Y-m-d') : '',
                    'invoice_no' => $batch->purchaseTransaction ? $batch->purchaseTransaction->invoice_no : null,
                    'invoice_date' => $batch->purchaseTransaction ? $batch->purchaseTransaction->invoice_date : null,
                    // Item related fields
                    'hsn_code' => $item->hsn_code ?? '',
                    'packing' => $item->packing ?? '',
                    'unit' => $item->unit ?? 'PCS',
                    'company_name' => $item->company->name ?? '',
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'total_cl_qty' => $totalClQty, // Total closing qty across all batches
                ];
            });

        return response()->json(['batches' => $batches]);
    }

    /**
     * Store a new purchase return transaction
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Generate PR number
            $prNo = PurchaseReturnTransaction::generatePRNumber();

            // Create purchase return transaction
            $purchaseReturn = PurchaseReturnTransaction::create([
                'pr_no' => $prNo,
                'series' => 'PR',
                'return_date' => $request->return_date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'gst_vno' => $request->gst_vno,
                'tax_flag' => $request->tax_flag ?? 'Y',
                'rate_diff_flag' => $request->rate_diff_flag ?? 'N',
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'dis1_amount' => $request->dis1_amount ?? 0,
                'remarks' => $request->remarks,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Save items
            $items = $request->items ?? [];
            $rowOrder = 0;
            
            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                // Handle expiry date format (MM/YYYY to proper date)
                $expiryDate = null;
                $expiryInput = $item['expiry'] ?? $item['expiry_date'] ?? null;
                if ($expiryInput) {
                    // Check if format is MM/YYYY
                    if (preg_match('/^(\d{1,2})\/(\d{4})$/', $expiryInput, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        $expiryDate = "{$year}-{$month}-01"; // First day of the month
                    } else {
                        // Try to parse as regular date
                        try {
                            $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                }
                
                $returnItem = PurchaseReturnTransactionItem::create([
                    'purchase_return_transaction_id' => $purchaseReturn->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'pur_rate' => $item['purchase_rate'] ?? $item['pur_rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'ws_rate' => $item['ws_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'spl_rate' => $item['spl_rate'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cess_percent' => $item['cess_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'cess_amount' => $item['cess_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Update batch quantity (decrease for return)
                if ($returnItem->batch_id) {
                    $batch = Batch::find($returnItem->batch_id);
                    if ($batch) {
                        // Calculate total return quantity (qty + free_qty)
                        $returnQty = ($returnItem->qty ?? 0) + ($returnItem->free_qty ?? 0);
                        
                        // Decrease batch qty
                        $currentBatchQty = $batch->qty;
                        $newBatchQty = $currentBatchQty - $returnQty;
                        
                        // Update batch qty (can go negative if returning more than available)
                        $batch->qty = $newBatchQty;
                        $batch->save();
                        
                        \Log::info('Batch qty updated', [
                            'batch_id' => $batch->id,
                            'batch_no' => $batch->batch_no,
                            'previous_qty' => $currentBatchQty,
                            'return_qty' => $returnQty,
                            'new_qty' => $newBatchQty
                        ]);
                    }
                    
                    // Create stock ledger entry for tracking (without observer auto-update)
                    $stockLedger = new StockLedger();
                    $stockLedger->item_id = $returnItem->item_id;
                    $stockLedger->batch_id = $returnItem->batch_id;
                    $stockLedger->transaction_type = 'OUT';
                    $stockLedger->reference_type = 'PURCHASE_RETURN';
                    $stockLedger->reference_id = $purchaseReturn->id;
                    $stockLedger->transaction_date = $purchaseReturn->return_date;
                    $stockLedger->quantity = $returnItem->qty;
                    $stockLedger->free_quantity = $returnItem->free_qty;
                    $stockLedger->rate = $returnItem->pur_rate;
                    $stockLedger->created_by = Auth::id();
                    $stockLedger->saveQuietly(); // Save without triggering observer
                }
            }

            // Save adjustments if provided (Credit adjustment against purchase invoices)
            $adjustments = $request->adjustments ?? [];
            $totalAdjusted = 0;
            
            foreach ($adjustments as $adjustment) {
                if (empty($adjustment['purchase_transaction_id']) || empty($adjustment['amount']) || $adjustment['amount'] <= 0) {
                    continue;
                }

                $adjustedAmount = floatval($adjustment['amount']);
                
                // Create adjustment record
                $adjRecord = PurchaseReturnAdjustment::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_transaction_id' => $adjustment['purchase_transaction_id'],
                    'adjusted_amount' => $adjustedAmount,
                    'adjustment_date' => $purchaseReturn->return_date,
                    'created_by' => Auth::id(),
                ]);
                
                $totalAdjusted += $adjustedAmount;
                
                \Log::info('Purchase invoice adjusted', [
                    'purchase_transaction_id' => $adjustment['purchase_transaction_id'],
                    'adjusted_amount' => $adjustedAmount,
                    'purchase_return_id' => $purchaseReturn->id
                ]);
            }
            
            \Log::info('Purchase return saved successfully', [
                'pr_no' => $prNo,
                'net_amount' => $purchaseReturn->net_amount,
                'total_adjusted' => $totalAdjusted,
                'items_count' => count($items)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase return saved successfully!',
                'pr_no' => $prNo,
                'id' => $purchaseReturn->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purchase Return Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving purchase return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier's outstanding purchase invoices for credit adjustment
     */
    public function getSupplierInvoices($supplierId)
    {
        try {
            // Get all purchase transactions for this supplier
            $invoices = PurchaseTransaction::where('supplier_id', $supplierId)
                ->where('status', '!=', 'deleted')
                ->orderBy('bill_date', 'desc')
                ->get()
                ->map(function ($invoice) {
                    // Calculate already adjusted amount from purchase_return_adjustments table
                    $adjustedAmount = PurchaseReturnAdjustment::where('purchase_transaction_id', $invoice->id)
                        ->sum('adjusted_amount');
                    
                    // Total invoice amount (use inv_amount or net_amount)
                    $totalAmount = $invoice->inv_amount ?? $invoice->net_amount ?? 0;
                    
                    // Calculate balance (Total - Already Adjusted)
                    $balanceAmount = $totalAmount - $adjustedAmount;

                    return [
                        'id' => $invoice->id,
                        'bill_no' => $invoice->bill_no,
                        'bill_date' => $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '',
                        'total_amount' => number_format($totalAmount, 2, '.', ''),
                        'adjusted_amount' => number_format($adjustedAmount, 2, '.', ''),
                        'balance_amount' => number_format(max(0, $balanceAmount), 2, '.', ''),
                    ];
                })
                ->filter(function ($invoice) {
                    // Only show invoices with balance > 0
                    return floatval($invoice['balance_amount']) > 0;
                })
                ->values();

            \Log::info('Supplier invoices loaded', [
                'supplier_id' => $supplierId,
                'total_invoices' => $invoices->count()
            ]);

            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Supplier Invoices Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get past purchase return transactions for modification screen
     * Optionally filtered by return_date and supplier
     */
    public function getPastReturns(Request $request)
    {
        try {
            $date = $request->get('date');
            $supplierId = $request->get('supplier_id');

            $query = PurchaseReturnTransaction::with('supplier:supplier_id,name')
                ->where('status', '!=', 'deleted')
                ->orderBy('id', 'desc');

            // Filter by date if provided
            if ($date) {
                $query->whereDate('return_date', $date);
            }

            // Filter by supplier if provided
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            // Limit to last 100 records if no date filter
            if (!$date) {
                $query->limit(100);
            }

            $transactions = $query->get()->map(function ($trn) {
                return [
                    'id' => $trn->id,
                    'pr_no' => $trn->pr_no,
                    'return_date' => $trn->return_date ? $trn->return_date->format('d-M-y') : '',
                    'supplier_name' => $trn->supplier ? $trn->supplier->name : ($trn->supplier_name ?? ''),
                    'time' => $trn->created_at ? $trn->created_at->format('H:i') : '',
                    'uid' => 'MASTER',
                    'f_uid' => 'MASTER',
                    'amount' => number_format($trn->net_amount ?? 0, 2, '.', ''),
                    'status' => $trn->status ?? 'active',
                ];
            });

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get Past Purchase Returns Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading past purchase returns: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get complete details of a purchase return transaction
     */
    public function getReturnDetails($id)
    {
        try {
            $transaction = PurchaseReturnTransaction::with(['items', 'adjustments', 'supplier:supplier_id,name'])
                ->findOrFail($id);

            $header = [
                'id' => $transaction->id,
                'pr_no' => $transaction->pr_no,
                'return_date' => $transaction->return_date ? $transaction->return_date->format('Y-m-d') : null,
                'supplier_id' => $transaction->supplier_id,
                'supplier_name' => $transaction->supplier_name ?? ($transaction->supplier->name ?? ''),
                'invoice_no' => $transaction->invoice_no,
                'invoice_date' => $transaction->invoice_date ? $transaction->invoice_date->format('Y-m-d') : null,
                'gst_vno' => $transaction->gst_vno,
                'tax_flag' => $transaction->tax_flag,
                'rate_diff_flag' => $transaction->rate_diff_flag,
                'nt_amount' => $transaction->nt_amount ?? 0,
                'sc_amount' => $transaction->sc_amount ?? 0,
                'dis_amount' => $transaction->dis_amount ?? 0,
                'scm_amount' => $transaction->scm_amount ?? 0,
                'scm_percent' => $transaction->scm_percent ?? 0,
                'tax_amount' => $transaction->tax_amount ?? 0,
                'net_amount' => $transaction->net_amount ?? 0,
                'tcs_amount' => $transaction->tcs_amount ?? 0,
                'dis1_amount' => $transaction->dis1_amount ?? 0,
                'remarks' => $transaction->remarks,
                'status' => $transaction->status,
            ];

            $items = $transaction->items->map(function ($item) {
                // Calculate total closing qty across all batches for this item
                $totalClQty = Batch::where('item_id', $item->item_id)
                    ->where('is_deleted', 0)
                    ->sum('qty');
                
                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'batch_id' => $item->batch_id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no,
                    'expiry' => $item->expiry_date ? $item->expiry_date->format('m/Y') : null,
                    'qty' => $item->qty,
                    'free_qty' => $item->free_qty,
                    'pur_rate' => $item->pur_rate,
                    'dis_percent' => $item->dis_percent,
                    'ft_rate' => $item->ft_rate,
                    'ft_amount' => $item->ft_amount,
                    'mrp' => $item->mrp,
                    'ws_rate' => $item->ws_rate,
                    's_rate' => $item->s_rate,
                    'spl_rate' => $item->spl_rate,
                    'cgst_percent' => $item->cgst_percent,
                    'sgst_percent' => $item->sgst_percent,
                    'cess_percent' => $item->cess_percent,
                    'cgst_amount' => $item->cgst_amount,
                    'sgst_amount' => $item->sgst_amount,
                    'cess_amount' => $item->cess_amount,
                    'tax_amount' => $item->tax_amount,
                    'net_amount' => $item->net_amount,
                    'hsn_code' => $item->hsn_code,
                    'packing' => $item->packing,
                    'unit' => $item->unit,
                    'company_name' => $item->company_name,
                    'row_order' => $item->row_order,
                    'total_cl_qty' => $totalClQty, // Total closing qty across all batches
                ];
            });

            $adjustments = $transaction->adjustments->map(function ($adj) {
                return [
                    'id' => $adj->id,
                    'purchase_transaction_id' => $adj->purchase_transaction_id,
                    'adjusted_amount' => $adj->adjusted_amount,
                    'adjustment_date' => $adj->adjustment_date ? $adj->adjustment_date->format('Y-m-d') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'header' => $header,
                'items' => $items,
                'adjustments' => $adjustments,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get Purchase Return Details Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading purchase return details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get purchase return by PR number (pr_no) like PR0001
     */
    public function getByPrNo($prNo)
    {
        try {
            $transaction = PurchaseReturnTransaction::where('pr_no', $prNo)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase return not found for PR number ' . $prNo,
                ], 404);
            }

            return $this->getReturnDetails($transaction->id);
        } catch (\Exception $e) {
            \Log::error('Get Purchase Return By PR No Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error searching purchase return: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing purchase return transaction
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find existing transaction
            $purchaseReturn = PurchaseReturnTransaction::findOrFail($id);
            
            // Store old items to restore batch quantities
            $oldItems = $purchaseReturn->items;
            
            // Revert old batch quantities first
            foreach ($oldItems as $oldItem) {
                if ($oldItem->batch_id) {
                    $batch = Batch::find($oldItem->batch_id);
                    if ($batch) {
                        // Add back the returned quantity
                        $returnQty = ($oldItem->qty ?? 0) + ($oldItem->free_qty ?? 0);
                        $batch->qty = $batch->qty + $returnQty;
                        $batch->save();
                    }
                }
            }
            
            // Revert old adjustments to purchase transactions
            $oldAdjustments = $purchaseReturn->adjustments;
            foreach ($oldAdjustments as $oldAdj) {
                $purchaseTransaction = PurchaseTransaction::find($oldAdj->purchase_transaction_id);
                if ($purchaseTransaction) {
                    // Add back the adjusted amount to balance
                    $purchaseTransaction->balance_amount = ($purchaseTransaction->balance_amount ?? 0) + ($oldAdj->adjusted_amount ?? 0);
                    $purchaseTransaction->save();
                }
            }

            // Update purchase return header
            $purchaseReturn->update([
                'return_date' => $request->return_date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $request->supplier_name,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'gst_vno' => $request->gst_vno,
                'tax_flag' => $request->tax_flag ?? 'Y',
                'rate_diff_flag' => $request->rate_diff_flag ?? 'N',
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'dis1_amount' => $request->dis1_amount ?? 0,
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items and adjustments
            $purchaseReturn->items()->delete();
            $purchaseReturn->adjustments()->delete();

            // Save new items (same logic as store method)
            $items = $request->items ?? [];
            $rowOrder = 0;
            
            foreach ($items as $item) {
                if (empty($item['item_id']) || empty($item['qty']) || $item['qty'] <= 0) {
                    continue;
                }

                // Handle expiry date format
                $expiryDate = null;
                $expiryInput = $item['expiry'] ?? $item['expiry_date'] ?? null;
                if ($expiryInput) {
                    if (preg_match('/^(\d{1,2})\/(\d{4})$/', $expiryInput, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        $expiryDate = "{$year}-{$month}-01";
                    } else {
                        try {
                            $expiryDate = \Carbon\Carbon::parse($expiryInput)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                }
                
                $returnItem = PurchaseReturnTransactionItem::create([
                    'purchase_return_transaction_id' => $purchaseReturn->id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'item_code' => $item['code'] ?? $item['item_code'] ?? '',
                    'item_name' => $item['name'] ?? $item['item_name'] ?? '',
                    'batch_no' => $item['batch'] ?? $item['batch_no'] ?? '',
                    'expiry_date' => $expiryDate,
                    'qty' => $item['qty'] ?? 0,
                    'free_qty' => $item['free_qty'] ?? 0,
                    'pur_rate' => $item['purchase_rate'] ?? $item['pur_rate'] ?? 0,
                    'dis_percent' => $item['dis_percent'] ?? 0,
                    'ft_rate' => $item['ft_rate'] ?? 0,
                    'ft_amount' => $item['ft_amount'] ?? 0,
                    'mrp' => $item['mrp'] ?? 0,
                    'ws_rate' => $item['ws_rate'] ?? 0,
                    's_rate' => $item['s_rate'] ?? 0,
                    'spl_rate' => $item['spl_rate'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'cess_percent' => $item['cess_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'cess_amount' => $item['cess_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'] ?? 0,
                    'hsn_code' => $item['hsn_code'] ?? '',
                    'packing' => $item['packing'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'company_name' => $item['company_name'] ?? '',
                    'row_order' => $rowOrder++,
                ]);

                // Update batch quantity (decrease for new return)
                if ($returnItem->batch_id) {
                    $batch = Batch::find($returnItem->batch_id);
                    if ($batch) {
                        $returnQty = ($returnItem->qty ?? 0) + ($returnItem->free_qty ?? 0);
                        $batch->qty = $batch->qty - $returnQty;
                        $batch->save();
                    }
                }
            }

            // Save new adjustments
            $adjustments = $request->adjustments ?? [];
            foreach ($adjustments as $adjustment) {
                if (empty($adjustment['purchase_transaction_id']) || empty($adjustment['amount']) || $adjustment['amount'] <= 0) {
                    continue;
                }

                PurchaseReturnAdjustment::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_transaction_id' => $adjustment['purchase_transaction_id'],
                    'adjusted_amount' => $adjustment['amount'],
                ]);

                // Update purchase transaction balance
                $purchaseTransaction = PurchaseTransaction::find($adjustment['purchase_transaction_id']);
                if ($purchaseTransaction) {
                    $purchaseTransaction->balance_amount = ($purchaseTransaction->balance_amount ?? $purchaseTransaction->net_amount) - $adjustment['amount'];
                    $purchaseTransaction->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase return updated successfully!',
                'pr_no' => $purchaseReturn->pr_no,
                'id' => $purchaseReturn->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purchase Return Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase return: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of purchase return transactions
     */
    public function index(Request $request)
    {
        $query = PurchaseReturnTransaction::with(['supplier']);

        // Apply search filters
        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'supplier_name':
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('supplier_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhereHas('supplier', function ($sq) use ($searchTerm) {
                              $sq->where('name', 'LIKE', "%{$searchTerm}%");
                          });
                    });
                    break;
                case 'pr_no':
                    $query->where('pr_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'invoice_no':
                    $query->where('invoice_no', 'LIKE', "%{$searchTerm}%");
                    break;
            }
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        // Exclude deleted transactions
        $query->where('status', '!=', 'deleted');

        // Order by latest first and paginate
        $transactions = $query->orderBy('return_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.purchase-return.index', compact('transactions'));
    }

    /**
     * Display the specified purchase return transaction
     */
    public function show($id)
    {
        $transaction = PurchaseReturnTransaction::with(['supplier', 'items', 'adjustments.purchaseTransaction'])
            ->findOrFail($id);

        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.purchase-return.show', compact('transaction'));
    }

    /**
     * Remove the specified purchase return transaction
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturnTransaction::with(['items', 'adjustments'])->findOrFail($id);

            // Restore batch quantities
            foreach ($purchaseReturn->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        // Add back the returned quantity
                        $returnQty = ($item->qty ?? 0) + ($item->free_qty ?? 0);
                        $batch->qty = $batch->qty + $returnQty;
                        $batch->save();
                    }
                }
            }

            // Delete stock ledger entries for this purchase return
            StockLedger::where('reference_type', 'PURCHASE_RETURN')
                ->where('reference_id', $purchaseReturn->id)
                ->delete();

            // Delete adjustments and items (cascade should handle this, but being explicit)
            $purchaseReturn->adjustments()->delete();
            $purchaseReturn->items()->delete();

            // Soft delete by setting status to deleted (or hard delete)
            $purchaseReturn->update(['status' => 'deleted']);
            // Or hard delete: $purchaseReturn->delete();

            DB::commit();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase return deleted successfully!'
                ]);
            }

            return redirect()->route('admin.purchase-return.index')
                ->with('success', 'Purchase return deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purchase Return Delete Error: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting purchase return: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.purchase-return.index')
                ->with('error', 'Error deleting purchase return: ' . $e->getMessage());
        }
    }
}
