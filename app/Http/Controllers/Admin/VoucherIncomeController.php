<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeVoucher;
use App\Models\IncomeVoucherItem;
use App\Models\IncomeVoucherAccount;
use App\Models\Customer;
use App\Models\HsnCode;
use App\Models\GeneralLedger;
use App\Models\CashBankBook;
use App\Models\SaleLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherIncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomeVoucher::with(['customer', 'items']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('from_date')) $query->whereDate('voucher_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('voucher_date', '<=', $request->to_date);
        $vouchers = $query->orderByDesc('voucher_date')->orderByDesc('voucher_no')->paginate(15);
        return view('admin.voucher-income.index', compact('vouchers'));
    }

    public function transaction()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        
        $hsnCodes = HsnCode::orderBy('hsn_code')->get()->map(function($hsn) {
            return [
                'id' => $hsn->id, 'hsn_code' => $hsn->hsn_code, 'name' => $hsn->name,
                'cgst_percent' => floatval($hsn->cgst_percent ?? 0),
                'sgst_percent' => floatval($hsn->sgst_percent ?? 0),
                'igst_percent' => floatval($hsn->igst_percent ?? 0),
                'total_gst_percent' => floatval($hsn->total_gst_percent ?? 0),
            ];
        });
        
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $SaleLedgers = SaleLedger::orderBy('ledger_name')->get();
        $nextVoucherNo = IncomeVoucher::getNextVoucherNo();

        return view('admin.voucher-income.transaction', compact(
            'customers', 'hsnCodes', 'generalLedgers', 'cashBankBooks', 'SaleLedgers', 'nextVoucherNo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate(['voucher_date' => 'required|date']);
        try {
            DB::beginTransaction();
            $data = $request->all();
            $voucher = IncomeVoucher::create([
                'voucher_date' => $data['voucher_date'],
                'voucher_no' => IncomeVoucher::getNextVoucherNo(),
                'local_inter' => $data['local_inter'] ?? 'L',
                'description' => $data['description'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'gst_no' => $data['gst_no'] ?? null,
                'pan_no' => $data['pan_no'] ?? null,
                'city' => $data['city'] ?? null,
                'pin' => $data['pin'] ?? null,
                'address' => $data['address'] ?? null,
                'amount' => $data['amount'] ?? 0,
                'total_gst' => $data['total_gst'] ?? 0,
                'net_amount' => $data['net_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'total_credit' => $data['total_credit'] ?? 0,
                'tds_percent' => $data['tds_percent'] ?? 0,
                'tds_amount' => $data['tds_amount'] ?? 0,
                'debit_account_id' => $data['debit_account_id'] ?? null,
                'debit_account_type' => $data['debit_account_type'] ?? null,
                'debit_account_name' => $data['debit_account_name'] ?? null,
                'total_debit' => $data['total_debit'] ?? 0,
                'total_cgst_amount' => $data['total_cgst_amount'] ?? 0,
                'total_sgst_amount' => $data['total_sgst_amount'] ?? 0,
                'total_igst_amount' => $data['total_igst_amount'] ?? 0,
                'status' => 'active',
            ]);

            if (!empty($data['items'])) {
                $sortOrder = 0;
                foreach ($data['items'] as $item) {
                    if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                    $sortOrder++;
                    IncomeVoucherItem::create([
                        'income_voucher_id' => $voucher->id,
                        'hsn_code' => $item['hsn_code'] ?? null,
                        'amount' => $item['amount'] ?? 0,
                        'gst_percent' => $item['gst_percent'] ?? 0,
                        'cgst_percent' => $item['cgst_percent'] ?? 0,
                        'cgst_amount' => $item['cgst_amount'] ?? 0,
                        'sgst_percent' => $item['sgst_percent'] ?? 0,
                        'sgst_amount' => $item['sgst_amount'] ?? 0,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }
            if (!empty($data['accounts'])) {
                $sortOrder = 0;
                foreach ($data['accounts'] as $account) {
                    if (empty($account['account_name'])) continue;
                    $sortOrder++;
                    IncomeVoucherAccount::create([
                        'income_voucher_id' => $voucher->id,
                        'account_type' => $account['account_type'] ?? null,
                        'account_id' => $account['account_id'] ?? null,
                        'account_code' => $account['account_code'] ?? null,
                        'account_name' => $account['account_name'] ?? null,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher saved', 'voucher_id' => $voucher->id, 'voucher_no' => $voucher->voucher_no]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Income Voucher Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $voucher = IncomeVoucher::with(['items', 'accounts', 'customer'])->findOrFail($id);
        return view('admin.voucher-income.show', compact('voucher'));
    }

    public function modification()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get()->map(function($hsn) {
            return ['id' => $hsn->id, 'hsn_code' => $hsn->hsn_code, 'name' => $hsn->name,
                'cgst_percent' => floatval($hsn->cgst_percent ?? 0), 'sgst_percent' => floatval($hsn->sgst_percent ?? 0),
                'total_gst_percent' => floatval($hsn->total_gst_percent ?? 0)];
        });
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $SaleLedgers = SaleLedger::orderBy('ledger_name')->get();
        return view('admin.voucher-income.modification', compact('customers', 'hsnCodes', 'generalLedgers', 'cashBankBooks', 'SaleLedgers'));
    }

    public function getByVoucherNo($voucherNo)
    {
        $voucher = IncomeVoucher::with(['items', 'accounts'])->where('voucher_no', $voucherNo)->first();
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        return response()->json(['success' => true, 'voucher' => $voucher]);
    }

    public function update(Request $request, $id)
    {
        $voucher = IncomeVoucher::findOrFail($id);
        try {
            DB::beginTransaction();
            $data = $request->all();
            $voucher->update([
                'voucher_date' => $data['voucher_date'], 'local_inter' => $data['local_inter'] ?? 'L',
                'description' => $data['description'] ?? null, 'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null, 'gst_no' => $data['gst_no'] ?? null,
                'pan_no' => $data['pan_no'] ?? null, 'city' => $data['city'] ?? null, 'pin' => $data['pin'] ?? null,
                'address' => $data['address'] ?? null, 'amount' => $data['amount'] ?? 0,
                'total_gst' => $data['total_gst'] ?? 0, 'net_amount' => $data['net_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0, 'total_credit' => $data['total_credit'] ?? 0,
                'tds_percent' => $data['tds_percent'] ?? 0, 'tds_amount' => $data['tds_amount'] ?? 0,
                'debit_account_id' => $data['debit_account_id'] ?? null, 'debit_account_type' => $data['debit_account_type'] ?? null,
                'debit_account_name' => $data['debit_account_name'] ?? null, 'total_debit' => $data['total_debit'] ?? 0,
                'total_cgst_amount' => $data['total_cgst_amount'] ?? 0, 'total_sgst_amount' => $data['total_sgst_amount'] ?? 0,
            ]);
            $voucher->items()->delete();
            if (!empty($data['items'])) {
                $sortOrder = 0;
                foreach ($data['items'] as $item) {
                    if (empty($item['hsn_code']) && empty($item['amount'])) continue;
                    IncomeVoucherItem::create(['income_voucher_id' => $voucher->id, 'hsn_code' => $item['hsn_code'] ?? null,
                        'amount' => $item['amount'] ?? 0, 'gst_percent' => $item['gst_percent'] ?? 0,
                        'cgst_percent' => $item['cgst_percent'] ?? 0, 'cgst_amount' => $item['cgst_amount'] ?? 0,
                        'sgst_percent' => $item['sgst_percent'] ?? 0, 'sgst_amount' => $item['sgst_amount'] ?? 0, 'sort_order' => ++$sortOrder]);
                }
            }
            $voucher->accounts()->delete();
            if (!empty($data['accounts'])) {
                $sortOrder = 0;
                foreach ($data['accounts'] as $account) {
                    if (empty($account['account_name'])) continue;
                    IncomeVoucherAccount::create(['income_voucher_id' => $voucher->id, 'account_type' => $account['account_type'] ?? null,
                        'account_id' => $account['account_id'] ?? null, 'account_code' => $account['account_code'] ?? null,
                        'account_name' => $account['account_name'] ?? null, 'sort_order' => ++$sortOrder]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $voucher = IncomeVoucher::findOrFail($id);
        $voucherNo = $voucher->voucher_no;
        $voucher->delete();
        return response()->json(['success' => true, 'message' => "Voucher #{$voucherNo} deleted"]);
    }

    public function reverse($id)
    {
        $voucher = IncomeVoucher::findOrFail($id);
        $voucher->update(['status' => 'reversed']);
        return response()->json(['success' => true, 'message' => "Voucher #{$voucher->voucher_no} reversed"]);
    }
}
