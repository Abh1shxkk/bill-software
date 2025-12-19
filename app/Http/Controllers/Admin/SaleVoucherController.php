<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\Customer;
use App\Models\SalesMan;
use App\Models\HsnCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleVoucherController extends Controller
{
    /**
     * Display a listing of vouchers.
     */
    public function index(Request $request)
    {
        $query = SaleTransaction::with(['customer', 'salesman', 'items'])
            ->where('voucher_type', 'voucher');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        $vouchers = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.sale-voucher.index', compact('vouchers'));
    }

    /**
     * Show the transaction form.
     */
    public function transaction()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        // Get next invoice number - same format as Sale module (INV-XXXXXX)
        $nextInvoiceNo = $this->generateInvoiceNo();

        return view('admin.sale-voucher.transaction', compact(
            'customers', 'salesmen', 'hsnCodes', 'nextInvoiceNo'
        ));
    }

    /**
     * Generate next invoice number matching Sale module format
     */
    private function generateInvoiceNo()
    {
        // Only consider invoices with proper INV-XXXXXX format
        $lastTransaction = SaleTransaction::where('invoice_no', 'LIKE', 'INV-%')
            ->orderByRaw('CAST(SUBSTRING(invoice_no, 5) AS UNSIGNED) DESC')
            ->first();
        $nextNumber = $lastTransaction ? (intval(preg_replace('/[^0-9]/', '', $lastTransaction->invoice_no)) + 1) : 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created voucher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date',
            'customer_id' => 'required|exists:customers,id',
            'salesman_id' => 'nullable|exists:sales_men,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.hsn_code' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $grossAmount = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalTax = 0;
            $netAmount = 0;

            foreach ($request->items as $item) {
                $grossAmount += floatval($item['gross_amount'] ?? $item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }

            $totalTax = $totalCgst + $totalSgst;
            $netAmount = $grossAmount + $totalTax;

            // Round off
            $roundOff = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            // Get next invoice number - same format as Sale module (INV-XXXXXX)
            $invoiceNo = $this->generateInvoiceNo();

            // Create voucher transaction with same format as Sale
            $voucher = SaleTransaction::create([
                'invoice_no' => $invoiceNo,
                'series' => 'S2',
                'voucher_type' => 'voucher',
                'sale_date' => $validated['sale_date'],
                'due_date' => $validated['due_date'] ?? $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
                'salesman_id' => $validated['salesman_id'],
                'remarks' => $validated['remarks'],
                'nt_amount' => $grossAmount,
                'ft_amount' => $grossAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $netAmount,
                'balance_amount' => $netAmount,
                'payment_status' => 'unpaid',
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Create voucher items
            $rowOrder = 1;
            foreach ($request->items as $item) {
                SaleTransactionItem::create([
                    'sale_transaction_id' => $voucher->id,
                    'hsn_code' => $item['hsn_code'],
                    'hsn_description' => $item['hsn_description'] ?? null,
                    'qty' => $item['qty'] ?? 1,
                    'amount' => $item['amount'] ?? 0,
                    'gross_amount' => $item['gross_amount'] ?? $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'tax_amount' => ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'net_amount' => ($item['gross_amount'] ?? $item['amount'] ?? 0) + ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale Voucher saved successfully',
                'voucher_id' => $voucher->id,
                'invoice_no' => $voucher->invoice_no
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all HSN codes for modal.
     */
    public function getHsnCodes()
    {
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        return response()->json([
            'success' => true,
            'hsn_codes' => $hsnCodes
        ]);
    }

    /**
     * Show modification page.
     */
    public function modification()
    {
        $customers = Customer::orderBy('name')->get();
        $salesmen = SalesMan::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('hsn_code')->get();
        
        return view('admin.sale-voucher.modification', compact(
            'customers', 'salesmen', 'hsnCodes'
        ));
    }

    /**
     * Get vouchers list for modal.
     */
    public function getVouchers()
    {
        $vouchers = SaleTransaction::with('customer')
            ->where('voucher_type', 'voucher')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(function($v) {
                return [
                    'id' => $v->id,
                    'invoice_no' => $v->invoice_no,
                    'sale_date' => $v->sale_date ? $v->sale_date->format('d/m/Y') : '',
                    'customer_name' => $v->customer?->name ?? '-',
                    'net_amount' => $v->net_amount,
                ];
            });

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers
        ]);
    }

    /**
     * Get voucher details.
     */
    public function getDetails($id)
    {
        try {
            $voucher = SaleTransaction::with('items')
                ->where('voucher_type', 'voucher')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'voucher' => [
                    'id' => $voucher->id,
                    'invoice_no' => $voucher->invoice_no,
                    'sale_date' => $voucher->sale_date ? $voucher->sale_date->format('Y-m-d') : '',
                    'due_date' => $voucher->due_date ? $voucher->due_date->format('Y-m-d') : '',
                    'customer_id' => $voucher->customer_id,
                    'salesman_id' => $voucher->salesman_id,
                    'remarks' => $voucher->remarks,
                    'cash_flag' => $voucher->cash_flag,
                    'items' => $voucher->items->map(function($item) {
                        return [
                            'hsn_code' => $item->hsn_code,
                            'amount' => $item->amount,
                            'gst_percent' => $item->gst_percent,
                            'cgst_percent' => $item->cgst_percent,
                            'cgst_amount' => $item->cgst_amount,
                            'sgst_percent' => $item->sgst_percent,
                            'sgst_amount' => $item->sgst_amount,
                            'qty' => $item->qty,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found'
            ], 404);
        }
    }

    /**
     * Search voucher by invoice number.
     */
    public function searchVoucher(Request $request)
    {
        $invoiceNo = $request->input('invoice_no');
        
        $voucher = SaleTransaction::with('items')
            ->where('voucher_type', 'voucher')
            ->where('invoice_no', $invoiceNo)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'invoice_no' => $voucher->invoice_no,
                'sale_date' => $voucher->sale_date ? $voucher->sale_date->format('Y-m-d') : '',
                'due_date' => $voucher->due_date ? $voucher->due_date->format('Y-m-d') : '',
                'customer_id' => $voucher->customer_id,
                'salesman_id' => $voucher->salesman_id,
                'remarks' => $voucher->remarks,
                'cash_flag' => $voucher->cash_flag,
                'items' => $voucher->items->map(function($item) {
                    return [
                        'hsn_code' => $item->hsn_code,
                        'amount' => $item->amount,
                        'gst_percent' => $item->gst_percent,
                        'cgst_percent' => $item->cgst_percent,
                        'cgst_amount' => $item->cgst_amount,
                        'sgst_percent' => $item->sgst_percent,
                        'sgst_amount' => $item->sgst_amount,
                        'qty' => $item->qty,
                    ];
                })
            ]
        ]);
    }

    /**
     * Update a voucher.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date',
            'customer_id' => 'required|exists:customers,id',
            'salesman_id' => 'nullable|exists:sales_men,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $voucher = SaleTransaction::where('voucher_type', 'voucher')->findOrFail($id);

            // Calculate totals
            $grossAmount = 0;
            $totalCgst = 0;
            $totalSgst = 0;

            foreach ($request->items as $item) {
                $grossAmount += floatval($item['gross_amount'] ?? $item['amount'] ?? 0);
                $totalCgst += floatval($item['cgst_amount'] ?? 0);
                $totalSgst += floatval($item['sgst_amount'] ?? 0);
            }

            $totalTax = $totalCgst + $totalSgst;
            $netAmount = $grossAmount + $totalTax;
            $roundOff = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            // Update voucher
            $voucher->update([
                'sale_date' => $validated['sale_date'],
                'due_date' => $validated['due_date'] ?? $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
                'salesman_id' => $validated['salesman_id'],
                'remarks' => $validated['remarks'],
                'nt_amount' => $grossAmount,
                'ft_amount' => $grossAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $netAmount,
                'balance_amount' => $netAmount,
            ]);

            // Delete old items and create new
            $voucher->items()->delete();

            $rowOrder = 1;
            foreach ($request->items as $item) {
                SaleTransactionItem::create([
                    'sale_transaction_id' => $voucher->id,
                    'hsn_code' => $item['hsn_code'],
                    'qty' => $item['qty'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'gross_amount' => $item['gross_amount'] ?? $item['amount'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'cgst_percent' => $item['cgst_percent'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_percent' => $item['sgst_percent'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'tax_amount' => ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'net_amount' => ($item['gross_amount'] ?? $item['amount'] ?? 0) + ($item['cgst_amount'] ?? 0) + ($item['sgst_amount'] ?? 0),
                    'row_order' => $rowOrder++,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = SaleTransaction::where('voucher_type', 'voucher')->findOrFail($id);
            $voucher->items()->delete();
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voucher deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}
