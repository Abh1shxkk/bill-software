<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\SaleReturnTransaction;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseReturnTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GstReportController extends Controller
{
    /**
     * GSTR-3B / Form 3B Report
     */
    public function form3b(Request $request)
    {
        $reportData = [];
        $gstr = [];

        // Default to current month/year
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? date('Y-m-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        $reduceCustExpiry = $request->has('reduce_cust_expiry');
        $withUnregisterSupplier = $request->has('with_unregister_supplier');

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            if ($useDateRange) {
                $startDate = Carbon::parse($fromDate);
                $endDate = Carbon::parse($toDate);
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }

            // Get Sales Data (Outward Supplies)
            $sales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->selectRaw('
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            // Get Sales Returns
            $salesReturns = SaleReturnTransaction::whereBetween('return_date', [$startDate, $endDate])
                ->selectRaw('
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            // Get Purchases Data (Inward Supplies / ITC)
            $purchaseQuery = PurchaseTransaction::whereBetween('bill_date', [$startDate, $endDate]);
            
            // Filter unregistered suppliers if option selected
            if ($withUnregisterSupplier) {
                $purchaseQuery->whereHas('supplier', function($q) {
                    $q->where('registered_unregistered_composite', 'U');
                });
            }

            $purchases = $purchaseQuery->selectRaw('
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            // Get Purchase Returns
            $purchaseReturns = PurchaseReturnTransaction::whereBetween('return_date', [$startDate, $endDate])
                ->selectRaw('
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            // Calculate GSTR-3B values
            // Section 3.1 - Outward Supplies
            $outwardTaxable = ($sales->total_amount ?? 0) - ($salesReturns->total_amount ?? 0);
            $outwardTax = ($sales->total_tax ?? 0) - ($salesReturns->total_tax ?? 0);

            // Section 4 - Eligible ITC
            $itcAvailable = ($purchases->total_tax ?? 0) - ($purchaseReturns->total_tax ?? 0);

            // Assuming 50% split between CGST and SGST for local transactions
            $gstr = [
                // 3.1 Outward Supplies
                'outward_taxable' => $outwardTaxable,
                'outward_igst' => 0, // Interstate - would need state comparison
                'outward_cgst' => $outwardTax / 2,
                'outward_sgst' => $outwardTax / 2,
                'outward_cess' => 0,

                // 4. Eligible ITC
                'itc_igst' => 0,
                'itc_cgst' => $itcAvailable / 2,
                'itc_sgst' => $itcAvailable / 2,
                'itc_cess' => 0,

                // Net ITC
                'net_itc_igst' => 0,
                'net_itc_cgst' => $itcAvailable / 2,
                'net_itc_sgst' => $itcAvailable / 2,
                'net_itc_cess' => 0,

                // 6. Payment of Tax
                'payable_igst' => 0,
                'payable_cgst' => max(0, ($outwardTax / 2) - ($itcAvailable / 2)),
                'payable_sgst' => max(0, ($outwardTax / 2) - ($itcAvailable / 2)),
                'payable_cess' => 0,

                // Summary
                'total_sales' => $sales->total_amount ?? 0,
                'total_sales_return' => $salesReturns->total_amount ?? 0,
                'total_purchase' => $purchases->total_amount ?? 0,
                'total_purchase_return' => $purchaseReturns->total_amount ?? 0,
            ];

            $reportData = [
                'period' => $useDateRange 
                    ? Carbon::parse($fromDate)->format('d-M-Y') . ' to ' . Carbon::parse($toDate)->format('d-M-Y')
                    : Carbon::createFromDate($year, $month, 1)->format('F Y'),
                'gstr' => $gstr,
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.form-3b-print', compact('reportData', 'gstr'));
            }
        }

        return view('admin.reports.gst-reports.form-3b', compact(
            'reportData', 'gstr', 'month', 'year', 'useDateRange', 
            'fromDate', 'toDate', 'reduceCustExpiry', 'withUnregisterSupplier'
        ));
    }

    /**
     * GSTR-1 Report - Outward Supplies
     */
    public function gstr1(Request $request)
    {
        $reportData = [];
        $b2bData = collect();
        $b2cData = collect();
        $hsnSummary = collect();

        // Default values
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? date('Y-m-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        $hsn = $request->hsn ?? 'Full';
        $reduceCustExpiry = $request->has('reduce_cust_expiry');
        $addSupplierExpiry = $request->has('add_supplier_expiry');
        $reduceUnregisteredB2C = $request->has('reduce_unregistered_b2c');
        $zeroRatedRemoveB2B = $request->has('zero_rated_remove_b2b');
        $amendmentRow = $request->has('amendment_row');
        $newHsnSummary = $request->has('new_hsn_summary') || !$request->has('view');
        $invalidGstnList = $request->has('invalid_gstn_list');

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            if ($useDateRange) {
                $startDate = Carbon::parse($fromDate);
                $endDate = Carbon::parse($toDate);
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }

            // B2B Sales - Sales to registered dealers (with valid GST number)
            $b2bQuery = SaleTransaction::with(['customer'])
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->whereHas('customer', function($q) {
                    $q->whereNotNull('gst_number')
                      ->where('gst_number', '!=', '')
                      ->whereRaw("LENGTH(gst_number) = 15");
                });

            if ($zeroRatedRemoveB2B) {
                $b2bQuery->where('tax_amount', '>', 0);
            }

            $b2bSales = $b2bQuery->get();

            // B2C Sales - Sales to unregistered dealers (without valid GST number)
            $b2cQuery = SaleTransaction::with(['customer'])
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->whereNull('customer_id')
                      ->orWhereHas('customer', function($q2) {
                          $q2->where(function($q3) {
                              $q3->whereNull('gst_number')
                                 ->orWhere('gst_number', '')
                                 ->orWhereRaw("LENGTH(gst_number) != 15");
                          });
                      });
                });

            $b2cSales = $b2cQuery->get();

            // Process B2B Data
            $b2bData = $b2bSales->map(function($sale) {
                $taxableValue = ($sale->net_amount ?? 0) - ($sale->tax_amount ?? 0);
                $totalTax = $sale->tax_amount ?? 0;
                
                return [
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? 'N/A',
                    'gstin' => $sale->customer->gst_number ?? '',
                    'taxable_value' => $taxableValue,
                    'igst' => 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'cess' => 0,
                    'total_tax' => $totalTax,
                ];
            });

            // Process B2C Data
            $b2cData = $b2cSales->map(function($sale) {
                $taxableValue = ($sale->net_amount ?? 0) - ($sale->tax_amount ?? 0);
                $totalTax = $sale->tax_amount ?? 0;
                
                return [
                    'invoice_no' => $sale->invoice_no,
                    'invoice_date' => $sale->sale_date,
                    'customer_name' => $sale->customer->name ?? 'Cash',
                    'taxable_value' => $taxableValue,
                    'igst' => 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'cess' => 0,
                    'total_tax' => $totalTax,
                ];
            });

            // HSN Summary
            if ($newHsnSummary) {
                $hsnSummary = DB::table('sale_transaction_items as sti')
                    ->join('sale_transactions as st', 'sti.sale_transaction_id', '=', 'st.id')
                    ->join('items as i', 'sti.item_id', '=', 'i.id')
                    ->whereBetween('st.sale_date', [$startDate, $endDate])
                    ->select(
                        'i.hsn_code',
                        'i.name as item_name',
                        'i.unit',
                        DB::raw('SUM(COALESCE(sti.qty, 0)) as total_qty'),
                        DB::raw('SUM(COALESCE(sti.net_amount, 0)) as total_value'),
                        DB::raw('SUM(COALESCE(sti.tax_amount, 0)) as total_tax')
                    )
                    ->groupBy('i.hsn_code', 'i.name', 'i.unit')
                    ->get()
                    ->map(function($row) {
                        return [
                            'hsn_code' => $row->hsn_code ?? 'N/A',
                            'description' => $row->item_name ?? 'N/A',
                            'uqc' => $row->unit ?? 'NOS',
                            'total_qty' => $row->total_qty ?? 0,
                            'total_value' => $row->total_value ?? 0,
                            'taxable_value' => ($row->total_value ?? 0) - ($row->total_tax ?? 0),
                            'igst' => 0,
                            'cgst' => ($row->total_tax ?? 0) / 2,
                            'sgst' => ($row->total_tax ?? 0) / 2,
                            'cess' => 0,
                        ];
                    });
            }

            // Calculate totals
            $reportData = [
                'period' => $useDateRange 
                    ? Carbon::parse($fromDate)->format('d-M-Y') . ' to ' . Carbon::parse($toDate)->format('d-M-Y')
                    : Carbon::createFromDate($year, $month, 1)->format('F Y'),
                'b2b_count' => $b2bData->count(),
                'b2b_taxable' => $b2bData->sum('taxable_value'),
                'b2b_igst' => $b2bData->sum('igst'),
                'b2b_cgst' => $b2bData->sum('cgst'),
                'b2b_sgst' => $b2bData->sum('sgst'),
                'b2b_cess' => $b2bData->sum('cess'),
                'b2b_total_tax' => $b2bData->sum('total_tax'),
                'b2c_count' => $b2cData->count(),
                'b2c_taxable' => $b2cData->sum('taxable_value'),
                'b2c_igst' => $b2cData->sum('igst'),
                'b2c_cgst' => $b2cData->sum('cgst'),
                'b2c_sgst' => $b2cData->sum('sgst'),
                'b2c_cess' => $b2cData->sum('cess'),
                'b2c_total_tax' => $b2cData->sum('total_tax'),
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.gstr-1-print', compact(
                    'reportData', 'b2bData', 'b2cData', 'hsnSummary'
                ));
            }
        }

        return view('admin.reports.gst-reports.gstr-1', compact(
            'reportData', 'b2bData', 'b2cData', 'hsnSummary',
            'month', 'year', 'useDateRange', 'fromDate', 'toDate', 'hsn',
            'reduceCustExpiry', 'addSupplierExpiry', 'reduceUnregisteredB2C',
            'zeroRatedRemoveB2B', 'amendmentRow', 'newHsnSummary', 'invalidGstnList'
        ));
    }

    /**
     * GSTR-2 Report - Inward Supplies (Purchases)
     */
    public function gstr2(Request $request)
    {
        $reportData = [];
        $b2bData = collect();
        $b2bUnregData = collect();
        $hsnSummary = collect();

        // Default values
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? date('Y-m-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        $hsn = $request->hsn ?? 'Full';
        $removeExpiryB2B = $request->has('remove_expiry_b2b');
        $removeRCM = $request->has('remove_rcm');
        $addExpiryReturn = $request->has('add_expiry_return');

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            if ($useDateRange) {
                $startDate = Carbon::parse($fromDate)->startOfDay();
                $endDate = Carbon::parse($toDate)->endOfDay();
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }

            // B2B Purchases - From registered suppliers (with valid GST number)
            $b2bQuery = PurchaseTransaction::with(['supplier'])
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->whereHas('supplier', function($q) {
                    $q->whereNotNull('gst_no')
                      ->where('gst_no', '!=', '')
                      ->whereRaw("LENGTH(gst_no) = 15");
                });

            $b2bPurchases = $b2bQuery->get();

            // B2B Purchases - From unregistered suppliers (without valid GST number)
            $b2bUnregQuery = PurchaseTransaction::with(['supplier'])
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->whereNull('supplier_id')
                      ->orWhereHas('supplier', function($q2) {
                          $q2->where(function($q3) {
                              $q3->whereNull('gst_no')
                                 ->orWhere('gst_no', '')
                                 ->orWhereRaw("LENGTH(gst_no) != 15");
                          });
                      });
                });

            $b2bUnregPurchases = $b2bUnregQuery->get();

            // Process B2B Data (Registered)
            $b2bData = $b2bPurchases->map(function($purchase) {
                $taxableValue = ($purchase->net_amount ?? 0) - ($purchase->tax_amount ?? 0);
                $totalTax = $purchase->tax_amount ?? 0;
                
                return [
                    'invoice_no' => $purchase->bill_no,
                    'invoice_date' => $purchase->bill_date,
                    'supplier_name' => $purchase->supplier->name ?? 'N/A',
                    'gstin' => $purchase->supplier->gst_no ?? '',
                    'taxable_value' => $taxableValue,
                    'igst' => 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'cess' => 0,
                    'total_tax' => $totalTax,
                ];
            });

            // Process B2B Data (Unregistered)
            $b2bUnregData = $b2bUnregPurchases->map(function($purchase) {
                $taxableValue = ($purchase->net_amount ?? 0) - ($purchase->tax_amount ?? 0);
                $totalTax = $purchase->tax_amount ?? 0;
                
                return [
                    'invoice_no' => $purchase->bill_no,
                    'invoice_date' => $purchase->bill_date,
                    'supplier_name' => $purchase->supplier->name ?? 'N/A',
                    'taxable_value' => $taxableValue,
                    'igst' => 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'cess' => 0,
                    'total_tax' => $totalTax,
                ];
            });

            // Purchase Returns (Credit/Debit Notes from registered suppliers)
            $cdnRegQuery = PurchaseReturnTransaction::with(['supplier'])
                ->whereBetween('return_date', [$startDate, $endDate])
                ->whereHas('supplier', function($q) {
                    $q->whereNotNull('gst_no')
                      ->where('gst_no', '!=', '')
                      ->whereRaw("LENGTH(gst_no) = 15");
                });

            $cdnRegReturns = $cdnRegQuery->get();

            $cdnRegData = $cdnRegReturns->map(function($return) {
                $taxableValue = ($return->net_amount ?? 0) - ($return->tax_amount ?? 0);
                $totalTax = $return->tax_amount ?? 0;
                
                return [
                    'taxable_value' => $taxableValue,
                    'igst' => 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'cess' => 0,
                    'total_tax' => $totalTax,
                ];
            });

            // HSN Summary for Purchases
            $hsnSummary = DB::table('purchase_transaction_items as pti')
                ->join('purchase_transactions as pt', 'pti.purchase_transaction_id', '=', 'pt.id')
                ->join('items as i', 'pti.item_id', '=', 'i.id')
                ->whereBetween('pt.bill_date', [$startDate, $endDate])
                ->select(
                    'i.hsn_code',
                    'i.name as item_name',
                    'i.unit',
                    DB::raw('SUM(COALESCE(pti.qty, 0)) as total_qty'),
                    DB::raw('SUM(COALESCE(pti.net_amount, 0)) as total_value'),
                    DB::raw('SUM(COALESCE(pti.tax_amount, 0)) as total_tax')
                )
                ->groupBy('i.hsn_code', 'i.name', 'i.unit')
                ->get()
                ->map(function($row) {
                    return [
                        'hsn_code' => $row->hsn_code ?? 'N/A',
                        'description' => $row->item_name ?? 'N/A',
                        'uqc' => $row->unit ?? 'NOS',
                        'total_qty' => $row->total_qty ?? 0,
                        'total_value' => $row->total_value ?? 0,
                        'taxable_value' => ($row->total_value ?? 0) - ($row->total_tax ?? 0),
                        'igst' => 0,
                        'cgst' => ($row->total_tax ?? 0) / 2,
                        'sgst' => ($row->total_tax ?? 0) / 2,
                        'cess' => 0,
                    ];
                });

            // Nil Rated purchases
            $nilRatedPurchases = PurchaseTransaction::whereBetween('bill_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where('tax_amount', 0)
                      ->orWhereNull('tax_amount');
                })
                ->get();

            // Calculate totals
            $reportData = [
                'period' => $useDateRange 
                    ? Carbon::parse($fromDate)->format('d-M-Y') . ' to ' . Carbon::parse($toDate)->format('d-M-Y')
                    : Carbon::createFromDate($year, $month, 1)->format('F Y'),
                'b2b_count' => $b2bData->count(),
                'b2b_taxable' => $b2bData->sum('taxable_value'),
                'b2b_igst' => $b2bData->sum('igst'),
                'b2b_cgst' => $b2bData->sum('cgst'),
                'b2b_sgst' => $b2bData->sum('sgst'),
                'b2b_cess' => $b2bData->sum('cess'),
                'b2b_total_tax' => $b2bData->sum('total_tax'),
                'b2b_unreg_count' => $b2bUnregData->count(),
                'b2b_unreg_taxable' => $b2bUnregData->sum('taxable_value'),
                'b2b_unreg_igst' => $b2bUnregData->sum('igst'),
                'b2b_unreg_cgst' => $b2bUnregData->sum('cgst'),
                'b2b_unreg_sgst' => $b2bUnregData->sum('sgst'),
                'b2b_unreg_cess' => $b2bUnregData->sum('cess'),
                'b2b_unreg_total_tax' => $b2bUnregData->sum('total_tax'),
                'cdn_reg_count' => $cdnRegData->count(),
                'cdn_reg_taxable' => $cdnRegData->sum('taxable_value'),
                'cdn_reg_igst' => $cdnRegData->sum('igst'),
                'cdn_reg_cgst' => $cdnRegData->sum('cgst'),
                'cdn_reg_sgst' => $cdnRegData->sum('sgst'),
                'cdn_reg_cess' => $cdnRegData->sum('cess'),
                'cdn_reg_total_tax' => $cdnRegData->sum('total_tax'),
                'nil_count' => $nilRatedPurchases->count(),
                'nil_taxable' => $nilRatedPurchases->sum('net_amount'),
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.gstr-2-print', compact(
                    'reportData', 'b2bData', 'b2bUnregData', 'hsnSummary'
                ));
            }
        }

        return view('admin.reports.gst-reports.gstr-2', compact(
            'reportData', 'b2bData', 'b2bUnregData', 'hsnSummary',
            'month', 'year', 'useDateRange', 'fromDate', 'toDate', 'hsn',
            'removeExpiryB2B', 'removeRCM', 'addExpiryReturn'
        ));
    }

    /**
     * GSTR-4 Report - Composition Scheme (Quarterly)
     */
    public function gstr4(Request $request)
    {
        $reportData = [];

        // Default values
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? date('Y-m-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        $showHsn = $request->has('show_hsn');

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            if ($useDateRange) {
                $startDate = Carbon::parse($fromDate)->startOfDay();
                $endDate = Carbon::parse($toDate)->endOfDay();
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }

            // Outward Supplies (Sales) - For composition dealers
            $outwardSales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            $outwardTaxable = ($outwardSales->total_amount ?? 0) - ($outwardSales->total_tax ?? 0);
            $outwardTax = $outwardSales->total_tax ?? 0;

            // Inward Supplies under RCM (Purchases from unregistered dealers)
            $inwardRcm = PurchaseTransaction::with(['supplier'])
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->whereHas('supplier', function($q) {
                    $q->where(function($q2) {
                        $q2->whereNull('gst_no')
                           ->orWhere('gst_no', '')
                           ->orWhereRaw("LENGTH(gst_no) != 15");
                    });
                })
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            $inwardRcmTaxable = ($inwardRcm->total_amount ?? 0) - ($inwardRcm->total_tax ?? 0);
            $inwardRcmTax = $inwardRcm->total_tax ?? 0;

            // Calculate totals
            $totalTaxable = $outwardTaxable + $inwardRcmTaxable;
            $totalTax = $outwardTax + $inwardRcmTax;

            $reportData = [
                'period' => $useDateRange 
                    ? Carbon::parse($fromDate)->format('d-M-Y') . ' to ' . Carbon::parse($toDate)->format('d-M-Y')
                    : Carbon::createFromDate($year, $month, 1)->format('F Y'),
                'outward_count' => $outwardSales->count ?? 0,
                'outward_taxable' => $outwardTaxable,
                'outward_igst' => 0,
                'outward_cgst' => $outwardTax / 2,
                'outward_sgst' => $outwardTax / 2,
                'outward_cess' => 0,
                'outward_total_tax' => $outwardTax,
                'inward_rcm_count' => $inwardRcm->count ?? 0,
                'inward_rcm_taxable' => $inwardRcmTaxable,
                'inward_rcm_igst' => 0,
                'inward_rcm_cgst' => $inwardRcmTax / 2,
                'inward_rcm_sgst' => $inwardRcmTax / 2,
                'inward_rcm_cess' => 0,
                'inward_rcm_total_tax' => $inwardRcmTax,
                'total_taxable' => $totalTaxable,
                'total_igst' => 0,
                'total_cgst' => $totalTax / 2,
                'total_sgst' => $totalTax / 2,
                'total_cess' => 0,
                'total_tax' => $totalTax,
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.gstr-4-print', compact('reportData'));
            }
        }

        return view('admin.reports.gst-reports.gstr-4', compact(
            'reportData', 'month', 'year', 'useDateRange', 'fromDate', 'toDate', 'showHsn'
        ));
    }

    /**
     * GSTR-4 Annual Report - Composition Scheme (Annual)
     */
    public function gstr4Annual(Request $request)
    {
        $reportData = [];

        // Default values
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? date('Y-01-01');
        $toDate = $request->to_date ?? date('Y-12-31');

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            if ($useDateRange) {
                $startDate = Carbon::parse($fromDate)->startOfDay();
                $endDate = Carbon::parse($toDate)->endOfDay();
            } else {
                // For annual report, use full financial year (April to March)
                $startDate = Carbon::createFromDate($year, 4, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year + 1, 3, 31)->endOfMonth();
            }

            // Annual Turnover (All Sales)
            $annualSales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            $annualTaxable = ($annualSales->total_amount ?? 0) - ($annualSales->total_tax ?? 0);
            $annualTax = $annualSales->total_tax ?? 0;

            // Outward Supplies (Taxable Sales)
            $outwardSales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where('tax_amount', '>', 0);
                })
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            $outwardTaxable = ($outwardSales->total_amount ?? 0) - ($outwardSales->total_tax ?? 0);
            $outwardTax = $outwardSales->total_tax ?? 0;

            // Inward Supplies under RCM (Purchases from unregistered dealers)
            $inwardRcm = PurchaseTransaction::with(['supplier'])
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->whereHas('supplier', function($q) {
                    $q->where(function($q2) {
                        $q2->whereNull('gst_no')
                           ->orWhere('gst_no', '')
                           ->orWhereRaw("LENGTH(gst_no) != 15");
                    });
                })
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount,
                    SUM(COALESCE(tax_amount, 0)) as total_tax
                ')
                ->first();

            $inwardRcmTaxable = ($inwardRcm->total_amount ?? 0) - ($inwardRcm->total_tax ?? 0);
            $inwardRcmTax = $inwardRcm->total_tax ?? 0;

            // Exempt Supplies (Sales with zero tax)
            $exemptSales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where('tax_amount', 0)
                      ->orWhereNull('tax_amount');
                })
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount
                ')
                ->first();

            $exemptTaxable = $exemptSales->total_amount ?? 0;

            // Nil Rated Supplies (same as exempt for this context)
            $nilSales = SaleTransaction::whereBetween('sale_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where('tax_amount', 0)
                      ->orWhereNull('tax_amount');
                })
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(COALESCE(net_amount, 0)) as total_amount
                ')
                ->first();

            $nilTaxable = $nilSales->total_amount ?? 0;

            // Calculate totals
            $totalTaxable = $outwardTaxable + $inwardRcmTaxable;
            $totalTax = $outwardTax + $inwardRcmTax;

            $reportData = [
                'period' => $useDateRange 
                    ? Carbon::parse($fromDate)->format('d-M-Y') . ' to ' . Carbon::parse($toDate)->format('d-M-Y')
                    : 'FY ' . $year . '-' . ($year + 1),
                'annual_count' => $annualSales->count ?? 0,
                'annual_taxable' => $annualTaxable,
                'annual_igst' => 0,
                'annual_cgst' => $annualTax / 2,
                'annual_sgst' => $annualTax / 2,
                'annual_cess' => 0,
                'annual_total_tax' => $annualTax,
                'outward_count' => $outwardSales->count ?? 0,
                'outward_taxable' => $outwardTaxable,
                'outward_igst' => 0,
                'outward_cgst' => $outwardTax / 2,
                'outward_sgst' => $outwardTax / 2,
                'outward_cess' => 0,
                'outward_total_tax' => $outwardTax,
                'inward_rcm_count' => $inwardRcm->count ?? 0,
                'inward_rcm_taxable' => $inwardRcmTaxable,
                'inward_rcm_igst' => 0,
                'inward_rcm_cgst' => $inwardRcmTax / 2,
                'inward_rcm_sgst' => $inwardRcmTax / 2,
                'inward_rcm_cess' => 0,
                'inward_rcm_total_tax' => $inwardRcmTax,
                'exempt_count' => $exemptSales->count ?? 0,
                'exempt_taxable' => $exemptTaxable,
                'nil_count' => $nilSales->count ?? 0,
                'nil_taxable' => $nilTaxable,
                'total_taxable' => $totalTaxable,
                'total_igst' => 0,
                'total_cgst' => $totalTax / 2,
                'total_sgst' => $totalTax / 2,
                'total_cess' => 0,
                'total_tax' => $totalTax,
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.gstr-4-annual-print', compact('reportData'));
            }
        }

        return view('admin.reports.gst-reports.gstr-4-annual', compact(
            'reportData', 'month', 'year', 'useDateRange', 'fromDate', 'toDate'
        ));
    }

    /**
     * Customer GST Detail Mail Report
     */
    public function customerGstDetailMail(Request $request)
    {
        $reportData = [];
        $summary = [];

        // Default values
        $fromDate = $request->from_date ?? date('Y-m-01');
        $toDate = $request->to_date ?? date('Y-m-d');
        $gstFilter = $request->gst_filter ?? 'all';
        $numberFilter = $request->number_filter ?? '3';
        $showBrExp = $request->show_br_exp ?? 'Y'; // Now a Y/N select instead of checkbox
        
        // Handle AJAX POST for sending emails
        if ($request->isMethod('post') && $request->action === 'send_mail') {
            // TODO: Implement actual email sending logic
            // For now, return success response
            return response()->json([
                'success' => true,
                'message' => 'Email functionality is not yet implemented.'
            ]);
        }

        if ($request->has('view') || $request->has('print')) {
            // Determine date range
            $startDate = Carbon::parse($fromDate)->startOfDay();
            $endDate = Carbon::parse($toDate)->endOfDay();

            // Build customer query with balance from sale_transactions within date range
            $customerQuery = Customer::query()
                ->select('customers.*')
                ->selectRaw("(SELECT COALESCE(SUM(net_amount), 0) FROM sale_transactions WHERE sale_transactions.customer_id = customers.id AND sale_transactions.sale_date BETWEEN '{$startDate}' AND '{$endDate}') as balance");

            // Apply GST filter
            if ($gstFilter == 'with_gstin') {
                $customerQuery->whereNotNull('gst_number')
                    ->where('gst_number', '!=', '')
                    ->whereRaw("LENGTH(gst_number) = 15");
            } elseif ($gstFilter == 'without_gstin') {
                $customerQuery->where(function($q) {
                    $q->whereNull('gst_number')
                      ->orWhere('gst_number', '')
                      ->orWhereRaw("LENGTH(gst_number) != 15");
                });
            }

            // Apply number filter if provided (search by code)
            if ($numberFilter && $numberFilter !== '3') {
                $customerQuery->where('code', 'LIKE', '%' . $numberFilter . '%');
            }

            // Apply Br. Exp filter based on Y/N selection
            if ($showBrExp === 'Y') {
                $customerQuery->where('tax_on_br_expiry', 'Y');
            } elseif ($showBrExp === 'N') {
                $customerQuery->where(function($q) {
                    $q->where('tax_on_br_expiry', 'N')
                      ->orWhereNull('tax_on_br_expiry');
                });
            }

            // Order by customer code
            $customerQuery->orderBy('customers.code');

            $customers = $customerQuery->get();

            // Process customer data
            $reportData = $customers->map(function($customer) {
                return [
                    'code' => $customer->code ?? 'N/A',
                    'name' => $customer->name ?? 'N/A',
                    'gst_number' => $customer->gst_number ?? '-',
                    'balance' => $customer->balance ?? 0,
                    'tag' => $customer->flag ?? '',
                    'mobile' => $customer->mobile ?? '',
                    'email' => $customer->email ?? '',
                ];
            });

            // Calculate summary
            $mobileCount = $reportData->filter(function($customer) {
                return !empty($customer['mobile']);
            })->count();

            $emailCount = $reportData->filter(function($customer) {
                return !empty($customer['email']);
            })->count();

            $summary = [
                'mobile_count' => $mobileCount,
                'email_count' => $emailCount,
                'total_amount' => $reportData->sum('balance'),
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.customer-gst-detail-mail-print', compact(
                    'reportData', 'summary', 'fromDate', 'toDate'
                ));
            }
        }

        return view('admin.reports.gst-reports.customer-gst-detail-mail', compact(
            'reportData', 'summary', 'fromDate', 'toDate', 'gstFilter', 
            'numberFilter', 'showBrExp'
        ));
    }

    /**
     * Stock Trans - 1 Report (GST Stock Transfer Report)
     */
    public function stockTrans1(Request $request)
    {
        $reportData = [];
        $totalStockValue = 0;

        // Default values
        $asOnDay = $request->as_on_day ?? date('j');
        $saleMonth = $request->sale_month ?? date('n');
        $year = $request->year ?? date('Y');
        $asOnDate = Carbon::createFromDate($year, $saleMonth, $asOnDay)->format('Y-m-d');
        $companyCode = $request->company_code ?? '';
        $divisionCode = $request->division_code ?? '';
        $reportType = $request->report_type ?? 'D'; // D = Detailed, S = Summarized
        $hsnType = $request->hsn_type ?? 'Full';

        if ($request->has('generate') || $request->has('print')) {
            // Build query for items with stock and GST details
            $query = DB::table('batches as b')
                ->join('items as i', 'b.item_id', '=', 'i.id')
                ->leftJoin('companies as c', 'i.company_id', '=', 'c.id')
                ->select(
                    'i.hsn_code',
                    'i.name as item_name',
                    'i.pack',
                    'i.unit',
                    'b.batch_no',
                    DB::raw('COALESCE(b.qty, 0) as qty'),
                    DB::raw('COALESCE(b.cost_rate, 0) as cost_rate'),
                    DB::raw('COALESCE(b.qty * b.cost_rate, 0) as value'),
                    DB::raw('COALESCE(i.cgst_percent, 0) as cgst_percent'),
                    DB::raw('COALESCE(i.sgst_percent, 0) as sgst_percent'),
                    DB::raw('COALESCE(i.igst_percent, 0) as igst_percent'),
                    'c.name as company_name',
                    'c.id as company_id'
                )
                ->where('b.qty', '>', 0);

            // Apply company filter
            if (!empty($companyCode)) {
                $query->where('c.id', $companyCode);
            }

            // Apply division filter (if exists)
            if (!empty($divisionCode)) {
                $query->where('i.division_code', $divisionCode);
            }

            // Order by HSN code and item name
            $query->orderBy('i.hsn_code')->orderBy('i.name');

            $items = $query->get();

            // Calculate GST amounts and prepare report data
            $reportData = $items->map(function($item) {
                $value = $item->value;
                $cgstAmount = ($value * $item->cgst_percent) / 100;
                $sgstAmount = ($value * $item->sgst_percent) / 100;
                $igstAmount = ($value * $item->igst_percent) / 100;

                // Get sale quantity for the period
                $saleQty = DB::table('sale_transaction_items as sti')
                    ->join('sale_transactions as st', 'sti.sale_transaction_id', '=', 'st.id')
                    ->where('sti.item_id', function($q) use ($item) {
                        $q->select('id')->from('items')->where('name', $item->item_name)->limit(1);
                    })
                    ->whereMonth('st.sale_date', request()->sale_month ?? date('n'))
                    ->whereYear('st.sale_date', request()->year ?? date('Y'))
                    ->sum('sti.qty');

                return [
                    'hsn_code' => $item->hsn_code ?? '-',
                    'item_name' => $item->item_name,
                    'pack' => $item->pack ?? '-',
                    'qty' => $item->qty,
                    'cost_rate' => $item->cost_rate,
                    'value' => $value,
                    'cgst' => $cgstAmount,
                    'sgst' => $sgstAmount,
                    'igst' => $igstAmount,
                    'sale_qty' => $saleQty ?? 0,
                ];
            });

            // If summarized, group by HSN code
            if ($reportType === 'S') {
                $reportData = $reportData->groupBy('hsn_code')->map(function($group, $hsnCode) {
                    return [
                        'hsn_code' => $hsnCode,
                        'item_name' => 'Multiple Items',
                        'pack' => '-',
                        'qty' => $group->sum('qty'),
                        'cost_rate' => $group->avg('cost_rate'),
                        'value' => $group->sum('value'),
                        'cgst' => $group->sum('cgst'),
                        'sgst' => $group->sum('sgst'),
                        'igst' => $group->sum('igst'),
                        'sale_qty' => $group->sum('sale_qty'),
                    ];
                })->values();
            }

            $totalStockValue = $reportData->sum('value');

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.stock-trans-1-print', compact(
                    'reportData', 'totalStockValue', 'asOnDate', 'saleMonth', 'year'
                ));
            }
        }

        // Get companies for dropdown
        $companies = DB::table('companies')->select('id', 'name', 'short_name')->orderBy('name')->get();

        return view('admin.reports.gst-reports.stock-trans-1', compact(
            'reportData', 'totalStockValue', 'asOnDate', 'asOnDay', 'saleMonth', 'year',
            'companyCode', 'divisionCode', 'reportType', 'hsnType', 'companies'
        ));
    }

    /**
     * Stock Trans - 2 Report (GST Stock Transfer Report - Opening/Outward/Closing)
     */
    public function stockTrans2(Request $request)
    {
        $reportData = [];
        $totals = [
            'opening_qty' => 0,
            'qty' => 0,
            'value' => 0,
            'value2' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'igst' => 0,
            'itc_allowed' => 0,
            'closing_qty' => 0,
        ];

        // Default values
        $saleMonth = $request->sale_month ?? date('n');
        $year = $request->year ?? date('Y');
        $hsnType = $request->hsn_type ?? 'Full';

        if ($request->has('generate') || $request->has('print')) {
            // Calculate period dates
            $periodStart = Carbon::createFromDate($year, $saleMonth, 1)->startOfMonth();
            $periodEnd = Carbon::createFromDate($year, $saleMonth, 1)->endOfMonth();

            // Get items grouped by HSN code
            $itemsQuery = DB::table('items as i')
                ->leftJoin('companies as c', 'i.company_id', '=', 'c.id')
                ->select(
                    'i.id',
                    'i.hsn_code',
                    'i.name as item_name',
                    DB::raw('COALESCE(i.cgst_percent, 0) as cgst_percent'),
                    DB::raw('COALESCE(i.sgst_percent, 0) as sgst_percent'),
                    DB::raw('COALESCE(i.igst_percent, 0) as igst_percent')
                )
                ->whereNotNull('i.hsn_code')
                ->where('i.hsn_code', '!=', '')
                ->groupBy('i.id', 'i.hsn_code', 'i.name', 'i.cgst_percent', 'i.sgst_percent', 'i.igst_percent')
                ->orderBy('i.hsn_code');

            $items = $itemsQuery->get();

            $reportData = $items->map(function($item) use ($periodStart, $periodEnd) {
                // Opening Stock: Sum of batches created before period start
                $openingQty = DB::table('batches')
                    ->where('item_id', $item->id)
                    ->where('created_at', '<', $periodStart)
                    ->sum('qty');

                // Current stock in batches
                $currentQty = DB::table('batches')
                    ->where('item_id', $item->id)
                    ->sum('qty');

                // Outward Supply Made (Sales during the period)
                $outwardData = DB::table('sale_transaction_items as sti')
                    ->join('sale_transactions as st', 'sti.sale_transaction_id', '=', 'st.id')
                    ->where('sti.item_id', $item->id)
                    ->whereBetween('st.sale_date', [$periodStart, $periodEnd])
                    ->selectRaw('
                        COALESCE(SUM(sti.qty), 0) as total_qty,
                        COALESCE(SUM(sti.net_amount), 0) as total_value
                    ')
                    ->first();

                $outwardQty = $outwardData->total_qty ?? 0;
                $outwardValue = $outwardData->total_value ?? 0;

                // Calculate GST on outward supply
                $cgstAmount = ($outwardValue * $item->cgst_percent) / 100;
                $sgstAmount = ($outwardValue * $item->sgst_percent) / 100;
                $igstAmount = ($outwardValue * $item->igst_percent) / 100;
                $itcAllowed = $cgstAmount + $sgstAmount + $igstAmount; // Total ITC

                // Closing balance
                $closingQty = $currentQty;

                return [
                    'hsn_code' => $item->hsn_code ?? '-',
                    'item_name' => $item->item_name,
                    'opening_qty' => $openingQty,
                    'qty' => $outwardQty,
                    'value' => $outwardValue,
                    'cgst' => $cgstAmount,
                    'sgst' => $sgstAmount,
                    'igst' => $igstAmount,
                    'itc_allowed' => $itcAllowed,
                    'closing_qty' => $closingQty,
                ];
            })->filter(function($item) {
                // Only show items with some activity
                return $item['opening_qty'] > 0 || $item['qty'] > 0 || $item['closing_qty'] > 0;
            })->values();

            // Calculate totals
            $totals = [
                'opening_qty' => $reportData->sum('opening_qty'),
                'qty' => $reportData->sum('qty'),
                'value' => $reportData->sum('value'),
                'value2' => $reportData->sum('value'),
                'cgst' => $reportData->sum('cgst'),
                'sgst' => $reportData->sum('sgst'),
                'igst' => $reportData->sum('igst'),
                'itc_allowed' => $reportData->sum('itc_allowed'),
                'closing_qty' => $reportData->sum('closing_qty'),
            ];

            if ($request->has('print')) {
                return view('admin.reports.gst-reports.stock-trans-2-print', compact(
                    'reportData', 'totals', 'saleMonth', 'year', 'hsnType'
                ));
            }
        }

        return view('admin.reports.gst-reports.stock-trans-2', compact(
            'reportData', 'totals', 'saleMonth', 'year', 'hsnType'
        ));
    }

    /**
     * E-Way Bill Generation Report
     */
    public function waybillGeneration(Request $request)
    {
        $reportData = [];
        $recordCount = 0;

        // Default values
        $documentType = $request->document_type ?? '1'; // 1=Challan, 2=Bill, 3=BT Trf, 4=Exp.Sale, 5=Pur.Ret, 6=Pur.Exp
        $billAmtThreshold = $request->bill_amt ?? 50000.00;
        $salesmanCode = $request->sman ?? '00';
        $fromDate = $request->from_date ?? date('Y-m-d');
        $toDate = $request->to_date ?? date('Y-m-d');
        $transactionType = $request->transaction_type ?? '3'; // 1=Local, 2=Inter, 3=Both
        $seriesCode = $request->series ?? '00';
        $areaCode = $request->area ?? '00';
        $partyCode = $request->party_code ?? '00';
        $hsnType = $request->hsn ?? 'Full';
        $routeCode = $request->route ?? '00';
        $gstFilter = $request->gst_filter ?? '3'; // 1=With GSTIN, 2=Without GSTIN, 3=All
        $orderBy = $request->order_by ?? '1'; // 1=VNO, 2=Party, 3=GSTIN
        $trnNo = $request->trn_no ?? '1';
        $trnDate = $request->trn_date ?? date('Y-m-d');

        // Selected record for E-Way Bill details
        $selectedRecord = null;
        $ewayBillFrom = [
            'pincode' => '',
            'gstin' => '',
            'place' => '',
            'state' => '',
            'address1' => '',
            'address2' => '',
        ];
        $ewayBillTo = [
            'pincode' => '',
            'gstin' => '',
            'place' => '',
            'state' => '',
            'address1' => '',
            'address2' => '',
        ];

        if ($request->has('ok') || $request->has('view') || $request->has('generate')) {
            // Build query based on document type
            $query = SaleTransaction::with(['customer'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->where('net_amount', '>=', $billAmtThreshold);

            // Apply GSTIN filter
            if ($gstFilter == '1') {
                $query->whereHas('customer', function($q) {
                    $q->whereNotNull('gst_number')
                      ->where('gst_number', '!=', '')
                      ->whereRaw("LENGTH(gst_number) = 15");
                });
            } elseif ($gstFilter == '2') {
                $query->where(function($q) {
                    $q->whereNull('customer_id')
                      ->orWhereHas('customer', function($q2) {
                          $q2->where(function($q3) {
                              $q3->whereNull('gst_number')
                                 ->orWhere('gst_number', '')
                                 ->orWhereRaw("LENGTH(gst_number) != 15");
                          });
                      });
                });
            }

            // Apply party code filter
            if ($partyCode && $partyCode !== '00') {
                $query->whereHas('customer', function($q) use ($partyCode) {
                    $q->where('code', 'LIKE', '%' . $partyCode . '%');
                });
            }

            // Order by
            switch ($orderBy) {
                case '2':
                    $query->join('customers', 'sale_transactions.customer_id', '=', 'customers.id')
                          ->orderBy('customers.name');
                    break;
                case '3':
                    $query->join('customers as c2', 'sale_transactions.customer_id', '=', 'c2.id')
                          ->orderBy('c2.gst_number');
                    break;
                default:
                    $query->orderBy('invoice_no');
            }

            $transactions = $query->select('sale_transactions.*')->get();

            $reportData = $transactions->map(function($sale) {
                $taxableValue = ($sale->net_amount ?? 0) - ($sale->tax_amount ?? 0);
                return [
                    'id' => $sale->id,
                    'gst_no' => $sale->customer->gst_number ?? '-',
                    'code' => $sale->customer->code ?? '-',
                    'party_name' => $sale->customer->name ?? 'Cash',
                    'bill_no' => $sale->invoice_no,
                    'date' => $sale->sale_date,
                    'amount' => $sale->net_amount ?? 0,
                    'taxable' => $taxableValue,
                    'tax' => $sale->tax_amount ?? 0,
                    'tag' => $sale->eway_tag ?? '',
                    'customer' => $sale->customer,
                ];
            });

            $recordCount = $reportData->count();

            // Get company info for eWayBill From
            $companySettings = DB::table('settings')->first();
            if ($companySettings) {
                $ewayBillFrom = [
                    'pincode' => $companySettings->pin_code ?? '',
                    'gstin' => $companySettings->gst_number ?? '',
                    'place' => $companySettings->city ?? '',
                    'state' => $companySettings->state ?? '',
                    'address1' => $companySettings->address ?? '',
                    'address2' => $companySettings->address2 ?? '',
                ];
            }
        }

        // Get areas and routes for dropdowns
        $areas = DB::table('areas')->select('id', 'name')->orderBy('name')->get();
        $routes = DB::table('routes')->select('id', 'name')->orderBy('name')->get();

        return view('admin.reports.gst-reports.waybill-generation', compact(
            'reportData', 'recordCount', 'documentType', 'billAmtThreshold', 'salesmanCode',
            'fromDate', 'toDate', 'transactionType', 'seriesCode', 'areaCode', 'partyCode',
            'hsnType', 'routeCode', 'gstFilter', 'orderBy', 'trnNo', 'trnDate',
            'ewayBillFrom', 'ewayBillTo', 'areas', 'routes'
        ));
    }

    /**
     * GSTR-9 Annual Return Report
     */
    public function gstr9(Request $request)
    {
        // Default values
        $year = $request->year ?? date('Y');
        $useDateRange = $request->has('date_range');
        $fromDate = $request->from_date ?? Carbon::createFromDate($year, 4, 1)->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::createFromDate($year + 1, 3, 31)->format('Y-m-d');
        $hsnType = $request->hsn ?? 'Full';
        
        // Checkbox options
        $reduceUnregCndn = $request->has('reduce_unreg_cndn');
        $reduceCustExpiry = $request->has('reduce_cust_expiry');
        $zeroRatedRemove = $request->has('zero_rated_remove');
        $addSupplierExpiry = $request->has('add_supplier_expiry');
        $withUnregSupplier = $request->has('with_unreg_supplier');

        $reportData = [];

        if ($request->has('export')) {
            // Calculate date range based on financial year
            if (!$useDateRange) {
                $fromDate = Carbon::createFromDate($year, 4, 1)->format('Y-m-d');
                $toDate = Carbon::createFromDate($year + 1, 3, 31)->format('Y-m-d');
            }

            // Get Sales Data (B2B - Registered)
            $b2bSales = DB::table('sale_transactions as st')
                ->join('customers as c', 'st.customer_id', '=', 'c.id')
                ->whereBetween('st.sale_date', [$fromDate, $toDate])
                ->whereNotNull('c.gst_number')
                ->where('c.gst_number', '!=', '')
                ->whereRaw("LENGTH(c.gst_number) = 15")
                ->select(
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(st.taxable_amount) as taxable_value'),
                    DB::raw('SUM(st.cgst_amount) as cgst'),
                    DB::raw('SUM(st.sgst_amount) as sgst'),
                    DB::raw('SUM(st.igst_amount) as igst'),
                    DB::raw('SUM(st.cess_amount) as cess')
                )
                ->first();

            // Get Sales Data (B2C - Unregistered)
            $b2cSales = DB::table('sale_transactions as st')
                ->leftJoin('customers as c', 'st.customer_id', '=', 'c.id')
                ->whereBetween('st.sale_date', [$fromDate, $toDate])
                ->where(function($q) {
                    $q->whereNull('c.gst_number')
                      ->orWhere('c.gst_number', '')
                      ->orWhereRaw("LENGTH(c.gst_number) != 15");
                })
                ->select(
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(st.taxable_amount) as taxable_value'),
                    DB::raw('SUM(st.cgst_amount) as cgst'),
                    DB::raw('SUM(st.sgst_amount) as sgst'),
                    DB::raw('SUM(st.igst_amount) as igst'),
                    DB::raw('SUM(st.cess_amount) as cess')
                )
                ->first();

            // Get Purchase Data (Registered)
            $b2bPurchases = DB::table('purchase_transactions as pt')
                ->join('suppliers as s', 'pt.supplier_id', '=', 's.id')
                ->whereBetween('pt.purchase_date', [$fromDate, $toDate])
                ->whereNotNull('s.gst_number')
                ->where('s.gst_number', '!=', '')
                ->whereRaw("LENGTH(s.gst_number) = 15")
                ->select(
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(pt.taxable_amount) as taxable_value'),
                    DB::raw('SUM(pt.cgst_amount) as cgst'),
                    DB::raw('SUM(pt.sgst_amount) as sgst'),
                    DB::raw('SUM(pt.igst_amount) as igst'),
                    DB::raw('SUM(pt.cess_amount) as cess')
                )
                ->first();

            // Get Credit/Debit Notes
            $creditNotes = DB::table('credit_note_transactions')
                ->whereBetween('note_date', [$fromDate, $toDate])
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(taxable_amount) as taxable_value'),
                    DB::raw('SUM(cgst_amount) as cgst'),
                    DB::raw('SUM(sgst_amount) as sgst'),
                    DB::raw('SUM(igst_amount) as igst')
                )
                ->first();

            $debitNotes = DB::table('debit_note_transactions')
                ->whereBetween('note_date', [$fromDate, $toDate])
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(taxable_amount) as taxable_value'),
                    DB::raw('SUM(cgst_amount) as cgst'),
                    DB::raw('SUM(sgst_amount) as sgst'),
                    DB::raw('SUM(igst_amount) as igst')
                )
                ->first();

            $reportData = [
                'b2b_sales' => $b2bSales,
                'b2c_sales' => $b2cSales,
                'b2b_purchases' => $b2bPurchases,
                'credit_notes' => $creditNotes,
                'debit_notes' => $debitNotes,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ];

            // Export to Excel
            return $this->exportGstr9ToExcel($reportData, $year);
        }

        // Generate years for dropdown (last 5 years)
        $years = [];
        $currentYear = date('Y');
        for ($i = 0; $i < 5; $i++) {
            $y = $currentYear - $i;
            $years[$y] = $y . '-' . ($y + 1);
        }

        return view('admin.reports.gst-reports.gstr-9', compact(
            'year', 'years', 'useDateRange', 'fromDate', 'toDate', 'hsnType',
            'reduceUnregCndn', 'reduceCustExpiry', 'zeroRatedRemove', 
            'addSupplierExpiry', 'withUnregSupplier', 'reportData'
        ));
    }

    /**
     * Export GSTR-9 to Excel
     */
    private function exportGstr9ToExcel($data, $year)
    {
        $filename = 'GSTR9_' . $year . '_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $year) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            // Header
            fputcsv($file, ['GSTR-9 Annual Return Report']);
            fputcsv($file, ['Financial Year: ' . $year . '-' . ($year + 1)]);
            fputcsv($file, ['Period: ' . $data['from_date'] . ' to ' . $data['to_date']]);
            fputcsv($file, []);
            
            // Outward Supplies
            fputcsv($file, ['PART II - DETAILS OF OUTWARD AND INWARD SUPPLIES']);
            fputcsv($file, []);
            fputcsv($file, ['4. Details of advances, inward and outward supplies']);
            fputcsv($file, ['Nature of Supplies', 'Taxable Value', 'CGST', 'SGST', 'IGST', 'Cess']);
            
            // B2B Sales
            fputcsv($file, [
                'A) Supplies made to registered persons (B2B)',
                number_format($data['b2b_sales']->taxable_value ?? 0, 2),
                number_format($data['b2b_sales']->cgst ?? 0, 2),
                number_format($data['b2b_sales']->sgst ?? 0, 2),
                number_format($data['b2b_sales']->igst ?? 0, 2),
                number_format($data['b2b_sales']->cess ?? 0, 2),
            ]);
            
            // B2C Sales
            fputcsv($file, [
                'B) Supplies made to unregistered persons (B2C)',
                number_format($data['b2c_sales']->taxable_value ?? 0, 2),
                number_format($data['b2c_sales']->cgst ?? 0, 2),
                number_format($data['b2c_sales']->sgst ?? 0, 2),
                number_format($data['b2c_sales']->igst ?? 0, 2),
                number_format($data['b2c_sales']->cess ?? 0, 2),
            ]);
            
            fputcsv($file, []);
            
            // Credit/Debit Notes
            fputcsv($file, ['5. Details of Credit/Debit Notes']);
            fputcsv($file, [
                'Credit Notes',
                number_format($data['credit_notes']->taxable_value ?? 0, 2),
                number_format($data['credit_notes']->cgst ?? 0, 2),
                number_format($data['credit_notes']->sgst ?? 0, 2),
                number_format($data['credit_notes']->igst ?? 0, 2),
                '0.00',
            ]);
            fputcsv($file, [
                'Debit Notes',
                number_format($data['debit_notes']->taxable_value ?? 0, 2),
                number_format($data['debit_notes']->cgst ?? 0, 2),
                number_format($data['debit_notes']->sgst ?? 0, 2),
                number_format($data['debit_notes']->igst ?? 0, 2),
                '0.00',
            ]);
            
            fputcsv($file, []);
            
            // Inward Supplies (Purchases)
            fputcsv($file, ['6. Details of ITC availed']);
            fputcsv($file, [
                'Inward supplies from registered persons',
                number_format($data['b2b_purchases']->taxable_value ?? 0, 2),
                number_format($data['b2b_purchases']->cgst ?? 0, 2),
                number_format($data['b2b_purchases']->sgst ?? 0, 2),
                number_format($data['b2b_purchases']->igst ?? 0, 2),
                number_format($data['b2b_purchases']->cess ?? 0, 2),
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
