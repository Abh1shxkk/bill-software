<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleReturnTransaction;
use App\Models\SaleReturnTransactionItem;
use App\Models\Customer;
use App\Models\HsnCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidatesTransactionDate;

class SaleReturnVoucherController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = SaleReturnTransaction::with(['customer', 'items'])
            ->where('voucher_type', 'voucher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('trn_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) $query->whereDate('return_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('return_date', '<=', $request->to_date);

        $vouchers = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.sale-return-voucher.index', compact('vouchers'));
    }

    public function show($id)
    {
        $voucher = SaleReturnTransaction::with(['customer', 'items'])
            ->where('voucher_type', 'voucher')
            ->findOrFail($id);
        
        return view('admin.sale-return-voucher.show', compact('voucher'));
    }

    public function transaction()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        $nextInvoiceNo = $this->generateInvoiceNo();

        return view('admin.sale-return-voucher.transaction', compact('customers', 'hsnCodes', 'nextInvoiceNo'));
    }

    /**
 * Generate next SR No - same sequence as Sale Return module (per organization)
 */
private function generateInvoiceNo()
{
    $orgId = auth()->user()->organization_id ?? 1;
    
    // Use same sr_no sequence as Sale Return module (SR0001, SR0002, etc.)
    $lastReturn = SaleReturnTransaction::withoutGlobalScopes()
        ->where('organization_id', $orgId)
        ->orderBy('id', 'desc')
        ->first();
    if ($lastReturn && $lastReturn->sr_no) {
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastReturn->sr_no);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    return 'SR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}
    public function store(Request $request)
    {
        // Validate transaction date
        $dateError = $this->validateTransactionDate($request, 'sale_return_voucher', 'return_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
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

            $srNo = $this->generateInvoiceNo();
            $voucher = SaleReturnTransaction::create([
                'sr_no' => $srNo,
                'series' => 'SR',
                'return_date' => $validated['return_date'],
                'customer_id' => $validated['customer_id'],
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
                SaleReturnTransactionItem::create([
                    'sale_return_transaction_id' => $voucher->id,
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
            return response()->json(['success' => true, 'message' => 'Voucher saved! Invoice: ' . $voucher->sr_no, 'invoice_no' => $voucher->sr_no]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function modification()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
        })->orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        return view('admin.sale-return-voucher.modification', compact('customers', 'hsnCodes'));
    }

    public function getVouchers()
    {
        $vouchers = SaleReturnTransaction::with('customer')
            ->where('voucher_type', 'voucher')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id, 'invoice_no' => $v->sr_no,
                'return_date' => $v->return_date ? $v->return_date->format('d/m/Y') : '',
                'customer_name' => $v->customer?->name ?? '-', 'net_amount' => $v->net_amount,
            ]);
        return response()->json(['success' => true, 'vouchers' => $vouchers]);
    }

    public function getDetails($id)
    {
        $voucher = SaleReturnTransaction::with('items')->where('voucher_type', 'voucher')->find($id);
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'voucher' => [
            'id' => $voucher->id, 'invoice_no' => $voucher->sr_no,
            'return_date' => $voucher->return_date ? $voucher->return_date->format('Y-m-d') : '',
            'customer_id' => $voucher->customer_id, 'remarks' => $voucher->remarks,
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
        $voucher = SaleReturnTransaction::with('items')->where('voucher_type', 'voucher')->where('sr_no', $invoiceNo)->first();
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'voucher' => [
            'id' => $voucher->id, 'invoice_no' => $voucher->sr_no,
            'return_date' => $voucher->return_date ? $voucher->return_date->format('Y-m-d') : '',
            'customer_id' => $voucher->customer_id, 'remarks' => $voucher->remarks,
            'items' => $voucher->items->map(fn($i) => [
                'hsn_code' => $i->hsn_code, 'amount' => $i->amount, 'gst_percent' => $i->gst_percent,
                'cgst_percent' => $i->cgst_percent, 'cgst_amount' => $i->cgst_amount,
                'sgst_percent' => $i->sgst_percent, 'sgst_amount' => $i->sgst_amount, 'qty' => $i->qty,
            ])
        ]]);
    }

    public function update(Request $request, $id)
    {
        $voucher = SaleReturnTransaction::where('voucher_type', 'voucher')->findOrFail($id);
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
                'return_date' => $request->return_date, 'customer_id' => $request->customer_id,
                'remarks' => $request->remarks, 'nt_amount' => $grossAmount,
                'tax_amount' => $totalCgst + $totalSgst, 'net_amount' => $netAmount,
            ]);
            $voucher->items()->delete();
            $rowOrder = 1;
            foreach ($request->items as $item) {
                if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                SaleReturnTransactionItem::create([
                    'sale_return_transaction_id' => $voucher->id,
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
        $voucher = SaleReturnTransaction::where('voucher_type', 'voucher')->findOrFail($id);
        $voucher->items()->delete();
        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
