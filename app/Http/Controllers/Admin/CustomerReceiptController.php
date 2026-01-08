<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReceipt;
use App\Models\CustomerReceiptItem;
use App\Models\CustomerReceiptAdjustment;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Models\Area;
use App\Models\Route;
use App\Models\CashBankBook;
use App\Traits\CrudNotificationTrait;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReceiptController extends Controller
{
    use CrudNotificationTrait, ValidatesTransactionDate;

    /**
     * Display a listing of the receipts (Index page).
     */
    public function index(Request $request)
    {
        $query = CustomerReceipt::with(['items', 'adjustments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('salesman_name', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('receipt_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('receipt_date', '<=', $request->to_date);
        }

        $receipts = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.customer-receipt.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new receipt (Transaction page).
     */
    public function transaction()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $banks = CashBankBook::orderBy('name')->get();
        
        // Get next transaction number
        $nextTrnNo = CustomerReceipt::max('trn_no') + 1;
        if ($nextTrnNo < 1) $nextTrnNo = 1;

        return view('admin.customer-receipt.transaction', compact(
            'customers', 'salesmen', 'areas', 'routes', 'banks', 'nextTrnNo'
        ));
    }

    /**
     * Store a newly created receipt.
     */
    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'customer_receipt', 'receipt_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'ledger' => 'nullable|string|max:10',
            'salesman_id' => 'nullable|integer',
            'salesman_code' => 'nullable|string|max:20',
            'area_id' => 'nullable|integer',
            'area_code' => 'nullable|string|max:20',
            'route_id' => 'nullable|integer',
            'route_code' => 'nullable|string|max:20',
            'bank_code' => 'nullable|string|max:20',
            'coll_boy_id' => 'nullable|integer',
            'coll_boy_code' => 'nullable|string|max:20',
            'day_value' => 'nullable|string|max:20',
            'tag' => 'nullable|string|max:50',
            'tds_amount' => 'nullable|numeric',
            'currency_detail' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Get next transaction number
            $nextTrnNo = CustomerReceipt::max('trn_no') + 1;
            if ($nextTrnNo < 1) $nextTrnNo = 1;

            // Get day name
            $dayName = date('l', strtotime($validated['receipt_date']));

            // Get related models by ID (preferred) or code (fallback)
            $salesman = null;
            if (!empty($validated['salesman_id'])) {
                $salesman = SalesMan::find($validated['salesman_id']);
            } elseif (!empty($validated['salesman_code'])) {
                $salesman = SalesMan::where('code', $validated['salesman_code'])->first();
            }

            $area = null;
            if (!empty($validated['area_id'])) {
                $area = Area::find($validated['area_id']);
            } elseif (!empty($validated['area_code'])) {
                $area = Area::where('alter_code', $validated['area_code'])->first();
            }

            $route = null;
            if (!empty($validated['route_id'])) {
                $route = Route::find($validated['route_id']);
            } elseif (!empty($validated['route_code'])) {
                $route = Route::where('alter_code', $validated['route_code'])->first();
            }

            $bank = CashBankBook::where('alter_code', $validated['bank_code'])->first();

            $collBoy = null;
            if (!empty($validated['coll_boy_id'])) {
                $collBoy = SalesMan::find($validated['coll_boy_id']);
            } elseif (!empty($validated['coll_boy_code'])) {
                $collBoy = SalesMan::where('code', $validated['coll_boy_code'])->first();
            }

            // Calculate totals
            $totalCash = 0;
            $totalCheque = 0;
            foreach ($request->items as $item) {
                if (($item['payment_type'] ?? 'cash') === 'cash') {
                    $totalCash += floatval($item['amount'] ?? 0);
                } else {
                    $totalCheque += floatval($item['amount'] ?? 0);
                }
            }

            // Calculate total adjusted amount
            $amtAdjusted = 0;
            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    $amtAdjusted += floatval($adj['adjusted_amount'] ?? 0);
                }
            }

            $receipt = CustomerReceipt::create([
                'receipt_date' => $validated['receipt_date'],
                'day_name' => $dayName,
                'trn_no' => $nextTrnNo,
                'ledger' => $validated['ledger'] ?? 'CL',
                'salesman_id' => $salesman?->id,
                'salesman_code' => $salesman?->code,
                'salesman_name' => $salesman?->name,
                'area_id' => $area?->id,
                'area_code' => $area?->alter_code,
                'area_name' => $area?->name,
                'route_id' => $route?->id,
                'route_code' => $route?->alter_code,
                'route_name' => $route?->name,
                'bank_code' => $validated['bank_code'] ?? null,
                'bank_name' => $bank?->name,
                'coll_boy_id' => $collBoy?->id,
                'coll_boy_code' => $collBoy?->code,
                'coll_boy_name' => $collBoy?->name,
                'day_value' => $validated['day_value'] ?? null,
                'tag' => $validated['tag'] ?? null,
                'total_cash' => $totalCash,
                'total_cheque' => $totalCheque,
                'amt_adjusted' => $amtAdjusted,
                'tds_amount' => $validated['tds_amount'] ?? 0,
                'currency_detail' => $validated['currency_detail'] ?? false,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Create items with bank details
            foreach ($request->items as $item) {
                if (empty($item['party_code']) && empty($item['party_name'])) continue;
                
                $customer = Customer::where('code', $item['party_code'])->first();
                
                CustomerReceiptItem::create([
                    'customer_receipt_id' => $receipt->id,
                    'party_code' => $item['party_code'] ?? null,
                    'party_name' => $item['party_name'] ?? $customer?->name,
                    'customer_id' => $customer?->id,
                    'cheque_no' => $item['cheque_no'] ?? null,
                    'cheque_date' => $item['cheque_date'] ?? null,
                    'cheque_bank_name' => $item['cheque_bank_name'] ?? null,
                    'cheque_bank_area' => $item['cheque_bank_area'] ?? null,
                    'cheque_closed_on' => $item['cheque_closed_on'] ?? null,
                    'amount' => $item['amount'] ?? 0,
                    'unadjusted' => $item['unadjusted'] ?? $item['amount'] ?? 0,
                    'payment_type' => $item['payment_type'] ?? 'cash',
                ]);
            }

            // Create adjustments if provided and update sale_transactions balance
            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    CustomerReceiptAdjustment::create([
                        'customer_receipt_id' => $receipt->id,
                        'sale_transaction_id' => $adj['sale_transaction_id'] ?? null,
                        'adjustment_type' => $adj['adjustment_type'] ?? 'outstanding',
                        'reference_no' => $adj['reference_no'] ?? null,
                        'reference_date' => $adj['reference_date'] ?? null,
                        'reference_amount' => $adj['reference_amount'] ?? 0,
                        'adjusted_amount' => $adj['adjusted_amount'] ?? 0,
                        'balance_amount' => $adj['balance_amount'] ?? 0,
                    ]);
                    
                    // Update sale_transactions balance when adjustment is made
                    if (!empty($adj['sale_transaction_id'])) {
                        DB::table('sale_transactions')
                            ->where('id', $adj['sale_transaction_id'])
                            ->decrement('balance_amount', floatval($adj['adjusted_amount'] ?? 0));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receipt saved successfully',
                'receipt_id' => $receipt->id,
                'trn_no' => $receipt->trn_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Customer Receipt Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified receipt.
     */
    public function show($id)
    {
        $receipt = CustomerReceipt::with(['items', 'adjustments'])->findOrFail($id);
        return view('admin.customer-receipt.show', compact('receipt'));
    }

    /**
     * Show the modification page.
     */
    public function modification()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        $banks = CashBankBook::orderBy('name')->get();
        
        // Get next transaction number
        $nextTrnNo = CustomerReceipt::max('trn_no') + 1;
        if ($nextTrnNo < 1) $nextTrnNo = 1;

        return view('admin.customer-receipt.modification', compact(
            'customers', 'salesmen', 'areas', 'routes', 'banks', 'nextTrnNo'
        ));
    }

    /**
     * Get receipt by transaction number for modification.
     */
    public function getByTrnNo($trnNo)
    {
        $receipt = CustomerReceipt::with(['items', 'adjustments'])
            ->where('trn_no', $trnNo)
            ->first();

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        }

        // Check if any cheque in this receipt is marked as returned
        $returnedCheque = \App\Models\ChequeReturn::where('customer_receipt_id', $receipt->id)
            ->where('status', 'returned')
            ->first();

        if ($returnedCheque) {
            return response()->json([
                'success' => false,
                'is_returned' => true,
                'message' => 'This receipt cannot be modified. Cheque No. "' . $returnedCheque->cheque_no . '" is marked as Returned. Please cancel the return first from Cheque Return module.'
            ], 403);
        }

        // Convert to array
        $receiptData = $receipt->toArray();
        
        // Ensure IDs are present (prefer saved IDs, fallback to lookup by code)
        if (!$receipt->salesman_id && $receipt->salesman_code) {
            $salesman = SalesMan::where('code', $receipt->salesman_code)->first();
            $receiptData['salesman_id'] = $salesman ? $salesman->id : null;
        } else {
            $receiptData['salesman_id'] = $receipt->salesman_id;
        }
        
        if (!$receipt->area_id && $receipt->area_code) {
            $area = Area::where('alter_code', $receipt->area_code)->first();
            $receiptData['area_id'] = $area ? $area->id : null;
        } else {
            $receiptData['area_id'] = $receipt->area_id;
        }
        
        if (!$receipt->route_id && $receipt->route_code) {
            $route = Route::where('alter_code', $receipt->route_code)->first();
            $receiptData['route_id'] = $route ? $route->id : null;
        } else {
            $receiptData['route_id'] = $receipt->route_id;
        }
        
        if (!$receipt->coll_boy_id && $receipt->coll_boy_code) {
            $collBoy = SalesMan::where('code', $receipt->coll_boy_code)->first();
            $receiptData['coll_boy_id'] = $collBoy ? $collBoy->id : null;
        } else {
            $receiptData['coll_boy_id'] = $receipt->coll_boy_id;
        }

        return response()->json([
            'success' => true,
            'receipt' => $receiptData
        ]);
    }

    /**
     * Get receipt details by ID for modification.
     */
    public function getDetails($id)
    {
        $receipt = CustomerReceipt::with(['items', 'adjustments'])
            ->find($id);

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        }

        // Check if any cheque in this receipt is marked as returned
        $returnedCheque = \App\Models\ChequeReturn::where('customer_receipt_id', $receipt->id)
            ->where('status', 'returned')
            ->first();

        if ($returnedCheque) {
            return response()->json([
                'success' => false,
                'is_returned' => true,
                'message' => 'This receipt cannot be modified. Cheque No. "' . $returnedCheque->cheque_no . '" is marked as Returned. Please cancel the return first from Cheque Return module.'
            ], 403);
        }

        // Look up IDs from codes for Select2 dropdowns
        $receiptData = $receipt->toArray();
        
        // Get salesman ID from code
        if ($receipt->salesman_code) {
            $salesman = SalesMan::where('code', $receipt->salesman_code)->first();
            $receiptData['salesman_id'] = $salesman ? $salesman->id : null;
        }
        
        // Get area ID from alter_code
        if ($receipt->area_code) {
            $area = Area::where('alter_code', $receipt->area_code)->first();
            $receiptData['area_id'] = $area ? $area->id : null;
        }
        
        // Get route ID from alter_code
        if ($receipt->route_code) {
            $route = Route::where('alter_code', $receipt->route_code)->first();
            $receiptData['route_id'] = $route ? $route->id : null;
        }
        
        // Get coll_boy ID from code (uses SalesMan table)
        if ($receipt->coll_boy_code) {
            $collBoy = SalesMan::where('code', $receipt->coll_boy_code)->first();
            $receiptData['coll_boy_id'] = $collBoy ? $collBoy->id : null;
        }

        return response()->json([
            'success' => true,
            'receipt' => $receiptData
        ]);
    }

    /**
     * Get list of receipts for selection.
     */
    public function getReceipts(Request $request)
    {
        try {
            $query = CustomerReceipt::with(['items']);

            if ($request->filled('from_date')) {
                $query->whereDate('receipt_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('receipt_date', '<=', $request->to_date);
            }

            $receipts = $query->orderByDesc('trn_no')->limit(100)->get();

            // Format the receipts for proper JSON response
            $formattedReceipts = $receipts->map(function ($receipt) {
                // Check if this receipt has any returned cheques
                $hasReturnedCheque = \App\Models\ChequeReturn::where('customer_receipt_id', $receipt->id)
                    ->where('status', 'returned')
                    ->exists();

                return [
                    'id' => $receipt->id,
                    'trn_no' => $receipt->trn_no,
                    'receipt_date' => $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : null,
                    'salesman_name' => $receipt->salesman_name ?? '-',
                    'total_cash' => floatval($receipt->total_cash ?? 0),
                    'total_cheque' => floatval($receipt->total_cheque ?? 0),
                    'items_count' => $receipt->items->count(),
                    'has_returned_cheque' => $hasReturnedCheque,
                ];
            });

            return response()->json([
                'success' => true,
                'receipts' => $formattedReceipts,
                'count' => $receipts->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading receipts: ' . $e->getMessage(),
                'receipts' => []
            ], 500);
        }
    }

    /**
     * Update the specified receipt.
     */
    public function update(Request $request, $id)
    {
        $receipt = CustomerReceipt::findOrFail($id);

        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'ledger' => 'nullable|string|max:10',
            'salesman_id' => 'nullable|integer',
            'salesman_code' => 'nullable|string|max:20',
            'area_id' => 'nullable|integer',
            'area_code' => 'nullable|string|max:20',
            'route_id' => 'nullable|integer',
            'route_code' => 'nullable|string|max:20',
            'bank_code' => 'nullable|string|max:20',
            'coll_boy_id' => 'nullable|integer',
            'coll_boy_code' => 'nullable|string|max:20',
            'day_value' => 'nullable|string|max:20',
            'tag' => 'nullable|string|max:50',
            'tds_amount' => 'nullable|numeric',
            'currency_detail' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Get day name
            $dayName = date('l', strtotime($validated['receipt_date']));

            // Get related models by ID (preferred) or code (fallback)
            $salesman = null;
            if (!empty($validated['salesman_id'])) {
                $salesman = SalesMan::find($validated['salesman_id']);
            } elseif (!empty($validated['salesman_code'])) {
                $salesman = SalesMan::where('code', $validated['salesman_code'])->first();
            }

            $area = null;
            if (!empty($validated['area_id'])) {
                $area = Area::find($validated['area_id']);
            } elseif (!empty($validated['area_code'])) {
                $area = Area::where('alter_code', $validated['area_code'])->first();
            }

            $route = null;
            if (!empty($validated['route_id'])) {
                $route = Route::find($validated['route_id']);
            } elseif (!empty($validated['route_code'])) {
                $route = Route::where('alter_code', $validated['route_code'])->first();
            }

            $bank = CashBankBook::where('alter_code', $validated['bank_code'])->first();

            $collBoy = null;
            if (!empty($validated['coll_boy_id'])) {
                $collBoy = SalesMan::find($validated['coll_boy_id']);
            } elseif (!empty($validated['coll_boy_code'])) {
                $collBoy = SalesMan::where('code', $validated['coll_boy_code'])->first();
            }

            // Calculate totals
            $totalCash = 0;
            $totalCheque = 0;
            foreach ($request->items as $item) {
                if (($item['payment_type'] ?? 'cash') === 'cash') {
                    $totalCash += floatval($item['amount'] ?? 0);
                } else {
                    $totalCheque += floatval($item['amount'] ?? 0);
                }
            }

            // Calculate total adjusted amount
            $amtAdjusted = 0;
            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    $amtAdjusted += floatval($adj['adjusted_amount'] ?? 0);
                }
            }

            $receipt->update([
                'receipt_date' => $validated['receipt_date'],
                'day_name' => $dayName,
                'ledger' => $validated['ledger'] ?? 'CL',
                'salesman_id' => $salesman?->id,
                'salesman_code' => $salesman?->code,
                'salesman_name' => $salesman?->name,
                'area_id' => $area?->id,
                'area_code' => $area?->alter_code,
                'area_name' => $area?->name,
                'route_id' => $route?->id,
                'route_code' => $route?->alter_code,
                'route_name' => $route?->name,
                'bank_code' => $validated['bank_code'] ?? null,
                'bank_name' => $bank?->name,
                'coll_boy_id' => $collBoy?->id,
                'coll_boy_code' => $collBoy?->code,
                'coll_boy_name' => $collBoy?->name,
                'day_value' => $validated['day_value'] ?? null,
                'tag' => $validated['tag'] ?? null,
                'total_cash' => $totalCash,
                'total_cheque' => $totalCheque,
                'amt_adjusted' => $amtAdjusted,
                'tds_amount' => $validated['tds_amount'] ?? 0,
                'currency_detail' => $validated['currency_detail'] ?? false,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Restore previous adjustments to sale_transactions before deleting
            foreach ($receipt->adjustments as $oldAdj) {
                if ($oldAdj->reference_no) {
                    $saleTransaction = DB::table('sale_transactions')
                        ->where('invoice_no', $oldAdj->reference_no)
                        ->first();
                    if ($saleTransaction) {
                        DB::table('sale_transactions')
                            ->where('id', $saleTransaction->id)
                            ->increment('balance_amount', floatval($oldAdj->adjusted_amount ?? 0));
                    }
                }
            }

            // Delete existing items and adjustments
            $receipt->items()->delete();
            $receipt->adjustments()->delete();

            // Create items with bank details
            foreach ($request->items as $item) {
                if (empty($item['party_code']) && empty($item['party_name'])) continue;
                
                $customer = Customer::where('code', $item['party_code'])->first();
                
                CustomerReceiptItem::create([
                    'customer_receipt_id' => $receipt->id,
                    'party_code' => $item['party_code'] ?? null,
                    'party_name' => $item['party_name'] ?? $customer?->name,
                    'customer_id' => $customer?->id,
                    'cheque_no' => $item['cheque_no'] ?? null,
                    'cheque_date' => $item['cheque_date'] ?? null,
                    'cheque_bank_name' => $item['cheque_bank_name'] ?? null,
                    'cheque_bank_area' => $item['cheque_bank_area'] ?? null,
                    'cheque_closed_on' => $item['cheque_closed_on'] ?? null,
                    'amount' => $item['amount'] ?? 0,
                    'unadjusted' => $item['unadjusted'] ?? $item['amount'] ?? 0,
                    'payment_type' => $item['payment_type'] ?? 'cash',
                ]);
            }

            // Create adjustments if provided and update sale_transactions balance
            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    CustomerReceiptAdjustment::create([
                        'customer_receipt_id' => $receipt->id,
                        'sale_transaction_id' => $adj['sale_transaction_id'] ?? null,
                        'adjustment_type' => $adj['adjustment_type'] ?? 'outstanding',
                        'reference_no' => $adj['reference_no'] ?? null,
                        'reference_date' => $adj['reference_date'] ?? null,
                        'reference_amount' => $adj['reference_amount'] ?? 0,
                        'adjusted_amount' => $adj['adjusted_amount'] ?? 0,
                        'balance_amount' => $adj['balance_amount'] ?? 0,
                    ]);
                    
                    // Update sale_transactions balance when adjustment is made
                    if (!empty($adj['sale_transaction_id'])) {
                        DB::table('sale_transactions')
                            ->where('id', $adj['sale_transaction_id'])
                            ->decrement('balance_amount', floatval($adj['adjusted_amount'] ?? 0));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receipt updated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Customer Receipt Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified receipt.
     */
    public function destroy($id)
    {
        try {
            $receipt = CustomerReceipt::findOrFail($id);
            $trnNo = $receipt->trn_no;
            $receipt->delete();

            return response()->json([
                'success' => true,
                'message' => "Receipt #{$trnNo} deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer outstanding invoices for adjustment with pagination.
     */
    public function getCustomerOutstanding($customerId, Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 100); // Increased for adjustment modal
            $offset = ($page - 1) * $perPage;
            $receiptId = $request->get('receipt_id'); // For modification - to get existing adjustments

            // Get existing adjustments for this receipt (if in modification mode)
            $existingAdjustments = [];
            if ($receiptId) {
                $adjustments = CustomerReceiptAdjustment::where('customer_receipt_id', $receiptId)->get();
                foreach ($adjustments as $adj) {
                    // Map by reference_no (invoice_no)
                    $existingAdjustments[$adj->reference_no] = floatval($adj->adjusted_amount);
                }
            }

            // Build query - include invoices that have balance OR have existing adjustments for this receipt
            $query = DB::table('sale_transactions')
                ->where('customer_id', $customerId);
            
            if ($receiptId && !empty($existingAdjustments)) {
                // In modification mode: include both outstanding and previously adjusted invoices
                $adjustedInvoiceNos = array_keys($existingAdjustments);
                $query->where(function($q) use ($adjustedInvoiceNos) {
                    $q->where('balance_amount', '>', 0)
                      ->orWhereIn('invoice_no', $adjustedInvoiceNos);
                });
            } else {
                // Normal mode: only outstanding
                $query->where('balance_amount', '>', 0);
            }

            // Get total count
            $total = (clone $query)->count();

            // Get total outstanding amount
            $totalAmount = (clone $query)->sum('balance_amount');

            // Get outstanding invoices
            $outstanding = $query
                ->select('id', 'invoice_no', 'sale_date as invoice_date', 'net_amount', 'balance_amount')
                ->orderBy('sale_date')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Add existing adjustment info to each invoice
            $outstanding = $outstanding->map(function($inv) use ($existingAdjustments) {
                $existingAdj = $existingAdjustments[$inv->invoice_no] ?? 0;
                $inv->existing_adjustment = $existingAdj;
                // Available amount = current balance + existing adjustment (what was available before this receipt adjusted it)
                $inv->available_amount = floatval($inv->balance_amount) + $existingAdj;
                return $inv;
            });

            // Also add back existing adjustments to total amount for modification
            $existingAdjTotal = array_sum($existingAdjustments);
            $totalAmount = floatval($totalAmount) + $existingAdjTotal;

            $hasMore = ($offset + $perPage) < $total;

            return response()->json([
                'success' => true,
                'outstanding' => $outstanding,
                'total_amount' => $totalAmount,
                'total_count' => $total,
                'current_page' => (int)$page,
                'has_more' => $hasMore,
                'existing_adjustments' => $existingAdjustments
            ]);
        } catch (\Exception $e) {
            \Log::error('Customer Outstanding Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'outstanding' => []
            ]);
        }
    }

    /**
     * Get next transaction number.
     */
    public function getNextTrnNo()
    {
        $nextTrnNo = CustomerReceipt::max('trn_no') + 1;
        if ($nextTrnNo < 1) $nextTrnNo = 1;

        return response()->json([
            'success' => true,
            'trn_no' => $nextTrnNo
        ]);
    }
}
