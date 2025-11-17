<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $searchField = request('search_field', 'all');
        
        $ledgers = GeneralLedger::query()
            ->when($search && trim($search) !== '', function ($query) use ($search, $searchField) {
                $search = trim($search);
                
                if ($searchField === 'all') {
                    // Search across all fields
                    $query->where(function ($q) use ($search) {
                        $q->where('account_name', 'like', "%{$search}%")
                            ->orWhere('under', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile_1', 'like', "%{$search}%");
                    });
                } else {
                    // Search in specific field
                    $validFields = ['name', 'under', 'telephone', 'email', 'mobile_1'];
                    if (in_array($searchField, $validFields)) {
                        $fieldMap = [
                            'name' => 'account_name',
                            'mobile_1' => 'mobile_1'
                        ];
                        $dbField = $fieldMap[$searchField] ?? $searchField;
                        $query->where($dbField, 'like', "%{$search}%");
                    }
                }
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
        
        if (request()->ajax()) {
            return view('admin.general-ledger.index', compact('ledgers', 'search', 'searchField'));
        }
        
        return view('admin.general-ledger.index', compact('ledgers', 'search', 'searchField'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.general-ledger.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'under' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'balance_type' => 'nullable|string|in:D,C',
            'input_gst_purchase' => 'nullable|boolean',
            'output_gst_income' => 'nullable|boolean',
            'flag' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'address_line2' => 'nullable|string|max:255',
            'address_line3' => 'nullable|string|max:255',
            'birth_day' => 'nullable|string|max:10',
            'anniversary_day' => 'nullable|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:20',
            'contact_person_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:20',
            'contact_person_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:20',
        ]);

        $generalLedger = GeneralLedger::create($validated);
        $this->notifyCreated($generalLedger->account_name);
        return redirect()->route('admin.general-ledger.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralLedger $generalLedger)
    {
        return view('admin.general-ledger.show', compact('generalLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralLedger $generalLedger)
    {
        return view('admin.general-ledger.edit', compact('generalLedger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneralLedger $generalLedger)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'under' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'balance_type' => 'nullable|string|in:D,C',
            'input_gst_purchase' => 'nullable|boolean',
            'output_gst_income' => 'nullable|boolean',
            'flag' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'address_line2' => 'nullable|string|max:255',
            'address_line3' => 'nullable|string|max:255',
            'birth_day' => 'nullable|string|max:10',
            'anniversary_day' => 'nullable|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:20',
            'contact_person_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:20',
            'contact_person_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:20',
        ]);

        $generalLedger->update($validated);
        $this->notifyUpdated($generalLedger->account_name);
        return redirect()->route('admin.general-ledger.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralLedger $generalLedger)
    {
        $accountName = $generalLedger->account_name;
        $generalLedger->delete();
        $this->notifyDeleted($accountName);
        return redirect()->route('admin.general-ledger.index');
    }

    /**
     * Delete multiple general ledger accounts
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'general_ledger_ids' => $request->input('general_ledger_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'general_ledger_ids' => 'required|array|min:1',
            'general_ledger_ids.*' => 'required|integer|exists:general_ledgers,id'
        ]);

        try {
            $ids = $request->general_ledger_ids;
            $records = GeneralLedger::whereIn('id', $ids)->get();

            if ($records->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No general ledger accounts found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($records as $rec) {
                $rec->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 general ledger account deleted successfully.'
                : $deletedCount . ' general ledger accounts deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple general ledger deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete general ledger accounts. Please try again.'
            ], 500);
        }
    }
}
