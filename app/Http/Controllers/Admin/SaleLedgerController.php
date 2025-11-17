<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleLedger;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class SaleLedgerController extends Controller
{
    use CrudNotificationTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $searchField = request('search_field', 'all');
        
        $ledgers = SaleLedger::query()
            ->when($search && trim($search) !== '', function ($query) use ($search, $searchField) {
                $search = trim($search);
                
                if ($searchField === 'all') {
                    // Search across all fields
                    $query->where(function ($q) use ($search) {
                        $q->where('ledger_name', 'like', "%{$search}%")
                            ->orWhere('alter_code', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('under', 'like', "%{$search}%")
                            ->orWhere('contact_1', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    });
                } else {
                    // Search in specific field
                    $validFields = ['ledger_name', 'alter_code', 'type', 'status', 'under', 'contact_1', 'email', 'telephone'];
                    if (in_array($searchField, $validFields)) {
                        $query->where($searchField, 'like', "%{$search}%");
                    }
                }
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
        
        if (request()->ajax()) {
            return view('admin.sale-ledger.index', compact('ledgers', 'search', 'searchField'));
        }
        
        return view('admin.sale-ledger.index', compact('ledgers', 'search', 'searchField'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sale-ledger.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ledger_name' => 'required|string|max:255',
            'form_type' => 'nullable|string|max:50',
            'sale_tax' => 'nullable|numeric',
            'desc' => 'nullable|string',
            'type' => 'required|in:L,C',
            'status' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'form_required' => 'nullable|in:Y,N',
            'charges' => 'nullable|numeric',
            'under' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'birth_day' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:20',
            'contact_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:20',
        ]);

        try {
            $saleLedger = SaleLedger::create($validated);
            $this->notifyCreated($saleLedger->ledger_name ?? 'Sale Ledger Entry');
            return redirect()->route('admin.sale-ledger.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating sale ledger entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleLedger $saleLedger)
    {
        return view('admin.sale-ledger.show', compact('saleLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleLedger $saleLedger)
    {
        return view('admin.sale-ledger.edit', compact('saleLedger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleLedger $saleLedger)
    {
        $validated = $request->validate([
            'ledger_name' => 'required|string|max:255',
            'form_type' => 'nullable|string|max:50',
            'sale_tax' => 'nullable|numeric',
            'desc' => 'nullable|string',
            'type' => 'required|in:L,C',
            'status' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'form_required' => 'nullable|in:Y,N',
            'charges' => 'nullable|numeric',
            'under' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'birth_day' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:20',
            'contact_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:20',
        ]);

        try {
            $saleLedger->update($validated);
            $this->notifyUpdated($saleLedger->ledger_name ?? 'Sale Ledger Entry');
            return redirect()->route('admin.sale-ledger.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating sale ledger entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleLedger $saleLedger)
    {
        try {
            $saleLedgerName = $saleLedger->ledger_name ?? 'Sale Ledger Entry';
            $saleLedger->delete();
            $this->notifyDeleted($saleLedgerName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting sale ledger entry: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Delete multiple sale ledger entries
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'sale_ledger_ids' => $request->input('sale_ledger_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'sale_ledger_ids' => 'required|array|min:1',
            'sale_ledger_ids.*' => 'required|integer|exists:sale_ledgers,id'
        ]);

        try {
            $ledgerIds = $request->sale_ledger_ids;
            $ledgers = SaleLedger::whereIn('id', $ledgerIds)->get();
            
            if ($ledgers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sale ledger entries found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $ledgerNames = [];

            foreach ($ledgers as $ledger) {
                $ledgerNames[] = $ledger->ledger_name ?? 'Sale Ledger Entry';
                $ledger->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1 
                ? "Sale ledger entry '{$ledgerNames[0]}' deleted successfully."
                : "{$deletedCount} sale ledger entries deleted successfully.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple sale ledger deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale ledger entries. Please try again.'
            ], 500);
        }
    }
}
