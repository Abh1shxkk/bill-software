<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\SalesMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreditNoteController extends Controller
{
    /**
     * Display credit note transaction form
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::all();
        $nextCreditNoteNo = $this->generateCreditNoteNo();
        
        return view('admin.credit-note.transaction', compact('suppliers', 'customers', 'salesmen', 'nextCreditNoteNo'));
    }

    /**
     * Display credit note modification form
     */
    public function modification($credit_note_no = null)
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::all();
        
        $preloadCreditNoteNo = $credit_note_no;
        
        return view('admin.credit-note.modification', compact('suppliers', 'customers', 'salesmen', 'preloadCreditNoteNo'));
    }

    /**
     * Display credit note invoices listing page
     */
    public function invoices(Request $request)
    {
        $query = CreditNote::query();

        // Apply search filter
        if ($request->filled('search')) {
            $filterBy = $request->get('filter_by', 'party_name');
            $searchTerm = $request->get('search');

            switch ($filterBy) {
                case 'party_name':
                    $query->where('credit_party_name', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'credit_note_no':
                    $query->where('credit_note_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'inv_ref_no':
                    $query->where('inv_ref_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'amount':
                    if (is_numeric($searchTerm)) {
                        $query->where('cn_amount', '>=', $searchTerm);
                    }
                    break;
            }
        }

        // Apply party type filter
        if ($request->filled('party_type')) {
            $query->where('credit_party_type', $request->get('party_type'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('credit_note_date', [
                $request->get('date_from'),
                $request->get('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('credit_note_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('credit_note_date', '<=', $request->get('date_to'));
        }

        // Order by latest first
        $query->orderBy('credit_note_date', 'desc')->orderBy('id', 'desc');

        // Return JSON for AJAX requests with all=1
        if ($request->has('all') && $request->ajax()) {
            $creditNotes = $query->get();
            return response()->json($creditNotes);
        }

        // Get paginated results
        $creditNotes = $query->paginate(10)->withQueryString();

        return view('admin.credit-note.invoices', compact('creditNotes'));
    }

    /**
     * Store a newly created credit note
     */
    public function store(Request $request)
    {
        $request->validate([
            'header.credit_note_date' => 'required|date',
            'header.credit_party_type' => 'required|in:S,C',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');

            // Generate credit note number
            $creditNoteNo = $this->generateCreditNoteNo();

            // Create credit note
            $creditNote = CreditNote::create([
                'credit_note_no' => $creditNoteNo,
                'credit_note_date' => $headerData['credit_note_date'],
                'day_name' => $headerData['day_name'] ?? null,
                'credit_party_type' => $headerData['credit_party_type'],
                'credit_party_id' => $headerData['credit_party_id'] ?? null,
                'credit_party_name' => $headerData['credit_party_name'] ?? null,
                'debit_account_type' => $headerData['debit_account_type'] ?? 'P',
                'debit_account_no' => $headerData['debit_account_no'] ?? null,
                'inv_ref_no' => $headerData['inv_ref_no'] ?? null,
                'invoice_date' => $headerData['invoice_date'] ?? null,
                'gst_vno' => $headerData['gst_vno'] ?? null,
                'party_trn_no' => $headerData['party_trn_no'] ?? null,
                'party_trn_date' => $headerData['party_trn_date'] ?? null,
                'amount' => $headerData['amount'] ?? 0,
                'salesman_id' => $headerData['salesman_id'] ?? null,
                'reason' => $headerData['reason'] ?? null,
                'gross_amount' => $headerData['gross_amount'] ?? 0,
                'total_gst' => $headerData['total_gst'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'round_off' => $headerData['round_off'] ?? 0,
                'cn_amount' => $headerData['cn_amount'] ?? 0,
                'narration' => $headerData['narration'] ?? null,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Create items
            foreach ($itemsData as $index => $itemData) {
                CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'hsn_code' => $itemData['hsn_code'] ?? null,
                    'amount' => $itemData['amount'] ?? 0,
                    'gst_percent' => $itemData['gst_percent'] ?? 0,
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'igst_percent' => $itemData['igst_percent'] ?? 0,
                    'igst_amount' => $itemData['igst_amount'] ?? 0,
                    'row_order' => $index + 1,
                ]);
            }

            // Handle adjustments if provided
            $withAdjustment = $request->input('with_adjustment', false);
            $adjustments = $request->input('adjustments', []);
            
            if ($withAdjustment && !empty($adjustments)) {
                $partyType = $headerData['credit_party_type'];
                
                foreach ($adjustments as $adjustment) {
                    $invoiceId = $adjustment['invoice_id'];
                    $adjustedAmount = floatval($adjustment['adjusted_amount']);
                    
                    if ($partyType === 'C') {
                        // Update balance_amount in sale_transactions
                        $transaction = \App\Models\SaleTransaction::find($invoiceId);
                        if ($transaction) {
                            $currentBalance = $transaction->balance_amount ?? $transaction->net_amount ?? 0;
                            $transaction->balance_amount = $currentBalance - $adjustedAmount;
                            $transaction->save();
                        }
                    } else {
                        // Update balance_amount in purchase_transactions
                        $transaction = \App\Models\PurchaseTransaction::find($invoiceId);
                        if ($transaction) {
                            $currentBalance = $transaction->balance_amount ?? $transaction->net_amount ?? 0;
                            $transaction->balance_amount = $currentBalance - $adjustedAmount;
                            $transaction->save();
                        }
                    }
                    
                    // Store adjustment record
                    DB::table('credit_note_adjustments')->insert([
                        'credit_note_id' => $creditNote->id,
                        'invoice_id' => $invoiceId,
                        'invoice_type' => $partyType === 'C' ? 'SALE' : 'PURCHASE',
                        'adjusted_amount' => $adjustedAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Update credit note status
                $creditNote->update(['status' => 'adjusted']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit note saved successfully' . ($withAdjustment ? ' with adjustments' : ''),
                'credit_note_no' => $creditNote->credit_note_no,
                'id' => $creditNote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit Note Save Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified credit note
     */
    public function update(Request $request, $id)
    {
        $creditNote = CreditNote::findOrFail($id);

        $request->validate([
            'header.credit_note_date' => 'required|date',
            'header.credit_party_type' => 'required|in:S,C',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');

            // Update credit note
            $creditNote->update([
                'credit_note_date' => $headerData['credit_note_date'],
                'day_name' => $headerData['day_name'] ?? null,
                'credit_party_type' => $headerData['credit_party_type'],
                'credit_party_id' => $headerData['credit_party_id'] ?? null,
                'credit_party_name' => $headerData['credit_party_name'] ?? null,
                'debit_account_type' => $headerData['debit_account_type'] ?? 'P',
                'debit_account_no' => $headerData['debit_account_no'] ?? null,
                'inv_ref_no' => $headerData['inv_ref_no'] ?? null,
                'invoice_date' => $headerData['invoice_date'] ?? null,
                'gst_vno' => $headerData['gst_vno'] ?? null,
                'party_trn_no' => $headerData['party_trn_no'] ?? null,
                'party_trn_date' => $headerData['party_trn_date'] ?? null,
                'amount' => $headerData['amount'] ?? 0,
                'salesman_id' => $headerData['salesman_id'] ?? null,
                'reason' => $headerData['reason'] ?? null,
                'gross_amount' => $headerData['gross_amount'] ?? 0,
                'total_gst' => $headerData['total_gst'] ?? 0,
                'net_amount' => $headerData['net_amount'] ?? 0,
                'tcs_amount' => $headerData['tcs_amount'] ?? 0,
                'round_off' => $headerData['round_off'] ?? 0,
                'cn_amount' => $headerData['cn_amount'] ?? 0,
                'narration' => $headerData['narration'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $creditNote->items()->delete();

            // Create new items
            foreach ($itemsData as $index => $itemData) {
                CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'hsn_code' => $itemData['hsn_code'] ?? null,
                    'amount' => $itemData['amount'] ?? 0,
                    'gst_percent' => $itemData['gst_percent'] ?? 0,
                    'cgst_percent' => $itemData['cgst_percent'] ?? 0,
                    'cgst_amount' => $itemData['cgst_amount'] ?? 0,
                    'sgst_percent' => $itemData['sgst_percent'] ?? 0,
                    'sgst_amount' => $itemData['sgst_amount'] ?? 0,
                    'igst_percent' => $itemData['igst_percent'] ?? 0,
                    'igst_amount' => $itemData['igst_amount'] ?? 0,
                    'row_order' => $index + 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit note updated successfully',
                'credit_note_no' => $creditNote->credit_note_no,
                'id' => $creditNote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit Note Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified credit note
     */
    public function destroy($id)
    {
        try {
            $creditNote = CreditNote::findOrFail($id);
            $creditNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Credit note deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Credit Note Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show credit note details
     */
    public function show($id)
    {
        $creditNote = CreditNote::with(['items', 'salesman'])->findOrFail($id);
        return view('admin.credit-note.show', compact('creditNote'));
    }

    /**
     * Fetch credit note by number
     */
    public function fetchByNumber($creditNoteNo)
    {
        try {
            $creditNote = CreditNote::with('items')
                ->where('credit_note_no', $creditNoteNo)
                ->first();

            if (!$creditNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credit note not found'
                ]);
            }

            return response()->json([
                'success' => true,
                'credit_note' => $creditNote
            ]);

        } catch (\Exception $e) {
            Log::error('Fetch Credit Note Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching credit note'
            ], 500);
        }
    }

    /**
     * Get next credit note number
     */
    public function getNextCreditNoteNo()
    {
        return response()->json([
            'success' => true,
            'credit_note_no' => $this->generateCreditNoteNo()
        ]);
    }

    /**
     * Get party invoices for adjustment
     */
    public function getPartyInvoices(Request $request)
    {
        try {
            $partyId = $request->party_id;
            $partyType = $request->party_type; // C = Customer, S = Supplier
            
            $invoices = [];
            
            if ($partyType === 'C') {
                // Get customer's sale invoices with outstanding balance
                $transactions = \App\Models\SaleTransaction::where('customer_id', $partyId)
                    ->whereNotNull('invoice_no')
                    ->orderBy('sale_date', 'desc')
                    ->get();
                
                $invoices = $transactions->map(function ($transaction) {
                    $originalBillAmount = (float) ($transaction->net_amount ?? 0);
                    $currentBalance = (float) ($transaction->balance_amount ?? $transaction->net_amount ?? 0);
                    
                    return [
                        'id' => $transaction->id,
                        'trans_no' => $transaction->invoice_no ?? '',
                        'date' => $transaction->sale_date ? $transaction->sale_date->format('d-m-Y') : '',
                        'bill_amount' => $originalBillAmount,
                        'balance' => $currentBalance,
                    ];
                })->filter(function ($invoice) {
                    return $invoice['balance'] > 0;
                })->values();
                
            } else {
                // Get supplier's purchase invoices with outstanding balance
                $transactions = \App\Models\PurchaseTransaction::where('supplier_id', $partyId)
                    ->whereNotNull('bill_no')
                    ->orderBy('bill_date', 'desc')
                    ->get();
                
                $invoices = $transactions->map(function ($transaction) {
                    $originalBillAmount = (float) ($transaction->inv_amount ?? $transaction->net_amount ?? 0);
                    $currentBalance = (float) ($transaction->balance_amount ?? $originalBillAmount);
                    
                    return [
                        'id' => $transaction->id,
                        'trans_no' => $transaction->bill_no ?? '',
                        'date' => $transaction->bill_date ? $transaction->bill_date->format('d-m-Y') : '',
                        'bill_amount' => $originalBillAmount,
                        'balance' => $currentBalance,
                    ];
                })->filter(function ($invoice) {
                    return $invoice['balance'] > 0;
                })->values();
            }
            
            if ($invoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices with outstanding balance found.'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching party invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing adjustments for a credit note
     */
    public function getAdjustments($id)
    {
        try {
            $creditNote = CreditNote::findOrFail($id);
            
            $adjustments = DB::table('credit_note_adjustments')
                ->where('credit_note_id', $id)
                ->get();
            
            // Get invoice details for each adjustment
            $adjustmentsWithDetails = $adjustments->map(function ($adj) use ($creditNote) {
                $invoice = null;
                
                if ($adj->invoice_type === 'SALE') {
                    $invoice = \App\Models\SaleTransaction::find($adj->invoice_id);
                    if ($invoice) {
                        return [
                            'invoice_id' => $adj->invoice_id,
                            'trans_no' => $invoice->invoice_no ?? '',
                            'date' => $invoice->sale_date ? $invoice->sale_date->format('d-m-Y') : '',
                            'bill_amount' => (float) ($invoice->net_amount ?? 0),
                            'balance' => (float) ($invoice->balance_amount ?? 0),
                            'adjusted_amount' => (float) $adj->adjusted_amount,
                        ];
                    }
                } else {
                    $invoice = \App\Models\PurchaseTransaction::find($adj->invoice_id);
                    if ($invoice) {
                        return [
                            'invoice_id' => $adj->invoice_id,
                            'trans_no' => $invoice->bill_no ?? '',
                            'date' => $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '',
                            'bill_amount' => (float) ($invoice->net_amount ?? 0),
                            'balance' => (float) ($invoice->balance_amount ?? 0),
                            'adjusted_amount' => (float) $adj->adjusted_amount,
                        ];
                    }
                }
                
                return null;
            })->filter()->values();
            
            return response()->json([
                'success' => true,
                'adjustments' => $adjustmentsWithDetails
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching adjustments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load adjustments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save adjustments for a credit note
     */
    public function saveAdjustments(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $creditNote = CreditNote::findOrFail($id);
            $adjustments = $request->input('adjustments', []);
            
            // Get existing adjustments to reverse them
            $existingAdjustments = DB::table('credit_note_adjustments')
                ->where('credit_note_id', $id)
                ->get();
            
            // Reverse existing adjustments
            foreach ($existingAdjustments as $adj) {
                if ($adj->invoice_type === 'SALE') {
                    $transaction = \App\Models\SaleTransaction::find($adj->invoice_id);
                    if ($transaction) {
                        $transaction->balance_amount = ($transaction->balance_amount ?? 0) + $adj->adjusted_amount;
                        $transaction->save();
                    }
                } else {
                    $transaction = \App\Models\PurchaseTransaction::find($adj->invoice_id);
                    if ($transaction) {
                        $transaction->balance_amount = ($transaction->balance_amount ?? 0) + $adj->adjusted_amount;
                        $transaction->save();
                    }
                }
            }
            
            // Delete existing adjustments
            DB::table('credit_note_adjustments')->where('credit_note_id', $id)->delete();
            
            // Apply new adjustments
            foreach ($adjustments as $adjustment) {
                $invoiceId = $adjustment['invoice_id'];
                $adjustedAmount = floatval($adjustment['adjusted_amount']);
                
                if ($adjustedAmount <= 0) continue;
                
                if ($creditNote->credit_party_type === 'C') {
                    $transaction = \App\Models\SaleTransaction::find($invoiceId);
                    if ($transaction) {
                        $currentBalance = $transaction->balance_amount ?? $transaction->net_amount ?? 0;
                        $transaction->balance_amount = $currentBalance - $adjustedAmount;
                        $transaction->save();
                    }
                    $invoiceType = 'SALE';
                } else {
                    $transaction = \App\Models\PurchaseTransaction::find($invoiceId);
                    if ($transaction) {
                        $currentBalance = $transaction->balance_amount ?? $transaction->net_amount ?? 0;
                        $transaction->balance_amount = $currentBalance - $adjustedAmount;
                        $transaction->save();
                    }
                    $invoiceType = 'PURCHASE';
                }
                
                // Store adjustment record
                DB::table('credit_note_adjustments')->insert([
                    'credit_note_id' => $id,
                    'invoice_id' => $invoiceId,
                    'invoice_type' => $invoiceType,
                    'adjusted_amount' => $adjustedAmount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Update credit note status
            if (count($adjustments) > 0) {
                $creditNote->update(['status' => 'adjusted']);
            } else {
                $creditNote->update(['status' => 'pending']);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Adjustments saved successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving adjustments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save adjustments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate credit note number
     */
    private function generateCreditNoteNo()
    {
        $lastCreditNote = CreditNote::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCreditNote) {
            $lastNumber = intval(preg_replace('/[^0-9]/', '', $lastCreditNote->credit_note_no));
            return $lastNumber + 1;
        }

        return 1;
    }
}
