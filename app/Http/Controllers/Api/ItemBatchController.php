<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Unified Item & Batch API Controller
 * 
 * Provides API endpoints for the reusable Item and Batch selection modal components.
 * Key feature: Batch endpoint supports filtering by available stock only.
 */
class ItemBatchController extends Controller
{
    /**
     * Get all items for the current organization
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItems(Request $request)
    {
        try {
            $organizationId = auth()->user()->organization_id;
            
            // Pagination parameters
            $page = max(1, intval($request->get('page', 1)));
            $limit = min(100, max(10, intval($request->get('limit', 50)))); // 50 items per page, min 10, max 100
            $offset = ($page - 1) * $limit;
            
            $query = Item::where('organization_id', $organizationId)
                ->with(['company:id,name']);
            
            // Optional search filter
            if ($request->has('search') && strlen($request->search) >= 2) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('bar_code', 'like', "%{$search}%")
                      ->orWhere('hsn_code', 'like', "%{$search}%");
                });
            }
            
            // Get total count for pagination info
            $totalCount = $query->count();
            
            // Fetch paginated items
            $items = $query->orderBy('name')
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name ?? '',
                        'bar_code' => $item->bar_code ?? '',
                        'hsn_code' => $item->hsn_code ?? '',
                        'packing' => $item->packing ?? '',
                        'unit' => $item->unit ?? '',
                        'mrp' => floatval($item->mrp ?? 0),
                        's_rate' => floatval($item->s_rate ?? 0),
                        'pur_rate' => floatval($item->pur_rate ?? 0),
                        'ws_rate' => floatval($item->ws_rate ?? 0),
                        'spl_rate' => floatval($item->spl_rate ?? 0),
                        'cgst_percent' => floatval($item->cgst_percent ?? 0),
                        'sgst_percent' => floatval($item->sgst_percent ?? 0),
                        'cess_percent' => floatval($item->cess_percent ?? 0),
                        'company_id' => $item->company_id,
                        'company_name' => $item->company->name ?? '',
                        'closing_stock' => $item->batches()->sum('qty') ?? 0,
                        'location' => $item->location ?? '',
                        'case_qty' => $item->case_qty ?? 0,
                        'box_qty' => $item->box_qty ?? 0,
                    ];
                });
            
            $hasMore = ($offset + $items->count()) < $totalCount;
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'count' => $items->count(),
                'total' => $totalCount,
                'page' => $page,
                'limit' => $limit,
                'has_more' => $hasMore
            ]);
            
        } catch (\Exception $e) {
            Log::error('ItemBatchController@getItems error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading items',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get batches for a specific item
     * 
     * ⭐ KEY FEATURE: When 'available_only=1' is passed, only returns batches with qty > 0
     * 
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBatches(Request $request, $itemId)
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $availableOnly = $request->boolean('available_only', false);
            
            $query = Batch::where('organization_id', $organizationId)
                ->where('item_id', $itemId)
                ->with(['supplier:id,name']);
            
            // ⭐ FILTER: Only show batches with available stock when requested
            if ($availableOnly) {
                $query->where('qty', '>', 0);
            }
            
            $batches = $query->orderByDesc('created_at')
                ->get()
                ->map(function ($batch) {
                    // Format expiry date for display (MM/YY)
                    $expiryDisplay = 'N/A';
                    if ($batch->expiry_date) {
                        try {
                            $expiryDisplay = date('m/y', strtotime($batch->expiry_date));
                        } catch (\Exception $e) {
                            $expiryDisplay = $batch->expiry_date;
                        }
                    }
                    
                    // Format purchase date for display (DD-MM-YY)
                    $purchaseDateDisplay = 'N/A';
                    if ($batch->purchase_date) {
                        try {
                            $purchaseDateDisplay = date('d-m-y', strtotime($batch->purchase_date));
                        } catch (\Exception $e) {
                            $purchaseDateDisplay = $batch->purchase_date;
                        }
                    }
                    
                    return [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'bar_code' => $batch->bar_code,
                        
                        // Quantity
                        'qty' => $batch->qty,
                        'available_qty' => $batch->qty,
                        
                        // Prices
                        'mrp' => $batch->mrp,
                        'avg_mrp' => $batch->avg_mrp ?? $batch->mrp,
                        's_rate' => $batch->s_rate,
                        'avg_s_rate' => $batch->avg_s_rate ?? $batch->s_rate,
                        'pur_rate' => $batch->pur_rate,
                        'avg_pur_rate' => $batch->avg_pur_rate ?? $batch->pur_rate,
                        'cost_gst' => $batch->cost_gst,
                        'avg_cost_gst' => $batch->avg_cost_gst ?? $batch->cost_gst,
                        
                        // Dates
                        'expiry_date' => $batch->expiry_date,
                        'expiry_display' => $expiryDisplay,
                        'purchase_date' => $batch->purchase_date,
                        'purchase_date_display' => $purchaseDateDisplay,
                        
                        // Supplier info
                        'supplier_id' => $batch->supplier_id,
                        'supplier_name' => $batch->supplier->name ?? '',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'batches' => $batches,
                'count' => $batches->count(),
                'item_id' => $itemId,
                'available_only' => $availableOnly
            ]);
            
        } catch (\Exception $e) {
            Log::error('ItemBatchController@getBatches error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading batches',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Search items with text query
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchItems(Request $request)
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $search = $request->get('q', '') ?: $request->get('search', '');
            $exact = $request->get('exact', false);
            
            if (strlen($search) < 1) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'message' => 'Search term required'
                ]);
            }
            
            $query = Item::where('organization_id', $organizationId);
            
            if ($exact) {
                // Exact barcode match
                $query->where('bar_code', $search);
            } else {
                // Fuzzy search
                if (strlen($search) < 2) {
                    return response()->json([
                        'success' => true,
                        'items' => [],
                        'message' => 'Search term must be at least 2 characters'
                    ]);
                }
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('bar_code', 'like', "%{$search}%")
                      ->orWhere('hsn_code', 'like', "%{$search}%");
                });
            }
            
            $items = $query->with(['company:id,name'])
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name ?? '',
                        'bar_code' => $item->bar_code ?? '',
                        'hsn_code' => $item->hsn_code ?? '',
                        'packing' => $item->packing ?? '',
                        'unit' => $item->unit ?? '',
                        'mrp' => floatval($item->mrp ?? 0),
                        's_rate' => floatval($item->s_rate ?? 0),
                        'pur_rate' => floatval($item->pur_rate ?? 0),
                        'ws_rate' => floatval($item->ws_rate ?? 0),
                        'spl_rate' => floatval($item->spl_rate ?? 0),
                        'cgst_percent' => floatval($item->cgst_percent ?? 0),
                        'sgst_percent' => floatval($item->sgst_percent ?? 0),
                        'cess_percent' => floatval($item->cess_percent ?? 0),
                        'company_id' => $item->company_id,
                        'company_name' => $item->company->name ?? '',
                        'closing_stock' => $item->batches()->sum('qty') ?? 0,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'count' => $items->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('ItemBatchController@searchItems error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching items'
            ], 500);
        }
    }
}
