<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseTransactionItem;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LabelGenerationController extends Controller
{
    /**
     * From Purchase Invoice - Generate labels from purchase invoices
     */
    public function fromPurchaseInvoice(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();
        
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        if ($request->has('view') || $request->has('print')) {
            $query = PurchaseTransaction::with('supplier')
                ->whereBetween('bill_date', [$dateFrom, $dateTo]);
            
            // Series filter
            if ($request->filled('series') && $request->series != '') {
                $query->where('voucher_type', $request->series);
            }
            
            // Supplier filter
            if ($request->filled('supplier') && $request->supplier != '00' && $request->supplier != '') {
                $query->where('supplier_id', $request->supplier);
            }
            
            $reportData = $query->orderBy('bill_date', 'desc')->orderBy('id', 'desc')->get();

            if ($request->has('print')) {
                return view('admin.reports.label-generation.from-purchase-invoice-print', compact('reportData', 'dateFrom', 'dateTo'));
            }
        }

        return view('admin.reports.label-generation.from-purchase-invoice', compact('reportData', 'suppliers', 'dateFrom', 'dateTo'));
    }

    /**
     * From Batches - Generate labels from batches
     */
    public function fromBatches(Request $request)
    {
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();

        if ($request->has('print')) {
            // Get selected batches from request
            $selectedBatches = $request->input('batches', []);
            $quantities = $request->input('quantities', []);
            
            if (!empty($selectedBatches)) {
                $reportData = Batch::whereIn('id', $selectedBatches)
                    ->where('is_deleted', 0)
                    ->get()
                    ->map(function($batch) use ($quantities) {
                        $batch->print_qty = $quantities[$batch->id] ?? 1;
                        return $batch;
                    });
            }

            return view('admin.reports.label-generation.from-batches-print', compact('reportData'));
        }

        return view('admin.reports.label-generation.from-batches', compact('items', 'reportData'));
    }

    /**
     * Get batches for a specific item (AJAX)
     */
    public function getBatches(Request $request)
    {
        $itemId = $request->input('item_id');
        
        $batches = Batch::where('item_id', $itemId)
            ->where('is_deleted', 0)
            ->where('total_qty', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
        
        return response()->json(['batches' => $batches]);
    }

    /**
     * From Item - Generate labels/barcodes from items
     */
    public function fromItem(Request $request)
    {
        $items = Item::where('is_deleted', 0)->orderBy('name')->get();
        $reportData = collect();

        if ($request->has('print')) {
            // Get selected items from request
            $selectedItems = $request->input('items', []);
            $quantities = $request->input('quantities', []);
            
            if (!empty($selectedItems)) {
                $reportData = Item::whereIn('id', $selectedItems)
                    ->where('is_deleted', 0)
                    ->get()
                    ->map(function($item) use ($quantities) {
                        $item->print_qty = $quantities[$item->id] ?? 1;
                        return $item;
                    });
            }

            return view('admin.reports.label-generation.from-item-print', compact('reportData'));
        }

        return view('admin.reports.label-generation.from-item', compact('items', 'reportData'));
    }
}
