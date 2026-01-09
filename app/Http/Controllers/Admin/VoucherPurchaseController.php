<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseVoucher;
use App\Models\PurchaseVoucherItem;
use App\Models\PurchaseVoucherAccount;
use App\Models\Supplier;
use App\Models\HsnCode;
use App\Models\GeneralLedger;
use App\Models\CashBankBook;
use App\Models\PurchaseLedger;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherPurchaseController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = PurchaseVoucher::with(['supplier', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('bill_no', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }

        $vouchers = $query->orderByDesc('voucher_date')->orderByDesc('voucher_no')->paginate(15);
        
        return view('admin.voucher-purchase.index', compact('vouchers'));
    }

    public function transaction()
    {
        $suppliers = Supplier::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        
        // Get HSN codes and ensure numeric values for GST percentages
        $hsnCodes = HsnCode::orderBy('hsn_code')->get()->map(function($hsn) {
            return [
                'id' => $hsn->id,
                'hsn_code' => $hsn->hsn_code,
                'name' => $hsn->name,
                'cgst_percent' => floatval($hsn->cgst_percent ?? 0),
                'sgst_percent' => floatval($hsn->sgst_percent ?? 0),
                'igst_percent' => floatval($hsn->igst_percent ?? 0),
                'total_gst_percent' => floatval($hsn->total_gst_percent ?? 0),
            ];
        });
        
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $purchaseLedgers = PurchaseLedger::orderBy('ledger_name')->get();
        $nextVoucherNo = PurchaseVoucher::getNextVoucherNo();

        return view('admin.voucher-purchase.transaction', compact(
            'suppliers', 'hsnCodes', 'generalLedgers', 'cashBankBooks', 
            'purchaseLedgers', 'nextVoucherNo'
        ));
    }

    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'voucher_purchase', 'voucher_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'bill_no' => 'nullable|string|max:100',
            'bill_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $nextVoucherNo = PurchaseVoucher::getNextVoucherNo();

            $voucher = PurchaseVoucher::create([
                'voucher_date' => $data['voucher_date'],
                'voucher_no' => $nextVoucherNo,
                'bill_no' => $data['bill_no'] ?? null,
                'bill_date' => $data['bill_date'] ?? null,
                'local_inter' => $data['local_inter'] ?? 'L',
                'rcm' => $data['rcm'] ?? 'N',
                'description' => $data['description'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'supplier_code' => $data['supplier_code'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'gst_no' => $data['gst_no'] ?? null,
                'pan_no' => $data['pan_no'] ?? null,
                'city' => $data['city'] ?? null,
                'pin' => $data['pin'] ?? null,
                'amount' => $data['amount'] ?? 0,
                'total_gst' => $data['total_gst'] ?? 0,
                'net_amount' => $data['net_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'total_debit' => $data['total_debit'] ?? 0,
                'tds_percent' => $data['tds_percent'] ?? 0,
                'tds_amount' => $data['tds_amount'] ?? 0,
                'payment_type' => $data['payment_type'] ?? '1',
                'credit_account_id' => $data['credit_account_id'] ?? null,
                'credit_account_type' => $data['credit_account_type'] ?? null,
                'credit_account_name' => $data['credit_account_name'] ?? null,
                'cheque_no' => $data['cheque_no'] ?? null,
                'total_credit' => $data['total_credit'] ?? 0,
                'total_cgst_amount' => $data['total_cgst_amount'] ?? 0,
                'total_sgst_amount' => $data['total_sgst_amount'] ?? 0,
                'total_igst_amount' => $data['total_igst_amount'] ?? 0,
                'status' => 'active',
            ]);

            // Save HSN items
            if (!empty($data['items'])) {
                $sortOrder = 0;
                foreach ($data['items'] as $item) {
                    if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                    $sortOrder++;
                    PurchaseVoucherItem::create([
                        'purchase_voucher_id' => $voucher->id,
                        'hsn_code' => $item['hsn_code'] ?? null,
                        'amount' => $item['amount'] ?? 0,
                        'gst_percent' => $item['gst_percent'] ?? 0,
                        'cgst_percent' => $item['cgst_percent'] ?? 0,
                        'cgst_amount' => $item['cgst_amount'] ?? 0,
                        'sgst_percent' => $item['sgst_percent'] ?? 0,
                        'sgst_amount' => $item['sgst_amount'] ?? 0,
                        'igst_percent' => $item['igst_percent'] ?? 0,
                        'igst_amount' => $item['igst_amount'] ?? 0,
                        'total_amount' => $item['total_amount'] ?? 0,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            // Save accounts
            if (!empty($data['accounts'])) {
                $sortOrder = 0;
                foreach ($data['accounts'] as $account) {
                    if (empty($account['account_name'])) continue;
                    $sortOrder++;
                    PurchaseVoucherAccount::create([
                        'purchase_voucher_id' => $voucher->id,
                        'account_type' => $account['account_type'] ?? null,
                        'account_id' => $account['account_id'] ?? null,
                        'account_code' => $account['account_code'] ?? null,
                        'account_name' => $account['account_name'] ?? null,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher saved successfully',
                'voucher_id' => $voucher->id,
                'voucher_no' => $voucher->voucher_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Voucher Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $voucher = PurchaseVoucher::with(['items', 'accounts', 'supplier'])->findOrFail($id);
        return view('admin.voucher-purchase.show', compact('voucher'));
    }

    public function modification()
    {
        $suppliers = Supplier::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        
        // Get HSN codes and ensure numeric values for GST percentages
        $hsnCodes = HsnCode::orderBy('hsn_code')->get()->map(function($hsn) {
            return [
                'id' => $hsn->id,
                'hsn_code' => $hsn->hsn_code,
                'name' => $hsn->name,
                'cgst_percent' => floatval($hsn->cgst_percent ?? 0),
                'sgst_percent' => floatval($hsn->sgst_percent ?? 0),
                'igst_percent' => floatval($hsn->igst_percent ?? 0),
                'total_gst_percent' => floatval($hsn->total_gst_percent ?? 0),
            ];
        });
        
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $purchaseLedgers = PurchaseLedger::orderBy('ledger_name')->get();

        return view('admin.voucher-purchase.modification', compact(
            'suppliers', 'hsnCodes', 'generalLedgers', 'cashBankBooks', 'purchaseLedgers'
        ));
    }

    public function getByVoucherNo($voucherNo)
    {
        $voucher = PurchaseVoucher::with(['items', 'accounts'])->where('voucher_no', $voucherNo)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }

    public function update(Request $request, $id)
    {
        $voucher = PurchaseVoucher::findOrFail($id);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $voucher->update([
                'voucher_date' => $data['voucher_date'],
                'bill_no' => $data['bill_no'] ?? null,
                'bill_date' => $data['bill_date'] ?? null,
                'local_inter' => $data['local_inter'] ?? 'L',
                'rcm' => $data['rcm'] ?? 'N',
                'description' => $data['description'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'supplier_code' => $data['supplier_code'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'gst_no' => $data['gst_no'] ?? null,
                'pan_no' => $data['pan_no'] ?? null,
                'city' => $data['city'] ?? null,
                'pin' => $data['pin'] ?? null,
                'amount' => $data['amount'] ?? 0,
                'total_gst' => $data['total_gst'] ?? 0,
                'net_amount' => $data['net_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'total_debit' => $data['total_debit'] ?? 0,
                'tds_percent' => $data['tds_percent'] ?? 0,
                'tds_amount' => $data['tds_amount'] ?? 0,
                'payment_type' => $data['payment_type'] ?? '1',
                'credit_account_id' => $data['credit_account_id'] ?? null,
                'credit_account_type' => $data['credit_account_type'] ?? null,
                'credit_account_name' => $data['credit_account_name'] ?? null,
                'cheque_no' => $data['cheque_no'] ?? null,
                'total_credit' => $data['total_credit'] ?? 0,
                'total_cgst_amount' => $data['total_cgst_amount'] ?? 0,
                'total_sgst_amount' => $data['total_sgst_amount'] ?? 0,
                'total_igst_amount' => $data['total_igst_amount'] ?? 0,
            ]);

            // Delete and recreate items
            $voucher->items()->delete();
            if (!empty($data['items'])) {
                $sortOrder = 0;
                foreach ($data['items'] as $item) {
                    if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                    $sortOrder++;
                    PurchaseVoucherItem::create([
                        'purchase_voucher_id' => $voucher->id,
                        'hsn_code' => $item['hsn_code'] ?? null,
                        'amount' => $item['amount'] ?? 0,
                        'gst_percent' => $item['gst_percent'] ?? 0,
                        'cgst_percent' => $item['cgst_percent'] ?? 0,
                        'cgst_amount' => $item['cgst_amount'] ?? 0,
                        'sgst_percent' => $item['sgst_percent'] ?? 0,
                        'sgst_amount' => $item['sgst_amount'] ?? 0,
                        'igst_percent' => $item['igst_percent'] ?? 0,
                        'igst_amount' => $item['igst_amount'] ?? 0,
                        'total_amount' => $item['total_amount'] ?? 0,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            // Delete and recreate accounts
            $voucher->accounts()->delete();
            if (!empty($data['accounts'])) {
                $sortOrder = 0;
                foreach ($data['accounts'] as $account) {
                    if (empty($account['account_name'])) continue;
                    $sortOrder++;
                    PurchaseVoucherAccount::create([
                        'purchase_voucher_id' => $voucher->id,
                        'account_type' => $account['account_type'] ?? null,
                        'account_id' => $account['account_id'] ?? null,
                        'account_code' => $account['account_code'] ?? null,
                        'account_name' => $account['account_name'] ?? null,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher updated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Voucher Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $voucher = PurchaseVoucher::findOrFail($id);
            $voucherNo = $voucher->voucher_no;
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => "Voucher #{$voucherNo} deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reverse($id)
    {
        try {
            $voucher = PurchaseVoucher::findOrFail($id);
            $voucher->update(['status' => 'reversed']);

            return response()->json([
                'success' => true,
                'message' => "Voucher #{$voucher->voucher_no} reversed successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNextVoucherNo()
    {
        $nextVoucherNo = PurchaseVoucher::getNextVoucherNo();
        return response()->json([
            'success' => true,
            'voucher_no' => $nextVoucherNo
        ]);
    }

    public function getSupplierDetails($supplierId)
    {
        $supplier = Supplier::find($supplierId);
        
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'supplier' => [
                'id' => $supplier->id,
                'supplier_id' => $supplier->supplier_id,
                'name' => $supplier->name,
                'gst_no' => $supplier->gst_no,
                'pan_no' => $supplier->pan_no,
                'city' => $supplier->city,
                'pin' => $supplier->pin,
            ]
        ]);
    }

    public function getHsnDetails($hsnCode)
    {
        $hsn = HsnCode::where('hsn_code', $hsnCode)->first();
        
        if (!$hsn) {
            return response()->json([
                'success' => false,
                'message' => 'HSN Code not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'hsn' => [
                'hsn_code' => $hsn->hsn_code,
                'gst_percent' => $hsn->gst_percent ?? 0,
                'cgst_percent' => ($hsn->gst_percent ?? 0) / 2,
                'sgst_percent' => ($hsn->gst_percent ?? 0) / 2,
            ]
        ]);
    }

    public function getVouchers(Request $request)
    {
        try {
            $query = PurchaseVoucher::query();

            if ($request->filled('from_date')) {
                $query->whereDate('voucher_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('voucher_date', '<=', $request->to_date);
            }

            $vouchers = $query->orderByDesc('voucher_no')->limit(100)->get();

            $formattedVouchers = $vouchers->map(function ($voucher) {
                return [
                    'id' => $voucher->id,
                    'voucher_no' => $voucher->voucher_no,
                    'voucher_date' => $voucher->voucher_date ? $voucher->voucher_date->format('Y-m-d') : null,
                    'bill_no' => $voucher->bill_no,
                    'supplier_name' => $voucher->supplier_name,
                    'net_amount' => floatval($voucher->net_amount ?? 0),
                ];
            });

            return response()->json([
                'success' => true,
                'vouchers' => $formattedVouchers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading vouchers: ' . $e->getMessage(),
                'vouchers' => []
            ], 500);
        }
    }
}
