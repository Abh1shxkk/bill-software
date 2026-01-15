<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleChallanTransaction;
use App\Models\SaleChallanTransactionItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesMan;
use App\Models\Batch;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SaleChallanController extends Controller
{
    use ValidatesTransactionDate;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = SaleChallanTransaction::with('customer')
            ->orderBy('challan_date', 'desc')
            ->paginate(20);
        
        return view('admin.sale-challan.index', compact('transactions'));
    }

    /**
     * Display sale challan transaction form
     */
    public function transaction()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextChallanNo = $this->generateChallanNo();
        
        return view('admin.sale-challan.transaction', compact('customers', 'salesmen', 'items', 'nextChallanNo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('is_deleted', '!=', 1)->get();
        $salesmen = SalesMan::all();
        $items = Item::all();
        $nextChallanNo = $this->generateChallanNo();
        
        return view('admin.sale-challan.transaction', compact('customers', 'salesmen', 'items', 'nextChallanNo'));
    }

    /**
     * Get all items for Choose Items modal (with pagination and search support)
     */
    public function getItems(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            $search = $request->input('search', '');
            
            // Build query
            $query = Item::select('id', 'name', 'bar_code', 'hsn_code', 'packing', 'company_id', 'company_short_name', 's_rate', 'mrp', 'cgst_percent', 'sgst_percent', 'cess_percent', 'unit', 'case_qty', 'box_qty')
                ->with(['batches' => function($q) {
                    $q->where('is_deleted', 0);
                }]);
            
            // Apply search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('hsn_code', 'LIKE', "%{$search}%")
                      ->orWhere('company_short_name', 'LIKE', "%{$search}%")
                      ->orWhere('bar_code', 'LIKE', "%{$search}%");
                });
            }
            
            // Paginate
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Map items
            $items = $paginator->getCollection()->map(function($item) {
                $totalQty = $item->getTotalQuantity();
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'bar_code' => $item->bar_code,
                    'hsn_code' => $item->hsn_code,
                    'packing' => $item->packing,
                    'company_name' => $item->company_short_name ?? 'N/A',
                    'company' => $item->company_short_name ?? 'N/A',
                    's_rate' => $item->s_rate ?? 0,
                    'mrp' => $item->mrp ?? 0,
                    'cgst_percent' => $item->cgst_percent ?? 0,
                    'sgst_percent' => $item->sgst_percent ?? 0,
                    'cess_percent' => $item->cess_percent ?? 0,
                    'unit' => $item->unit ?? '1',
                    'case_qty' => $item->case_qty ?? 0,
                    'box_qty' => $item->box_qty ?? 0,
                    'qty' => $totalQty,
                    'available_qty' => $totalQty,
                ];
            });
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'has_more' => $paginator->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Sale Challan Store Request', [
                'data' => $request->all()
            ]);
            
            // Validate transaction date (no backdating, max 1 day future)
            $dateError = $this->validateTransactionDate($request, 'sale_challan', 'date');
            if ($dateError) {
                return $this->dateValidationErrorResponse($dateError);
            }
            
            $validated = $request->validate([
                'date' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'challan_no' => 'required|string|max:100',
                'items' => 'required|array|min:1',
                'items.*.qty' => 'required|numeric|min:0',
                'items.*.rate' => 'required|numeric|min:0',
            ]);
            
            Log::info('Validation passed');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $itemsData = $request->input('items');
            
            // Auto-generate unique challan number
            $challanNo = $this->generateChallanNo();
            
            // Create Master Record
            $transaction = SaleChallanTransaction::create([
                'challan_no' => $challanNo,
                'series' => $request->input('series', 'SC'),
                'challan_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'customer_id' => $request->input('customer_id'),
                'salesman_id' => $request->input('salesman_id'),
                'remarks' => $request->input('remarks'),
                
                // Summary amounts
                'nt_amount' => $request->input('nt_amount', 0),
                'sc_amount' => $request->input('sc_amount', 0),
                'ft_amount' => $request->input('ft_amount', 0),
                'dis_amount' => $request->input('dis_amount', 0),
                'scm_amount' => $request->input('scm_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'net_amount' => $request->input('net_amount', 0),
                'scm_percent' => $request->input('scm_percent', 0),
                'tcs_amount' => $request->input('tcs_amount', 0),
                'excise_amount' => $request->input('excise_amount', 0),
                
                'is_invoiced' => false,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);
            
            // Create Detail Records (Items)
            foreach ($itemsData as $index => $itemData) {
                Log::info('Processing challan item', [
                    'item_name' => $itemData['item_name'] ?? 'N/A',
                    'batch_id' => $itemData['batch_id'] ?? 'NULL',
                    'batch_no' => $itemData['batch'] ?? 'N/A',
                    'qty' => $itemData['qty'] ?? 0
                ]);
                
                $itemCode = $itemData['item_code'] ?? $itemData['code'] ?? '';
                
                // Try to find item from Item Master
                $item = null;
                if (!empty($itemCode)) {
                    $item = Item::where('bar_code', $itemCode)->first();
                    if (!$item) {
                        $item = Item::find($itemCode);
                    }
                }
                
                if (!$item && !empty($itemData['item_name'])) {
                    $item = Item::where('name', $itemData['item_name'])->first();
                }
                
                // Calculate item amounts
                $qty = floatval($itemData['qty']);
                $rate = floatval($itemData['rate']);
                $discountPercent = floatval($itemData['discount'] ?? 0);
                
                $amount = $qty * $rate;
                $discountAmount = $amount * ($discountPercent / 100);
                $amountAfterDiscount = $amount - $discountAmount;
                
                $cgstPercent = $item ? floatval($item->cgst_percent ?? 0) : 0;
                $sgstPercent = $item ? floatval($item->sgst_percent ?? 0) : 0;
                $cessPercent = $item ? floatval($item->cess_percent ?? 0) : 0;
                
                $cgstAmount = $amountAfterDiscount * ($cgstPercent / 100);
                $sgstAmount = $amountAfterDiscount * ($sgstPercent / 100);
                $cessAmount = $amountAfterDiscount * ($cessPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount + $cessAmount;
                
                $netAmount = $amountAfterDiscount + $taxAmount;
                
                // Parse expiry date - handle MM/YYYY or MM/YY format
                $expiryDate = null;
                if (!empty($itemData['expiry'])) {
                    $expiryStr = $itemData['expiry'];
                    // Try to parse MM/YYYY or MM/YY format
                    if (preg_match('/^(\d{1,2})\/(\d{2,4})$/', $expiryStr, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        if (strlen($year) == 2) {
                            $year = '20' . $year;
                        }
                        $expiryDate = $year . '-' . $month . '-01';
                    } elseif (strtotime($expiryStr)) {
                        // If it's already a valid date format
                        $expiryDate = date('Y-m-d', strtotime($expiryStr));
                    }
                }
                
                SaleChallanTransactionItem::create([
                    'sale_challan_transaction_id' => $transaction->id,
                    'item_id' => $item ? $item->id : null,
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => floatval($itemData['free_qty'] ?? 0),
                    'sale_rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? ($item ? $item->mrp : 0)),
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'net_amount' => $netAmount,
                    
                    // GST data
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cess_percent' => $cessPercent,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'cess_amount' => $cessAmount,
                    
                    'row_order' => $itemData['row_order'] ?? $index,
                ]);
                
                // REDUCE BATCH QUANTITY - Challan deducts stock immediately
                $batchId = null;
                
                if (isset($itemData['batch_id']) && !empty($itemData['batch_id'])) {
                    $batchId = is_numeric($itemData['batch_id']) ? (int)$itemData['batch_id'] : null;
                }
                
                if (!$batchId && !empty($itemData['batch']) && $item) {
                    $batch = Batch::where('batch_no', $itemData['batch'])
                        ->where('item_id', $item->id)
                        ->where('is_deleted', 0)
                        ->where('total_qty', '>', 0)
                        ->first();
                    
                    if ($batch) {
                        $batchId = $batch->id;
                    }
                }
                
                if ($batchId) {
                    try {
                        $batch = Batch::find($batchId);
                        
                        if ($batch) {
                            $soldQty = floatval($qty);
                            
                            Log::info('Reducing batch quantity for challan', [
                                'batch_id' => $batchId,
                                'batch_no' => $batch->batch_no,
                                'current_total_qty' => $batch->total_qty,
                                'sold_qty' => $soldQty
                            ]);
                            
                            $oldTotalQty = $batch->total_qty;
                            $oldQty = $batch->qty;
                            
                            $batch->total_qty = $batch->total_qty - $soldQty;
                            $batch->qty = $batch->qty - $soldQty;
                            
                            $saved = $batch->save();
                            
                            if ($saved) {
                                Log::info('✅ Batch quantity reduced for challan', [
                                    'batch_id' => $batchId,
                                    'old_total_qty' => $oldTotalQty,
                                    'new_total_qty' => $batch->total_qty,
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error reducing batch quantity for challan', [
                            'batch_id' => $batchId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale challan saved successfully. Stock has been deducted.',
                'challan_no' => $challanNo,
                'id' => $transaction->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Challan Save Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale challan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show Sale Challan Modification page
     */
    public function modification()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        
        return view('admin.sale-challan.modification', compact('customers', 'salesmen'));
    }
    
    /**
     * Get list of challans for modification
     */
    public function getChallans(Request $request)
    {
        try {
            $query = SaleChallanTransaction::with('customer')
                ->orderBy('challan_date', 'desc')
                ->orderBy('challan_no', 'desc');
            
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('challan_date', [$request->input('from_date'), $request->input('to_date')]);
            }
            
            if ($request->has('invoiced_only') && $request->input('invoiced_only') == 'yes') {
                $query->where('is_invoiced', true);
            } elseif ($request->has('invoiced_only') && $request->input('invoiced_only') == 'no') {
                $query->where('is_invoiced', false);
            }
            
            $transactions = $query->limit(100)->get();
            
            $challans = $transactions->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'challan_no' => $transaction->challan_no,
                    'challan_date' => $transaction->challan_date->format('d-m-Y'),
                    'customer_name' => $transaction->customer ? $transaction->customer->name : 'N/A',
                    'net_amount' => $transaction->net_amount,
                    'is_invoiced' => $transaction->is_invoiced,
                    'status' => $transaction->status,
                    'status_badge' => $transaction->is_invoiced 
                        ? '<span class="badge bg-success">Invoiced</span>' 
                        : '<span class="badge bg-warning">Pending</span>',
                ];
            });
            
            return response()->json([
                'success' => true,
                'challans' => $challans
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching challans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching challans: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search challan by challan number
     */
    public function searchByChallanNo(Request $request)
    {
        try {
            $challanNo = $request->input('challan_no');
            
            if (!$challanNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challan number is required'
                ], 400);
            }
            
            $transaction = SaleChallanTransaction::with(['items', 'customer', 'salesman'])
                ->where('challan_no', $challanNo)
                ->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challan not found: ' . $challanNo
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'challan_no' => $transaction->challan_no,
                    'series' => $transaction->series,
                    'challan_date' => $transaction->challan_date->format('Y-m-d'),
                    'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                    'customer_id' => $transaction->customer_id,
                    'salesman_id' => $transaction->salesman_id,
                    'remarks' => $transaction->remarks,
                    'nt_amount' => $transaction->nt_amount,
                    'sc_amount' => $transaction->sc_amount,
                    'ft_amount' => $transaction->ft_amount,
                    'dis_amount' => $transaction->dis_amount,
                    'scm_amount' => $transaction->scm_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'net_amount' => $transaction->net_amount,
                    'scm_percent' => $transaction->scm_percent,
                    'is_invoiced' => $transaction->is_invoiced,
                    'items' => $transaction->items->map(function($item) {
                        return [
                            'item_id' => $item->item_id,
                            'item_code' => $item->item ? $item->item->bar_code : '',
                            'item_name' => $item->item ? $item->item->name : '',
                            'batch_no' => $item->batch_no,
                            'batch_id' => $item->batch_id,
                            'expiry_date' => $item->expiry_date,
                            'qty' => $item->qty,
                            'free_qty' => $item->free_qty,
                            'sale_rate' => $item->sale_rate,
                            'discount_percent' => $item->discount_percent,
                            'mrp' => $item->mrp,
                            'net_amount' => $item->net_amount,
                            'cgst_percent' => $item->cgst_percent,
                            'sgst_percent' => $item->sgst_percent,
                            'cess_percent' => $item->cess_percent,
                            'hsn_code' => $item->item ? $item->item->hsn_code : '',
                            'packing' => $item->item ? $item->item->packing : '',
                            'unit' => $item->item ? $item->item->unit : '',
                            'company_name' => $item->item ? $item->item->company_short_name : '',
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching by challan number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching challan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get single challan details for modification
     */
    public function getChallan($id)
    {
        try {
            $transaction = SaleChallanTransaction::with(['items.item', 'customer', 'salesman'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'challan_no' => $transaction->challan_no,
                    'series' => $transaction->series,
                    'challan_date' => $transaction->challan_date->format('Y-m-d'),
                    'due_date' => $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null,
                    'customer_id' => $transaction->customer_id,
                    'customer_name' => $transaction->customer ? $transaction->customer->name : '',
                    'salesman_id' => $transaction->salesman_id,
                    'salesman_name' => $transaction->salesman ? $transaction->salesman->name : '',
                    'remarks' => $transaction->remarks,
                    'nt_amount' => $transaction->nt_amount,
                    'sc_amount' => $transaction->sc_amount,
                    'ft_amount' => $transaction->ft_amount,
                    'dis_amount' => $transaction->dis_amount,
                    'scm_amount' => $transaction->scm_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'net_amount' => $transaction->net_amount,
                    'scm_percent' => $transaction->scm_percent,
                    'is_invoiced' => $transaction->is_invoiced,
                    'items' => $transaction->items->map(function($item) {
                        // Calculate amount = qty * rate
                        $qty = floatval($item->qty);
                        $rate = floatval($item->sale_rate);
                        $discount = floatval($item->discount_percent);
                        $amount = $qty * $rate;
                        $discountAmount = $amount * ($discount / 100);
                        $netAmount = $amount - $discountAmount;
                        
                        return [
                            'item_id' => $item->item_id,
                            'item_code' => $item->item ? $item->item->bar_code : '',
                            'item_name' => $item->item ? $item->item->name : '',
                            'batch_no' => $item->batch_no,
                            'batch_id' => $item->batch_id,
                            'expiry_date' => $item->expiry_date,
                            'qty' => $qty,
                            'free_qty' => $item->free_qty,
                            'sale_rate' => $rate,
                            'discount_percent' => $discount,
                            'mrp' => $item->mrp,
                            'amount' => round($amount, 2),
                            'net_amount' => round($netAmount, 2),
                            'cgst_percent' => $item->cgst_percent,
                            'sgst_percent' => $item->sgst_percent,
                            'cess_percent' => $item->cess_percent,
                            'hsn_code' => $item->item ? $item->item->hsn_code : '',
                            'packing' => $item->item ? $item->item->packing : '',
                            'unit' => $item->item ? $item->item->unit : '',
                            'company_name' => $item->item ? $item->item->company_short_name : '',
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching challan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching challan details'
            ], 500);
        }
    }
    
    /**
     * Update Sale Challan
     */
    public function updateChallan(Request $request, $id)
    {
        try {
            $transaction = SaleChallanTransaction::findOrFail($id);
            
            // Check if already invoiced
            if ($transaction->is_invoiced) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify an invoiced challan'
                ], 400);
            }
            
            $validated = $request->validate([
                'date' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
            ]);
            
            DB::beginTransaction();
            
            // Get old items to restore batch quantities
            $oldItems = $transaction->items()->get();
            
            // Restore batch quantities for old items
            foreach ($oldItems as $oldItem) {
                if ($oldItem->batch_id) {
                    $batch = Batch::find($oldItem->batch_id);
                    if ($batch) {
                        $restoredQty = floatval($oldItem->qty);
                        $batch->total_qty = $batch->total_qty + $restoredQty;
                        $batch->qty = $batch->qty + $restoredQty;
                        $batch->save();
                        
                        Log::info('✅ Restored batch quantity for old challan item', [
                            'batch_id' => $batch->id,
                            'restored_qty' => $restoredQty,
                            'new_total_qty' => $batch->total_qty
                        ]);
                    }
                }
            }
            
            // Delete old items
            $transaction->items()->delete();
            
            // Update master record
            $transaction->update([
                'series' => $request->input('series', 'SC'),
                'challan_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'customer_id' => $request->input('customer_id'),
                'salesman_id' => $request->input('salesman_id'),
                'remarks' => $request->input('remarks'),
                'nt_amount' => $request->input('nt_amount', 0),
                'sc_amount' => $request->input('sc_amount', 0),
                'ft_amount' => $request->input('ft_amount', 0),
                'dis_amount' => $request->input('dis_amount', 0),
                'scm_amount' => $request->input('scm_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'net_amount' => $request->input('net_amount', 0),
                'scm_percent' => $request->input('scm_percent', 0),
                'updated_by' => Auth::id(),
            ]);
            
            // Create new items
            foreach ($request->input('items') as $index => $itemData) {
                $itemCode = $itemData['item_code'] ?? '';
                
                $item = null;
                if (!empty($itemCode)) {
                    $item = Item::where('bar_code', $itemCode)->first();
                    if (!$item) {
                        $item = Item::find($itemCode);
                    }
                }
                
                if (!$item && !empty($itemData['item_name'])) {
                    $item = Item::where('name', $itemData['item_name'])->first();
                }
                
                $qty = floatval($itemData['qty']);
                $rate = floatval($itemData['rate']);
                $discountPercent = floatval($itemData['discount'] ?? 0);
                
                $amount = $qty * $rate;
                $discountAmount = $amount * ($discountPercent / 100);
                $amountAfterDiscount = $amount - $discountAmount;
                
                $cgstPercent = $item ? floatval($item->cgst_percent ?? 0) : 0;
                $sgstPercent = $item ? floatval($item->sgst_percent ?? 0) : 0;
                $cessPercent = $item ? floatval($item->cess_percent ?? 0) : 0;
                
                $cgstAmount = $amountAfterDiscount * ($cgstPercent / 100);
                $sgstAmount = $amountAfterDiscount * ($sgstPercent / 100);
                $cessAmount = $amountAfterDiscount * ($cessPercent / 100);
                $taxAmount = $cgstAmount + $sgstAmount + $cessAmount;
                
                $netAmount = $amountAfterDiscount + $taxAmount;
                
                // Reduce batch quantity
                $batchId = $itemData['batch_id'] ?? null;
                if ($batchId) {
                    $batch = Batch::find($batchId);
                    if ($batch) {
                        $soldQty = floatval($qty);
                        $batch->total_qty = $batch->total_qty - $soldQty;
                        $batch->qty = $batch->qty - $soldQty;
                        $batch->save();
                        
                        Log::info('✅ Batch quantity reduced for updated challan', [
                            'batch_id' => $batchId,
                            'sold_qty' => $soldQty,
                            'new_total_qty' => $batch->total_qty
                        ]);
                    }
                }
                
                // Parse expiry date - handle MM/YYYY or MM/YY format
                $expiryDate = null;
                if (!empty($itemData['expiry'])) {
                    $expiryStr = $itemData['expiry'];
                    if (preg_match('/^(\d{1,2})\/(\d{2,4})$/', $expiryStr, $matches)) {
                        $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $year = $matches[2];
                        if (strlen($year) == 2) {
                            $year = '20' . $year;
                        }
                        $expiryDate = $year . '-' . $month . '-01';
                    } elseif (strtotime($expiryStr)) {
                        $expiryDate = date('Y-m-d', strtotime($expiryStr));
                    }
                }
                
                SaleChallanTransactionItem::create([
                    'sale_challan_transaction_id' => $transaction->id,
                    'item_id' => $item ? $item->id : null,
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $expiryDate,
                    'qty' => $qty,
                    'free_qty' => floatval($itemData['free_qty'] ?? 0),
                    'sale_rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? ($item ? $item->mrp : 0)),
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'net_amount' => $netAmount,
                    'cgst_percent' => $cgstPercent,
                    'sgst_percent' => $sgstPercent,
                    'cess_percent' => $cessPercent,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'cess_amount' => $cessAmount,
                    'row_order' => $itemData['row_order'] ?? $index,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale challan updated successfully',
                'challan_no' => $transaction->challan_no,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating challan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating challan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next challan number (API endpoint)
     */
    public function getNextChallanNo()
    {
        try {
            $nextChallanNo = $this->generateChallanNo();
            
            return response()->json([
                'success' => true,
                'next_challan_no' => $nextChallanNo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating challan number'
            ], 500);
        }
    }

    /**
     * Generate next challan number (per organization)
     */
    private function generateChallanNo()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        $lastTransaction = SaleChallanTransaction::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastTransaction ? (intval(preg_replace('/[^0-9]/', '', $lastTransaction->challan_no)) + 1) : 1;
        return 'SCH-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Display list of all sale challan invoices with filters
     */
    public function invoices(Request $request)
    {
        $query = SaleChallanTransaction::with(['customer', 'salesman'])
            ->orderBy('challan_date', 'desc')
            ->orderBy('challan_no', 'desc');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filterBy = $request->input('filter_by', 'customer_name');
            
            switch ($filterBy) {
                case 'customer_name':
                    $query->whereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                    break;
                case 'challan_no':
                    $query->where('challan_no', 'like', '%' . $search . '%');
                    break;
                case 'salesman_name':
                    $query->whereHas('salesman', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                    break;
                case 'challan_amount':
                    $query->where('net_amount', '>=', floatval($search));
                    break;
            }
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('challan_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('challan_date', '<=', $request->input('date_to'));
        }
        
        // Invoiced status filter
        if ($request->filled('invoiced_status')) {
            if ($request->input('invoiced_status') === 'yes') {
                $query->where('is_invoiced', true);
            } elseif ($request->input('invoiced_status') === 'no') {
                $query->where('is_invoiced', false);
            }
        }
        
        $challans = $query->paginate(10);
        
        return view('admin.sale-challan.invoices', compact('challans'));
    }

    /**
     * Display detailed view of a sale challan
     */
    public function show($id)
    {
        $transaction = SaleChallanTransaction::with(['items.item', 'customer', 'salesman'])->findOrFail($id);
        
        return view('admin.sale-challan.show', compact('transaction'));
    }

    /**
     * Delete a sale challan
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $transaction = SaleChallanTransaction::findOrFail($id);
            
            // Check if already invoiced
            if ($transaction->is_invoiced) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete an invoiced challan'
                ], 400);
            }
            
            // Restore batch quantities before deleting
            foreach ($transaction->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $batch->total_qty += $item->qty;
                        $batch->qty += $item->qty;
                        $batch->save();
                        
                        Log::info('Batch quantity restored for deleted challan', [
                            'batch_id' => $batch->id,
                            'restored_qty' => $item->qty,
                            'new_total_qty' => $batch->total_qty
                        ]);
                    }
                }
            }
            
            // Delete items first
            $transaction->items()->delete();
            
            // Delete transaction
            $transaction->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale challan deleted successfully. Stock has been restored.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Challan Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sale challan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending challans (not yet invoiced) for a customer
     */
    public function getPendingChallans(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            
            $query = SaleChallanTransaction::with(['customer', 'items.item'])
                ->where('is_invoiced', false)
                ->orderBy('challan_date', 'desc');
            
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            
            $challans = $query->get();
            
            // Transform data for frontend
            $transformedChallans = $challans->map(function($challan) {
                return [
                    'id' => $challan->id,
                    'challan_no' => $challan->challan_no,
                    'challan_date' => $challan->challan_date,
                    'net_amount' => $challan->net_amount,
                    'items_count' => $challan->items->count(),
                    'item_names' => $challan->items->take(3)->map(function($item) {
                        return $item->item ? $item->item->name : 'Unknown';
                    })->implode(', ') . ($challan->items->count() > 3 ? ' +' . ($challan->items->count() - 3) . ' more' : '')
                ];
            });
            
            return response()->json([
                'success' => true,
                'challans' => $transformedChallans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pending challans'
            ], 500);
        }
    }

    /**
     * Get customer's total outstanding due amount from pending challans
     */
    public function getCustomerDue($customerId)
    {
        try {
            // Get total due from pending (non-invoiced) challans
            $totalDue = SaleChallanTransaction::where('customer_id', $customerId)
                ->where('is_invoiced', false)
                ->sum('net_amount');
            
            // Get count of pending challans
            $pendingCount = SaleChallanTransaction::where('customer_id', $customerId)
                ->where('is_invoiced', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'due_amount' => $totalDue ?? 0,
                'formatted_due' => number_format($totalDue ?? 0, 2),
                'pending_count' => $pendingCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching customer due: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer due'
            ], 500);
        }
    }
}
