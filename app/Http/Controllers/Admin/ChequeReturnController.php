<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChequeReturn;
use App\Models\CustomerReceiptItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChequeReturnController extends Controller
{
    /**
     * Display the cheque return unpaid index page.
     */
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        
        // Fetch all cheques on page load
        $cheques = CustomerReceiptItem::with(['receipt', 'customer'])
            ->where('payment_type', 'cheque')
            ->whereNotNull('cheque_no')
            ->where('cheque_no', '!=', '')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get return status for each cheque
        $chequeData = $cheques->map(function ($item) {
            $chequeReturn = ChequeReturn::where('customer_receipt_item_id', $item->id)->first();
            
            return [
                'id' => $item->id,
                'receipt_id' => $item->customer_receipt_id,
                'customer_id' => $item->customer_id,
                'customer_code' => $item->party_code,
                'customer_name' => $item->party_name,
                'date' => $item->receipt?->receipt_date?->format('d/m/Y') ?? '-',
                'trn_no' => $item->receipt?->trn_no ?? '-',
                'bank_name' => $item->cheque_bank_name ?? '-',
                'cheque_no' => $item->cheque_no,
                'amount' => floatval($item->amount),
                'status' => $chequeReturn?->status ?? 'pending',
                'status_date' => $chequeReturn?->status_date?->format('d/m/Y') ?? '-',
                'cheque_return_id' => $chequeReturn?->id,
                'deposit_date' => $chequeReturn?->deposit_date?->format('Y-m-d') ?? null,
            ];
        });

        return view('admin.cheque-return.index', compact('customers', 'chequeData'));
    }

    /**
     * Get all cheques for a customer with optional cheque number filter.
     */
    public function getCheques(Request $request)
    {
        try {
            $query = CustomerReceiptItem::with(['receipt', 'customer'])
                ->where('payment_type', 'cheque')
                ->whereNotNull('cheque_no')
                ->where('cheque_no', '!=', '');

            // Filter by customer
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            // Filter by cheque number
            if ($request->filled('cheque_no')) {
                $query->where('cheque_no', 'like', '%' . $request->cheque_no . '%');
            }

            $cheques = $query->orderBy('created_at', 'desc')->get();

            // Get return status for each cheque
            $chequeData = $cheques->map(function ($item) {
                $chequeReturn = ChequeReturn::where('customer_receipt_item_id', $item->id)->first();
                
                return [
                    'id' => $item->id,
                    'receipt_id' => $item->customer_receipt_id,
                    'customer_id' => $item->customer_id,
                    'customer_code' => $item->party_code,
                    'customer_name' => $item->party_name,
                    'date' => $item->receipt?->receipt_date?->format('d/m/Y') ?? '-',
                    'trn_no' => $item->receipt?->trn_no ?? '-',
                    'bank_name' => $item->cheque_bank_name ?? '-',
                    'cheque_no' => $item->cheque_no,
                    'amount' => floatval($item->amount),
                    'status' => $chequeReturn?->status ?? 'pending',
                    'status_date' => $chequeReturn?->status_date?->format('d/m/Y') ?? '-',
                    'cheque_return_id' => $chequeReturn?->id,
                    'deposit_date' => $chequeReturn?->deposit_date?->format('Y-m-d') ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'cheques' => $chequeData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cheques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a cheque as returned unpaid.
     */
    public function returnCheque(Request $request)
    {
        $validated = $request->validate([
            'customer_receipt_item_id' => 'required|exists:customer_receipt_items,id',
            'deposit_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $receiptItem = CustomerReceiptItem::with(['receipt', 'customer'])->findOrFail($validated['customer_receipt_item_id']);

            // Check if already returned
            $existing = ChequeReturn::where('customer_receipt_item_id', $receiptItem->id)
                ->where('status', 'returned')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This cheque is already marked as returned'
                ], 400);
            }

            // Create or update cheque return record
            $chequeReturn = ChequeReturn::updateOrCreate(
                ['customer_receipt_item_id' => $receiptItem->id],
                [
                    'customer_receipt_id' => $receiptItem->customer_receipt_id,
                    'customer_id' => $receiptItem->customer_id,
                    'customer_code' => $receiptItem->party_code,
                    'customer_name' => $receiptItem->party_name,
                    'cheque_no' => $receiptItem->cheque_no,
                    'cheque_date' => $receiptItem->cheque_date,
                    'bank_name' => $receiptItem->cheque_bank_name,
                    'bank_area' => $receiptItem->cheque_bank_area,
                    'amount' => $receiptItem->amount,
                    'trn_no' => $receiptItem->receipt?->trn_no,
                    'receipt_date' => $receiptItem->receipt?->receipt_date,
                    'deposit_date' => $validated['deposit_date'] ?? null,
                    'status' => 'returned',
                    'return_date' => now(),
                    'status_date' => now(),
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cheque marked as returned unpaid successfully',
                'cheque_return' => $chequeReturn
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error marking cheque as returned: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a cheque return.
     */
    public function cancelReturn(Request $request)
    {
        $validated = $request->validate([
            'cheque_return_id' => 'required|exists:cheque_returns,id',
        ]);

        try {
            DB::beginTransaction();

            $chequeReturn = ChequeReturn::findOrFail($validated['cheque_return_id']);

            if ($chequeReturn->status !== 'returned') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only returned cheques can be cancelled'
                ], 400);
            }

            $chequeReturn->update([
                'status' => 'cancelled',
                'status_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cheque return cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cheque return history.
     */
    public function getHistory(Request $request)
    {
        try {
            $query = ChequeReturn::with(['customer']);

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $returns = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'returns' => $returns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage()
            ], 500);
        }
    }
}
