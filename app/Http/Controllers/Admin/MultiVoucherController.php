<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MultiVoucher;
use App\Models\MultiVoucherEntry;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\GeneralLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = MultiVoucher::with('entries');
        if ($request->filled('search')) {
            $query->where('voucher_no', 'like', "%{$request->search}%")
                  ->orWhere('narration', 'like', "%{$request->search}%");
        }
        if ($request->filled('from_date')) $query->whereDate('voucher_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('voucher_date', '<=', $request->to_date);
        $vouchers = $query->orderByDesc('voucher_date')->orderByDesc('voucher_no')->paginate(15);
        return view('admin.multi-voucher.index', compact('vouchers'));
    }

    public function transaction()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $suppliers = Supplier::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $nextVoucherNo = MultiVoucher::getNextVoucherNo();
        return view('admin.multi-voucher.transaction', compact('customers', 'suppliers', 'generalLedgers', 'nextVoucherNo'));
    }

    public function store(Request $request)
    {
        $request->validate(['voucher_date' => 'required|date']);
        try {
            DB::beginTransaction();
            $data = $request->all();
            $voucher = MultiVoucher::create([
                'voucher_date' => $data['voucher_date'],
                'voucher_no' => MultiVoucher::getNextVoucherNo(),
                'narration' => $data['narration'] ?? null,
                'total_amount' => $data['total_amount'] ?? 0,
                'status' => 'active',
            ]);
            if (!empty($data['entries'])) {
                $sortOrder = 0;
                foreach ($data['entries'] as $entry) {
                    if (empty($entry['amount']) || $entry['amount'] <= 0) continue;
                    $sortOrder++;
                    MultiVoucherEntry::create([
                        'multi_voucher_id' => $voucher->id,
                        'entry_date' => $entry['entry_date'] ?? $data['voucher_date'],
                        'debit_account_type' => $entry['debit_account_type'] ?? null,
                        'debit_account_id' => $entry['debit_account_id'] ?? null,
                        'debit_account_name' => $entry['debit_account_name'] ?? null,
                        'credit_account_type' => $entry['credit_account_type'] ?? null,
                        'credit_account_id' => $entry['credit_account_id'] ?? null,
                        'credit_account_name' => $entry['credit_account_name'] ?? null,
                        'amount' => $entry['amount'] ?? 0,
                        'dr_slcd' => $entry['dr_slcd'] ?? null,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher saved', 'voucher_no' => $voucher->voucher_no]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $voucher = MultiVoucher::with('entries')->findOrFail($id);
        return view('admin.multi-voucher.show', compact('voucher'));
    }

    public function modification()
    {
        $customers = Customer::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $suppliers = Supplier::where(function($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', 0)->orWhere('is_deleted', '!=', 1);
        })->orderBy('name')->get();
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        return view('admin.multi-voucher.modification', compact('customers', 'suppliers', 'generalLedgers'));
    }

    public function getByVoucherNo($voucherNo)
    {
        $voucher = MultiVoucher::with('entries')->where('voucher_no', $voucherNo)->first();
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'voucher' => $voucher]);
    }

    public function update(Request $request, $id)
    {
        $voucher = MultiVoucher::findOrFail($id);
        try {
            DB::beginTransaction();
            $data = $request->all();
            $voucher->update([
                'voucher_date' => $data['voucher_date'],
                'narration' => $data['narration'] ?? null,
                'total_amount' => $data['total_amount'] ?? 0,
            ]);
            $voucher->entries()->delete();
            if (!empty($data['entries'])) {
                $sortOrder = 0;
                foreach ($data['entries'] as $entry) {
                    if (empty($entry['amount']) || $entry['amount'] <= 0) continue;
                    MultiVoucherEntry::create([
                        'multi_voucher_id' => $voucher->id,
                        'entry_date' => $entry['entry_date'] ?? $data['voucher_date'],
                        'debit_account_type' => $entry['debit_account_type'] ?? null,
                        'debit_account_id' => $entry['debit_account_id'] ?? null,
                        'debit_account_name' => $entry['debit_account_name'] ?? null,
                        'credit_account_type' => $entry['credit_account_type'] ?? null,
                        'credit_account_id' => $entry['credit_account_id'] ?? null,
                        'credit_account_name' => $entry['credit_account_name'] ?? null,
                        'amount' => $entry['amount'] ?? 0,
                        'dr_slcd' => $entry['dr_slcd'] ?? null,
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $voucher = MultiVoucher::findOrFail($id);
        $voucherNo = $voucher->voucher_no;
        $voucher->delete();
        return response()->json(['success' => true, 'message' => "Voucher #{$voucherNo} deleted"]);
    }
}
