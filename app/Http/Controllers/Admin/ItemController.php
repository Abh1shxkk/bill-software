<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Company;
use App\Models\StockLedger;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\PendingOrder;
use App\Models\GodownExpiry;
use App\Models\ExpiryLedger;
use App\Models\Batch;
use App\Models\PurchaseTransactionItem;
use App\Models\SaleTransactionItem;
use App\Models\SaleReturnTransactionItem;
use App\Models\StockAdjustmentItem;
use App\Models\PurchaseTransaction;
use App\Models\SaleTransaction;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use CrudNotificationTrait;
    public function index()
    {
        $search = request('search');
        $searchField = request('search_field', 'all');
        $status = request('status');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $items = Item::query()
            ->with('company') // Eager load company relationship
            ->addSelect([
                // Calculate total_units from batches table (actual available stock)
                'total_units' => Batch::selectRaw('COALESCE(SUM(qty), 0)')
                    ->whereColumn('item_id', 'items.id')
                    ->where('is_deleted', 0)
            ])
            ->when($search && trim($search) !== '', function ($query) use ($search, $searchField) {
                $search = trim($search);
                
                if ($searchField === 'all') {
                    // Search across all fields
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('bar_code', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhere('packing', 'like', "%{$search}%")
                            ->orWhere('mrp', 'like', "%{$search}%")
                            ->orWhere('hsn_code', 'like', "%{$search}%")
                            ->orWhere('mfg_by', 'like', "%{$search}%");
                    });
                } else {
                    // Search in specific field - ensure field name is valid
                    $validFields = ['name', 'bar_code', 'location', 'packing', 'mrp', 'code', 'hsn_code'];
                    if (in_array($searchField, $validFields)) {
                        $query->where($searchField, 'like', "%{$search}%");
                    }
                }
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status === 'active' ? 1 : 0);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // AJAX request - return same view
        if (request()->ajax()) {
            return view('admin.items.index', compact('items', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
        }

        return view('admin.items.index', compact('items', 'search', 'searchField', 'status', 'dateFrom', 'dateTo'));
    }
    public function create()
    {
        $companies = Company::where('is_deleted', '!=', 1)->get();
        return view('admin.items.create', compact('companies'));
    }

    public function store(Request $request)
    {
        try {
            // Validate required fields
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'company_id' => 'required|exists:companies,id',
            ]);

            // Get all data and merge validated fields
            $data = array_merge($request->all(), $validated);
            $data = $this->bools($request, $data);
            
            // Ensure required fields with defaults are set
            $data['is_deleted'] = 0;
            $data['unit'] = $data['unit'] ?? 1;
            $data['locks_flag'] = $data['locks_flag'] ?? 'N';
            
            // Set default values for char fields that might be empty
            $data['narcotic_flag'] = $data['narcotic_flag'] ?? 'N';
            $data['ws_net_toggle'] = $data['ws_net_toggle'] ?? 'Y';
            $data['spl_net_toggle'] = $data['spl_net_toggle'] ?? 'Y';
            $data['expiry_flag'] = $data['expiry_flag'] ?? 'N';
            $data['inclusive_flag'] = $data['inclusive_flag'] ?? 'N';
            $data['generic_flag'] = $data['generic_flag'] ?? 'N';
            $data['h_scm_flag'] = $data['h_scm_flag'] ?? 'N';
            $data['q_scm_flag'] = $data['q_scm_flag'] ?? 'N';
            $data['bar_code_flag'] = $data['bar_code_flag'] ?? 'N';
            $data['def_qty_flag'] = $data['def_qty_flag'] ?? 'N';
            $data['dpc_item_flag'] = $data['dpc_item_flag'] ?? 'N';
            $data['lock_sale_flag'] = $data['lock_sale_flag'] ?? 'N';
            $data['current_scheme_flag'] = $data['current_scheme_flag'] ?? 'N';
            $data['max_min_flag'] = $data['max_min_flag'] ?? '1';
            
            $item = Item::create($data);
            $this->notifyCreated($item->name);
            return redirect()->route('admin.items.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $this->notifyError('Error creating item: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function show(Request $request, Item $item)
    {
        // Get latest batch details from batches table (most recent purchase batch with rates)
        // Total quantity is calculated in view using $item->getTotalQuantity() which uses batches table
        $latestBatch = Batch::where('item_id', $item->id)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Fallback: If no batch found, get from purchase_transaction_items for rates
        if (!$latestBatch) {
            $latestBatch = \App\Models\PurchaseTransactionItem::where('item_id', $item->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'packing' => $item->packing,
                    'company_name' => $item->company_name,
                    'mrp' => $latestBatch ? $latestBatch->mrp : $item->mrp,
                    's_rate' => $latestBatch ? $latestBatch->s_rate : $item->s_rate,
                    'pur_rate' => $latestBatch ? $latestBatch->pur_rate : $item->pur_rate,
                    'ws_rate' => $latestBatch ? $latestBatch->ws_rate : $item->ws_rate,
                    'spl_rate' => $latestBatch ? $latestBatch->spl_rate : $item->spl_rate,
                ]
            ]);
        }
        
        return view('admin.items.show', compact('item', 'latestBatch'));
    }
    
    public function edit(Item $item)
    {
        $companies = Company::where('is_deleted', '!=', 1)->get();
        
        // Get latest batch details to pre-fill rates
        $latestBatch = \App\Models\PurchaseTransactionItem::where('item_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        return view('admin.items.edit', compact('item', 'companies', 'latestBatch'));
    }
    public function update(Request $request, Item $item)
    {
        try {
            // Validate required fields
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'company_id' => 'required|exists:companies,id',
            ]);

            // Get all data and merge validated fields
            $data = array_merge($request->all(), $validated);
            $data = $this->bools($request, $data);
            
            // Ensure required fields with defaults are set
            $data['is_deleted'] = 0;
            $data['unit'] = $data['unit'] ?? 1;
            $data['locks_flag'] = $data['locks_flag'] ?? 'N';
            
            // Set default values for char fields that might be empty
            $data['narcotic_flag'] = $data['narcotic_flag'] ?? 'N';
            $data['ws_net_toggle'] = $data['ws_net_toggle'] ?? 'Y';
            $data['spl_net_toggle'] = $data['spl_net_toggle'] ?? 'Y';
            $data['expiry_flag'] = $data['expiry_flag'] ?? 'N';
            $data['inclusive_flag'] = $data['inclusive_flag'] ?? 'N';
            $data['generic_flag'] = $data['generic_flag'] ?? 'N';
            $data['h_scm_flag'] = $data['h_scm_flag'] ?? 'N';
            $data['q_scm_flag'] = $data['q_scm_flag'] ?? 'N';
            $data['bar_code_flag'] = $data['bar_code_flag'] ?? 'N';
            $data['def_qty_flag'] = $data['def_qty_flag'] ?? 'N';
            $data['dpc_item_flag'] = $data['dpc_item_flag'] ?? 'N';
            $data['lock_sale_flag'] = $data['lock_sale_flag'] ?? 'N';
            $data['current_scheme_flag'] = $data['current_scheme_flag'] ?? 'N';
            $data['max_min_flag'] = $data['max_min_flag'] ?? '1';
            
            $item->update($data);
            $this->notifyUpdated($item->name);
            return redirect()->route('admin.items.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $this->notifyError('Error updating item: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function destroy(Item $item)
    {
        $itemName = $item->name;
        
        // Check if item has related records
        $hasRelatedRecords = $this->checkItemRelatedRecords($item);
        
        if ($hasRelatedRecords['has_relations']) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete item "' . $itemName . '". ' . $hasRelatedRecords['message']
                ], 400);
            }
            
            $this->notifyError('Cannot delete item "' . $itemName . '". ' . $hasRelatedRecords['message']);
            return back();
        }
        
        try {
            $item->delete();
            
            // For AJAX requests, return JSON success
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item "' . $itemName . '" deleted successfully.'
                ]);
            }
            
            $this->notifyDeleted($itemName);
            return back();
        } catch (\Exception $e) {
            // For AJAX requests, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete item "' . $itemName . '". It has related records in the database.'
                ], 400);
            }
            
            $this->notifyError('Cannot delete item "' . $itemName . '". It has related records in the database.');
            return back();
        }
    }

    /**
     * Delete multiple items
     */
    public function multipleDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'required|integer|exists:items,id'
            ]);

            $itemIds = $validated['item_ids'];
            $deletedCount = 0;
            $errors = [];
            $skippedItems = [];

            foreach ($itemIds as $itemId) {
                try {
                    $item = Item::find($itemId);
                    if ($item) {
                        // Check if item has related records
                        $hasRelatedRecords = $this->checkItemRelatedRecords($item);
                        
                        if ($hasRelatedRecords['has_relations']) {
                            $skippedItems[] = $item->name . ' (' . $hasRelatedRecords['message'] . ')';
                            continue;
                        }
                        
                        $item->delete();
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete item ID {$itemId}: " . $e->getMessage();
                }
            }

            if ($deletedCount > 0) {
                $message = $deletedCount === 1 
                    ? "1 item deleted successfully" 
                    : "{$deletedCount} items deleted successfully";
                
                if (!empty($skippedItems)) {
                    $message .= ". However, " . count($skippedItems) . " items were skipped due to existing relations.";
                }
                
                if (!empty($errors)) {
                    $message .= ". " . count($errors) . " items had errors.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'skipped_items' => $skippedItems
                ]);
            }
            
            if (!empty($skippedItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete selected items. They have existing relations: ' . implode(', ', array_slice($skippedItems, 0, 3)) . (count($skippedItems) > 3 ? ' and ' . (count($skippedItems) - 3) . ' more' : '')
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'No items were deleted. ' . implode(' ', $errors)
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage()
            ], 500);
        }
    }

    private function bools(Request $r, array $data): array
    {
        foreach(['CommonItem','PresReq','Inclusive','TaxonMrp','VATonSrate','Exon','DisContinue','LockScm','RateLock','LockBilling','SameBatchCost'] as $b){ $data[$b] = $r->boolean($b); }
        return $data;
    }

    /**
     * View stock ledger for an item (F10) - Basic Version
     */
    public function stockLedger(Item $item)
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');
        $transactionType = request('transaction_type');
        $referenceType = request('reference_type');

        $ledgers = StockLedger::query()
            ->where('item_id', $item->id)
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('transaction_date', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('transaction_date', '<=', $toDate);
            })
            ->when($transactionType, function ($query) use ($transactionType) {
                return $query->where('transaction_type', $transactionType);
            })
            ->when($referenceType, function ($query) use ($referenceType) {
                return $query->where('reference_type', $referenceType);
            })
            ->with('batch', 'createdBy')
            ->orderByDesc('transaction_date')
            ->paginate(25)
            ->withQueryString();

        // Calculate totals
        $totalInMovements = StockLedger::where('item_id', $item->id)
            ->whereIn('transaction_type', ['IN', 'RETURN'])
            ->sum('quantity');

        $totalOutMovements = StockLedger::where('item_id', $item->id)
            ->whereIn('transaction_type', ['OUT', 'ADJUSTMENT'])
            ->sum('quantity');

        return view('admin.items.stock-ledger', compact(
            'item', 'ledgers', 'fromDate', 'toDate', 
            'transactionType', 'referenceType',
            'totalInMovements', 'totalOutMovements'
        ));
    }

    /**
     * Get party details via AJAX
     */
    public function getPartyDetails($type, $id)
    {
        if ($type === 'customer') {
            $party = Customer::find($id);
        } else {
            $party = Supplier::find($id);
        }

        if (!$party) {
            return response()->json(['error' => 'Party not found'], 404);
        }

        return response()->json([
            'name' => $party->name ?? '',
            'address' => $party->address ?? '',
            'city' => $party->city ?? '',
            'phone' => $party->phone ?? '',
        ]);
    }
    
    /**
     * Get item by code for purchase transaction
     */
    public function getByCode($code)
    {
        // Try to find by ID (exact match only)
        $item = Item::where('id', $code)->first();
        
        // If not found by ID, try bar_code
        if (!$item) {
            $item = Item::where('bar_code', $code)->first();
        }
        
        if ($item) {
            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'packing' => $item->packing,
                    'case_qty' => $item->case_qty ?? 0,
                    'box_qty' => $item->box_qty ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'pur_rate' => $item->pur_rate ?? 0,
                    's_rate' => $item->s_rate ?? 0,
                    'ws_rate' => $item->ws_rate ?? 0,
                    'spl_rate' => $item->spl_rate ?? 0,
                    'hsn_code' => $item->hsn_code ?? '',
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'fixed_dis_percent' => $item->fixed_dis_percent ?? 0,
                ]
            ]);
        }
        
        return response()->json(['success' => false]);
    }

    /**
     * Get all items for insert modal in purchase/sale transactions
     */
    public function getAllItems()
    {
        try {
            $items = Item::select(
                    'id as code',
                    'id', 
                    'name', 
                    'mrp', 
                    's_rate',
                    'hsn_code',
                    'cgst_percent as cgst',
                    'sgst_percent as sgst',
                    'cess_percent as gst_cess',
                    'packing',
                    'unit',
                    'company_id'
                )
                ->with('company:id,name,short_name')
                ->where('is_deleted', '!=', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($item) {
                    // Get total qty from all batches for this item
                    $totalQty = \App\Models\Batch::where('item_id', $item->id)
                        ->where('is_deleted', 0)
                        ->sum('qty');
                    
                    // Get company short_name
                    $companyShortName = '';
                    if ($item->company) {
                        $companyShortName = $item->company->short_name ?: $item->company->name ?: '';
                    }
                    
                    return [
                        'code' => $item->code,
                        'id' => $item->id,
                        'name' => $item->name,
                        'mrp' => $item->mrp,
                        's_rate' => $item->s_rate,
                        'hsn_code' => $item->hsn_code,
                        'cgst' => $item->cgst,
                        'sgst' => $item->sgst,
                        'gst_cess' => $item->gst_cess,
                        'packing' => $item->packing,
                        'unit' => $item->unit,
                        'company_name' => $companyShortName,
                        'total_qty' => $totalQty
                    ];
                });
            
            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search items by code or name (AJAX)
     */
    public function search(Request $request)
    {
        try {
            $query = Item::select(
                    'id',
                    'id as code', 
                    'name', 
                    'mrp', 
                    's_rate',
                    'pur_rate',
                    'hsn_code',
                    'cgst_percent',
                    'sgst_percent',
                    'packing',
                    'unit',
                    'location',
                    'mfg_by',
                    'company_id'
                )
                ->with('company:id,name,short_name')
                ->where('is_deleted', '!=', 1);
            
            // Search by exact code
            if ($request->has('code') && $request->code) {
                $query->where('id', $request->code);
            }
            // Search by query (name or code)
            elseif ($request->has('q') && $request->q) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('bar_code', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            $items = $query->orderBy('name', 'asc')
                ->limit(50)
                ->get()
                ->map(function($item) {
                    // Get total qty from all batches for this item
                    $totalQty = \App\Models\Batch::where('item_id', $item->id)
                        ->where('is_deleted', 0)
                        ->sum('qty');
                    
                    // Get company name
                    $companyName = $item->mfg_by;
                    if (empty($companyName) && $item->company) {
                        $companyName = $item->company->short_name ?: $item->company->name ?: '';
                    }
                    
                    return [
                        'id' => $item->id,
                        'code' => $item->id,
                        'name' => $item->name,
                        'mrp' => $item->mrp,
                        's_rate' => $item->s_rate,
                        'pur_rate' => $item->pur_rate,
                        'hsn_code' => $item->hsn_code,
                        'cgst_percent' => $item->cgst_percent,
                        'sgst_percent' => $item->sgst_percent,
                        'packing' => $item->packing,
                        'unit' => $item->unit,
                        'location' => $item->location,
                        'mfg_by' => $companyName,
                        'company' => $companyName,
                        'closing_qty' => $totalQty
                    ];
                });
            
            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total quantity of an item from all batches
     */
    public function getItemTotalQty($itemId)
    {
        try {
            $totalQty = \App\Models\Batch::where('item_id', $itemId)
                ->where('is_deleted', 0)
                ->sum('total_qty');
            
            return response()->json([
                'success' => true,
                'total_qty' => $totalQty ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'total_qty' => 0
            ], 500);
        }
    }

    /**
     * View stock ledger for an item (F10) - Complete EasySol Style
     */
    public function stockLedgerComplete(Item $item)
    {
        $fromDate = request('from_date', now()->startOfMonth()->toDateString());
        $toDate = request('to_date', now()->toDateString());
        $partyId = request('party_id');
        $selectedPartyId = $partyId;
        $partyName = '';
        $partyCode = '';

        // Parse party ID (C for customer, S for supplier)
        $customerId = null;
        $supplierId = null;

        if ($partyId) {
            if (str_starts_with($partyId, 'C')) {
                $customerId = substr($partyId, 1);
                $customer = Customer::find($customerId);
                $partyName = $customer->name ?? '';
                $partyCode = $customer->code ?? '';
            } elseif (str_starts_with($partyId, 'S')) {
                $supplierId = substr($partyId, 1);
                $supplier = Supplier::find($supplierId);
                $partyName = $supplier->name ?? '';
                $partyCode = $supplier->code ?? '';
            }
        }

        // Collect all transactions (Purchase = Received, Sale = Issued)
        $transactions = collect();

        // Get Purchase Transactions (RECEIVED)
        $purchaseItems = PurchaseTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $supplierId) {
                $query->whereDate('bill_date', '>=', $fromDate)
                    ->whereDate('bill_date', '<=', $toDate)
                    ->when($supplierId, function ($q) use ($supplierId) {
                        return $q->where('supplier_id', $supplierId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction.supplier'])
            ->get();

        foreach ($purchaseItems as $purchaseItem) {
            $transactions->push([
                'trans_no' => 'PB/' . $purchaseItem->purchase_transaction_id,
                'date' => $purchaseItem->transaction->bill_date,
                'party_name' => $purchaseItem->transaction->supplier->name ?? '-',
                'batch' => $purchaseItem->batch_no ?? '-',
                'received_qty' => $purchaseItem->qty,
                'received_free' => $purchaseItem->free_qty ?? 0,
                'issued_qty' => 0,
                'issued_free' => 0,
                'type' => 'PURCHASE',
                'transaction_id' => $purchaseItem->purchase_transaction_id,
            ]);
        }

        // Get Sale Transactions (ISSUED)
        $saleItems = SaleTransactionItem::query()
            ->whereHas('saleTransaction', function ($query) use ($fromDate, $toDate, $customerId) {
                $query->whereDate('sale_date', '>=', $fromDate)
                    ->whereDate('sale_date', '<=', $toDate)
                    ->when($customerId, function ($q) use ($customerId) {
                        return $q->where('customer_id', $customerId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['saleTransaction.customer'])
            ->get();

        foreach ($saleItems as $saleItem) {
            $transactions->push([
                'trans_no' => 'S2/' . $saleItem->sale_transaction_id,
                'date' => $saleItem->saleTransaction->sale_date,
                'party_name' => $saleItem->saleTransaction->customer->name ?? '-',
                'batch' => $saleItem->batch_no ?? '-',
                'received_qty' => 0,
                'received_free' => 0,
                'issued_qty' => $saleItem->qty,
                'issued_free' => $saleItem->free_qty ?? 0,
                'type' => 'SALE',
                'transaction_id' => $saleItem->sale_transaction_id,
            ]);
        }

        // Get Sale Return Transactions (RECEIVED - items returned back to stock)
        $saleReturnItems = SaleReturnTransactionItem::query()
            ->whereHas('saleReturnTransaction', function ($query) use ($fromDate, $toDate, $customerId) {
                $query->whereDate('return_date', '>=', $fromDate)
                    ->whereDate('return_date', '<=', $toDate)
                    ->when($customerId, function ($q) use ($customerId) {
                        return $q->where('customer_id', $customerId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['saleReturnTransaction.customer', 'batch'])
            ->get();

        foreach ($saleReturnItems as $returnItem) {
            // Try to get batch number from batch_no field first, then from batch relationship
            $batchNo = $returnItem->batch_no;
            if (empty($batchNo) && $returnItem->batch) {
                $batchNo = $returnItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'SR/' . $returnItem->sale_return_transaction_id,
                'date' => $returnItem->saleReturnTransaction->return_date,
                'party_name' => $returnItem->saleReturnTransaction->customer->name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => $returnItem->qty, // Sale return = received back
                'received_free' => $returnItem->free_qty ?? 0,
                'issued_qty' => 0,
                'issued_free' => 0,
                'type' => 'SALE_RETURN',
                'transaction_id' => $returnItem->sale_return_transaction_id,
            ]);
        }

        // Get Stock Adjustment Transactions
        $stockAdjustmentItems = StockAdjustmentItem::query()
            ->whereHas('stockAdjustment', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('adjustment_date', '>=', $fromDate)
                    ->whereDate('adjustment_date', '<=', $toDate);
            })
            ->where('item_id', $item->id)
            ->with(['stockAdjustment', 'batch'])
            ->get();

        foreach ($stockAdjustmentItems as $adjItem) {
            $batchNo = $adjItem->batch_no;
            if (empty($batchNo) && $adjItem->batch) {
                $batchNo = $adjItem->batch->batch_no ?? '-';
            }
            
            // Shortage (S) = Issued, Excess (E) = Received
            $isShortage = $adjItem->adjustment_type === 'S';
            
            $transactions->push([
                'trans_no' => 'SA/' . $adjItem->stock_adjustment_id,
                'date' => $adjItem->stockAdjustment->adjustment_date,
                'party_name' => $isShortage ? 'Shortage Adj.' : 'Excess Adj.',
                'batch' => $batchNo ?: '-',
                'received_qty' => $isShortage ? 0 : $adjItem->qty, // Excess = received
                'received_free' => 0,
                'issued_qty' => $isShortage ? $adjItem->qty : 0, // Shortage = issued
                'issued_free' => 0,
                'type' => 'STOCK_ADJUSTMENT',
                'transaction_id' => $adjItem->stock_adjustment_id,
            ]);
        }

        // Get Replacement Note Transactions (ISSUED - items sent out for replacement)
        $replacementNoteItems = \App\Models\ReplacementNoteTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $supplierId) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate)
                    ->when($supplierId, function ($q) use ($supplierId) {
                        return $q->where('supplier_id', $supplierId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction.supplier', 'batch'])
            ->get();

        foreach ($replacementNoteItems as $rnItem) {
            $batchNo = $rnItem->batch_no;
            if (empty($batchNo) && $rnItem->batch) {
                $batchNo = $rnItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'RN/' . $rnItem->replacement_note_transaction_id,
                'date' => $rnItem->transaction->transaction_date,
                'party_name' => $rnItem->transaction->supplier->name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => 0,
                'received_free' => 0,
                'issued_qty' => $rnItem->qty, // Replacement Note = issued
                'issued_free' => $rnItem->free_qty ?? 0,
                'type' => 'REPLACEMENT_NOTE',
                'transaction_id' => $rnItem->replacement_note_transaction_id,
            ]);
        }

        // Get Replacement Received Transactions (RECEIVED - items received back from replacement)
        $replacementReceivedItems = \App\Models\ReplacementReceivedTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $supplierId) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate)
                    ->when($supplierId, function ($q) use ($supplierId) {
                        return $q->where('supplier_id', $supplierId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction.supplier', 'batch'])
            ->get();

        foreach ($replacementReceivedItems as $rrItem) {
            $batchNo = $rrItem->batch_no;
            if (empty($batchNo) && $rrItem->batch) {
                $batchNo = $rrItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'RR/' . $rrItem->replacement_received_transaction_id,
                'date' => $rrItem->transaction->transaction_date,
                'party_name' => $rrItem->transaction->supplier->name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => $rrItem->qty, // Replacement Received = received
                'received_free' => $rrItem->free_qty ?? 0,
                'issued_qty' => 0,
                'issued_free' => 0,
                'type' => 'REPLACEMENT_RECEIVED',
                'transaction_id' => $rrItem->replacement_received_transaction_id,
            ]);
        }

        // Get Stock Transfer Outgoing Transactions (ISSUED - items transferred out)
        $stockTransferOutgoingItems = \App\Models\StockTransferOutgoingTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $customerId) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate)
                    ->when($customerId, function ($q) use ($customerId) {
                        return $q->where('transfer_to', $customerId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction', 'batch'])
            ->get();

        foreach ($stockTransferOutgoingItems as $stoItem) {
            $batchNo = $stoItem->batch_no;
            if (empty($batchNo) && $stoItem->batch) {
                $batchNo = $stoItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'STO/' . $stoItem->stock_transfer_outgoing_transaction_id,
                'date' => $stoItem->transaction->transaction_date,
                'party_name' => $stoItem->transaction->transfer_to_name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => 0,
                'received_free' => 0,
                'issued_qty' => $stoItem->qty, // Stock Transfer Outgoing = issued
                'issued_free' => $stoItem->f_qty ?? 0,
                'type' => 'STOCK_TRANSFER_OUTGOING',
                'transaction_id' => $stoItem->stock_transfer_outgoing_transaction_id,
            ]);
        }

        // Get Stock Transfer Outgoing Return Transactions (RECEIVED - items returned back)
        $stockTransferOutgoingReturnItems = \App\Models\StockTransferOutgoingReturnTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $customerId) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate)
                    ->when($customerId, function ($q) use ($customerId) {
                        return $q->where('transfer_from', $customerId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction', 'batch'])
            ->get();

        foreach ($stockTransferOutgoingReturnItems as $storItem) {
            $batchNo = $storItem->batch_no;
            if (empty($batchNo) && $storItem->batch) {
                $batchNo = $storItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'STOR/' . $storItem->stock_transfer_outgoing_return_transaction_id,
                'date' => $storItem->transaction->transaction_date,
                'party_name' => $storItem->transaction->transfer_from_name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => $storItem->qty, // Stock Transfer Outgoing Return = received
                'received_free' => $storItem->f_qty ?? 0,
                'issued_qty' => 0,
                'issued_free' => 0,
                'type' => 'STOCK_TRANSFER_OUTGOING_RETURN',
                'transaction_id' => $storItem->stock_transfer_outgoing_return_transaction_id,
            ]);
        }

        // Get Stock Transfer Incoming Transactions (RECEIVED - items received from another branch)
        $stockTransferIncomingItems = \App\Models\StockTransferIncomingTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate, $supplierId) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate)
                    ->when($supplierId, function ($q) use ($supplierId) {
                        return $q->where('supplier_id', $supplierId);
                    });
            })
            ->where('item_id', $item->id)
            ->with(['transaction', 'batch'])
            ->get();

        foreach ($stockTransferIncomingItems as $stiItem) {
            $batchNo = $stiItem->batch_no;
            if (empty($batchNo) && $stiItem->batch) {
                $batchNo = $stiItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'STI/' . $stiItem->stock_transfer_incoming_transaction_id,
                'date' => $stiItem->transaction->transaction_date,
                'party_name' => $stiItem->transaction->supplier_name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => $stiItem->qty, // Stock Transfer Incoming = received
                'received_free' => $stiItem->f_qty ?? 0,
                'issued_qty' => 0,
                'issued_free' => 0,
                'type' => 'STOCK_TRANSFER_INCOMING',
                'transaction_id' => $stiItem->stock_transfer_incoming_transaction_id,
            ]);
        }

        // Get Stock Transfer Incoming Return Transactions (ISSUED - items returned to sending branch)
        $stockTransferIncomingReturnItems = \App\Models\StockTransferIncomingReturnTransactionItem::query()
            ->whereHas('transaction', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate);
            })
            ->where('item_id', $item->id)
            ->with(['transaction', 'batch'])
            ->get();

        foreach ($stockTransferIncomingReturnItems as $stirItem) {
            $batchNo = $stirItem->batch_no;
            if (empty($batchNo) && $stirItem->batch) {
                $batchNo = $stirItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'STIR/' . $stirItem->stock_transfer_incoming_return_transaction_id,
                'date' => $stirItem->transaction->transaction_date,
                'party_name' => $stirItem->transaction->name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => 0,
                'received_free' => 0,
                'issued_qty' => $stirItem->qty, // Stock Transfer Incoming Return = issued (returned out)
                'issued_free' => 0,
                'type' => 'STOCK_TRANSFER_INCOMING_RETURN',
                'transaction_id' => $stirItem->stock_transfer_incoming_return_transaction_id,
            ]);
        }

        // Get Sample Issued Transactions (ISSUED - samples given out)
        $sampleIssuedItems = \App\Models\SampleIssuedTransactionItem::query()
            ->whereHas('sampleIssuedTransaction', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('transaction_date', '>=', $fromDate)
                    ->whereDate('transaction_date', '<=', $toDate);
            })
            ->where('item_id', $item->id)
            ->with(['sampleIssuedTransaction', 'batch'])
            ->get();

        foreach ($sampleIssuedItems as $siItem) {
            $batchNo = $siItem->batch_no;
            if (empty($batchNo) && $siItem->batch) {
                $batchNo = $siItem->batch->batch_no ?? '-';
            }
            
            $transactions->push([
                'trans_no' => 'SI/' . $siItem->sample_issued_transaction_id,
                'date' => $siItem->sampleIssuedTransaction->transaction_date,
                'party_name' => $siItem->sampleIssuedTransaction->party_name ?? '-',
                'batch' => $batchNo ?: '-',
                'received_qty' => 0,
                'received_free' => 0,
                'issued_qty' => $siItem->qty, // Sample Issued = issued
                'issued_free' => $siItem->free_qty ?? 0,
                'type' => 'SAMPLE_ISSUED',
                'transaction_id' => $siItem->sample_issued_transaction_id,
            ]);
        }

        // Sort by date
        $transactions = $transactions->sortBy('date')->values();

        // Calculate running balance and totals
        $balance = 0;
        $totalReceived = 0;
        $totalIssued = 0;
        
        $transactions = $transactions->map(function ($transaction) use (&$balance, &$totalReceived, &$totalIssued) {
            $received = $transaction['received_qty'] + $transaction['received_free'];
            $issued = $transaction['issued_qty'] + $transaction['issued_free'];
            $balance += $received - $issued;
            $totalReceived += $received;
            $totalIssued += $issued;
            $transaction['balance'] = $balance;
            return $transaction;
        });

        // Paginate manually - 10 records per page for infinite scroll
        $perPage = 10;
        $currentPage = request('page', 1);
        $pagedData = $transactions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $ledgers = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $transactions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get all customers and suppliers
        $customers = Customer::where('is_deleted', 0)
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();
        $suppliers = Supplier::where('is_deleted', 0)
            ->select('supplier_id', 'name', 'code')
            ->orderBy('name')
            ->get();

        // For AJAX requests, return the full view as HTML
        if (request()->ajax()) {
            return view('admin.items.stock-ledger-complete', compact(
                'item', 'ledgers', 'fromDate', 'toDate',
                'partyName', 'partyCode', 'selectedPartyId',
                'customers', 'suppliers', 'totalReceived', 'totalIssued', 'balance'
            ));
        }

        return view('admin.items.stock-ledger-complete', compact(
            'item', 'ledgers', 'fromDate', 'toDate',
            'partyName', 'partyCode', 'selectedPartyId',
            'customers', 'suppliers', 'totalReceived', 'totalIssued', 'balance'
        ));
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'Barcode' => 'nullable|string|max:100',
            'Compcode' => 'nullable|string|max:100',
            'Compname' => 'nullable|string|max:255',
            'Pack' => 'nullable|string|max:50',
            'Unit' => 'nullable|string|in:Kg.,L.,Gm.,Ml.,Doz.,Mtr.',
            'status' => 'nullable|string|max:5',
            'Location' => 'nullable|string|max:100',
            // Add validation rules for all new fields
            'MfgBy' => 'nullable|string|max:255',
            'Division' => 'nullable|string|max:50',
            'BoxQty' => 'nullable|integer',
            'CaseQty' => 'nullable|integer',
            'Schedule' => 'nullable|string|max:50',
            'MinLevel' => 'nullable|integer',
            'MaxLevel' => 'nullable|integer',
            'Flag' => 'nullable|string|max:100',
            'Srate' => 'nullable|numeric',
            'Mrp' => 'nullable|numeric',
            'Net' => 'nullable|numeric',
            'Wsrate' => 'nullable|numeric',
            'SplRate' => 'nullable|numeric',
            'MinGP' => 'nullable|numeric',
            'Commodity' => 'nullable|string|max:255',
            'Scheme' => 'nullable|integer',
            'Prate' => 'nullable|numeric',
            'Cost' => 'nullable|numeric',
            'PurScheme' => 'nullable|integer',
            'NR' => 'nullable|numeric',
            'HSNCode' => 'nullable|string|max:100',
            'CGST' => 'nullable|numeric',
            'SGST' => 'nullable|numeric',
            'IGST' => 'nullable|numeric',
            'Cess' => 'nullable|numeric',
            'VAT' => 'nullable|numeric',
            'Expiry' => 'nullable|date',
            'Generic' => 'nullable|string|max:50',
            'Stax' => 'nullable|numeric',
            'FixedDis' => 'nullable|numeric',
            'Category' => 'nullable|string|max:50',
            'MaxInvQty' => 'nullable|integer',
            'Weight' => 'nullable|numeric',
            'Volume' => 'nullable|numeric',
            'Lock' => 'nullable|string|max:50',
            'BarCodeFlag' => 'nullable|string|max:10',
            'DetQty' => 'nullable|string|max:10',
            'CompNameBC' => 'nullable|string|max:10',
            'DPCItem' => 'nullable|string|max:10',
            'LockSale' => 'nullable|string|max:10',
            'CommodityClass' => 'nullable|string|max:255',
            'CurrentScheme' => 'nullable|string|max:255',
            'CategoryClass' => 'nullable|string|max:50',
            
            // New restructured fields validation
            'unit_1' => 'nullable|string|max:50',
            'unit_2' => 'nullable|string|max:50',
            'min_level' => 'nullable|numeric',
            'max_level' => 'nullable|numeric',
            'narcotic_flag' => 'nullable|string|in:Y,N',
            's_rate' => 'nullable|numeric',
            'net_toggle' => 'nullable|numeric',
            'ws_rate' => 'nullable|numeric',
            'ws_net_toggle' => 'nullable|string|in:Y,N',
            'spl_rate' => 'nullable|numeric',
            'spl_net_toggle' => 'nullable|string|in:Y,N',
            'sale_scheme' => 'nullable|numeric',
            'min_gp' => 'nullable|numeric',
            'pur_rate' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'pur_scheme' => 'nullable|numeric',
            'nr' => 'nullable|numeric',
            'hsn_code' => 'nullable|string|max:100',
            'cgst_percent' => 'nullable|numeric',
            'sgst_percent' => 'nullable|numeric',
            'igst_percent' => 'nullable|numeric',
            'cess_percent' => 'nullable|numeric',
            'vat_percent' => 'nullable|numeric',
            'fixed_dis' => 'nullable|string|in:Y,N,M',
            'fixed_dis_percent' => 'nullable|numeric',
            'fixed_dis_type' => 'nullable|string|in:W,R,I',
            'expiry_flag' => 'nullable|string|in:Y,N',
            'inclusive_flag' => 'nullable|string|in:Y,N',
            'generic_flag' => 'nullable|string|in:Y,N',
            'h_scm_flag' => 'nullable|string|in:Y,N',
            'q_scm_flag' => 'nullable|string|in:Y,N',
            'locks_flag' => 'nullable|string|in:Y,N,S',
            'max_inv_qty_value' => 'nullable|numeric',
            'max_inv_qty_new' => 'nullable|string|in:W,R,I',
            'weight_new' => 'nullable|numeric',
            'bar_code_flag' => 'nullable|string|in:Y,N',
            'def_qty_flag' => 'nullable|string|in:Y,N',
            'volume_new' => 'nullable|numeric',
            'comp_name_bc_new' => 'nullable|string|in:Y,N',
            'dpc_item_flag' => 'nullable|string|in:Y,N',
            'lock_sale_flag' => 'nullable|string|in:Y,N',
            'max_min_flag' => 'nullable|string|in:1,2',
            'mrp_for_sale_new' => 'nullable|numeric',
            'commodity' => 'nullable|string|max:255',
            'current_scheme_flag' => 'nullable|string|in:Y,N',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'scheme_plus_value' => 'nullable|numeric',
            'scheme_minus_value' => 'nullable|numeric',
            'category' => 'nullable|string|max:100',
            'category_2' => 'nullable|string|max:100',
            'upc' => 'nullable|string|max:100',
        ];
    }

    /**
     * View Pending Orders for an item (F7)
     */
    public function pendingOrders(Item $item)
    {
        $pendingOrders = PendingOrder::where('item_id', $item->id)
            ->with('supplier')
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        $suppliers = Supplier::where('is_deleted', 0)
            ->select('supplier_id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('admin.items.pending-orders', compact(
            'item',
            'pendingOrders',
            'suppliers'
        ));
    }

    /**
     * Store a new pending order
     */
    public function storePendingOrder(Request $request, Item $item)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'order_date' => 'nullable|date',
            'rate' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'cost' => 'nullable|numeric|min:0',
            'scm_percent' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'nullable|numeric|min:0',
        ]);

        // Set defaults for null values
        $validated['supplier_id'] = $validated['supplier_id'] ?? null;
        $validated['order_date'] = $validated['order_date'] ?? now()->toDateString();
        $validated['quantity'] = $validated['quantity'] ?? 0;
        $validated['rate'] = $validated['rate'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['tax_percent'] = $validated['tax_percent'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['scm_percent'] = $validated['scm_percent'] ?? 0;

        // Get supplier details
        $supplier = null;
        if ($validated['supplier_id']) {
            $supplier = Supplier::where('supplier_id', $validated['supplier_id'])->first();
        }

        // Prepare data
        $data = array_merge($validated, [
            'item_id' => $item->id,
            'supplier_name' => $supplier->name ?? '',
            'supplier_code' => $supplier->code ?? '',
        ]);

        PendingOrder::create($data);

        return redirect()->route('admin.items.pending-orders', $item)
            ->with('success', 'Pending Order created successfully');
    }


    /**
     * Update pending order quantity
     */
    public function updatePendingOrderQty(Request $request, PendingOrder $pendingOrder)
    {
        $validated = $request->validate([
            'order_qty' => 'required|numeric|min:0',
            'free_qty' => 'required|numeric|min:0',
        ]);

        $pendingOrder->update([
            'order_qty' => $validated['order_qty'],
            'free_qty' => $validated['free_qty'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
            'order' => [
                'id' => $pendingOrder->id,
                'order_qty' => $pendingOrder->order_qty,
                'free_qty' => $pendingOrder->free_qty,
            ]
        ]);
    }

    /**
     * Delete pending order
     */
    public function deletePendingOrder(Item $item, PendingOrder $pendingOrder)
    {
        $pendingOrder->delete();

        return redirect()->route('admin.items.pending-orders', $item)
            ->with('success', 'Pending Order deleted successfully');
    }

    /**
     * View Godown Expiry for an item
     */
    public function godownExpiry(Item $item)
    {
        $expiryRecords = GodownExpiry::where('item_id', $item->id)
            ->with('batch')
            ->orderBy('expiry_date', 'asc')
            ->paginate(20);

        return view('admin.items.godown-expiry', compact(
            'item',
            'expiryRecords'
        ));
    }

    /**
     * Store godown expiry record
     */
    public function storeGodownExpiry(Request $request, Item $item)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'expiry_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'godown_location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['item_id'] = $item->id;
        $validated['status'] = 'active';

        GodownExpiry::create($validated);

        return redirect()->route('admin.items.godown-expiry', $item)
            ->with('success', 'Expiry record created successfully');
    }

    /**
     * Mark godown expiry as expired
     */
    public function markExpired(Item $item, GodownExpiry $godownExpiry)
    {
        $godownExpiry->update(['status' => 'expired']);

        return redirect()->route('admin.items.godown-expiry', $item)
            ->with('success', 'Record marked as expired');
    }

    /**
     * Delete godown expiry record
     */
    public function deleteGodownExpiry(Item $item, GodownExpiry $godownExpiry)
    {
        $godownExpiry->delete();

        return redirect()->route('admin.items.godown-expiry', $item)
            ->with('success', 'Expiry record deleted successfully');
    }

    /**
     * Store expiry ledger entry (Legacy - kept for compatibility)
     */
    public function storeExpiryLedger(Request $request, Item $item)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'transaction_date' => 'required|date',
            'trans_no' => 'nullable|string|max:100',
            'transaction_type' => 'required|in:IN,OUT,RETURN,ADJUSTMENT',
            'party_name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'free_quantity' => 'nullable|integer|min:0',
            'running_balance' => 'required|numeric',
            'expiry_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['item_id'] = $item->id;

        ExpiryLedger::create($validated);

        return redirect()->route('admin.items.expiry-ledger', $item)
            ->with('success', 'Expiry ledger entry created successfully');
    }

    /**
     * Delete expiry ledger entry (Legacy - kept for compatibility)
     */
    public function deleteExpiryLedger(Item $item, ExpiryLedger $expiryLedger)
    {
        $expiryLedger->delete();

        return redirect()->route('admin.items.expiry-ledger', $item)
            ->with('success', 'Expiry ledger entry deleted successfully');
    }

    /**
     * Get purchase transaction details for stock ledger modal
     */
    public function getPurchaseTransactionDetails($id)
    {
        try {
            $transaction = \App\Models\PurchaseTransaction::with(['supplier', 'creator', 'items'])
                ->findOrFail($id);

            $item = $transaction->items->first();

            return response()->json([
                'salesman' => $transaction->creator->name ?? 'MASTER',
                'supplier_name' => $transaction->supplier->name ?? '-',
                'rate' => $item->pur_rate ?? '0.00',
                'mrp' => $item->mrp ?? '0.00',
                'srino' => '1',
                'code' => $item->item_code ?? '-',
                'discount_percent' => $item->dis_percent ?? '0.00',
                'user_id' => $transaction->creator->name ?? 'MASTER',
                'bill_no' => $transaction->bill_no ?? '-',
                'bill_date' => $transaction->bill_date ? $transaction->bill_date->format('d-M-Y') : '-',
                'address' => $transaction->supplier->address ?? '-',
                'pur_rate' => $item->pur_rate ?? '0.00',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
    }

    /**
     * Get sale transaction details for stock ledger modal
     */
    public function getSaleTransactionDetails($id)
    {
        try {
            $transaction = \App\Models\SaleTransaction::with(['customer', 'creator', 'items'])
                ->findOrFail($id);

            $item = $transaction->items->first();

            return response()->json([
                'salesman' => $transaction->creator->name ?? 'MASTER',
                'customer_name' => $transaction->customer->name ?? '-',
                'rate' => $item->rate ?? '0.00',
                'mrp' => $item->mrp ?? '0.00',
                'srino' => '1',
                'code' => $item->item_code ?? '-',
                'discount_percent' => $item->dis_percent ?? '0.00',
                'user_id' => $transaction->creator->name ?? 'MASTER',
                'invoice_no' => $transaction->invoice_no ?? '-',
                'sale_date' => $transaction->sale_date ? $transaction->sale_date->format('d-M-Y') : '-',
                'address' => $transaction->customer->address ?? '-',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
    }

    /**
     * Get all items for selection modal (AJAX)
     */
    public function getAll()
    {
        try {
            $items = Item::select('id', 'name', 'packing', 'mrp', 's_rate', 'pur_rate', 'ws_rate', 'spl_rate', 'mfg_by', 'hsn_code', 'cgst_percent', 'sgst_percent', 'igst_percent', 'unit', 'cost', 'company_short_name', 'location')
                ->addSelect([
                    'total_qty' => Batch::selectRaw('COALESCE(SUM(total_qty), 0)')
                        ->whereColumn('item_id', 'items.id')
                        ->where('is_deleted', 0)
                ])
                ->where('is_deleted', '!=', 1)
                ->orderBy('name')
                ->get()
                ->map(function($item) {
                    // Calculate total GST percent
                    $item->gst_percent = ($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0);
                    return $item;
                });

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching all items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display expiry ledger page
     */
    public function expiryLedger(Item $item)
    {
        $fromDate = request('from_date', now()->startOfMonth()->toDateString());
        $toDate = request('to_date', now()->toDateString());

        return view('admin.items.expiry-ledger', compact('item', 'fromDate', 'toDate'));
    }

    /**
     * Get expiry ledger data (AJAX)
     */
    public function getExpiryLedgerData(Request $request)
    {
        try {
            $itemId = $request->input('item_id');
            $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
            $toDate = $request->input('to_date', now()->toDateString());
            $type = $request->input('type');

            if (!$itemId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ID is required'
                ], 400);
            }

            // Get item details
            $item = Item::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Get breakage/expiry transactions for this item
            $query = \App\Models\BreakageExpiryTransactionItem::query()
                ->with(['transaction.customer', 'transaction.salesman', 'batch'])
                ->where('item_id', $itemId)
                ->whereHas('transaction', function($q) use ($fromDate, $toDate) {
                    $q->whereDate('transaction_date', '>=', $fromDate)
                      ->whereDate('transaction_date', '<=', $toDate);
                });

            if ($type) {
                $query->where('br_ex', $type);
            }

            $transactionItems = $query->orderBy('id')->get();

            // Format transactions
            $transactions = [];
            $totalReceived = 0;
            $totalIssued = 0;

            foreach ($transactionItems as $item) {
                $transaction = $item->transaction;
                
                // All breakage/expiry transactions are received (customer returns to us)
                $qty = floatval($item->qty ?? 0);
                $rcvd = $qty;
                $issued = 0;

                $totalReceived += $rcvd;
                $totalIssued += $issued;

                $transactions[] = [
                    'id' => $transaction->id,
                    'transaction_date' => $transaction->transaction_date,
                    'trans_no' => ($transaction->series ?? 'BE') . ' / ' . ($transaction->sr_no ?? '---'),
                    'type' => $item->br_ex ?? 'B', // Use the actual br_ex value from the item row
                    'party_name' => $transaction->customer_name ?? '---',
                    'batch_no' => $item->batch_no ?? '---',
                    'rcvd' => $rcvd,
                    'issued' => $issued,
                ];
            }

            // Calculate summary - balance = received - issued
            $balance = $totalReceived - $totalIssued;
            $summary = [
                'total_received' => $totalReceived,
                'total_issued' => $totalIssued,
                'balance' => $balance,
                'excess_issued' => $balance < 0 ? abs($balance) : 0,
                'effective' => $balance > 0 ? $balance : 0,
            ];

            // Get latest batch for item info
            $latestBatch = Batch::where('item_id', $itemId)
                ->where('is_deleted', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            $itemInfo = [
                'item_name' => $item->name,
                'item_code' => $item->code ?? $item->id,
                'rate' => $latestBatch ? $latestBatch->s_rate : ($item->s_rate ?? 0),
                'discount' => $item->fixed_dis_percent ?? 0,
                'address' => $item->location ?? '---',
                'sr_no' => isset($transaction) ? $transaction->sr_no : '---',
            ];

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'summary' => $summary,
                'item' => $itemInfo,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching expiry ledger data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching ledger data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expiry ledger to Excel
     */
    public function exportExpiryLedger(Request $request)
    {
        try {
            $itemId = $request->input('item_id');
            $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
            $toDate = $request->input('to_date', now()->toDateString());
            $type = $request->input('type');

            if (!$itemId) {
                return redirect()->back()->with('error', 'Item ID is required');
            }

            // Get item details
            $item = Item::find($itemId);
            if (!$item) {
                return redirect()->back()->with('error', 'Item not found');
            }

            // Get breakage/expiry transactions
            $query = \App\Models\BreakageExpiryTransactionItem::query()
                ->with(['transaction.customer', 'transaction.salesman', 'batch'])
                ->where('item_id', $itemId)
                ->whereHas('transaction', function($q) use ($fromDate, $toDate) {
                    $q->whereDate('transaction_date', '>=', $fromDate)
                      ->whereDate('transaction_date', '<=', $toDate);
                });

            if ($type) {
                $query->where('br_ex', $type);
            }

            $transactionItems = $query->orderBy('id')->get();

            // Create CSV content
            $filename = 'expiry_ledger_' . $item->name . '_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($transactionItems, $item, $fromDate, $toDate) {
                $file = fopen('php://output', 'w');

                // Add header information
                fputcsv($file, ['Item Expiry Ledger']);
                fputcsv($file, ['Item:', $item->name]);
                fputcsv($file, ['Code:', $item->code ?? $item->item_code]);
                fputcsv($file, ['Period:', $fromDate . ' to ' . $toDate]);
                fputcsv($file, []); // Empty line

                // Add column headers
                fputcsv($file, ['S.No', 'Date', 'Trans. No', 'Type', 'Party Name', 'Batch', 'Rcvd', 'Issued', 'Balance']);

                // Add data rows
                $balance = 0;
                $sno = 1;
                foreach ($transactionItems as $item) {
                    $transaction = $item->transaction;
                    $qty = floatval($item->qty ?? 0);
                    $rcvd = 0;
                    $issued = $qty;
                    $balance += $rcvd - $issued;

                    fputcsv($file, [
                        $sno++,
                        $transaction->transaction_date ? $transaction->transaction_date->format('d-m-Y') : '---',
                        ($transaction->series ?? 'BE') . ' / ' . ($transaction->sr_no ?? '---'),
                        'Brk. Exp.',
                        $transaction->customer_name ?? '---',
                        $item->batch_no ?? '---',
                        $rcvd > 0 ? $rcvd : '',
                        $issued > 0 ? $issued : '',
                        number_format($balance, 2),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error exporting expiry ledger: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting ledger: ' . $e->getMessage());
        }
    }

    /**
     * Check if item has related records that prevent deletion
     */
    private function checkItemRelatedRecords(Item $item)
    {
        $relations = [];
        
        // Check if item has available quantity (stock)
        $totalQty = Batch::where('item_id', $item->id)
            ->where('is_deleted', 0)
            ->sum('total_qty');
        
        if ($totalQty != 0) {
            $relations[] = "available stock quantity (" . number_format($totalQty, 2) . " units)";
        }
        
        // Check for any batches (including deleted ones - if item was ever used in transactions)
        $batchCount = Batch::where('item_id', $item->id)->count();
        if ($batchCount > 0) {
            $relations[] = "{$batchCount} batch record(s)";
        }
        
        // Check for purchase transaction items
        $purchaseCount = PurchaseTransactionItem::where('item_id', $item->id)->count();
        if ($purchaseCount > 0) {
            $relations[] = "{$purchaseCount} purchase transaction(s)";
        }
        
        // Check for sale transaction items
        $saleCount = SaleTransactionItem::where('item_id', $item->id)->count();
        if ($saleCount > 0) {
            $relations[] = "{$saleCount} sale transaction(s)";
        }
        
        // Check for sale return transaction items
        $saleReturnCount = SaleReturnTransactionItem::where('item_id', $item->id)->count();
        if ($saleReturnCount > 0) {
            $relations[] = "{$saleReturnCount} sale return transaction(s)";
        }
        
        if (!empty($relations)) {
            return [
                'has_relations' => true,
                'message' => 'This item cannot be deleted as it has: ' . implode(', ', $relations)
            ];
        }
        
        return ['has_relations' => false, 'message' => ''];
    }
}


