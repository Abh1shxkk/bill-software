<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentItem;
use App\Models\SupplierPaymentAdjustment;
use App\Models\Supplier;
use App\Models\CashBankBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the payments (Index page).
     */
    public function index(Request $request)
    {
        $query = SupplierPayment::with(['items', 'adjustments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.supplier-payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment (Transaction page).
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $banks = CashBankBook::orderBy('name')->get();
        
        $nextTrnNo = SupplierPayment::max('trn_no') + 1;
        if ($nextTrnNo < 1) $nextTrnNo = 1;

        return view('admin.supplier-payment.transaction', compact('suppliers', 'banks', 'nextTrnNo'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'ledger' => 'nullable|string|max:10',
            'bank_code' => 'nullable|string|max:20',
            'tds_amount' => 'nullable|numeric',
            'currency_detail' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $nextTrnNo = SupplierPayment::max('trn_no') + 1;
            if ($nextTrnNo < 1) $nextTrnNo = 1;

            $dayName = date('l', strtotime($validated['payment_date']));
            $bank = CashBankBook::where('alter_code', $validated['bank_code'])->first();

            $totalCash = 0;
            $totalCheque = 0;
            foreach ($request->items as $item) {
                if (($item['payment_type'] ?? 'cash') === 'cash') {
                    $totalCash += floatval($item['amount'] ?? 0);
                } else {
                    $totalCheque += floatval($item['amount'] ?? 0);
                }
            }

            $payment = SupplierPayment::create([
                'payment_date' => $validated['payment_date'],
                'day_name' => $dayName,
                'trn_no' => $nextTrnNo,
                'ledger' => $validated['ledger'] ?? 'SL',
                'bank_code' => $validated['bank_code'] ?? null,
                'bank_name' => $bank?->name,
                'total_cash' => $totalCash,
                'total_cheque' => $totalCheque,
                'tds_amount' => $validated['tds_amount'] ?? 0,
                'currency_detail' => $validated['currency_detail'] ?? false,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($request->items as $item) {
                if (empty($item['party_code']) && empty($item['party_name'])) continue;
                
                $supplier = Supplier::where('code', $item['party_code'])->first();
                
                SupplierPaymentItem::create([
                    'supplier_payment_id' => $payment->id,
                    'party_code' => $item['party_code'] ?? null,
                    'party_name' => $item['party_name'] ?? $supplier?->name,
                    'supplier_id' => $supplier?->supplier_id,
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

            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    SupplierPaymentAdjustment::create([
                        'supplier_payment_id' => $payment->id,
                        'adjustment_type' => $adj['adjustment_type'] ?? 'outstanding',
                        'reference_no' => $adj['reference_no'] ?? null,
                        'reference_date' => $adj['reference_date'] ?? null,
                        'reference_amount' => $adj['reference_amount'] ?? 0,
                        'adjusted_amount' => $adj['adjusted_amount'] ?? 0,
                        'balance_amount' => $adj['balance_amount'] ?? 0,
                    ]);
                    
                    if (!empty($adj['purchase_transaction_id'])) {
                        DB::table('purchase_transactions')
                            ->where('id', $adj['purchase_transaction_id'])
                            ->decrement('balance_amount', floatval($adj['adjusted_amount'] ?? 0));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment saved successfully',
                'payment_id' => $payment->id,
                'trn_no' => $payment->trn_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supplier Payment Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = SupplierPayment::with(['items', 'adjustments'])->findOrFail($id);
        return view('admin.supplier-payment.show', compact('payment'));
    }

    /**
     * Show the modification page.
     */
    public function modification()
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $banks = CashBankBook::orderBy('name')->get();

        return view('admin.supplier-payment.modification', compact('suppliers', 'banks'));
    }

    /**
     * Get payment by transaction number for modification.
     */
    public function getByTrnNo($trnNo)
    {
        $payment = SupplierPayment::with(['items', 'adjustments'])
            ->where('trn_no', $trnNo)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }

    /**
     * Get list of payments for selection.
     */
    public function getPayments(Request $request)
    {
        $query = SupplierPayment::with(['items']);

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderByDesc('trn_no')->limit(100)->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, $id)
    {
        $payment = SupplierPayment::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'ledger' => 'nullable|string|max:10',
            'bank_code' => 'nullable|string|max:20',
            'tds_amount' => 'nullable|numeric',
            'currency_detail' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $dayName = date('l', strtotime($validated['payment_date']));
            $bank = CashBankBook::where('alter_code', $validated['bank_code'])->first();

            $totalCash = 0;
            $totalCheque = 0;
            foreach ($request->items as $item) {
                if (($item['payment_type'] ?? 'cash') === 'cash') {
                    $totalCash += floatval($item['amount'] ?? 0);
                } else {
                    $totalCheque += floatval($item['amount'] ?? 0);
                }
            }

            $payment->update([
                'payment_date' => $validated['payment_date'],
                'day_name' => $dayName,
                'ledger' => $validated['ledger'] ?? 'SL',
                'bank_code' => $validated['bank_code'] ?? null,
                'bank_name' => $bank?->name,
                'total_cash' => $totalCash,
                'total_cheque' => $totalCheque,
                'tds_amount' => $validated['tds_amount'] ?? 0,
                'currency_detail' => $validated['currency_detail'] ?? false,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Restore previous adjustments to purchase_transactions before deleting
            foreach ($payment->adjustments as $oldAdj) {
                if ($oldAdj->reference_no) {
                    $purchaseTransaction = DB::table('purchase_transactions')
                        ->where('bill_no', $oldAdj->reference_no)
                        ->first();
                    if ($purchaseTransaction) {
                        DB::table('purchase_transactions')
                            ->where('id', $purchaseTransaction->id)
                            ->increment('balance_amount', floatval($oldAdj->adjusted_amount ?? 0));
                    }
                }
            }

            $payment->items()->delete();
            $payment->adjustments()->delete();

            foreach ($request->items as $item) {
                if (empty($item['party_code']) && empty($item['party_name'])) continue;
                
                $supplier = Supplier::where('code', $item['party_code'])->first();
                
                SupplierPaymentItem::create([
                    'supplier_payment_id' => $payment->id,
                    'party_code' => $item['party_code'] ?? null,
                    'party_name' => $item['party_name'] ?? $supplier?->name,
                    'supplier_id' => $supplier?->supplier_id,
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

            if ($request->has('adjustments')) {
                foreach ($request->adjustments as $adj) {
                    SupplierPaymentAdjustment::create([
                        'supplier_payment_id' => $payment->id,
                        'adjustment_type' => $adj['adjustment_type'] ?? 'outstanding',
                        'reference_no' => $adj['reference_no'] ?? null,
                        'reference_date' => $adj['reference_date'] ?? null,
                        'reference_amount' => $adj['reference_amount'] ?? 0,
                        'adjusted_amount' => $adj['adjusted_amount'] ?? 0,
                        'balance_amount' => $adj['balance_amount'] ?? 0,
                    ]);
                    
                    if (!empty($adj['purchase_transaction_id'])) {
                        DB::table('purchase_transactions')
                            ->where('id', $adj['purchase_transaction_id'])
                            ->decrement('balance_amount', floatval($adj['adjusted_amount'] ?? 0));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supplier Payment Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        try {
            $payment = SupplierPayment::findOrFail($id);
            $trnNo = $payment->trn_no;
            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => "Payment #{$trnNo} deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier outstanding invoices for adjustment with pagination.
     */
    public function getSupplierOutstanding($supplierId, Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 100); // Increased for adjustment modal
            $offset = ($page - 1) * $perPage;
            $paymentId = $request->get('payment_id'); // For modification - to get existing adjustments

            // Get existing adjustments for this payment (if in modification mode)
            $existingAdjustments = [];
            if ($paymentId) {
                $adjustments = SupplierPaymentAdjustment::where('supplier_payment_id', $paymentId)->get();
                foreach ($adjustments as $adj) {
                    // Map by reference_no (bill_no)
                    $existingAdjustments[$adj->reference_no] = floatval($adj->adjusted_amount);
                }
            }

            // Build query - include invoices that have balance OR have existing adjustments for this payment
            $query = DB::table('purchase_transactions')
                ->where('supplier_id', $supplierId);
            
            if ($paymentId && !empty($existingAdjustments)) {
                // In modification mode: include both outstanding and previously adjusted invoices
                $adjustedInvoiceNos = array_keys($existingAdjustments);
                $query->where(function($q) use ($adjustedInvoiceNos) {
                    $q->where('balance_amount', '>', 0)
                      ->orWhereIn('bill_no', $adjustedInvoiceNos);
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
                ->select('id', 'bill_no as invoice_no', 'bill_date as invoice_date', 'inv_amount as net_amount', 'balance_amount')
                ->orderBy('bill_date')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Add existing adjustment info to each invoice
            $outstanding = $outstanding->map(function($inv) use ($existingAdjustments) {
                $existingAdj = $existingAdjustments[$inv->invoice_no] ?? 0;
                $inv->existing_adjustment = $existingAdj;
                // Available amount = current balance + existing adjustment
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
            \Log::error('Supplier Outstanding Error: ' . $e->getMessage());
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
        $nextTrnNo = SupplierPayment::max('trn_no') + 1;
        if ($nextTrnNo < 1) $nextTrnNo = 1;

        return response()->json([
            'success' => true,
            'trn_no' => $nextTrnNo
        ]);
    }
}
