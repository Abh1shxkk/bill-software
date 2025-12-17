<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Item;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::orderBy('quotation_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);
        
        return view('admin.quotation.index', compact('quotations'));
    }

    public function transaction()
    {
        $items = Item::all();
        $customers = Customer::where('is_deleted', '!=', 1)->orderBy('name')->get();
        $nextQuotationNo = $this->generateQuotationNo();
        
        return view('admin.quotation.transaction', compact('items', 'customers', 'nextQuotationNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            $itemsData = $request->input('items');
            $quotationNo = $this->generateQuotationNo();
            
            // Calculate totals
            $totalAmount = 0;
            foreach ($itemsData as $itemData) {
                $qty = floatval($itemData['qty'] ?? 0);
                $rate = floatval($itemData['rate'] ?? 0);
                $totalAmount += $qty * $rate;
            }
            
            $discountPercent = floatval($request->input('discount_percent', 0));
            $netAmount = $totalAmount * (1 - $discountPercent / 100);
            
            $quotation = Quotation::create([
                'quotation_no' => $quotationNo,
                'series' => $request->input('series', 'QT'),
                'quotation_date' => $request->input('date'),
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name'),
                'discount_percent' => $discountPercent,
                'remarks' => $request->input('remarks'),
                'terms' => $request->input('terms'),
                'net_amount' => $netAmount,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);
            
            foreach ($itemsData as $index => $itemData) {
                $qty = floatval($itemData['qty'] ?? 0);
                $rate = floatval($itemData['rate'] ?? 0);
                $amount = $qty * $rate;
                
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'item_code' => $itemData['code'] ?? '',
                    'item_name' => $itemData['item_name'] ?? '',
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $itemData['expiry'] ?? null,
                    'packing' => $itemData['packing'] ?? null,
                    'company_name' => $itemData['company_name'] ?? null,
                    'location' => $itemData['location'] ?? null,
                    'qty' => $qty,
                    'rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? 0),
                    'amount' => $amount,
                    'unit' => $itemData['unit'] ?? null,
                    'row_order' => $index,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Quotation saved successfully',
                'quotation_no' => $quotationNo,
                'id' => $quotation->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation Save Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        return view('admin.quotation.show', compact('quotation'));
    }

    public function modification()
    {
        $customers = Customer::where(function($query) {
            $query->where('is_deleted', '!=', 1)
                  ->orWhereNull('is_deleted');
        })->orderBy('name')->get();
        return view('admin.quotation.modification', compact('customers'));
    }

    public function edit($id)
    {
        $quotation = Quotation::with(['items', 'customer'])->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json($quotation);
        }
        
        $items = Item::all();
        return view('admin.quotation.transaction', compact('quotation', 'items'));
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            $itemsData = $request->input('items');
            
            // Calculate totals
            $totalAmount = 0;
            foreach ($itemsData as $itemData) {
                $qty = floatval($itemData['qty'] ?? 0);
                $rate = floatval($itemData['rate'] ?? 0);
                $totalAmount += $qty * $rate;
            }
            
            $discountPercent = floatval($request->input('discount_percent', 0));
            $netAmount = $totalAmount * (1 - $discountPercent / 100);
            
            $quotation->update([
                'quotation_date' => $request->input('date'),
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name'),
                'discount_percent' => $discountPercent,
                'remarks' => $request->input('remarks'),
                'terms' => $request->input('terms'),
                'net_amount' => $netAmount,
                'updated_by' => Auth::id(),
            ]);
            
            // Delete old items and insert new
            $quotation->items()->delete();
            
            foreach ($itemsData as $index => $itemData) {
                $qty = floatval($itemData['qty'] ?? 0);
                $rate = floatval($itemData['rate'] ?? 0);
                $amount = $qty * $rate;
                
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'item_code' => $itemData['code'] ?? '',
                    'item_name' => $itemData['item_name'] ?? '',
                    'batch_no' => $itemData['batch'] ?? null,
                    'expiry_date' => $itemData['expiry'] ?? null,
                    'packing' => $itemData['packing'] ?? null,
                    'company_name' => $itemData['company_name'] ?? null,
                    'location' => $itemData['location'] ?? null,
                    'qty' => $qty,
                    'rate' => $rate,
                    'mrp' => floatval($itemData['mrp'] ?? 0),
                    'amount' => $amount,
                    'unit' => $itemData['unit'] ?? null,
                    'row_order' => $index,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Quotation updated successfully',
                'quotation_no' => $quotation->quotation_no,
                'id' => $quotation->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation Update Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update(['status' => 'cancelled', 'updated_by' => Auth::id()]);
        
        return response()->json([
            'success' => true,
            'message' => 'Quotation cancelled successfully'
        ]);
    }

    public function getItems()
    {
        try {
            // Get all items (quotation doesn't need stock check)
            $items = Item::select('id', 'name', 'bar_code', 'hsn_code', 'packing', 'company_short_name', 's_rate', 'mrp', 'unit')
                ->where(function($query) {
                    $query->where('is_deleted', 0)
                          ->orWhere('is_deleted', '0')
                          ->orWhereNull('is_deleted');
                })
                ->orderBy('name')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'bar_code' => $item->bar_code ?? '',
                        'packing' => $item->packing ?? '',
                        'company_name' => $item->company_short_name ?? '',
                        's_rate' => $item->s_rate ?? 0,
                        'mrp' => $item->mrp ?? 0,
                        'unit' => $item->unit ?? '1',
                    ];
                });
            
            return response()->json($items);
        } catch (\Exception $e) {
            \Log::error('QuotationController getItems error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getBatches($itemId)
    {
        try {
            // Only get available batches (total_qty > 0)
            $batches = \App\Models\Batch::where('item_id', $itemId)
                ->where('is_deleted', 0)
                ->where('total_qty', '>', 0)  // Only available batches
                ->orderBy('expiry_date', 'asc')
                ->get()
                ->map(function($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => $batch->expiry_date ? date('m/Y', strtotime($batch->expiry_date)) : null,
                        'total_qty' => $batch->total_qty ?? 0,
                        'mrp' => $batch->mrp ?? 0,
                        's_rate' => $batch->s_rate ?? 0,
                    ];
                });
            
            return response()->json($batches);
        } catch (\Exception $e) {
            Log::error('Error fetching batches: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function getQuotations(Request $request)
    {
        $query = Quotation::orderBy('quotation_date', 'desc')->orderBy('id', 'desc');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quotation_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        
        $quotations = $query->limit(100)->get();
        
        return response()->json($quotations->map(function($q) {
            return [
                'id' => $q->id,
                'quotation_no' => $q->quotation_no,
                'quotation_date' => $q->quotation_date->format('Y-m-d'),
                'customer_name' => $q->customer_name ?? '-',
                'net_amount' => $q->net_amount,
                'status' => $q->status,
            ];
        }));
    }

    private function generateQuotationNo()
    {
        $lastQuotation = Quotation::where('series', 'QT')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastQuotation) {
            $lastNo = intval(preg_replace('/[^0-9]/', '', $lastQuotation->quotation_no));
            $nextNo = $lastNo + 1;
        } else {
            $nextNo = 1;
        }
        
        return 'QT' . str_pad($nextNo, 6, '0', STR_PAD_LEFT);
    }
}
