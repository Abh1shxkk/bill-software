<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositSlip;
use App\Models\CustomerReceiptItem;
use App\Models\Customer;
use App\Models\CashBankBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositSlipController extends Controller
{
    /**
     * Display the deposit slip index page.
     */
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        $banks = CashBankBook::orderBy('name')->get();
        
        // Get next slip number
        $lastSlip = DepositSlip::max('slip_no');
        $nextSlipNo = $lastSlip ? $lastSlip + 1 : 1;

        // Fetch all pending cheques (not yet deposited)
        $cheques = CustomerReceiptItem::with(['receipt', 'customer'])
            ->where('payment_type', 'cheque')
            ->whereNotNull('cheque_no')
            ->where('cheque_no', '!=', '')
            ->orderBy('cheque_date', 'desc')
            ->get();

        // Get deposit status for each cheque
        $chequeData = $cheques->map(function ($item) {
            $depositSlip = DepositSlip::where('cheque_no', $item->cheque_no)
                ->where('customer_id', $item->customer_id)
                ->first();
            
            return [
                'id' => $item->id,
                'receipt_id' => $item->customer_receipt_id,
                'customer_id' => $item->customer_id,
                'customer_code' => $item->party_code,
                'customer_name' => $item->party_name,
                'cheque_no' => $item->cheque_no,
                'cheque_date' => $item->cheque_date?->format('d-M-y') ?? '-',
                'cheque_date_raw' => $item->cheque_date?->format('Y-m-d'),
                'bank_name' => $item->cheque_bank_name ?? '-',
                'amount' => floatval($item->amount),
                'trn_no' => $item->receipt?->trn_no ?? '-',
                'status' => $depositSlip?->status ?? 'pending',
                'deposit_slip_id' => $depositSlip?->id,
            ];
        });

        return view('admin.deposit-slip.index', compact('customers', 'banks', 'chequeData', 'nextSlipNo'));
    }

    /**
     * Get cheques based on filters.
     */
    public function getCheques(Request $request)
    {
        try {
            $query = CustomerReceiptItem::with(['receipt', 'customer'])
                ->where('payment_type', 'cheque')
                ->whereNotNull('cheque_no')
                ->where('cheque_no', '!=', '');

            // Filter by date range (cheques upto)
            if ($request->filled('cheques_upto')) {
                $query->whereDate('cheque_date', '<=', $request->cheques_upto);
            }

            // Filter by bank name
            if ($request->filled('bank_name')) {
                $query->where('cheque_bank_name', 'like', '%' . $request->bank_name . '%');
            }

            // Filter by customer
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            // Filter by search type (D for Date, N for Name)
            if ($request->filled('search_type') && $request->filled('search_value')) {
                if ($request->search_type === 'D') {
                    $query->whereDate('cheque_date', $request->search_value);
                } else {
                    $query->where('party_name', 'like', '%' . $request->search_value . '%');
                }
            }

            $cheques = $query->orderBy('cheque_date', 'desc')->get();

            $chequeData = $cheques->map(function ($item) {
                $depositSlip = DepositSlip::where('cheque_no', $item->cheque_no)
                    ->where('customer_id', $item->customer_id)
                    ->first();
                
                return [
                    'id' => $item->id,
                    'customer_id' => $item->customer_id,
                    'customer_code' => $item->party_code,
                    'customer_name' => $item->party_name,
                    'cheque_no' => $item->cheque_no,
                    'cheque_date' => $item->cheque_date?->format('d-M-y') ?? '-',
                    'cheque_date_raw' => $item->cheque_date?->format('Y-m-d'),
                    'bank_name' => $item->cheque_bank_name ?? '-',
                    'amount' => floatval($item->amount),
                    'trn_no' => $item->receipt?->trn_no ?? '-',
                    'status' => $depositSlip?->status ?? 'pending',
                    'deposit_slip_id' => $depositSlip?->id,
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
     * Store deposit slip (mark cheque as posted).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_receipt_item_id' => 'required|exists:customer_receipt_items,id',
            'slip_no' => 'required|integer',
            'deposit_date' => 'required|date',
            'clearing_date' => 'nullable|date',
            'payin_slip_date' => 'nullable|date',
            'bank_id' => 'nullable|exists:cash_bank_books,id',
            'bank_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $receiptItem = CustomerReceiptItem::with(['receipt', 'customer'])->findOrFail($validated['customer_receipt_item_id']);

            // Check if already posted
            $existing = DepositSlip::where('cheque_no', $receiptItem->cheque_no)
                ->where('customer_id', $receiptItem->customer_id)
                ->where('status', 'posted')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This cheque is already posted'
                ], 400);
            }

            // Create deposit slip record
            $depositSlip = DepositSlip::updateOrCreate(
                [
                    'cheque_no' => $receiptItem->cheque_no,
                    'customer_id' => $receiptItem->customer_id,
                ],
                [
                    'slip_no' => $validated['slip_no'],
                    'deposit_date' => $validated['deposit_date'],
                    'clearing_date' => $validated['clearing_date'] ?? $validated['deposit_date'],
                    'payin_slip_date' => $validated['payin_slip_date'] ?? $validated['deposit_date'],
                    'bank_id' => $validated['bank_id'] ?? null,
                    'bank_name' => $validated['bank_name'] ?? $receiptItem->cheque_bank_name,
                    'customer_code' => $receiptItem->party_code,
                    'customer_name' => $receiptItem->party_name,
                    'cheque_date' => $receiptItem->cheque_date,
                    'amount' => $receiptItem->amount,
                    'status' => 'posted',
                    'posted_date' => now(),
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cheque posted successfully',
                'deposit_slip' => $depositSlip
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error posting cheque: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel/Unpost a deposit slip.
     */
    public function unpost(Request $request)
    {
        $validated = $request->validate([
            'deposit_slip_id' => 'required|exists:deposit_slips,id',
        ]);

        try {
            DB::beginTransaction();

            $depositSlip = DepositSlip::findOrFail($validated['deposit_slip_id']);

            if ($depositSlip->status !== 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only posted cheques can be unposted'
                ], 400);
            }

            $depositSlip->update([
                'status' => 'pending',
                'posted_date' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cheque unposted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error unposting cheque: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get deposit slip summary/totals.
     */
    public function getSummary(Request $request)
    {
        try {
            $query = DepositSlip::query();

            if ($request->filled('deposit_date')) {
                $query->whereDate('deposit_date', $request->deposit_date);
            }

            if ($request->filled('bank_id')) {
                $query->where('bank_id', $request->bank_id);
            }

            $total = $query->where('status', 'posted')->sum('amount');
            $count = $query->where('status', 'posted')->count();
            $unpostedCount = DepositSlip::where('status', 'pending')->count();

            return response()->json([
                'success' => true,
                'total' => $total,
                'count' => $count,
                'unposted_count' => $unpostedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching summary: ' . $e->getMessage()
            ], 500);
        }
    }
}
