<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleTransaction;
use App\Models\SaleReturnTransaction;
use App\Models\PurchaseTransaction;
use App\Models\PurchaseReturnTransaction;
use Illuminate\Http\Request;
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
}
