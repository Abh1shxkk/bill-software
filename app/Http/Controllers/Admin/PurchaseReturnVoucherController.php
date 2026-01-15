<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseReturnTransactionItem;
use App\Models\Supplier;
use App\Models\HsnCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidatesTransactionDate;

class PurchaseReturnVoucherController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = PurchaseReturnTransaction::with(['supplier', 'items'])
            ->where('voucher_type', 'voucher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) $query->whereDate('return_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('return_date', '<=', $request->to_date);

        $vouchers = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.purchase-return-voucher.index', compact('vouchers'));
    }

    public function show($id)
    {
        $voucher = PurchaseReturnTransaction::with(['supplier', 'items'])
            ->where('voucher_type', 'voucher')
            ->findOrFail($id);
        
        return view('admin.purchase-return-voucher.show', compact('voucher'));
    }

    public function transaction()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        $nextInvoiceNo = $this->generateInvoiceNo();

        return view('admin.purchase-return-voucher.transaction', compact('suppliers', 'hsnCodes', 'nextInvoiceNo'));
    }

    /**
     * Generate next PR No - same sequence as Purchase Return module (per organization)
     */
    private function generateInvoiceNo()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        // Use same pr_no sequence as Purchase Return module (PR0001, PR0002, etc.)
        $lastReturn = PurchaseReturnTransaction::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();
        if ($lastReturn && $lastReturn->pr_no) {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastReturn->pr_no);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        return 'PR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        // Validate transaction date
        $dateError = $this->validateTransactionDate($request, 'purchase_return_voucher', 'return_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $grossAmount = 0; $totalCgst = 0; $totalSgst = 0;
            foreach ($request->items as $item) {
                $grossAmount += floatval($item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }
            $totalTax = $totalCgst + $totalSgst;
            $netAmount = round($grossAmount + $totalTax);

            $prNo = $this->generateInvoiceNo();
            $voucher = PurchaseReturnTransaction::create([
                'pr_no' => $prNo,
                'series' => 'PR',
                'return_date' => $validated['return_date'],
                'supplier_id' => $validated['supplier_id'],
                'voucher_type' => 'voucher',
                'nt_amount' => $grossAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $netAmount,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            $rowOrder = 1;
            foreach ($request->items as $item) {
                if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                PurchaseReturnTransactionItem::create([
                    'purchase_return_transaction_id' => $voucher->id,
                    'item_code' => $item['hsn_code'] ?? 'HSN',
                    'item_name' => 'HSN-' . ($item['hsn_code'] ?? 'Item'),
                    'hsn_code' => $item['hsn_code'] ?? null,
                    'qty' => $item['qty'] ?? 0,
                    'rate' => $item['amount'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher saved! Invoice: ' . $voucher->pr_no, 'invoice_no' => $voucher->pr_no]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function modification()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        return view('admin.purchase-return-voucher.modification', compact('suppliers', 'hsnCodes'));
    }

    public function getVouchers()
    {
        $vouchers = PurchaseReturnTransaction::with('supplier')
            ->where('voucher_type', 'voucher')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id, 'invoice_no' => $v->pr_no,
                'return_date' => $v->return_date ? $v->return_date->format('d/m/Y') : '',
                'supplier_name' => $v->supplier?->name ?? '-', 'net_amount' => $v->net_amount,
            ]);
        return response()->json(['success' => true, 'vouchers' => $vouchers]);
    }

    public function getDetails($id)
    {
        $voucher = PurchaseReturnTransaction::with('items')->where('voucher_type', 'voucher')->find($id);
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'voucher' => [
            'id' => $voucher->id, 'invoice_no' => $voucher->pr_no,
            'return_date' => $voucher->return_date ? $voucher->return_date->format('Y-m-d') : '',
            'supplier_id' => $voucher->supplier_id, 'remarks' => $voucher->remarks,
            'items' => $voucher->items->map(fn($i) => [
                'hsn_code' => $i->hsn_code, 'amount' => $i->amount, 'gst_percent' => $i->gst_percent,
                'cgst_percent' => $i->cgst_percent, 'cgst_amount' => $i->cgst_amount,
                'sgst_percent' => $i->sgst_percent, 'sgst_amount' => $i->sgst_amount, 'qty' => $i->qty,
            ])
        ]]);
    }

    public function searchVoucher(Request $request)
    {
        $invoiceNo = $request->input('invoice_no');
        $voucher = PurchaseReturnTransaction::with('items')->where('voucher_type', 'voucher')->where('pr_no', $invoiceNo)->first();
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'voucher' => [
            'id' => $voucher->id, 'invoice_no' => $voucher->pr_no,
            'return_date' => $voucher->return_date ? $voucher->return_date->format('Y-m-d') : '',
            'supplier_id' => $voucher->supplier_id, 'remarks' => $voucher->remarks,
            'items' => $voucher->items->map(fn($i) => [
                'hsn_code' => $i->hsn_code, 'amount' => $i->amount, 'gst_percent' => $i->gst_percent,
                'cgst_percent' => $i->cgst_percent, 'cgst_amount' => $i->cgst_amount,
                'sgst_percent' => $i->sgst_percent, 'sgst_amount' => $i->sgst_amount, 'qty' => $i->qty,
            ])
        ]]);
    }

    public function update(Request $request, $id)
    {
        $voucher = PurchaseReturnTransaction::where('voucher_type', 'voucher')->findOrFail($id);
        try {
            DB::beginTransaction();
            $grossAmount = 0; $totalCgst = 0; $totalSgst = 0;
            foreach ($request->items as $item) {
                $grossAmount += floatval($item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }
            $netAmount = round($grossAmount + $totalCgst + $totalSgst);

            $voucher->update([
                'return_date' => $request->return_date, 'supplier_id' => $request->supplier_id,
                'remarks' => $request->remarks, 'nt_amount' => $grossAmount,
                'tax_amount' => $totalCgst + $totalSgst, 'net_amount' => $netAmount,
            ]);
            $voucher->items()->delete();
            $rowOrder = 1;
            foreach ($request->items as $item) {
                if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                PurchaseReturnTransactionItem::create([
                    'purchase_return_transaction_id' => $voucher->id,
                    'hsn_code' => $item['hsn_code'], 'qty' => $item['qty'] ?? 0, 'amount' => $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0, 'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0, 'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0, 'row_order' => $rowOrder++,
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $voucher = PurchaseReturnTransaction::where('voucher_type', 'voucher')->findOrFail($id);
        $voucher->items()->delete();
        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
