<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\DebitNoteAdjustment;
use App\Models\PurchaseTransaction;
use App\Models\CreditNote;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\SalesMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DebitNoteController extends Controller
{
    /**
     * Display debit note transaction form
     */
    public function transaction()
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::all();
        $nextDebitNoteNo = $this->generateDebitNoteNo();
        
        return view('admin.debit-note.transaction', compact('suppliers', 'customers', 'salesmen', 'nextDebitNoteNo'));
    }

    /**
     * Display debit note modification form
     */
    public function modification(Request $request, $debit_note_no = null)
    {
        $suppliers = Supplier::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $salesmen = SalesMan::all();
        
        // Support both route param and query param ?debit_note_no=
        $preloadDebitNoteNo = $debit_note_no ?: $request->query('debit_note_no');
        
        return view('admin.debit-note.modification', compact('suppliers', 'customers', 'salesmen', 'preloadDebitNoteNo'));
    }

    /**
     * Display debit note invoices listing page
     */
    public function invoices(Request $request)
    {
        $query = DebitNote::query();

        // Apply search filter
        if ($request->filled('search')) {
            $filterBy = $request->get('filter_by', 'party_name');
            $searchTerm = $request->get('search');

            switch ($filterBy) {
                case 'party_name':
                    $query->where('debit_party_name', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'debit_note_no':
                    $query->where('debit_note_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'inv_ref_no':
                    $query->where('inv_ref_no', 'LIKE', '%' . $searchTerm . '%');
                    break;
                    
                case 'amount':
                    if (is_numeric($searchTerm)) {
                        $query->where('dn_amount', '>=', $searchTerm);
                    }
                    break;
            }
        }

        // Apply party type filter
        if ($request->filled('party_type')) {
            $query->where('debit_party_type', $request->get('party_type'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('debit_note_date', [
                $request->get('date_from'),
                $request->get('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('debit_note_date', '>=', $request->get('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('debit_note_date', '<=', $request->get('date_to'));
        }

        // Order by latest first
        $query->orderBy('debit_note_date', 'desc')->orderBy('id', 'desc');

        // Return JSON for AJAX requests with all=1
        if ($request->has('all') && $request->ajax()) {
            $debitNotes = $query->get();
            return response()->json($debitNotes);
        }

        // Get paginated results
        $debitNotes = $query->paginate(10)->withQueryString();

        return view('admin.debit-note.invoices', compact('debitNotes'));
    }

    /**
     * Store a newly created debit note
     */
    public function store(Request $request)
    {
        $request->validate([
            'header.debit_note_date' => 'required|date',
            'header.debit_party_type' => 'required|in:S,C',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');

            // Generate debit note number
            $debitNoteNo = $this->generateDebitNoteNo();

            // Create debit note
            $debitNote = DebitNote::create([
                'debit_note_no' => $debitNoteNo,
                'debit_note_date' => $headerData['debit_note_date'],
                'day_name' => $headerData['day_name'] ?? null,
                'debit_party_type' => $headerData['debit_party_type'],
                'debit_party_id' => $headerData['debit_party_id'] ?? null,
                'debit_party_name' => $headerData['debit_party_name'] ?? null,
                'credit_account_type' => $headerData['credit_account_type'] ?? 'P',
                'credit_account_no' => $headerData['credit_account_no'] ?? null,
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
                'dn_amount' => $headerData['dn_amount'] ?? 0,
                'narration' => $headerData['narration'] ?? null,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Create items
            foreach ($itemsData as $index => $itemData) {
                DebitNoteItem::create([
                    'debit_note_id' => $debitNote->id,
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
            if ($request->input('with_adjustment') && $request->has('adjustments')) {
                $adjustments = $request->input('adjustments');
                foreach ($adjustments as $adj) {
                    DebitNoteAdjustment::create([
                        'debit_note_id' => $debitNote->id,
                        'adjustment_type' => $adj['invoice_type'] ?? 'PURCHASE',
                        'purchase_transaction_id' => $adj['invoice_type'] === 'PURCHASE' ? $adj['invoice_id'] : null,
                        'credit_note_id' => $adj['invoice_type'] === 'CREDIT_NOTE' ? $adj['invoice_id'] : null,
                        'adjusted_amount' => $adj['adjusted_amount'] ?? 0,
                        'adjustment_date' => now(),
                        'remarks' => $adj['remarks'] ?? null,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Debit note saved successfully',
                'debit_note_no' => $debitNote->debit_note_no,
                'id' => $debitNote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Debit Note Save Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified debit note
     */
    public function update(Request $request, $id)
    {
        $debitNote = DebitNote::findOrFail($id);

        $request->validate([
            'header.debit_note_date' => 'required|date',
            'header.debit_party_type' => 'required|in:S,C',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $headerData = $request->input('header');
            $itemsData = $request->input('items');

            // Update debit note
            $debitNote->update([
                'debit_note_date' => $headerData['debit_note_date'],
                'day_name' => $headerData['day_name'] ?? null,
                'debit_party_type' => $headerData['debit_party_type'],
                'debit_party_id' => $headerData['debit_party_id'] ?? null,
                'debit_party_name' => $headerData['debit_party_name'] ?? null,
                'credit_account_type' => $headerData['credit_account_type'] ?? 'P',
                'credit_account_no' => $headerData['credit_account_no'] ?? null,
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
                'dn_amount' => $headerData['dn_amount'] ?? 0,
                'narration' => $headerData['narration'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Delete old items
            $debitNote->items()->delete();

            // Create new items
            foreach ($itemsData as $index => $itemData) {
                DebitNoteItem::create([
                    'debit_note_id' => $debitNote->id,
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
                'message' => 'Debit note updated successfully',
                'debit_note_no' => $debitNote->debit_note_no,
                'id' => $debitNote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Debit Note Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified debit note
     */
    public function destroy($id)
    {
        try {
            $debitNote = DebitNote::findOrFail($id);
            $debitNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Debit note deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Debit Note Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show debit note details
     */
    public function show($id)
    {
        $debitNote = DebitNote::with(['items', 'salesman'])->findOrFail($id);
        return view('admin.debit-note.show', compact('debitNote'));
    }

    /**
     * Fetch debit note by number
     */
    public function fetchByNumber($debitNoteNo)
    {
        try {
            $debitNote = DebitNote::with(['items', 'salesman', 'supplier', 'customer'])
                ->where('debit_note_no', $debitNoteNo)
                ->first();

            if (!$debitNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debit note not found'
                ]);
            }

            return response()->json([
                'success' => true,
                'debit_note' => $debitNote
            ]);

        } catch (\Exception $e) {
            Log::error('Fetch Debit Note Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching debit note'
            ], 500);
        }
    }

    /**
     * Get next debit note number
     */
    public function getNextDebitNoteNo()
    {
        return response()->json([
            'success' => true,
            'debit_note_no' => $this->generateDebitNoteNo()
        ]);
    }

    /**
     * Generate debit note number
     */
    private function generateDebitNoteNo()
    {
        $lastDebitNote = DebitNote::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDebitNote) {
            $lastNumber = intval(preg_replace('/[^0-9]/', '', $lastDebitNote->debit_note_no));
            return $lastNumber + 1;
        }

        return 1;
    }

    /**
     * Get pending purchase invoices for a supplier (for adjustment)
     */
    public function getSupplierPurchaseInvoices($supplierId)
    {
        try {
            $invoices = PurchaseTransaction::where('supplier_id', $supplierId)
                ->where('inv_amount', '>', 0)
                ->orderBy('bill_date', 'desc')
                ->get()
                ->map(function ($invoice) {
                    // Calculate already adjusted amount from debit note adjustments
                    $adjustedAmount = DebitNoteAdjustment::where('purchase_transaction_id', $invoice->id)
                        ->sum('adjusted_amount');
                    
                    $balanceAmount = $invoice->inv_amount - $adjustedAmount;
                    
                    return [
                        'id' => $invoice->id,
                        'bill_no' => $invoice->bill_no,
                        'bill_date' => $invoice->bill_date ? $invoice->bill_date->format('Y-m-d') : null,
                        'bill_date_formatted' => $invoice->bill_date ? $invoice->bill_date->format('d-m-Y') : '-',
                        'trn_no' => $invoice->trn_no,
                        'inv_amount' => $invoice->inv_amount,
                        'adjusted_amount' => $adjustedAmount,
                        'balance_amount' => $balanceAmount,
                    ];
                })
                ->filter(function ($invoice) {
                    return $invoice['balance_amount'] > 0;
                })
                ->values();

            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            Log::error('Get Supplier Purchase Invoices Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoices'
            ], 500);
        }
    }

    /**
     * Get pending credit notes for a supplier (for adjustment)
     */
    public function getSupplierCreditNotes($supplierId)
    {
        try {
            $creditNotes = CreditNote::where('credit_party_type', 'S')
                ->where('credit_party_id', $supplierId)
                ->where('cn_amount', '>', 0)
                ->orderBy('credit_note_date', 'desc')
                ->get()
                ->map(function ($cn) {
                    // Calculate already adjusted amount from debit note adjustments
                    $adjustedAmount = DebitNoteAdjustment::where('credit_note_id', $cn->id)
                        ->sum('adjusted_amount');
                    
                    $balanceAmount = $cn->cn_amount - $adjustedAmount;
                    
                    return [
                        'id' => $cn->id,
                        'credit_note_no' => $cn->credit_note_no,
                        'credit_note_date' => $cn->credit_note_date ? $cn->credit_note_date->format('Y-m-d') : null,
                        'credit_note_date_formatted' => $cn->credit_note_date ? $cn->credit_note_date->format('d-m-Y') : '-',
                        'cn_amount' => $cn->cn_amount,
                        'adjusted_amount' => $adjustedAmount,
                        'balance_amount' => $balanceAmount,
                    ];
                })
                ->filter(function ($cn) {
                    return $cn['balance_amount'] > 0;
                })
                ->values();

            return response()->json([
                'success' => true,
                'credit_notes' => $creditNotes
            ]);

        } catch (\Exception $e) {
            Log::error('Get Supplier Credit Notes Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching credit notes'
            ], 500);
        }
    }

    /**
     * Get past adjustments for a debit note
     */
    public function getAdjustments($debitNoteId)
    {
        try {
            $debitNote = DebitNote::with('adjustments')->findOrFail($debitNoteId);
            
            $adjustments = $debitNote->adjustments->map(function ($adj) use ($debitNoteId) {
                // Get current invoice data and calculate balance
                $balance = 0;
                $billAmount = 0;
                $transNo = '';
                $date = '-';
                
                if ($adj->adjustment_type === 'PURCHASE' && $adj->purchase_transaction_id) {
                    $purchase = PurchaseTransaction::find($adj->purchase_transaction_id);
                    if ($purchase) {
                        $billAmount = $purchase->inv_amount;
                        $transNo = $purchase->bill_no;
                        $date = $purchase->bill_date ? $purchase->bill_date->format('d-m-Y') : '-';
                        
                        // Calculate total adjusted by OTHER debit notes (not current one)
                        $otherAdjusted = DebitNoteAdjustment::where('purchase_transaction_id', $purchase->id)
                            ->where('debit_note_id', '!=', $debitNoteId)
                            ->sum('adjusted_amount');
                        $balance = $purchase->inv_amount - $otherAdjusted;
                    }
                } elseif ($adj->credit_note_id) {
                    $creditNote = CreditNote::find($adj->credit_note_id);
                    if ($creditNote) {
                        $billAmount = $creditNote->cn_amount;
                        $transNo = $creditNote->credit_note_no;
                        $date = $creditNote->credit_note_date ? $creditNote->credit_note_date->format('d-m-Y') : '-';
                        
                        // Calculate total adjusted by OTHER debit notes (not current one)
                        $otherAdjusted = DebitNoteAdjustment::where('credit_note_id', $creditNote->id)
                            ->where('debit_note_id', '!=', $debitNoteId)
                            ->sum('adjusted_amount');
                        $balance = $creditNote->cn_amount - $otherAdjusted;
                    }
                }
                
                return [
                    'id' => $adj->id,
                    'adjustment_type' => $adj->adjustment_type,
                    'purchase_transaction_id' => $adj->purchase_transaction_id,
                    'credit_note_id' => $adj->credit_note_id,
                    'invoice_id' => $adj->adjustment_type === 'PURCHASE' ? $adj->purchase_transaction_id : $adj->credit_note_id,
                    'trans_no' => $transNo,
                    'invoice_no' => $transNo,
                    'date' => $date,
                    'invoice_date' => $date,
                    'bill_amount' => $billAmount,
                    'invoice_amount' => $billAmount,
                    'balance' => $balance,
                    'adjusted_amount' => $adj->adjusted_amount,
                    'remarks' => $adj->remarks,
                ];
            });

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments,
                'total_adjusted' => $debitNote->total_adjusted,
                'remaining_balance' => $debitNote->remaining_balance,
                'dn_amount' => $debitNote->dn_amount
            ]);

        } catch (\Exception $e) {
            Log::error('Get Adjustments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching adjustments'
            ], 500);
        }
    }

    /**
     * Save adjustment for a debit note
     */
    public function saveAdjustment(Request $request)
    {
        $request->validate([
            'debit_note_id' => 'required|exists:debit_notes,id',
            'adjustments' => 'required|array|min:1',
            'adjustments.*.adjustment_type' => 'required|in:PURCHASE,CREDIT_NOTE',
            'adjustments.*.adjusted_amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $debitNote = DebitNote::findOrFail($request->debit_note_id);
            $adjustmentsData = $request->adjustments;
            
            $totalNewAdjustment = array_sum(array_column($adjustmentsData, 'adjusted_amount'));
            $currentAdjusted = $debitNote->total_adjusted;
            
            // Check if total adjustment exceeds DN amount
            if (($currentAdjusted + $totalNewAdjustment) > $debitNote->dn_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total adjustment amount exceeds debit note amount'
                ], 400);
            }

            foreach ($adjustmentsData as $adjData) {
                $adjustment = new DebitNoteAdjustment();
                $adjustment->debit_note_id = $debitNote->id;
                $adjustment->adjustment_type = $adjData['adjustment_type'];
                $adjustment->adjusted_amount = $adjData['adjusted_amount'];
                $adjustment->remarks = $adjData['remarks'] ?? null;

                if ($adjData['adjustment_type'] === 'PURCHASE') {
                    $purchase = PurchaseTransaction::find($adjData['purchase_transaction_id']);
                    if ($purchase) {
                        $adjustment->purchase_transaction_id = $purchase->id;
                        $adjustment->purchase_invoice_no = $purchase->bill_no;
                        $adjustment->purchase_invoice_date = $purchase->bill_date;
                        $adjustment->purchase_invoice_amount = $purchase->inv_amount;
                        
                        // Calculate current balance
                        $alreadyAdjusted = DebitNoteAdjustment::where('purchase_transaction_id', $purchase->id)
                            ->sum('adjusted_amount');
                        $adjustment->purchase_balance_amount = $purchase->inv_amount - $alreadyAdjusted;
                    }
                } else {
                    $creditNote = CreditNote::find($adjData['credit_note_id']);
                    if ($creditNote) {
                        $adjustment->credit_note_id = $creditNote->id;
                        $adjustment->credit_note_no = $creditNote->credit_note_no;
                        $adjustment->credit_note_date = $creditNote->credit_note_date;
                        $adjustment->credit_note_amount = $creditNote->cn_amount;
                        
                        // Calculate current balance
                        $alreadyAdjusted = DebitNoteAdjustment::where('credit_note_id', $creditNote->id)
                            ->sum('adjusted_amount');
                        $adjustment->credit_note_balance = $creditNote->cn_amount - $alreadyAdjusted;
                    }
                }

                $adjustment->save();
            }

            // Update debit note status if fully adjusted
            $debitNote->refresh();
            if ($debitNote->remaining_balance <= 0) {
                $debitNote->status = 'approved';
                $debitNote->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Adjustment saved successfully',
                'total_adjusted' => $debitNote->total_adjusted,
                'remaining_balance' => $debitNote->remaining_balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Adjustment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save adjustments for a debit note (like credit note modification)
     */
    public function saveAdjustments(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $debitNote = DebitNote::findOrFail($id);
            $adjustmentsData = $request->input('adjustments', []);
            
            // Delete existing adjustments
            DebitNoteAdjustment::where('debit_note_id', $id)->delete();
            
            // Save new adjustments
            foreach ($adjustmentsData as $adjData) {
                $adjustedAmount = floatval($adjData['adjusted_amount'] ?? 0);
                if ($adjustedAmount <= 0) continue;
                
                $invoiceType = $adjData['invoice_type'] ?? 'PURCHASE';
                $invoiceId = $adjData['invoice_id'] ?? null;
                
                $adjustment = new DebitNoteAdjustment();
                $adjustment->debit_note_id = $id;
                $adjustment->adjustment_type = $invoiceType;
                $adjustment->adjusted_amount = $adjustedAmount;

                if ($invoiceType === 'PURCHASE') {
                    $purchase = PurchaseTransaction::find($invoiceId);
                    if ($purchase) {
                        $adjustment->purchase_transaction_id = $purchase->id;
                        $adjustment->purchase_invoice_no = $purchase->bill_no;
                        $adjustment->purchase_invoice_date = $purchase->bill_date;
                        $adjustment->purchase_invoice_amount = $purchase->inv_amount;
                        
                        // Calculate balance
                        $alreadyAdjusted = DebitNoteAdjustment::where('purchase_transaction_id', $purchase->id)
                            ->where('debit_note_id', '!=', $id)
                            ->sum('adjusted_amount');
                        $adjustment->purchase_balance_amount = $purchase->inv_amount - $alreadyAdjusted - $adjustedAmount;
                    }
                } else {
                    $creditNote = CreditNote::find($invoiceId);
                    if ($creditNote) {
                        $adjustment->credit_note_id = $creditNote->id;
                        $adjustment->credit_note_no = $creditNote->credit_note_no;
                        $adjustment->credit_note_date = $creditNote->credit_note_date;
                        $adjustment->credit_note_amount = $creditNote->cn_amount;
                        
                        // Calculate balance
                        $alreadyAdjusted = DebitNoteAdjustment::where('credit_note_id', $creditNote->id)
                            ->where('debit_note_id', '!=', $id)
                            ->sum('adjusted_amount');
                        $adjustment->credit_note_balance = $creditNote->cn_amount - $alreadyAdjusted - $adjustedAmount;
                    }
                }

                $adjustment->save();
            }

            // Update debit note status
            $debitNote->refresh();
            if ($debitNote->remaining_balance <= 0) {
                $debitNote->status = 'approved';
            } else {
                $debitNote->status = 'pending';
            }
            $debitNote->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Adjustments saved successfully',
                'total_adjusted' => $debitNote->total_adjusted,
                'remaining_balance' => $debitNote->remaining_balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Adjustments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an adjustment
     */
    public function deleteAdjustment($adjustmentId)
    {
        try {
            $adjustment = DebitNoteAdjustment::findOrFail($adjustmentId);
            $debitNote = $adjustment->debitNote;
            
            $adjustment->delete();
            
            // Update debit note status if needed
            $debitNote->refresh();
            if ($debitNote->remaining_balance > 0 && $debitNote->status === 'approved') {
                $debitNote->status = 'pending';
                $debitNote->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Adjustment deleted successfully',
                'total_adjusted' => $debitNote->total_adjusted,
                'remaining_balance' => $debitNote->remaining_balance
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Adjustment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get past debit note adjustments for a supplier (to show history)
     */
    public function getSupplierPastAdjustments($supplierId)
    {
        try {
            $adjustments = DebitNoteAdjustment::whereHas('debitNote', function ($query) use ($supplierId) {
                $query->where('debit_party_type', 'S')
                      ->where('debit_party_id', $supplierId);
            })
            ->with(['debitNote:id,debit_note_no,debit_note_date,dn_amount'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($adj) {
                return [
                    'id' => $adj->id,
                    'debit_note_no' => $adj->debitNote->debit_note_no,
                    'debit_note_date' => $adj->debitNote->debit_note_date->format('d-m-Y'),
                    'dn_amount' => $adj->debitNote->dn_amount,
                    'adjustment_type' => $adj->adjustment_type,
                    'invoice_no' => $adj->adjustment_type === 'PURCHASE' ? $adj->purchase_invoice_no : $adj->credit_note_no,
                    'invoice_date' => $adj->adjustment_type === 'PURCHASE' 
                        ? ($adj->purchase_invoice_date ? $adj->purchase_invoice_date->format('d-m-Y') : '-')
                        : ($adj->credit_note_date ? $adj->credit_note_date->format('d-m-Y') : '-'),
                    'adjusted_amount' => $adj->adjusted_amount,
                    'created_at' => $adj->created_at->format('d-m-Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'adjustments' => $adjustments
            ]);

        } catch (\Exception $e) {
            Log::error('Get Supplier Past Adjustments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching past adjustments'
            ], 500);
        }
    }
}
