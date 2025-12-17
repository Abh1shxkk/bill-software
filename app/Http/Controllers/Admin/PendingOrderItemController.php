<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingOrderItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PendingOrderItemController extends Controller
{
    public function index()
    {
        $items = PendingOrderItem::with('item')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.pending-order-item.index', compact('items'));
    }

    public function transaction()
    {
        return view('admin.pending-order-item.transaction');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'action_type' => 'required|in:I,D',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            $item = Item::find($request->item_id);
            
            $pendingOrderItem = PendingOrderItem::create([
                'item_id' => $request->item_id,
                'item_code' => $item->bar_code ?? $item->id,
                'item_name' => $item->name,
                'action_type' => $request->action_type,
                'quantity' => $request->quantity,
                'remarks' => $request->remarks,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item saved successfully',
                'data' => $pendingOrderItem
            ]);
        } catch (\Exception $e) {
            Log::error('Pending Order Item Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getItems()
    {
        try {
            $items = Item::select('id', 'name', 'bar_code', 'packing', 'company_short_name', 's_rate', 'mrp', 'unit')
                ->orderBy('name')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'bar_code' => $item->bar_code,
                        'packing' => $item->packing,
                        'company_name' => $item->company_short_name ?? 'N/A',
                        's_rate' => $item->s_rate ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'unit' => $item->unit ?? '1',
                    ];
                });
            
            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = PendingOrderItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }
}
