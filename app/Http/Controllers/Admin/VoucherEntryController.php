<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Models\GeneralLedger;
use App\Models\CashBankBook;
use App\Models\SaleLedger;
use App\Models\PurchaseLedger;
use App\Models\Customer;
use App\Models\Supplier;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherEntryController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display a listing of vouchers.
     */
    public function index(Request $request)
    {
        $query = Voucher::with('items');

        // Filter by voucher type
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }

        $vouchers = $query->orderByDesc('voucher_date')->orderByDesc('voucher_no')->paginate(15);
        
        return view('admin.voucher-entry.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new voucher (Transaction page).
     */
    public function transaction(Request $request)
    {
        $voucherType = $request->get('type', 'receipt');
        
        // Get all account types for selection
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        // Get next voucher number
        $nextVoucherNo = Voucher::getNextVoucherNo($voucherType);

        return view('admin.voucher-entry.transaction', compact(
            'voucherType', 'generalLedgers', 'cashBankBooks', 
            'customers', 'suppliers', 'nextVoucherNo'
        ));
    }

    /**
     * Store a newly created voucher.
     */
    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'voucher_entry', 'voucher_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'voucher_type' => 'required|in:receipt,payment,contra,journal',
            'multi_narration' => 'nullable|boolean',
            'narration' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Get day name
            $dayName = date('l', strtotime($validated['voucher_date']));

            // Get next voucher number for this type
            $nextVoucherNo = Voucher::getNextVoucherNo($validated['voucher_type']);

            // Calculate totals
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($request->items as $item) {
                $totalDebit += floatval($item['debit_amount'] ?? 0);
                $totalCredit += floatval($item['credit_amount'] ?? 0);
            }

            $voucher = Voucher::create([
                'voucher_no' => $nextVoucherNo,
                'voucher_date' => $validated['voucher_date'],
                'day_name' => $dayName,
                'voucher_type' => $validated['voucher_type'],
                'multi_narration' => $validated['multi_narration'] ?? false,
                'narration' => $validated['narration'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'active',
            ]);

            // Create voucher items
            $sortOrder = 0;
            foreach ($request->items as $item) {
                if (empty($item['account_name']) && empty($item['debit_amount']) && empty($item['credit_amount'])) {
                    continue;
                }
                
                $sortOrder++;
                VoucherItem::create([
                    'voucher_id' => $voucher->id,
                    'account_type' => $item['account_type'] ?? null,
                    'account_id' => $item['account_id'] ?? null,
                    'account_code' => $item['account_code'] ?? null,
                    'account_name' => $item['account_name'] ?? null,
                    'debit_amount' => $item['debit_amount'] ?? 0,
                    'credit_amount' => $item['credit_amount'] ?? 0,
                    'item_narration' => $item['item_narration'] ?? null,
                    'sort_order' => $sortOrder,
                ]);
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
            \Log::error('Voucher Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified voucher.
     */
    public function show($id)
    {
        $voucher = Voucher::with('items')->findOrFail($id);
        return view('admin.voucher-entry.show', compact('voucher'));
    }

    /**
     * Show the modification page.
     */
    public function modification(Request $request)
    {
        $voucherType = $request->get('type', 'receipt');
        
        // Get all account types for selection
        $generalLedgers = GeneralLedger::orderBy('account_name')->get();
        $cashBankBooks = CashBankBook::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        // Get next voucher number
        $nextVoucherNo = Voucher::getNextVoucherNo($voucherType);

        return view('admin.voucher-entry.modification', compact(
            'voucherType', 'generalLedgers', 'cashBankBooks', 
            'customers', 'suppliers', 'nextVoucherNo'
        ));
    }

    /**
     * Get voucher by number.
     */
    public function getByVoucherNo($voucherNo, Request $request)
    {
        $voucherType = $request->get('type');
        
        $query = Voucher::with('items')->where('voucher_no', $voucherNo);
        if ($voucherType) {
            $query->where('voucher_type', $voucherType);
        }
        
        $voucher = $query->first();

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

    /**
     * Get list of vouchers for selection.
     */
    public function getVouchers(Request $request)
    {
        try {
            $query = Voucher::query();

            if ($request->filled('voucher_type')) {
                $query->where('voucher_type', $request->voucher_type);
            }

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
                    'voucher_type' => $voucher->voucher_type,
                    'voucher_type_label' => $voucher->voucher_type_label,
                    'total_debit' => floatval($voucher->total_debit ?? 0),
                    'total_credit' => floatval($voucher->total_credit ?? 0),
                    'narration' => $voucher->narration,
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

    /**
     * Update the specified voucher.
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'voucher_type' => 'required|in:receipt,payment,contra,journal',
            'multi_narration' => 'nullable|boolean',
            'narration' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Get day name
            $dayName = date('l', strtotime($validated['voucher_date']));

            // Calculate totals
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($request->items as $item) {
                $totalDebit += floatval($item['debit_amount'] ?? 0);
                $totalCredit += floatval($item['credit_amount'] ?? 0);
            }

            $voucher->update([
                'voucher_date' => $validated['voucher_date'],
                'day_name' => $dayName,
                'voucher_type' => $validated['voucher_type'],
                'multi_narration' => $validated['multi_narration'] ?? false,
                'narration' => $validated['narration'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // Delete existing items
            $voucher->items()->delete();

            // Create new voucher items
            $sortOrder = 0;
            foreach ($request->items as $item) {
                if (empty($item['account_name']) && empty($item['debit_amount']) && empty($item['credit_amount'])) {
                    continue;
                }
                
                $sortOrder++;
                VoucherItem::create([
                    'voucher_id' => $voucher->id,
                    'account_type' => $item['account_type'] ?? null,
                    'account_id' => $item['account_id'] ?? null,
                    'account_code' => $item['account_code'] ?? null,
                    'account_name' => $item['account_name'] ?? null,
                    'debit_amount' => $item['debit_amount'] ?? 0,
                    'credit_amount' => $item['credit_amount'] ?? 0,
                    'item_narration' => $item['item_narration'] ?? null,
                    'sort_order' => $sortOrder,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher updated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Voucher Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
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

    /**
     * Cancel a voucher.
     */
    public function cancel($id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
            $voucher->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => "Voucher #{$voucher->voucher_no} cancelled successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next voucher number.
     */
    public function getNextVoucherNo(Request $request)
    {
        $type = $request->get('type', 'receipt');
        $nextVoucherNo = Voucher::getNextVoucherNo($type);

        return response()->json([
            'success' => true,
            'voucher_no' => $nextVoucherNo
        ]);
    }

    /**
     * Get all accounts for selection (combined list).
     */
    public function getAccounts(Request $request)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', '');
        
        $accounts = [];
        
        // General Ledger
        if (!$type || $type === 'GL') {
            $glQuery = GeneralLedger::query();
            if ($search) {
                $glQuery->where(function($q) use ($search) {
                    $q->where('account_name', 'like', "%{$search}%")
                      ->orWhere('account_code', 'like', "%{$search}%");
                });
            }
            foreach ($glQuery->limit(50)->get() as $gl) {
                $accounts[] = [
                    'type' => 'GL',
                    'id' => $gl->id,
                    'code' => $gl->account_code ?? $gl->alter_code,
                    'name' => $gl->account_name,
                    'label' => 'General Ledger',
                ];
            }
        }
        
        // Cash Bank Book
        if (!$type || $type === 'CB') {
            $cbQuery = CashBankBook::query();
            if ($search) {
                $cbQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('alter_code', 'like', "%{$search}%");
                });
            }
            foreach ($cbQuery->limit(50)->get() as $cb) {
                $accounts[] = [
                    'type' => 'CB',
                    'id' => $cb->id,
                    'code' => $cb->alter_code,
                    'name' => $cb->name,
                    'label' => 'Cash/Bank',
                ];
            }
        }
        
        // Customers
        if (!$type || $type === 'CL') {
            $clQuery = Customer::query();
            if ($search) {
                $clQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            foreach ($clQuery->limit(50)->get() as $cl) {
                $accounts[] = [
                    'type' => 'CL',
                    'id' => $cl->id,
                    'code' => $cl->code,
                    'name' => $cl->name,
                    'label' => 'Customer',
                ];
            }
        }
        
        // Suppliers
        if (!$type || $type === 'SU') {
            $suQuery = Supplier::query();
            if ($search) {
                $suQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            foreach ($suQuery->limit(50)->get() as $su) {
                $accounts[] = [
                    'type' => 'SU',
                    'id' => $su->id,
                    'code' => $su->code,
                    'name' => $su->name,
                    'label' => 'Supplier',
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'accounts' => $accounts
        ]);
    }
}
