<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\Supplier;
use App\Models\HsnCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseVoucherController extends Controller
{
    /**
     * Display a listing of vouchers.
     */
    public function index(Request $request)
    {
        $query = PurchaseTransaction::with(['supplier', 'items'])
            ->where('voucher_type', 'voucher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bill_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('bill_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('bill_date', '<=', $request->to_date);
        }

        $vouchers = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.purchase-voucher.index', compact('vouchers'));
    }

    /**
     * Show voucher details
     */
    public function show($id)
    {
        $voucher = PurchaseTransaction::with(['supplier', 'items'])
            ->where('voucher_type', 'voucher')
            ->findOrFail($id);
        
        return view('admin.purchase-voucher.show', compact('voucher'));
    }

    /**
     * Show the transaction form.
     */
    public function transaction()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        // Get next trn_no (same sequence as Purchase module - padded format, per organization)
        $orgId = auth()->user()->organization_id ?? 1;
        
        $lastTransaction = PurchaseTransaction::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();
        $lastTrnNo = $lastTransaction ? intval($lastTransaction->trn_no) : 0;
        $nextTrnNo = str_pad($lastTrnNo + 1, 6, '0', STR_PAD_LEFT);  // Format: 000044
        $nextBillNo = $nextTrnNo;  // Default bill no same as trn_no

        return view('admin.purchase-voucher.transaction', compact(
            'suppliers', 'hsnCodes', 'nextTrnNo', 'nextBillNo'
        ));
    }

    /**
     * Generate next bill number - same sequence as Purchase module (per organization)
     */
    private function generateBillNo()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        // Use the same sequence as main Purchase module (trn_no)
        $lastTransaction = PurchaseTransaction::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastTransaction ? (intval($lastTransaction->trn_no) + 1) : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created voucher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $grossAmount = 0;
            $totalCgst = 0;
            $totalSgst = 0;

            foreach ($request->items as $item) {
                $grossAmount += floatval($item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }

            $totalTax = $totalCgst + $totalSgst;
            $netAmount = $grossAmount + $totalTax;
            $netAmount = round($netAmount);

            // Get next trn_no (same sequence as Purchase module - padded format, per organization)
            $orgId = auth()->user()->organization_id ?? 1;
            
            $lastTransaction = PurchaseTransaction::withoutGlobalScopes()
                ->where('organization_id', $orgId)
                ->orderBy('id', 'desc')
                ->first();
            $lastTrnNo = $lastTransaction ? intval($lastTransaction->trn_no) : 0;
            $nextTrnNo = $lastTrnNo + 1;
            $paddedTrnNo = str_pad($nextTrnNo, 6, '0', STR_PAD_LEFT);  // Format: 000044
            
            // Use user-provided bill_no
            $billNo = $request->bill_no ?: $paddedTrnNo;

            // Create voucher
            $voucher = PurchaseTransaction::create([
                'trn_no' => $paddedTrnNo,
                'bill_no' => $billNo,
                'bill_date' => $validated['bill_date'],
                'receive_date' => $validated['bill_date'],
                'supplier_id' => $validated['supplier_id'],
                'voucher_type' => 'voucher',
                'nt_amount' => $grossAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $netAmount,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Create items
            $rowOrder = 1;
            foreach ($request->items as $item) {
                if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                
                PurchaseTransactionItem::create([
                    'purchase_transaction_id' => $voucher->id,
                    'item_code' => $item['hsn_code'] ?? 'HSN',  // Use HSN code as item code for voucher
                    'item_name' => 'HSN-' . ($item['hsn_code'] ?? 'Item'),  // Generic item name for voucher
                    'hsn_code' => $item['hsn_code'] ?? null,
                    'qty' => $item['qty'] ?? 0,
                    'pur_rate' => $item['amount'] ?? 0,  // Amount as purchase rate
                    'amount' => $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'tax_amount' => ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'net_amount' => ($item['amount'] ?? 0) + ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Voucher saved successfully',
                'voucher_id' => $voucher->id,
                'bill_no' => $voucher->bill_no
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show modification page.
     */
    public function modification()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        return view('admin.purchase-voucher.modification', compact(
            'suppliers', 'hsnCodes'
        ));
    }

    /**
     * Get vouchers list for modal.
     */
    public function getVouchers()
    {
        $vouchers = PurchaseTransaction::with('supplier')
            ->where('voucher_type', 'voucher')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(function($v) {
                return [
                    'id' => $v->id,
                    'bill_no' => $v->bill_no,
                    'bill_date' => $v->bill_date ? $v->bill_date->format('d/m/Y') : '',
                    'supplier_name' => $v->supplier?->name ?? '-',
                    'net_amount' => $v->net_amount,
                ];
            });

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers
        ]);
    }

    /**
     * Get voucher details.
     */
    public function getDetails($id)
    {
        try {
            $voucher = PurchaseTransaction::with('items')
                ->where('voucher_type', 'voucher')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'voucher' => [
                    'id' => $voucher->id,
                    'bill_no' => $voucher->bill_no,
                    'bill_date' => $voucher->bill_date ? $voucher->bill_date->format('Y-m-d') : '',
                    'supplier_id' => $voucher->supplier_id,
                    'remarks' => $voucher->remarks,
                    'items' => $voucher->items->map(function($item) {
                        return [
                            'hsn_code' => $item->hsn_code,
                            'amount' => $item->amount,
                            'gst_percent' => $item->gst_percent,
                            'cgst_percent' => $item->cgst_percent,
                            'cgst_amount' => $item->cgst_amount,
                            'sgst_percent' => $item->sgst_percent,
                            'sgst_amount' => $item->sgst_amount,
                            'qty' => $item->qty,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found'
            ], 404);
        }
    }

    /**
     * Search voucher by bill number.
     */
    public function searchVoucher(Request $request)
    {
        $billNo = $request->input('bill_no');
        
        $voucher = PurchaseTransaction::with('items')
            ->where('voucher_type', 'voucher')
            ->where('bill_no', $billNo)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'bill_no' => $voucher->bill_no,
                'bill_date' => $voucher->bill_date ? $voucher->bill_date->format('Y-m-d') : '',
                'supplier_id' => $voucher->supplier_id,
                'remarks' => $voucher->remarks,
                'items' => $voucher->items->map(function($item) {
                    return [
                        'hsn_code' => $item->hsn_code,
                        'amount' => $item->amount,
                        'gst_percent' => $item->gst_percent,
                        'cgst_percent' => $item->cgst_percent,
                        'cgst_amount' => $item->cgst_amount,
                        'sgst_percent' => $item->sgst_percent,
                        'sgst_amount' => $item->sgst_amount,
                        'qty' => $item->qty,
                    ];
                })
            ]
        ]);
    }

    /**
     * Update a voucher.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'bill_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $voucher = PurchaseTransaction::where('voucher_type', 'voucher')->findOrFail($id);

            // Calculate totals
            $grossAmount = 0;
            $totalCgst = 0;
            $totalSgst = 0;

            foreach ($request->items as $item) {
                $grossAmount += floatval($item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }

            $totalTax = $totalCgst + $totalSgst;
            $netAmount = round($grossAmount + $totalTax);

            $voucher->update([
                'bill_date' => $validated['bill_date'],
                'supplier_id' => $validated['supplier_id'],
                'remarks' => $request->remarks,
                'nt_amount' => $grossAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $netAmount,
            ]);

            $voucher->items()->delete();

            $rowOrder = 1;
            foreach ($request->items as $item) {
                if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                
                PurchaseTransactionItem::create([
                    'purchase_transaction_id' => $voucher->id,
                    'item_code' => $item['hsn_code'] ?? 'HSN',
                    'item_name' => 'HSN-' . ($item['hsn_code'] ?? 'Item'),
                    'hsn_code' => $item['hsn_code'] ?? null,
                    'qty' => $item['qty'] ?? 0,
                    'pur_rate' => $item['amount'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'tax_amount' => ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'net_amount' => ($item['amount'] ?? 0) + ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = PurchaseTransaction::where('voucher_type', 'voucher')->findOrFail($id);
            $voucher->items()->delete();
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voucher deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get HSN codes.
     */
    public function getHsnCodes()
    {
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        return response()->json([
            'success' => true,
            'hsn_codes' => $hsnCodes
        ]);
    }
}
