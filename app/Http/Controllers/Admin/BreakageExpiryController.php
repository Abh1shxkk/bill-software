<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;

class BreakageExpiryController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display list of breakage/expiry transactions
     */
    public function index(Request $request)
    {
        $query = \App\Models\BreakageExpiryTransaction::with(['customer', 'salesman']);

        // Apply filters
        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'customer_name':
                    $query->where('customer_name', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'sr_no':
                    $query->where('sr_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'salesman_name':
                    $query->where('salesman_name', 'LIKE', "%{$searchTerm}%");
                    break;
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Handle AJAX request
        if ($request->ajax || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'transactions' => $transactions->items()
            ]);
        }

        return view('admin.breakage-expiry.index', compact('transactions'));
    }

    /**
     * Display breakage/expiry transaction details
     */
    public function show($id)
    {
        $transaction = \App\Models\BreakageExpiryTransaction::with(['items', 'customer', 'salesman'])
            ->findOrFail($id);

        // Handle AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return view('admin.breakage-expiry.show', compact('transaction'));
    }

    /**
     * Get breakage/expiry transaction by SR No
     */
    public function getBySrNo($srNo)
    {
        try {
            $transaction = \App\Models\BreakageExpiryTransaction::with(['items'])
                ->where('sr_no', $srNo)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching transaction by SR No: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transaction'
            ], 500);
        }
    }

    /**
     * Display breakage/expiry transaction form
     */
    public function transaction()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        $nextSrNo = $this->generateSrNo('BE');
        
        return view('admin.breakage-expiry.transaction', compact('customers', 'salesmen', 'nextSrNo'));
    }

    /**
     * Delete breakage/expiry transaction
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            
            $transaction = \App\Models\BreakageExpiryTransaction::findOrFail($id);
            
            // Restore sale transaction balances before deleting
            $existingAdjustments = \App\Models\BreakageExpiryAdjustment::where('breakage_expiry_transaction_id', $id)->get();
            foreach ($existingAdjustments as $adj) {
                $saleTransaction = \App\Models\SaleTransaction::find($adj->sale_transaction_id);
                if ($saleTransaction) {
                    // Restore the adjusted amount back to balance
                    $saleTransaction->balance_amount = ($saleTransaction->balance_amount ?? 0) + $adj->adjusted_amount;
                    $saleTransaction->save();
                    \Log::info("Restored balance for Sale {$saleTransaction->invoice_no}: +{$adj->adjusted_amount} (transaction deleted)");
                }
            }
            
            // Delete related adjustments
            \App\Models\BreakageExpiryAdjustment::where('breakage_expiry_transaction_id', $id)->delete();
            
            // Delete related transaction items
            \App\Models\BreakageExpiryTransactionItem::where('breakage_expiry_transaction_id', $id)->delete();
            
            // Delete the main transaction
            $transaction->delete();
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting breakage/expiry transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display breakage/expiry modification form
     */
    public function modification()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        
        return view('admin.breakage-expiry.modification', compact('customers', 'salesmen'));
    }

    /**
     * Display expiry date modification form
     */
    public function expiryDate()
    {
        return view('admin.breakage-expiry.expiry-date');
    }

    /**
     * Store breakage/expiry transaction
     */
    public function storeTransaction(Request $request)
    {
        try {
            // Validate transaction date (no backdating, max 1 day future)
            $dateError = $this->validateTransactionDate($request, 'breakage_expiry', 'transaction_date');
            if ($dateError) {
                return $this->dateValidationErrorResponse($dateError);
            }
            
            // Validate the request
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'salesman_id' => 'nullable|exists:sales_men,id',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'with_credit_note' => 'boolean',
                'adjustments' => 'array',
                'adjustments.*.sale_id' => 'required_with:adjustments|exists:sale_transactions,id',
                'adjustments.*.adjusted_amount' => 'required_with:adjustments|numeric|min:0',
            ]);

            \DB::beginTransaction();

            // Get customer and salesman names
            $customer = Customer::find($request->customer_id);
            $salesman = $request->salesman_id ? SalesMan::find($request->salesman_id) : null;

            // Generate SR No
            $srNo = $this->generateSrNo($request->series ?? 'BE');

            // Create transaction
            $transaction = \App\Models\BreakageExpiryTransaction::create([
                'sr_no' => $srNo,
                'series' => $request->series ?? 'BE',
                'transaction_date' => $request->transaction_date,
                'end_date' => $request->end_date,
                'customer_id' => $request->customer_id,
                'customer_name' => $customer->name,
                'salesman_id' => $request->salesman_id,
                'salesman_name' => $salesman ? $salesman->name : null,
                'gst_vno' => $request->gst_vno ?? 'N',
                'note_type' => $request->note_type ?? 'N',
                'with_gst' => $request->with_gst ?? 'N',
                'inc' => $request->inc ?? 'N',
                'rev_charge' => $request->rev_charge ?? 'Y',
                'adjusted' => $request->adjusted ?? 'X',
                'dis_rpl' => $request->dis_rpl,
                'brk' => $request->brk,
                'exp' => $request->exp,
                'mrp_value' => $request->summary_mrp_value ?? 0,
                'gross_amount' => $request->summary_gross ?? 0,
                'discount_amount' => $request->summary_discount ?? 0,
                'scheme_amount' => $request->summary_scheme ?? 0,
                'tax_amount' => $request->summary_tax ?? 0,
                'net_amount' => $request->summary_net ?? 0,
                'packing' => $request->detail_packing ?? 0,
                'unit' => $request->detail_unit ?? 0,
                'cl_qty' => $request->detail_cl_qty ?? 0,
                'scm_amt' => $request->detail_scm_amt ?? 0,
                'dis_amt' => $request->detail_dis_amt ?? 0,
                'subtotal' => $request->detail_subtotal ?? 0,
                'tax_amt' => $request->detail_tax_amt ?? 0,
                'net_amt' => $request->detail_net_amt ?? 0,
                'remarks' => $request->remarks,
                'status' => 'active',
                'created_by' => auth()->user()->user_id ?? null,
            ]);

            // Create transaction items
            foreach ($request->items as $index => $item) {
                if (empty($item['code']) || empty($item['name'])) {
                    continue; // Skip empty rows
                }

                $itemModel = \App\Models\Item::find($item['code']);
                $batchModel = null;
                
                if (!empty($item['batch'])) {
                    $batchModel = \App\Models\Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                }

                // Calculate tax amounts
                $qty = floatval($item['qty'] ?? 0);
                $mrp = floatval($item['mrp'] ?? 0);
                $scmPercent = floatval($item['scm_percent'] ?? 0);
                $disPercent = floatval($item['dis_percent'] ?? 0);
                
                // Calculate gross amount
                $grossAmount = $qty * $mrp;
                
                // Apply scheme discount
                $schemeAmount = $grossAmount * ($scmPercent / 100);
                $afterScheme = $grossAmount - $schemeAmount;
                
                // Apply discount
                $discountAmount = $afterScheme * ($disPercent / 100);
                $subtotal = $afterScheme - $discountAmount;
                
                // Calculate tax amounts
                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                $cgstAmount = $subtotal * ($cgstPercent / 100);
                $sgstAmount = $subtotal * ($sgstPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount;

                \App\Models\BreakageExpiryTransactionItem::create([
                    'breakage_expiry_transaction_id' => $transaction->id,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'item_code' => $item['code'],
                    'item_name' => $item['name'],
                    'batch_no' => $item['batch'] ?? null,
                    'expiry' => $item['expiry'] ?? null,
                    'br_ex' => $item['br_ex'] ?? null,
                    'qty' => $qty,
                    'f_qty' => $item['f_qty'] ?? 0,
                    'mrp' => $mrp,
                    'scm_percent' => $scmPercent,
                    'dis_percent' => $disPercent,
                    'amount' => $item['amount'] ?? 0,
                    'hsn_code' => $itemModel ? $itemModel->hsn_code : null,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cgst_amount' => round($cgstAmount, 2),
                    'sgst_amount' => round($sgstAmount, 2),
                    'tax_percent' => $cgstPercent + $sgstPercent,
                    'tax_amount' => round($taxAmount, 2),
                    's_rate' => $batchModel ? $batchModel->s_rate : 0,
                    'p_rate' => $batchModel ? $batchModel->pur_rate : 0,
                    'packing' => $itemModel ? $itemModel->packing : null,
                    'company_name' => $itemModel ? $itemModel->mfg_by : null,
                    'row_order' => $index,
                ]);
            }

            // Handle credit note adjustments if requested
            if ($request->with_credit_note && !empty($request->adjustments)) {
                $this->processAdjustments($transaction, $request->adjustments);
            }

            \DB::commit();

            $message = 'Breakage/Expiry transaction saved successfully';
            if ($request->with_credit_note && !empty($request->adjustments)) {
                $adjustmentCount = count($request->adjustments);
                $message .= " with {$adjustmentCount} credit note adjustment(s)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'transaction_id' => $transaction->id,
                'sr_no' => $srNo,
                'with_credit_note' => $request->with_credit_note ?? false,
                'adjustments_count' => !empty($request->adjustments) ? count($request->adjustments) : 0
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error storing breakage/expiry transaction: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error saving transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate next SR No
     */
    private function generateSrNo($series = 'BE')
    {
        $lastTransaction = \App\Models\BreakageExpiryTransaction::where('series', $series)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            // Extract number from last SR No (e.g., BE-001 -> 001)
            $lastNumber = (int) substr($lastTransaction->sr_no, strlen($series) + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $series . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get transaction details for modal (AJAX)
     */
    public function getTransactionDetails($id)
    {
        try {
            $transaction = \App\Models\BreakageExpiryTransaction::with(['items', 'customer', 'salesman'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'items' => $transaction->items
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching transaction details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transaction details'
            ], 500);
        }
    }

    /**
     * Get adjustment details for a transaction (AJAX)
     */
    public function getAdjustments($transactionId)
    {
        try {
            $adjustments = \App\Models\BreakageExpiryAdjustment::where('breakage_expiry_transaction_id', $transactionId)
                ->with('saleTransaction')
                ->get()
                ->map(function($adjustment) {
                    return [
                        'id' => $adjustment->id,
                        'sale_transaction_id' => $adjustment->sale_transaction_id,
                        'sale_invoice_no' => $adjustment->saleTransaction->invoice_no ?? null,
                        'adjusted_amount' => $adjustment->adjusted_amount,
                        'adjustment_date' => $adjustment->adjustment_date,
                    ];
                });

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching adjustments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching adjustments'
            ], 500);
        }
    }

    /**
     * Process credit note adjustments
     */
    private function processAdjustments($breakageExpiryTransaction, $adjustments)
    {
        foreach ($adjustments as $adjustment) {
            $saleTransaction = \App\Models\SaleTransaction::find($adjustment['sale_id']);
            $adjustedAmount = floatval($adjustment['adjusted_amount']);
            
            if (!$saleTransaction || $adjustedAmount <= 0) {
                continue;
            }
            
            // Get current balance (use balance_amount if set, otherwise use net_amount)
            $currentBalance = $saleTransaction->balance_amount ?? $saleTransaction->net_amount;
            $newBalance = $currentBalance - $adjustedAmount;
            
            // Prevent negative balance
            if ($newBalance < 0) {
                \Log::warning("Adjustment would create negative balance for Sale {$saleTransaction->invoice_no}. Current: {$currentBalance}, Attempting: {$adjustedAmount}");
                continue; // Skip this adjustment
            }
            
            // Update sale transaction balance
            $saleTransaction->balance_amount = $newBalance;
            $saleTransaction->save();
            
            // Create adjustment record for tracking
            \App\Models\BreakageExpiryAdjustment::create([
                'breakage_expiry_transaction_id' => $breakageExpiryTransaction->id,
                'sale_transaction_id' => $saleTransaction->id,
                'adjusted_amount' => $adjustedAmount,
                'adjustment_date' => now()->toDateString(),
                'created_by' => auth()->user()->user_id ?? null,
            ]);
            
            \Log::info("Credit note adjustment: Breakage/Expiry {$breakageExpiryTransaction->sr_no} adjusted Rs {$adjustedAmount} against Sale {$saleTransaction->invoice_no}. Balance updated from {$currentBalance} to {$newBalance}");
        }
    }

    /**
     * Update breakage/expiry transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'salesman_id' => 'nullable|exists:sales_men,id',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'with_credit_note' => 'boolean',
                'adjustments' => 'array',
                'adjustments.*.sale_id' => 'required_with:adjustments|exists:sale_transactions,id',
                'adjustments.*.adjusted_amount' => 'required_with:adjustments|numeric|min:0',
            ]);

            \DB::beginTransaction();

            // Find existing transaction
            $transaction = \App\Models\BreakageExpiryTransaction::findOrFail($id);

            // Get customer and salesman names
            $customer = Customer::find($request->customer_id);
            $salesman = $request->salesman_id ? SalesMan::find($request->salesman_id) : null;

            // Update transaction
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'end_date' => $request->end_date,
                'customer_id' => $request->customer_id,
                'customer_name' => $customer->name,
                'salesman_id' => $request->salesman_id,
                'salesman_name' => $salesman ? $salesman->name : null,
                'gst_vno' => $request->gst_vno ?? 'N',
                'note_type' => $request->note_type ?? 'N',
                'with_gst' => $request->with_gst ?? 'N',
                'inc' => $request->inc ?? 'N',
                'rev_charge' => $request->rev_charge ?? 'Y',
                'adjusted' => $request->adjusted ?? 'X',
                'dis_rpl' => $request->dis_rpl,
                'brk' => $request->brk,
                'exp' => $request->exp,
                'mrp_value' => $request->summary_mrp_value ?? 0,
                'gross_amount' => $request->summary_gross ?? 0,
                'discount_amount' => $request->summary_discount ?? 0,
                'scheme_amount' => $request->summary_scheme ?? 0,
                'tax_amount' => $request->summary_tax ?? 0,
                'net_amount' => $request->summary_net ?? 0,
                'packing' => $request->detail_packing ?? 0,
                'unit' => $request->detail_unit ?? 0,
                'cl_qty' => $request->detail_cl_qty ?? 0,
                'scm_amt' => $request->detail_scm_amt ?? 0,
                'dis_amt' => $request->detail_dis_amt ?? 0,
                'subtotal' => $request->detail_subtotal ?? 0,
                'tax_amt' => $request->detail_tax_amt ?? 0,
                'net_amt' => $request->detail_net_amt ?? 0,
                'remarks' => $request->remarks,
                'updated_by' => auth()->user()->user_id ?? null,
            ]);

            // Restore sale transaction balances before deleting adjustments
            $existingAdjustments = \App\Models\BreakageExpiryAdjustment::where('breakage_expiry_transaction_id', $transaction->id)->get();
            foreach ($existingAdjustments as $adj) {
                $saleTransaction = \App\Models\SaleTransaction::find($adj->sale_transaction_id);
                if ($saleTransaction) {
                    // Restore the adjusted amount back to balance
                    $saleTransaction->balance_amount = ($saleTransaction->balance_amount ?? 0) + $adj->adjusted_amount;
                    $saleTransaction->save();
                    \Log::info("Restored balance for Sale {$saleTransaction->invoice_no}: +{$adj->adjusted_amount}");
                }
            }

            // Delete existing items and adjustments
            \App\Models\BreakageExpiryTransactionItem::where('breakage_expiry_transaction_id', $transaction->id)->delete();
            \App\Models\BreakageExpiryAdjustment::where('breakage_expiry_transaction_id', $transaction->id)->delete();

            // Create new transaction items
            foreach ($request->items as $index => $item) {
                if (empty($item['code']) || empty($item['name'])) {
                    continue; // Skip empty rows
                }

                $itemModel = \App\Models\Item::find($item['code']);
                $batchModel = null;
                
                if (!empty($item['batch'])) {
                    $batchModel = \App\Models\Batch::where('item_id', $item['code'])
                        ->where('batch_no', $item['batch'])
                        ->first();
                }

                // Calculate tax amounts
                $qty = floatval($item['qty'] ?? 0);
                $mrp = floatval($item['mrp'] ?? 0);
                $scmPercent = floatval($item['scm_percent'] ?? 0);
                $disPercent = floatval($item['dis_percent'] ?? 0);
                
                // Calculate gross amount
                $grossAmount = $qty * $mrp;
                
                // Apply scheme discount
                $schemeAmount = $grossAmount * ($scmPercent / 100);
                $afterScheme = $grossAmount - $schemeAmount;
                
                // Apply discount
                $discountAmount = $afterScheme * ($disPercent / 100);
                $subtotal = $afterScheme - $discountAmount;
                
                // Calculate tax amounts
                $cgstPercent = $itemModel ? floatval($itemModel->cgst_percent ?? 0) : 0;
                $sgstPercent = $itemModel ? floatval($itemModel->sgst_percent ?? 0) : 0;
                
                $cgstAmount = $subtotal * ($cgstPercent / 100);
                $sgstAmount = $subtotal * ($sgstPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount;

                \App\Models\BreakageExpiryTransactionItem::create([
                    'breakage_expiry_transaction_id' => $transaction->id,
                    'item_id' => $item['code'],
                    'batch_id' => $batchModel ? $batchModel->id : null,
                    'item_code' => $item['code'],
                    'item_name' => $item['name'],
                    'batch_no' => $item['batch'] ?? null,
                    'expiry' => $item['expiry'] ?? null,
                    'br_ex' => $item['br_ex'] ?? null,
                    'qty' => $qty,
                    'f_qty' => $item['f_qty'] ?? 0,
                    'mrp' => $mrp,
                    'scm_percent' => $scmPercent,
                    'dis_percent' => $disPercent,
                    'amount' => $item['amount'] ?? 0,
                    'hsn_code' => $itemModel ? $itemModel->hsn_code : null,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cgst_amount' => round($cgstAmount, 2),
                    'sgst_amount' => round($sgstAmount, 2),
                    'tax_percent' => $cgstPercent + $sgstPercent,
                    'tax_amount' => round($taxAmount, 2),
                    's_rate' => $batchModel ? $batchModel->s_rate : 0,
                    'p_rate' => $batchModel ? $batchModel->pur_rate : 0,
                    'packing' => $itemModel ? $itemModel->packing : null,
                    'company_name' => $itemModel ? $itemModel->mfg_by : null,
                    'row_order' => $index,
                ]);
            }

            // Handle credit note adjustments if requested
            if ($request->with_credit_note && !empty($request->adjustments)) {
                $this->processAdjustments($transaction, $request->adjustments);
            }

            \DB::commit();

            $message = 'Breakage/Expiry transaction updated successfully';
            if ($request->with_credit_note && !empty($request->adjustments)) {
                $adjustmentCount = count($request->adjustments);
                $message .= " with {$adjustmentCount} credit note adjustment(s)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'transaction_id' => $transaction->id,
                'sr_no' => $transaction->sr_no,
                'with_credit_note' => $request->with_credit_note ?? false,
                'adjustments_count' => !empty($request->adjustments) ? count($request->adjustments) : 0
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating breakage/expiry transaction: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
