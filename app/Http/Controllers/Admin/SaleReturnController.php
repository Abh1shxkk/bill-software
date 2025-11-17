<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SaleReturnTransaction;
use App\Models\SalesMan;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of sale return invoices
     */
    public function index(Request $request)
    {
        $query = SaleReturnTransaction::with(['customer', 'salesman']);

        // Apply search filters
        if ($request->filled('search') && $request->filled('filter_by')) {
            $searchTerm = $request->search;
            $filterBy = $request->filter_by;

            switch ($filterBy) {
                case 'customer_name':
                    $query->whereHas('customer', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%{$searchTerm}%");
                    });
                    break;
                case 'sr_no':
                    $query->where('sr_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'original_invoice_no':
                    $query->where('original_invoice_no', 'LIKE', "%{$searchTerm}%");
                    break;
                case 'salesman_name':
                    $query->whereHas('salesman', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%{$searchTerm}%");
                    });
                    break;
                case 'return_amount':
                    $query->where('net_amount', '>=', $searchTerm);
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

        // Order by latest first and paginate with 10 records per page
        $saleReturns = $query->orderBy('return_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // If it's an AJAX request, return only the table content
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.sale-return.index', compact('saleReturns'));
        }

        return view('admin.sale-return.index', compact('saleReturns'));
    }

    /**
     * Display the specified sale return transaction
     */
    public function show($id)
    {
        $transaction = SaleReturnTransaction::with(['customer', 'salesman', 'items'])
            ->findOrFail($id);

        // If AJAX request, return JSON with adjustments
        if (request()->ajax() || request()->wantsJson()) {
            $saleReturnData = $this->formatSaleReturnForModification($transaction);
            
            return response()->json([
                'success' => true,
                'saleReturn' => $saleReturnData
            ]);
        }

        // Otherwise return view
        return view('admin.sale-return.show', compact('transaction'));
    }

    /**
     * Remove the specified sale return transaction from storage
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $saleReturn = SaleReturnTransaction::with('items')->findOrFail($id);

            // Reverse stock changes - subtract quantities that were added during return
            foreach ($saleReturn->items as $item) {
                if ($item->batch_id) {
                    $batch = \App\Models\Batch::find($item->batch_id);
                    
                    if ($batch) {
                        $returnQty = (float)($item->qty ?? 0);
                        $returnFreeQty = (float)($item->free_qty ?? 0);
                        
                        // Get current quantities
                        $currentQty = (float)($batch->qty ?? 0);
                        $currentFreeQty = (float)($batch->free_qty ?? 0);
                        $currentTotalQty = (float)($batch->total_qty ?? 0);
                        
                        // SUBTRACT quantities (reversing the return)
                        $newQty = max(0, $currentQty - $returnQty);
                        $newFreeQty = max(0, $currentFreeQty - $returnFreeQty);
                        $newTotalQty = max(0, $currentTotalQty - $returnQty - $returnFreeQty);
                        
                        // Update batch
                        $batch->update([
                            'qty' => $newQty,
                            'free_qty' => $newFreeQty,
                            'total_qty' => $newTotalQty,
                        ]);

                        // Create Stock Ledger Entry for deletion
                        \App\Models\StockLedger::create([
                            'trans_no' => $saleReturn->sr_no . '-DEL',
                            'item_id' => $item->item_id,
                            'batch_id' => $item->batch_id,
                            'customer_id' => $saleReturn->customer_id,
                            'transaction_type' => 'SALE_RETURN_DELETE',
                            'quantity' => -$returnQty,
                            'free_quantity' => -$returnFreeQty,
                            'opening_qty' => $currentTotalQty,
                            'closing_qty' => $newTotalQty,
                            'running_balance' => $newTotalQty,
                            'reference_type' => 'App\\Models\\SaleReturnTransaction',
                            'reference_id' => $saleReturn->id,
                            'transaction_date' => $saleReturn->return_date,
                            'godown' => $batch->godown ?? '',
                            'remarks' => 'Sale Return Deleted - ' . $saleReturn->sr_no,
                            'salesman_id' => $saleReturn->salesman_id,
                            'bill_number' => $saleReturn->sr_no,
                            'bill_date' => $saleReturn->return_date,
                            'rate' => $item->sale_rate ?? 0,
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            }

            // Restore sale transaction balances before deleting adjustments
            $existingAdjustments = \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturn->id)->get();
            foreach ($existingAdjustments as $adj) {
                $saleTransaction = \App\Models\SaleTransaction::find($adj->sale_transaction_id);
                if ($saleTransaction) {
                    // Restore the balance by adding back the adjusted amount
                    $currentBalance = $saleTransaction->balance_amount ?? $saleTransaction->net_amount;
                    $newBalance = $currentBalance + $adj->adjusted_amount;
                    $saleTransaction->balance_amount = $newBalance;
                    $saleTransaction->save();
                    
                    \Log::info("Sale Return deletion: Restored Rs {$adj->adjusted_amount} to Sale {$saleTransaction->invoice_no}. Balance updated from {$currentBalance} to {$newBalance}");
                }
            }
            
            // Delete related adjustments
            \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturn->id)->delete();

            // Delete items
            \App\Models\SaleReturnTransactionItem::where('sale_return_transaction_id', $saleReturn->id)->delete();

            // Delete the sale return transaction
            $saleReturn->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale return transaction deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Sale Return Delete Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sale return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display sale return transaction form
     */
    public function transaction()
    {
        // Get next SR number based on existing sale return transactions
        $lastReturn = SaleReturnTransaction::orderBy('id', 'desc')->first();
        
        if ($lastReturn) {
            // Extract number from last SR number (e.g., SR0001 -> 1)
            $lastNumber = (int) substr($lastReturn->sr_no, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $nextSRNo = 'SR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Get customers, salesmen, and items
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->orderBy('name')->get();
        
        return view('admin.sale-return.transaction', compact('nextSRNo', 'customers', 'salesmen', 'items'));
    }

    /**
     * Search for sale invoices by invoice number and customer
     */
    public function searchInvoice(Request $request)
    {
        $invoiceNo = $request->input('invoice_no');
        $customerId = $request->input('customer_id');

        if (!$invoiceNo || !$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice number and customer are required.'
            ]);
        }

        // Search for sale transactions matching the invoice number and customer
        $transactions = \App\Models\SaleTransaction::where('customer_id', $customerId)
            ->where('invoice_no', 'LIKE', '%' . $invoiceNo . '%')
            ->with(['items.item', 'items.batch'])
            ->orderBy('sale_date', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No invoice found for this customer with the given invoice number.'
            ]);
        }

        // Format the transactions for the modal
        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->sale_date ? $transaction->sale_date->format('d-m-Y') : '',
                'invoice_no' => $transaction->invoice_no,
                'amount' => number_format((float) ($transaction->net_amount ?? 0), 2),
                'series' => $transaction->series,
                'sale_date' => $transaction->sale_date ? $transaction->sale_date->format('Y-m-d') : '',
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $formattedTransactions
        ]);
    }

    /**
     * Get sale transaction details for return
     */
    public function getTransactionDetails($id)
    {
        try {
            $transaction = \App\Models\SaleTransaction::with(['items.item', 'items.batch', 'customer', 'salesman'])
                ->findOrFail($id);

            // Get first item's location safely
            $location = '';
            if ($transaction->items->isNotEmpty()) {
                $firstItem = $transaction->items->first();
                if ($firstItem && $firstItem->item) {
                    $location = $firstItem->item->location ?? '';
                }
            }

            // Safely compute numeric totals to avoid non-numeric warnings
            $packingTotal = $transaction->items->sum(function ($i) {
                $val = $i->packing;
                return is_numeric($val) ? (float) $val : 0.0;
            });

            $unitTotal = $transaction->items->sum(function ($i) {
                $val = $i->unit;
                return is_numeric($val) ? (float) $val : 0.0;
            });

            $clQtyTotal = $transaction->items->sum(function ($i) {
                $val = $i->qty;
                return is_numeric($val) ? (float) $val : 0.0;
            });

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'series' => $transaction->series,
                    'sale_date' => $transaction->sale_date ? $transaction->sale_date->format('Y-m-d') : '',
                    'net_amount' => number_format((float) ($transaction->net_amount ?? 0), 2, '.', ''),
                    'nt_amount' => number_format((float) ($transaction->nt_amount ?? 0), 2, '.', ''),
                    'sc_amount' => number_format((float) ($transaction->sc_amount ?? 0), 2, '.', ''),
                    'ft_amount' => number_format((float) ($transaction->ft_amount ?? 0), 2, '.', ''),
                    'dis_amount' => number_format((float) ($transaction->dis_amount ?? 0), 2, '.', ''),
                    'scm_amount' => number_format((float) ($transaction->scm_amount ?? 0), 2, '.', ''),
                    'tax_amount' => number_format((float) ($transaction->tax_amount ?? 0), 2, '.', ''),
                    'scm_percent' => number_format((float) ($transaction->scm_percent ?? 0), 3, '.', ''),
                    'tcs_amount' => number_format((float) ($transaction->tcs_amount ?? 0), 2, '.', ''),
                    'excise_amount' => number_format((float) ($transaction->excise_amount ?? 0), 2, '.', ''),
                    'packing' => $packingTotal,
                    'unit' => $unitTotal,
                    'cl_qty' => $clQtyTotal,
                    'location' => $location,
                    'hs_amount' => 0,
                    'customer_id' => $transaction->customer_id,
                    'customer_name' => data_get($transaction, 'customer.name', ''),
                    'salesman_id' => $transaction->salesman_id,
                    'salesman_name' => data_get($transaction, 'salesman.name', ''),
                    'items' => $transaction->items->map(function ($item) use ($transaction) {
                        $originalQty = (float)($item->qty ?? 0);
                        $originalFreeQty = (float)($item->free_qty ?? 0);
                        
                        // Calculate already returned quantities for this specific item
                        $returnedQty = \App\Models\SaleReturnTransactionItem::whereHas('saleReturnTransaction', function($query) use ($transaction) {
                            $query->where('original_invoice_no', $transaction->invoice_no)
                                  ->where('customer_id', $transaction->customer_id);
                        })
                        ->where('item_id', $item->item_id)
                        ->where('batch_id', $item->batch_id)
                        ->sum('qty');
                        
                        $returnedFreeQty = \App\Models\SaleReturnTransactionItem::whereHas('saleReturnTransaction', function($query) use ($transaction) {
                            $query->where('original_invoice_no', $transaction->invoice_no)
                                  ->where('customer_id', $transaction->customer_id);
                        })
                        ->where('item_id', $item->item_id)
                        ->where('batch_id', $item->batch_id)
                        ->sum('free_qty');
                        
                        // Calculate balance (remaining quantity that can be returned)
                        $balanceQty = $originalQty - $returnedQty;
                        $balanceFreeQty = $originalFreeQty - $returnedFreeQty;
                        
                        // Ensure balance is not negative
                        $balanceQty = max(0, $balanceQty);
                        $balanceFreeQty = max(0, $balanceFreeQty);
                        
                        return [
                            'item_id' => $item->item_id,
                            'item_code' => data_get($item, 'item.code', ''),
                            'item_name' => data_get($item, 'item.name', ''),
                            'batch_id' => $item->batch_id,
                            'batch_no' => data_get($item, 'batch.batch_no', ''),
                            'expiry_date' => data_get($item, 'batch.expiry_date', ''),
                            'quantity' => $originalQty, // Original total quantity
                            'free_quantity' => $originalFreeQty, // Original free quantity
                            'balance_qty' => $balanceQty, // Remaining quantity that can be returned
                            'balance_free_qty' => $balanceFreeQty, // Remaining free quantity
                            'returned_qty' => $returnedQty, // Already returned quantity
                            'returned_free_qty' => $returnedFreeQty, // Already returned free quantity
                            'sale_rate' => $item->sale_rate ?? 0,
                            'discount_percent' => $item->discount_percent ?? 0,
                            'mrp' => $item->mrp ?? 0,
                            'amount' => $item->amount ?? 0,
                            'packing' => $item->packing ?? '',
                            'unit' => $item->unit ?? '',
                            'company_name' => $item->company_name ?? '',
                            'hsn_code' => $item->hsn_code ?? '',
                            'cgst_percent' => $item->cgst_percent ?? 0,
                            'sgst_percent' => $item->sgst_percent ?? 0,
                            'cess_percent' => $item->cess_percent ?? 0,
                            'cgst_amount' => $item->cgst_amount ?? 0,
                            'sgst_amount' => $item->sgst_amount ?? 0,
                            'cess_amount' => $item->cess_amount ?? 0,
                            'tax_amount' => $item->tax_amount ?? 0,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Sale Return Transaction Details Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store sale return transaction
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            // Validate request
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'return_date' => 'required|date',
                'sr_no' => 'required',
            ]);

            // Get customer name
            $customerName = '';
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customerName = $customer ? $customer->name : '';
            }

            // Get salesman name
            $salesmanName = '';
            if ($request->salesman_id) {
                $salesman = SalesMan::find($request->salesman_id);
                $salesmanName = $salesman ? $salesman->name : '';
            }

            // Create sale return transaction
            $saleReturn = SaleReturnTransaction::create([
                'sr_no' => $request->sr_no,
                'series' => $request->series ?? 'SR',
                'return_date' => $request->return_date,
                'customer_id' => $request->customer_id,
                'customer_name' => $customerName,
                'salesman_id' => $request->salesman_id,
                'salesman_name' => $salesmanName,
                'original_invoice_no' => $request->original_invoice_no,
                'original_invoice_date' => $request->original_invoice_date,
                'original_series' => $request->original_series,
                'original_amount' => $request->original_amount ?? 0,
                'rate_diff_flag' => $request->rate_diff_flag ?? 'N',
                'cash_flag' => $request->cash_flag ?? 'N',
                'tax_flag' => $request->tax_flag ?? 'N',
                'remarks' => $request->remarks,
                'fixed_discount' => $request->fixed_discount ?? 0,
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'ft_amount' => $request->ft_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'hs_amount' => $request->hs_amount ?? 0,
            ]);

            // Save items and update batch stock
            if ($request->has('items')) {
                $rowOrder = 1;
                
                foreach ($request->items as $itemData) {
                    // Format expiry date to Y-m-d format if present
                    $expiryDate = null;
                    if (!empty($itemData['expiry']) || !empty($itemData['expiry_date'])) {
                        try {
                            $dateStr = $itemData['expiry'] ?? $itemData['expiry_date'];
                            $expiryDate = \Carbon\Carbon::parse($dateStr)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                    
                    // Save sale return item
                    $returnItem = \App\Models\SaleReturnTransactionItem::create([
                        'sale_return_transaction_id' => $saleReturn->id,
                        'item_id' => $itemData['item_id'] ?? null,
                        'batch_id' => $itemData['batch_id'] ?? null,
                        'item_code' => $itemData['code'] ?? $itemData['item_code'] ?? '',
                        'item_name' => $itemData['name'] ?? $itemData['item_name'] ?? '',
                        'batch_no' => $itemData['batch'] ?? $itemData['batch_no'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => $itemData['qty'] ?? 0,
                        'free_qty' => $itemData['free_qty'] ?? 0,
                        'sale_rate' => $itemData['sale_rate'] ?? 0,
                        'mrp' => $itemData['mrp'] ?? 0,
                        'discount_percent' => $itemData['dis_percent'] ?? $itemData['discount_percent'] ?? 0,
                        'discount_amount' => ((float)($itemData['amount'] ?? 0) * (float)($itemData['dis_percent'] ?? $itemData['discount_percent'] ?? 0)) / 100,
                        'amount' => $itemData['amount'] ?? 0,
                        'net_amount' => $itemData['amount'] ?? 0,
                        'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                        'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                        'cess_percent' => $itemData['cess_percent'] ?? 0,
                        'cgst_amount' => 0,
                        'sgst_amount' => 0,
                        'cess_amount' => 0,
                        'tax_amount' => 0,
                        'unit' => $itemData['unit'] ?? '',
                        'packing' => $itemData['packing'] ?? '',
                        'company_name' => $itemData['company_name'] ?? '',
                        'hsn_code' => $itemData['hsn_code'] ?? '',
                        'row_order' => $rowOrder++,
                    ]);

                    // Update batch stock - ADD quantity back (sale return increases stock)
                    if (!empty($itemData['batch_id'])) {
                        $batch = \App\Models\Batch::find($itemData['batch_id']);
                        
                        if ($batch) {
                            $returnQty = (float)($itemData['qty'] ?? 0);
                            $returnFreeQty = (float)($itemData['free_qty'] ?? 0);
                            
                            // Get current quantities
                            $currentQty = (float)($batch->qty ?? 0);
                            $currentFreeQty = (float)($batch->free_qty ?? 0);
                            $currentTotalQty = (float)($batch->total_qty ?? 0);
                            
                            // ADD quantities (return increases stock)
                            $newQty = $currentQty + $returnQty;
                            $newFreeQty = $currentFreeQty + $returnFreeQty;
                            $newTotalQty = $currentTotalQty + $returnQty + $returnFreeQty;
                            
                            // Update batch
                            $batch->update([
                                'qty' => $newQty,
                                'free_qty' => $newFreeQty,
                                'total_qty' => $newTotalQty,
                            ]);

                            // Create Stock Ledger Entry with unique trans_no per item
                            \App\Models\StockLedger::create([
                                'trans_no' => $saleReturn->sr_no . '-' . $rowOrder,
                                'item_id' => $itemData['item_id'] ?? null,
                                'batch_id' => $itemData['batch_id'],
                                'customer_id' => $request->customer_id,
                                'transaction_type' => 'SALE_RETURN',
                                'quantity' => $returnQty,
                                'free_quantity' => $returnFreeQty,
                                'opening_qty' => $currentTotalQty,
                                'closing_qty' => $newTotalQty,
                                'running_balance' => $newTotalQty,
                                'reference_type' => 'App\\Models\\SaleReturnTransaction',
                                'reference_id' => $saleReturn->id,
                                'transaction_date' => $request->return_date,
                                'godown' => $batch->godown ?? '',
                                'remarks' => 'Sale Return - ' . ($request->remarks ?? ''),
                                'salesman_id' => $request->salesman_id,
                                'bill_number' => $saleReturn->sr_no,
                                'bill_date' => $request->return_date,
                                'rate' => $itemData['sale_rate'] ?? 0,
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // Save adjustments if provided
            if ($request->has('adjustments')) {
                $adjustments = json_decode($request->adjustments, true);
                
                if (is_array($adjustments)) {
                    foreach ($adjustments as $adjustment) {
                        if (isset($adjustment['invoice_id']) && isset($adjustment['adjusted_amount']) && $adjustment['adjusted_amount'] > 0) {
                            $saleTransaction = \App\Models\SaleTransaction::find($adjustment['invoice_id']);
                            $adjustedAmount = floatval($adjustment['adjusted_amount']);
                            
                            if ($saleTransaction && $adjustedAmount > 0) {
                                // Get current balance (use balance_amount if set, otherwise use net_amount)
                                $currentBalance = $saleTransaction->balance_amount ?? $saleTransaction->net_amount;
                                $newBalance = $currentBalance - $adjustedAmount;
                                
                                // Prevent negative balance
                                if ($newBalance < 0) {
                                    \Log::warning("Sale Return adjustment would create negative balance for Sale {$saleTransaction->invoice_no}. Current: {$currentBalance}, Attempting: {$adjustedAmount}");
                                    continue; // Skip this adjustment
                                }
                                
                                // Update sale transaction balance
                                $saleTransaction->balance_amount = $newBalance;
                                $saleTransaction->save();
                                
                                // Create adjustment record for tracking
                                \App\Models\SaleReturnAdjustment::create([
                                    'sale_return_id' => $saleReturn->id,
                                    'sale_transaction_id' => $adjustment['invoice_id'],
                                    'adjusted_amount' => $adjustedAmount,
                                ]);
                                
                                \Log::info("Sale Return credit note adjustment: SR {$saleReturn->sr_no} adjusted Rs {$adjustedAmount} against Sale {$saleTransaction->invoice_no}. Balance updated from {$currentBalance} to {$newBalance}");
                            }
                        }
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale return transaction saved successfully!',
                'sale_return_id' => $saleReturn->id,
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Sale Return Store Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer's past invoices for adjustment
     */
    public function getCustomerInvoices(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');

            if (!$customerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer ID is required.'
                ]);
            }

            // Get all sale transactions for this customer
            // Exclude any that are already fully returned/adjusted
            $transactions = \App\Models\SaleTransaction::where('customer_id', $customerId)
                ->whereNotNull('invoice_no')
                ->orderBy('sale_date', 'desc')
                ->get();

            if ($transactions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No past invoices found for this customer.'
                ]);
            }

            // Format the transactions for the adjustment modal
        $formattedInvoices = $transactions->map(function ($transaction) {
            // Get original bill amount (net_amount) - this never changes
            $originalBillAmount = (float) ($transaction->net_amount ?? 0);
            
            // Get current balance (after previous adjustments)
            $currentBalance = (float) ($transaction->balance_amount ?? $transaction->net_amount ?? 0);

            return [
                'id' => $transaction->id,
                'trans_no' => $transaction->invoice_no ?? '',
                'date' => $transaction->sale_date ? $transaction->sale_date->format('d-M-y') : '',
                'bill_amount' => $originalBillAmount, // Show ORIGINAL bill amount (1000)
                'balance' => $currentBalance, // Show CURRENT balance (after adjustments)
            ];
        })->filter(function ($invoice) {
            // Only show invoices with remaining balance
            return $invoice['balance'] > 0;
        })->values(); // Re-index array

            if ($formattedInvoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices with outstanding balance found for this customer.'
                ]);
            }

            return response()->json([
                'success' => true,
                'invoices' => $formattedInvoices
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Customer Invoices Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading customer invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display sale return modification form
     */
    public function modification()
    {
        // Get next SR number (same logic as transaction)
        $lastReturn = SaleReturnTransaction::orderBy('id', 'desc')->first();
        
        if ($lastReturn) {
            $lastNumber = (int) substr($lastReturn->sr_no, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $nextSRNo = 'SR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Get customers, salesmen, and items (same as transaction)
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $items = Item::where('is_deleted', '!=', 1)->orderBy('name')->get();
        
        return view('admin.sale-return.modification', compact('nextSRNo', 'customers', 'salesmen', 'items'));
    }

    /**
     * Get past sale return invoices for modification modal
     */
    public function getPastInvoices(Request $request)
    {
        try {
            $saleReturns = SaleReturnTransaction::with(['customer'])
                ->orderBy('return_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(100) // Limit to last 100 records
                ->get();

            $formattedInvoices = $saleReturns->map(function ($saleReturn) {
                return [
                    'id' => $saleReturn->id,
                    'trn_no' => $saleReturn->sr_no,
                    'ac_no' => $saleReturn->customer_id ?? '',
                    'customer_name' => $saleReturn->customer->name ?? $saleReturn->customer_name ?? 'N/A',
                    'amount' => number_format((float) ($saleReturn->net_amount ?? 0), 2, '.', ''),
                    'status' => $saleReturn->status ?? '',
                    'user_id' => 'MASTER',
                    'f_user' => 'MASTER'
                ];
            });

            return response()->json([
                'success' => true,
                'invoices' => $formattedInvoices
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Past Sale Return Invoices Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading past invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale return details for modification
     */
    public function getDetails($id)
    {
        try {
            $saleReturn = SaleReturnTransaction::with(['customer', 'salesman', 'items'])
                ->findOrFail($id);

            $formattedItems = $saleReturn->items->map(function ($item, $index) use ($saleReturn) {
                return [
                    'id' => $item->id,
                    'trn_no' => $saleReturn->sr_no,
                    'ac_no' => $saleReturn->customer_id ?? '',
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code ?? '',
                    'batch_id' => $item->batch_id,
                    'item_name' => $item->item_name,
                    'batch_no' => $item->batch_no ?? '',
                    'expiry_date' => $item->expiry_date ?? '',
                    'packing' => $item->packing ?? '',
                    'unit' => $item->unit ?? 0,
                    'cl_qty' => $item->qty ?? 0,
                    'qty' => $item->qty ?? 0,
                    'free_qty' => $item->free_qty ?? 0,
                    'sale_rate' => $item->sale_rate ?? 0,
                    'discount_percent' => $item->discount_percent ?? 0,
                    'amount' => $item->amount ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'company_name' => $item->company_name ?? '',
                    'hsn_code' => $item->hsn_code ?? '',
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'status' => '',
                    'f_user' => 'MASTER',
                    'user_id' => 'MASTER'
                ];
            });

            $formattedSaleReturn = [
                'id' => $saleReturn->id,
                'sr_no' => $saleReturn->sr_no,
                'return_date' => $saleReturn->return_date ? $saleReturn->return_date->format('Y-m-d') : '',
                'customer_id' => $saleReturn->customer_id,
                'customer_name' => $saleReturn->customer->name ?? $saleReturn->customer_name ?? '',
                'salesman_id' => $saleReturn->salesman_id,
                'salesman_name' => $saleReturn->salesman->name ?? $saleReturn->salesman_name ?? '',
                'original_invoice_no' => $saleReturn->original_invoice_no ?? '',
                'original_invoice_date' => $saleReturn->original_invoice_date ? $saleReturn->original_invoice_date->format('Y-m-d') : '',
                'original_series' => $saleReturn->original_series ?? '',
                'location' => $saleReturn->location ?? '',
                'remarks' => $saleReturn->remarks ?? '',
                'fixed_discount' => $saleReturn->fixed_discount ?? 0,
                'rate_diff_flag' => $saleReturn->rate_diff_flag ?? 'N',
                'cash_flag' => $saleReturn->cash_flag ?? 'N',
                'tax_flag' => $saleReturn->tax_flag ?? 'N',
                'nt_amount' => $saleReturn->nt_amount ?? 0,
                'sc_amount' => $saleReturn->sc_amount ?? 0,
                'ft_amount' => $saleReturn->ft_amount ?? 0,
                'dis_amount' => $saleReturn->dis_amount ?? 0,
                'scm_amount' => $saleReturn->scm_amount ?? 0,
                'tax_amount' => $saleReturn->tax_amount ?? 0,
                'net_amount' => $saleReturn->net_amount ?? 0,
                'scm_percent' => $saleReturn->scm_percent ?? 0,
                'tcs_amount' => $saleReturn->tcs_amount ?? 0,
                'excise_amount' => $saleReturn->excise_amount ?? 0,
                'hs_amount' => $saleReturn->hs_amount ?? 0,
                'items' => $formattedItems
            ];

            return response()->json([
                'success' => true,
                'saleReturn' => $formattedSaleReturn
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Sale Return Details Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading sale return details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale return by SR number
     */
    public function getBySrNo($srNo)
    {
        try {
            $saleReturn = SaleReturnTransaction::with(['customer', 'salesman', 'items'])
                ->where('sr_no', $srNo)
                ->firstOrFail();

            return $this->getDetails($saleReturn->id);

        } catch (\Exception $e) {
            \Log::error('Get Sale Return By SR No Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sale return not found'
            ], 404);
        }
    }

    /**
     * Update sale return modification
     */
    public function updateModification(Request $request)
    {
        try {
            \DB::beginTransaction();

            $saleReturnId = $request->input('sale_return_id');
            $saleReturn = SaleReturnTransaction::findOrFail($saleReturnId);

            // Update items with new sale rates and discounts
            $items = $request->input('items', []);
            
            foreach ($items as $itemData) {
                $item = \App\Models\SaleReturnTransactionItem::find($itemData['id']);
                
                if ($item) {
                    $item->update([
                        'sale_rate' => $itemData['sale_rate'] ?? $item->sale_rate,
                        'discount_percent' => $itemData['discount_percent'] ?? $item->discount_percent,
                        'amount' => $itemData['amount'] ?? $item->amount,
                    ]);
                }
            }

            // Update sale return totals
            $saleReturn->update([
                'nt_amount' => $request->input('nt_amount', $saleReturn->nt_amount),
                'ft_amount' => $request->input('ft_amount', $saleReturn->ft_amount),
                'dis_amount' => $request->input('dis_amount', $saleReturn->dis_amount),
                'scm_amount' => $request->input('scm_amount', $saleReturn->scm_amount),
                'tax_amount' => $request->input('tax_amount', $saleReturn->tax_amount),
                'net_amount' => $request->input('net_amount', $saleReturn->net_amount),
                'updated_by' => auth()->id(),
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale return updated successfully!'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Update Sale Return Modification Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating sale return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search sale return by SR number for modification
     */
    public function searchBySRNo(Request $request)
    {
        try {
            $srNo = $request->input('sr_no');
            
            if (!$srNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'SR number is required.'
                ]);
            }

            $saleReturn = SaleReturnTransaction::with(['customer', 'salesman', 'items.item', 'items.batch'])
                ->where('sr_no', $srNo)
                ->first();

            if (!$saleReturn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale return not found with SR No: ' . $srNo
                ]);
            }

            return response()->json([
                'success' => true,
                'saleReturn' => $this->formatSaleReturnForModification($saleReturn)
            ]);

        } catch (\Exception $e) {
            \Log::error('Search Sale Return By SR No Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching sale return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all past sale returns for modification modal
     */
    public function getPastReturns(Request $request)
    {
        try {
            $saleReturns = SaleReturnTransaction::with(['customer'])
                ->orderBy('return_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();

            $formattedReturns = $saleReturns->map(function ($saleReturn) {
                return [
                    'id' => $saleReturn->id,
                    'sr_no' => $saleReturn->sr_no,
                    'return_date' => $saleReturn->return_date ? $saleReturn->return_date->format('d-M-Y') : '',
                    'customer_name' => $saleReturn->customer->name ?? 'N/A',
                    'net_amount' => number_format((float) ($saleReturn->net_amount ?? 0), 2, '.', ''),
                ];
            });

            return response()->json([
                'success' => true,
                'saleReturns' => $formattedReturns
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Past Sale Returns Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading past sale returns: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale return details for modification
     */
    public function getSaleReturnDetails($id)
    {
        try {
            $saleReturn = SaleReturnTransaction::with(['customer', 'salesman', 'items.item', 'items.batch'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'saleReturn' => $this->formatSaleReturnForModification($saleReturn)
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Sale Return Details Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading sale return details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update sale return (for modification)
     */
    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();

            $saleReturn = SaleReturnTransaction::findOrFail($id);

            // Store old quantities for stock adjustment
            $oldItems = $saleReturn->items()->get();

            // Update sale return header
            $saleReturn->update([
                'return_date' => $request->return_date,
                'customer_id' => $request->customer_id,
                'salesman_id' => $request->salesman_id,
                'original_invoice_no' => $request->original_invoice_no,
                'original_invoice_date' => $request->original_invoice_date,
                'original_series' => $request->original_series,
                'original_amount' => $request->original_amount ?? 0,
                'rate_diff_flag' => $request->rate_diff_flag ?? 'N',
                'cash_flag' => $request->cash_flag ?? 'N',
                'tax_flag' => $request->tax_flag ?? 'N',
                'remarks' => $request->remarks,
                'fixed_discount' => $request->fixed_discount ?? 0,
                'nt_amount' => $request->nt_amount ?? 0,
                'sc_amount' => $request->sc_amount ?? 0,
                'ft_amount' => $request->ft_amount ?? 0,
                'dis_amount' => $request->dis_amount ?? 0,
                'scm_amount' => $request->scm_amount ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'scm_percent' => $request->scm_percent ?? 0,
                'tcs_amount' => $request->tcs_amount ?? 0,
                'hs_amount' => $request->hs_amount ?? 0,
                'updated_by' => auth()->id(),
            ]);

            // Reverse old stock changes
            foreach ($oldItems as $oldItem) {
                if ($oldItem->batch_id) {
                    $batch = \App\Models\Batch::find($oldItem->batch_id);
                    if ($batch) {
                        $batch->update([
                            'qty' => $batch->qty - ($oldItem->qty ?? 0),
                            'free_qty' => $batch->free_qty - ($oldItem->free_qty ?? 0),
                            'total_qty' => $batch->total_qty - ($oldItem->qty ?? 0) - ($oldItem->free_qty ?? 0),
                        ]);
                    }
                }
            }

            // Delete old items
            \App\Models\SaleReturnTransactionItem::where('sale_return_transaction_id', $saleReturn->id)->delete();

            // Add new items and update stock
            if ($request->has('items')) {
                $rowOrder = 1;
                
                foreach ($request->items as $itemData) {
                    $expiryDate = null;
                    if (!empty($itemData['expiry'])) {
                        try {
                            $expiryDate = \Carbon\Carbon::parse($itemData['expiry'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = null;
                        }
                    }
                    
                    \App\Models\SaleReturnTransactionItem::create([
                        'sale_return_transaction_id' => $saleReturn->id,
                        'item_id' => $itemData['item_id'] ?? null,
                        'batch_id' => $itemData['batch_id'] ?? null,
                        'item_code' => $itemData['code'] ?? '',
                        'item_name' => $itemData['name'] ?? '',
                        'batch_no' => $itemData['batch'] ?? '',
                        'expiry_date' => $expiryDate,
                        'qty' => $itemData['qty'] ?? 0,
                        'free_qty' => $itemData['free_qty'] ?? 0,
                        'sale_rate' => $itemData['sale_rate'] ?? 0,
                        'mrp' => $itemData['mrp'] ?? 0,
                        'discount_percent' => $itemData['dis_percent'] ?? 0,
                        'amount' => $itemData['amount'] ?? 0,
                        'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                        'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                        'cess_percent' => $itemData['cess_percent'] ?? 0,
                        'row_order' => $rowOrder++,
                    ]);

                    // Update batch stock with new quantities
                    if (!empty($itemData['batch_id'])) {
                        $batch = \App\Models\Batch::find($itemData['batch_id']);
                        if ($batch) {
                            $batch->update([
                                'qty' => $batch->qty + ($itemData['qty'] ?? 0),
                                'free_qty' => $batch->free_qty + ($itemData['free_qty'] ?? 0),
                                'total_qty' => $batch->total_qty + ($itemData['qty'] ?? 0) + ($itemData['free_qty'] ?? 0),
                            ]);
                        }
                    }
                }
            }

            // Update adjustments
            if ($request->has('adjustments')) {
                // First, restore balances from old adjustments
                $existingAdjustments = \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturn->id)->get();
                foreach ($existingAdjustments as $adj) {
                    $saleTransaction = \App\Models\SaleTransaction::find($adj->sale_transaction_id);
                    if ($saleTransaction) {
                        // Restore the balance by adding back the adjusted amount
                        $currentBalance = $saleTransaction->balance_amount ?? $saleTransaction->net_amount;
                        $newBalance = $currentBalance + $adj->adjusted_amount;
                        $saleTransaction->balance_amount = $newBalance;
                        $saleTransaction->save();
                        
                        \Log::info("Sale Return update: Restored Rs {$adj->adjusted_amount} to Sale {$saleTransaction->invoice_no}. Balance updated from {$currentBalance} to {$newBalance}");
                    }
                }
                
                // Delete old adjustments
                \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturn->id)->delete();
                
                // Create new adjustments and update balances
                $adjustments = json_decode($request->adjustments, true);
                if (is_array($adjustments)) {
                    foreach ($adjustments as $adjustment) {
                        if (isset($adjustment['invoice_id']) && isset($adjustment['adjusted_amount']) && $adjustment['adjusted_amount'] > 0) {
                            $saleTransaction = \App\Models\SaleTransaction::find($adjustment['invoice_id']);
                            $adjustedAmount = floatval($adjustment['adjusted_amount']);
                            
                            if ($saleTransaction && $adjustedAmount > 0) {
                                // Get current balance (use balance_amount if set, otherwise use net_amount)
                                $currentBalance = $saleTransaction->balance_amount ?? $saleTransaction->net_amount;
                                $newBalance = $currentBalance - $adjustedAmount;
                                
                                // Prevent negative balance
                                if ($newBalance < 0) {
                                    \Log::warning("Sale Return update adjustment would create negative balance for Sale {$saleTransaction->invoice_no}. Current: {$currentBalance}, Attempting: {$adjustedAmount}");
                                    continue; // Skip this adjustment
                                }
                                
                                // Update sale transaction balance
                                $saleTransaction->balance_amount = $newBalance;
                                $saleTransaction->save();
                                
                                // Create adjustment record for tracking
                                \App\Models\SaleReturnAdjustment::create([
                                    'sale_return_id' => $saleReturn->id,
                                    'sale_transaction_id' => $adjustment['invoice_id'],
                                    'adjusted_amount' => $adjustedAmount,
                                ]);
                                
                                \Log::info("Sale Return update: SR {$saleReturn->sr_no} adjusted Rs {$adjustedAmount} against Sale {$saleTransaction->invoice_no}. Balance updated from {$currentBalance} to {$newBalance}");
                            }
                        }
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale return updated successfully!',
                'sale_return_id' => $saleReturn->id,
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Sale Return Update Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating sale return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get adjustment details for a sale return (AJAX)
     */
    public function getAdjustments($saleReturnId)
    {
        try {
            $adjustments = \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturnId)
                ->with('saleTransaction')
                ->get()
                ->map(function($adjustment) {
                    return [
                        'id' => $adjustment->id,
                        'sale_transaction_id' => $adjustment->sale_transaction_id,
                        'sale_invoice_no' => $adjustment->saleTransaction->invoice_no ?? null,
                        'adjusted_amount' => $adjustment->adjusted_amount,
                    ];
                });

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching sale return adjustments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching adjustments'
            ], 500);
        }
    }

    /**
     * Format sale return data for modification
     */
    private function formatSaleReturnForModification($saleReturn)
    {
        // Get existing adjustments for this sale return
        $adjustments = \App\Models\SaleReturnAdjustment::where('sale_return_id', $saleReturn->id)
            ->with('saleTransaction')
            ->get()
            ->map(function ($adjustment) {
                // For modification modal:
                // - bill_amount = what the balance was BEFORE this adjustment
                // - adjusted_amount = what was adjusted in this sale return
                // - balance = current remaining balance (after this adjustment)
                
                $currentBalance = $adjustment->saleTransaction->balance_amount ?? $adjustment->saleTransaction->net_amount ?? 0;
                $adjustedAmount = $adjustment->adjusted_amount;
                
                // Bill amount = what it was before this adjustment = current + what we took
                $billAmountBeforeAdjustment = $currentBalance + $adjustedAmount;
                
                return [
                    'invoice_id' => $adjustment->sale_transaction_id,
                    'trans_no' => $adjustment->saleTransaction->invoice_no ?? '',
                    'date' => $adjustment->saleTransaction->sale_date ? $adjustment->saleTransaction->sale_date->format('d-M-y') : '',
                    'bill_amount' => $billAmountBeforeAdjustment, // Balance before this adjustment
                    'adjusted_amount' => $adjustedAmount, // What was adjusted
                    'balance' => $currentBalance, // Current remaining balance
                ];
            });

        return [
            'id' => $saleReturn->id,
            'sr_no' => $saleReturn->sr_no,
            'series' => $saleReturn->series ?? 'SR',
            'return_date' => $saleReturn->return_date ? $saleReturn->return_date->format('Y-m-d') : '',
            'customer_id' => $saleReturn->customer_id,
            'customer_name' => $saleReturn->customer->name ?? '',
            'salesman_id' => $saleReturn->salesman_id,
            'salesman_name' => $saleReturn->salesman->name ?? '',
            'original_invoice_no' => $saleReturn->original_invoice_no ?? '',
            'original_invoice_date' => $saleReturn->original_invoice_date ?? '',
            'original_series' => $saleReturn->original_series ?? '',
            'original_amount' => $saleReturn->original_amount ?? 0,
            'rate_diff_flag' => $saleReturn->rate_diff_flag ?? 'N',
            'cash_flag' => $saleReturn->cash_flag ?? 'N',
            'tax_flag' => $saleReturn->tax_flag ?? 'N',
            'remarks' => $saleReturn->remarks ?? '',
            'fixed_discount' => $saleReturn->fixed_discount ?? 0,
            'nt_amount' => $saleReturn->nt_amount ?? 0,
            'sc_amount' => $saleReturn->sc_amount ?? 0,
            'ft_amount' => $saleReturn->ft_amount ?? 0,
            'dis_amount' => $saleReturn->dis_amount ?? 0,
            'scm_amount' => $saleReturn->scm_amount ?? 0,
            'tax_amount' => $saleReturn->tax_amount ?? 0,
            'net_amount' => $saleReturn->net_amount ?? 0,
            'scm_percent' => $saleReturn->scm_percent ?? 0,
            'tcs_amount' => $saleReturn->tcs_amount ?? 0,
            'adjustments' => $adjustments,
            'items' => $saleReturn->items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code ?? $item->item->code ?? '',
                    'item_name' => $item->item_name ?? $item->item->name ?? '',
                    'batch_id' => $item->batch_id,
                    'batch_no' => $item->batch_no ?? $item->batch->batch_no ?? '',
                    'expiry_date' => $item->expiry_date ?? $item->batch->expiry_date ?? '',
                    'packing' => $item->packing ?? '',
                    'unit' => $item->unit ?? '',
                    'company_name' => $item->company_name ?? '',
                    'location' => $item->location ?? '',
                    'return_qty' => $item->qty ?? 0,
                    'return_fqty' => $item->free_qty ?? 0,
                    'sale_rate' => $item->sale_rate ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'discount_percent' => $item->discount_percent ?? 0,
                    'hsn_code' => $item->hsn_code ?? '',
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                ];
            })
        ];
    }
}
