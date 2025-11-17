<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseLedger;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class PurchaseLedgerController extends Controller
{
    use CrudNotificationTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = PurchaseLedger::query();
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $searchField = request('search_field', 'all');
            
            if ($searchField === 'all') {
                $query->where(function ($q) use ($search) {
                    $q->where('ledger_name', 'like', "%{$search}%")
                      ->orWhere('alter_code', 'like', "%{$search}%")
                      ->orWhere('contact_1', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile_1', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }
        
        $ledgers = $query->orderByDesc('id')->paginate(10);
        
        if (request()->ajax()) {
            return view('admin.purchase-ledger.index', compact('ledgers'));
        }
        
        return view('admin.purchase-ledger.index', compact('ledgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.purchase-ledger.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Ledger Information
            'ledger_name' => 'nullable|string|max:255',
            'form_type' => 'nullable|string|max:50',
            'sale_tax' => 'nullable|numeric',
            'desc' => 'nullable|string',
            'type' => 'nullable|in:L,C',
            'status' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'form_required' => 'nullable|in:Y,N',
            'charges' => 'nullable|numeric',
            'under' => 'nullable|string|max:255',
            // Contact Information
            'address' => 'nullable|string',
            'birth_day' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:50',
            'contact_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:50',
        ]);

        try {
            $purchaseLedger = PurchaseLedger::create($validated);
            $this->notifyCreated($purchaseLedger->ledger_name ?? 'Purchase Ledger Entry');
            return redirect()->route('admin.purchase-ledger.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating purchase ledger entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseLedger $purchaseLedger)
    {
        return view('admin.purchase-ledger.show', compact('purchaseLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseLedger $purchaseLedger)
    {
        return view('admin.purchase-ledger.edit', compact('purchaseLedger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseLedger $purchaseLedger)
    {
        $validated = $request->validate([
            // Ledger Information
            'ledger_name' => 'nullable|string|max:255',
            'form_type' => 'nullable|string|max:50',
            'sale_tax' => 'nullable|numeric',
            'desc' => 'nullable|string',
            'type' => 'nullable|in:L,C',
            'status' => 'nullable|string|max:50',
            'alter_code' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'form_required' => 'nullable|in:Y,N',
            'charges' => 'nullable|numeric',
            'under' => 'nullable|string|max:255',
            // Contact Information
            'address' => 'nullable|string',
            'birth_day' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_1' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:50',
            'contact_2' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:50',
        ]);

        try {
            $purchaseLedger->update($validated);
            $this->notifyUpdated($purchaseLedger->ledger_name ?? 'Purchase Ledger Entry');
            return redirect()->route('admin.purchase-ledger.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating purchase ledger entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseLedger $purchaseLedger)
    {
        try {
            $purchaseLedgerName = $purchaseLedger->ledger_name ?? 'Purchase Ledger Entry';
            $purchaseLedger->delete();
            $this->notifyDeleted($purchaseLedgerName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting purchase ledger entry: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Delete multiple purchase ledger entries
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'purchase_ledger_ids' => $request->input('purchase_ledger_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'purchase_ledger_ids' => 'required|array|min:1',
            'purchase_ledger_ids.*' => 'required|integer|exists:purchase_ledgers,id'
        ]);

        try {
            $ledgerIds = $request->purchase_ledger_ids;
            $ledgers = PurchaseLedger::whereIn('id', $ledgerIds)->get();
            
            if ($ledgers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No purchase ledger entries found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $ledgerNames = [];

            foreach ($ledgers as $ledger) {
                $ledgerNames[] = $ledger->ledger_name ?? 'Purchase Ledger Entry';
                $ledger->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1 
                ? "Purchase ledger entry '{$ledgerNames[0]}' deleted successfully."
                : "{$deletedCount} purchase ledger entries deleted successfully.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple purchase ledger deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purchase ledger entries. Please try again.'
            ], 500);
        }
    }
}
