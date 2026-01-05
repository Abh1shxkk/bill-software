<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SalesMan;
use App\Models\Area;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceiptPaymentReportController extends Controller
{
    // Receipt from Customer Report
    public function receiptFromCustomer(Request $request)
    {
        // Fetch dropdown data
        $customers = Customer::where('is_deleted', false)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', false)->orderBy('name')->get();
        $areas = Area::where('is_deleted', false)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get(); // Route table doesn't have is_deleted
        $users = User::where('is_active', true)->orderBy('full_name')->get();
        
        $reportData = [];

        if ($request->has('view') || $request->has('print') || $request->has('excel')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $customerCode = $request->customer_id ?? '';
            $paymentMode = $request->payment_mode ?? '8'; // Default All
            $orderBy = $request->order_by ?? 'Date';

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->where('customer_ledgers.transaction_type', 'Receipt')
                    ->whereBetween('customer_ledgers.transaction_date', [$fromDate, $toDate]);

                // Customer filter
                if ($customerCode && $customerCode !== '') {
                    $query->where('customers.id', $customerCode);
                }

                // Payment mode filter
                $paymentModes = [
                    '1' => 'Cash',
                    '2' => 'Cheque',
                    '3' => 'Adjustment',
                    '4' => 'Discount',
                    '5' => 'NEFT',
                    '6' => 'RTGS',
                    '7' => 'Wallet',
                ];
                if ($paymentMode !== '8' && isset($paymentModes[$paymentMode])) {
                    $query->where('customer_ledgers.payment_mode', $paymentModes[$paymentMode]);
                }

                // Salesman filter
                if ($request->salesman_id && $request->salesman_id !== '') {
                    $query->where('customers.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id && $request->area_id !== '') {
                    $query->where('customers.area_id', $request->area_id);
                }

                // Route filter
                if ($request->route_id && $request->route_id !== '') {
                    $query->where('customers.route_id', $request->route_id);
                }

                // User filter
                if ($request->user_id && $request->user_id !== '') {
                    $query->where('customer_ledgers.created_by', $request->user_id);
                }

                // Order by
                if ($orderBy === 'Customer') {
                    $query->orderBy('customers.name');
                } elseif ($orderBy === 'Amount') {
                    $query->orderBy('customer_ledgers.amount', 'desc');
                } else {
                    $query->orderBy('customer_ledgers.transaction_date');
                }

                $receipts = $query->select(
                    'customer_ledgers.*',
                    'customers.name as customer_name',
                    'customers.gstin'
                )->get();

                foreach ($receipts as $receipt) {
                    $reportData[] = [
                        'date' => Carbon::parse($receipt->transaction_date)->format('d-M-Y'),
                        'receipt_no' => $receipt->reference_no ?? $receipt->id,
                        'customer_name' => $receipt->customer_name ?? 'Unknown',
                        'mode' => $receipt->payment_mode ?? 'Cash',
                        'amount' => abs($receipt->amount ?? 0),
                        'narration' => $receipt->narration ?? '',
                        'gstin' => $receipt->gstin ?? '',
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.receipt-from-customer-print', compact('reportData', 'request'));
            }

            if ($request->has('excel')) {
                return $this->exportReceiptFromCustomerExcel($reportData, $request);
            }
        }

        return view('admin.reports.receipt-payment-reports.receipt-from-customer', compact('customers', 'salesmen', 'areas', 'routes', 'users', 'reportData'));
    }

    // Export Receipt from Customer to Excel
    private function exportReceiptFromCustomerExcel($reportData, $request)
    {
        $filename = 'Receipt_from_Customer_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Date', 'Receipt No', 'Customer Name', 'Mode', 'Amount', 'Narration']);
            
            $total = 0;
            foreach ($reportData as $index => $row) {
                $total += $row['amount'];
                fputcsv($file, [
                    $index + 1,
                    $row['date'],
                    $row['receipt_no'],
                    $row['customer_name'],
                    $row['mode'],
                    number_format($row['amount'], 2),
                    $row['narration']
                ]);
            }
            fputcsv($file, ['', '', '', '', 'Total:', number_format($total, 2), '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    // Payment to Supplier Report
    public function paymentToSupplier(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', false)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print') || $request->has('excel')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');
            $supplierId = $request->supplier_id;
            $paymentMode = $request->payment_mode ?? '3'; // Default Both
            $groupBy = $request->group_by ?? 'Date';
            $chequeNo = $request->cheque_no;

            try {
                $query = DB::table('supplier_ledgers')
                    ->leftJoin('suppliers', 'supplier_ledgers.supplier_id', '=', 'suppliers.supplier_id')
                    ->where('supplier_ledgers.transaction_type', 'Payment')
                    ->whereBetween('supplier_ledgers.transaction_date', [$fromDate, $toDate]);

                // Supplier filter
                if ($supplierId) {
                    $query->where('supplier_ledgers.supplier_id', $supplierId);
                }

                // Payment mode filter
                if ($paymentMode == '1') {
                    $query->where('supplier_ledgers.payment_mode', 'Cash');
                } elseif ($paymentMode == '2') {
                    $query->where('supplier_ledgers.payment_mode', 'Cheque');
                }

                // Cheque number filter
                if ($chequeNo) {
                    $query->where('supplier_ledgers.cheque_no', 'LIKE', '%' . $chequeNo . '%');
                }

                // Order by / Group by
                if ($groupBy === 'Supplier') {
                    $query->orderBy('suppliers.name');
                } elseif ($groupBy === 'Amount') {
                    $query->orderBy('supplier_ledgers.amount', 'desc');
                } else {
                    $query->orderBy('supplier_ledgers.transaction_date');
                }

                $payments = $query->select(
                    'supplier_ledgers.*',
                    'suppliers.name as supplier_name'
                )->get();

                foreach ($payments as $payment) {
                    $reportData[] = [
                        'date' => Carbon::parse($payment->transaction_date)->format('d-M-Y'),
                        'payment_no' => $payment->reference_no ?? $payment->id,
                        'supplier_name' => $payment->supplier_name ?? 'Unknown',
                        'mode' => $payment->payment_mode ?? 'Cash',
                        'cheque_no' => $payment->cheque_no ?? '',
                        'amount' => abs($payment->amount ?? 0),
                        'narration' => $payment->narration ?? '',
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.payment-to-supplier-print', compact('reportData', 'request'));
            }

            if ($request->has('excel')) {
                return $this->exportPaymentToSupplierExcel($reportData, $request);
            }
        }

        return view('admin.reports.receipt-payment-reports.payment-to-supplier', compact('suppliers', 'reportData'));
    }

    // Export Payment to Supplier to Excel
    private function exportPaymentToSupplierExcel($reportData, $request)
    {
        $filename = 'Payment_to_Supplier_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Date', 'Payment No', 'Supplier Name', 'Mode', 'Cheque No', 'Amount', 'Narration']);
            
            $total = 0;
            foreach ($reportData as $index => $row) {
                $total += $row['amount'];
                fputcsv($file, [
                    $index + 1,
                    $row['date'],
                    $row['payment_no'],
                    $row['supplier_name'],
                    $row['mode'],
                    $row['cheque_no'],
                    number_format($row['amount'], 2),
                    $row['narration']
                ]);
            }
            fputcsv($file, ['', '', '', '', '', 'Total:', number_format($total, 2), '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Post Dated Cheques
    public function postDatedCheques(Request $request)
    {
        // Fetch dropdown data
        $customers = Customer::where('is_deleted', false)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', false)->orderBy('name')->get();
        $areas = Area::where('is_deleted', false)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-04-01');
            $toDate = $request->to_date ?? date('Y-m-d');

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->where('customer_ledgers.payment_mode', 'Cheque')
                    ->whereNotNull('customer_ledgers.cheque_date')
                    ->where('customer_ledgers.cheque_date', '>', DB::raw('customer_ledgers.transaction_date'))
                    ->whereBetween('customer_ledgers.cheque_date', [$fromDate, $toDate]);

                // Party filter
                if ($request->party_id) {
                    $query->where('customer_ledgers.customer_id', $request->party_id);
                }

                // Salesman filter
                if ($request->salesman_id) {
                    $query->where('customers.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id) {
                    $query->where('customers.area_id', $request->area_id);
                }

                // Route filter
                if ($request->route_id) {
                    $query->where('customers.route_id', $request->route_id);
                }

                $cheques = $query->select(
                    'customer_ledgers.*',
                    'customers.name as party_name'
                )->orderBy('customer_ledgers.cheque_date')->get();

                foreach ($cheques as $cheque) {
                    $reportData[] = [
                        'cheque_date' => Carbon::parse($cheque->cheque_date)->format('d-M-Y'),
                        'cheque_no' => $cheque->cheque_no ?? '',
                        'party_name' => $cheque->party_name ?? 'Unknown',
                        'bank' => $cheque->bank_name ?? '',
                        'amount' => abs($cheque->amount ?? 0),
                        'status' => $cheque->cheque_status ?? 'Pending',
                        'invoice_no' => $cheque->reference_no ?? '',
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.post-dated-cheques-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.post-dated-cheques', compact('customers', 'salesmen', 'areas', 'routes', 'reportData'));
    }

    // Returned Cheques
    public function returnedCheques(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-01');
            $toDate = $request->to_date ?? date('Y-m-d');
            $orderBy = $request->order_by ?? 'D';

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->where('customer_ledgers.payment_mode', 'Cheque')
                    ->where(function($q) {
                        $q->where('customer_ledgers.cheque_status', 'Returned')
                          ->orWhere('customer_ledgers.cheque_status', 'Bounced');
                    })
                    ->whereBetween('customer_ledgers.cheque_date', [$fromDate, $toDate]);

                // Order by
                if ($orderBy === 'P') {
                    $query->orderBy('customers.name');
                } else {
                    $query->orderBy('customer_ledgers.cheque_date');
                }

                $cheques = $query->select(
                    'customer_ledgers.*',
                    'customers.name as party_name',
                    'customers.id as customer_code'
                )->get();

                foreach ($cheques as $cheque) {
                    $reportData[] = [
                        'cheque_date' => Carbon::parse($cheque->cheque_date)->format('d-M-Y'),
                        'cheque_no' => $cheque->cheque_no ?? '',
                        'code' => $cheque->customer_code ?? '',
                        'party_name' => $cheque->party_name ?? 'Unknown',
                        'amount' => abs($cheque->amount ?? 0),
                        'return_date' => $cheque->return_date ? Carbon::parse($cheque->return_date)->format('d-M-Y') : '',
                        'charges' => $cheque->return_charges ?? 0,
                        'reason' => $cheque->return_reason ?? '',
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.returned-cheques-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.returned-cheques', compact('reportData'));
    }

    // Cash/Cheque Collection
    public function cashChequeCollection(Request $request)
    {
        // Fetch dropdown data
        $salesmen = SalesMan::where('is_deleted', false)->orderBy('name')->get();
        $areas = Area::where('is_deleted', false)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-01');
            $toDate = $request->to_date ?? date('Y-m-d');
            $mode = $request->mode ?? 'B'; // B=Both, C=Cash, Q=Cheque
            $chequeNo = $request->cheque_no;

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->where('customer_ledgers.transaction_type', 'Receipt')
                    ->whereBetween('customer_ledgers.transaction_date', [$fromDate, $toDate]);

                // Mode filter
                if ($mode == 'C') {
                    $query->where('customer_ledgers.payment_mode', 'Cash');
                } elseif ($mode == 'Q') {
                    $query->where('customer_ledgers.payment_mode', 'Cheque');
                }

                // Cheque number filter
                if ($chequeNo) {
                    $query->where('customer_ledgers.cheque_no', 'LIKE', '%' . $chequeNo . '%');
                }

                // Salesman filter
                if ($request->salesman_id) {
                    $query->where('customers.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id) {
                    $query->where('customers.area_id', $request->area_id);
                }

                // Route filter
                if ($request->route_id) {
                    $query->where('customers.route_id', $request->route_id);
                }

                // Collection boy filter
                if ($request->coll_boy_id) {
                    $query->where('customers.salesman_id', $request->coll_boy_id);
                }

                $receipts = $query->select('customer_ledgers.*', 'customers.name as customer_name')
                    ->orderBy('customer_ledgers.transaction_date')
                    ->get();

                foreach ($receipts as $receipt) {
                    $paymentMode = $receipt->payment_mode ?? 'Cash';
                    $cashAmount = ($paymentMode == 'Cash') ? abs($receipt->amount ?? 0) : 0;
                    $chequeAmount = ($paymentMode == 'Cheque') ? abs($receipt->amount ?? 0) : 0;

                    $reportData[] = [
                        'date' => Carbon::parse($receipt->transaction_date)->format('d-M-Y'),
                        'receipt_no' => $receipt->reference_no ?? $receipt->id,
                        'customer_name' => $receipt->customer_name ?? 'Unknown',
                        'mode' => $paymentMode,
                        'cash_amount' => $cashAmount,
                        'cheque_amount' => $chequeAmount,
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.cash-cheque-collection-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.cash-cheque-collection', compact('salesmen', 'areas', 'routes', 'reportData'));
    }

    // Cash Collection Summary
    public function cashCollectionSummary(Request $request)
    {
        // Fetch dropdown data
        $salesmen = SalesMan::where('is_deleted', false)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('full_name')->get();
        
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-01');
            $toDate = $request->to_date ?? date('Y-m-d');

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->leftJoin('sales_men', 'customers.salesman_id', '=', 'sales_men.id')
                    ->where('customer_ledgers.transaction_type', 'Receipt')
                    ->whereBetween('customer_ledgers.transaction_date', [$fromDate, $toDate]);

                // Collection boy filter
                if ($request->coll_boy_id) {
                    $query->where('customers.salesman_id', $request->coll_boy_id);
                }

                // User filter
                if ($request->user_id) {
                    $query->where('customer_ledgers.created_by', $request->user_id);
                }

                $summary = $query->select(
                    'customer_ledgers.transaction_date',
                    'sales_men.name as collection_boy',
                    DB::raw('SUM(CASE WHEN customer_ledgers.payment_mode = "Cash" THEN ABS(customer_ledgers.amount) ELSE 0 END) as cash_amount'),
                    DB::raw('SUM(CASE WHEN customer_ledgers.payment_mode = "Cheque" THEN ABS(customer_ledgers.amount) ELSE 0 END) as cheque_amount'),
                    DB::raw('COUNT(*) as receipt_count')
                )
                ->groupBy('customer_ledgers.transaction_date', 'sales_men.name')
                ->orderBy('customer_ledgers.transaction_date')
                ->get();

                foreach ($summary as $row) {
                    $reportData[] = [
                        'date' => Carbon::parse($row->transaction_date)->format('d-M-Y'),
                        'collection_boy' => $row->collection_boy ?? 'Unassigned',
                        'cash_amount' => $row->cash_amount ?? 0,
                        'cheque_amount' => $row->cheque_amount ?? 0,
                        'receipt_count' => $row->receipt_count ?? 0,
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.cash-collection-summary-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.cash-collection-summary', compact('salesmen', 'users', 'reportData'));
    }

    // Pay-In Slip Report
    public function payInSlip(Request $request)
    {
        // Fetch banks from settings or a dedicated table
        $banks = collect(); // Placeholder - replace with actual bank model if available
        
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $fromDate = $request->from_date ?? date('Y-m-d');
            $toDate = $request->to_date ?? date('Y-m-d');

            try {
                // Query pay-in slips - adjust table name based on your schema
                $query = DB::table('pay_in_slips')
                    ->whereBetween('slip_date', [$fromDate, $toDate]);

                if ($request->bank_id) {
                    $query->where('bank_id', $request->bank_id);
                }

                $slips = $query->orderBy('slip_date')->get();

                foreach ($slips as $slip) {
                    $reportData[] = [
                        'date' => Carbon::parse($slip->slip_date)->format('d-M-Y'),
                        'slip_no' => $slip->slip_no ?? $slip->id,
                        'bank_name' => $slip->bank_name ?? '',
                        'account_no' => $slip->account_no ?? '',
                        'cash_amount' => $slip->cash_amount ?? 0,
                        'cheque_amount' => $slip->cheque_amount ?? 0,
                    ];
                }
            } catch (\Exception $e) {
                // Table may not exist, use sample structure
            }

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.pay-in-slip-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.pay-in-slip', compact('banks', 'reportData'));
    }

    // Currency Detail
    public function currencyDetail(Request $request)
    {
        $reportData = [];

        if ($request->has('view') || $request->has('print')) {
            $reportDate = $request->report_date ?? date('Y-m-d');

            try {
                // Query currency details for the date - adjust table name based on your schema
                $currencies = DB::table('currency_details')
                    ->where('transaction_date', $reportDate)
                    ->get();

                foreach ($currencies as $currency) {
                    $reportData[$currency->denomination] = $currency->count ?? 0;
                }
            } catch (\Exception $e) {
                // Table may not exist, return empty data
            }

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.currency-detail-print', compact('reportData', 'request'));
            }
        }

        return view('admin.reports.receipt-payment-reports.currency-detail', compact('reportData'));
    }

    // Receipt from Customer - Month Wise
    public function receiptCustomerMonthWise(Request $request)
    {
        // Fetch dropdown data
        $customers = Customer::where('is_deleted', false)->orderBy('name')->get();
        $salesmen = SalesMan::where('is_deleted', false)->orderBy('name')->get();
        $areas = Area::where('is_deleted', false)->orderBy('name')->get();
        $routes = Route::orderBy('name')->get();
        
        $reportData = [];

        if ($request->has('view') || $request->has('print') || $request->has('excel')) {
            $fromYear = $request->from_year ?? date('Y');
            $toYear = $request->to_year ?? date('Y');
            $paymentMode = $request->payment_mode ?? '5'; // 5 = All

            // Fiscal year: April to March
            $startDate = $fromYear . '-04-01';
            $endDate = $toYear . '-03-31';

            try {
                $query = DB::table('customer_ledgers')
                    ->leftJoin('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
                    ->where('customer_ledgers.transaction_type', 'Receipt')
                    ->whereBetween('customer_ledgers.transaction_date', [$startDate, $endDate]);

                // Payment mode filter
                $modes = ['1' => 'Cash', '2' => 'Cheque', '3' => 'RTGS', '4' => 'NEFT'];
                if ($paymentMode != '5' && isset($modes[$paymentMode])) {
                    $query->where('customer_ledgers.payment_mode', $modes[$paymentMode]);
                }

                // Customer filter
                if ($request->customer_id) {
                    $query->where('customer_ledgers.customer_id', $request->customer_id);
                }

                // Salesman filter
                if ($request->salesman_id) {
                    $query->where('customers.salesman_id', $request->salesman_id);
                }

                // Area filter
                if ($request->area_id) {
                    $query->where('customers.area_id', $request->area_id);
                }

                // Route filter
                if ($request->route_id) {
                    $query->where('customers.route_id', $request->route_id);
                }

                $receipts = $query->select(
                    'customers.id as customer_id',
                    'customers.name as customer_name',
                    'customer_ledgers.transaction_date',
                    'customer_ledgers.amount'
                )->get();

                // Group by customer and month
                $customerData = [];
                foreach ($receipts as $receipt) {
                    $customerId = $receipt->customer_id;
                    if (!isset($customerData[$customerId])) {
                        $customerData[$customerId] = [
                            'customer_name' => $receipt->customer_name ?? 'Unknown',
                            'months' => array_fill(0, 12, 0), // Apr to Mar (0-11)
                        ];
                    }
                    
                    $month = (int) Carbon::parse($receipt->transaction_date)->format('n');
                    // Convert calendar month to fiscal month (Apr=0, May=1, ..., Mar=11)
                    $fiscalMonth = ($month >= 4) ? $month - 4 : $month + 8;
                    $customerData[$customerId]['months'][$fiscalMonth] += abs($receipt->amount ?? 0);
                }

                $reportData = array_values($customerData);
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.receipt-customer-month-wise-print', compact('reportData', 'request'));
            }

            if ($request->has('excel')) {
                return $this->exportReceiptMonthWiseExcel($reportData, $request);
            }
        }

        return view('admin.reports.receipt-payment-reports.receipt-customer-month-wise', compact('customers', 'salesmen', 'areas', 'routes', 'reportData'));
    }

    // Export Receipt Month Wise to Excel
    private function exportReceiptMonthWiseExcel($reportData, $request)
    {
        $filename = 'Receipt_Month_Wise_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Customer Name', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Total']);
            
            foreach ($reportData as $index => $row) {
                $rowData = [$index + 1, $row['customer_name']];
                foreach ($row['months'] as $amt) {
                    $rowData[] = number_format($amt, 2);
                }
                $rowData[] = number_format(array_sum($row['months']), 2);
                fputcsv($file, $rowData);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Payment History
    public function paymentHistory(Request $request)
    {
        $suppliers = Supplier::where('is_deleted', false)->orderBy('name')->get();
        $reportData = [];

        if ($request->has('view') || $request->has('print') || $request->has('excel')) {
            $fromDate = $request->from_date ?? date('Y-m-01');
            $toDate = $request->to_date ?? date('Y-m-d');
            $supplierId = $request->supplier_id;
            $paymentMode = $request->payment_mode ?? '5'; // 5 = All
            $sortBy = $request->sort_by ?? 'party';

            try {
                $query = DB::table('supplier_ledgers')
                    ->leftJoin('suppliers', 'supplier_ledgers.supplier_id', '=', 'suppliers.supplier_id')
                    ->where('supplier_ledgers.transaction_type', 'Payment')
                    ->whereBetween('supplier_ledgers.transaction_date', [$fromDate, $toDate]);

                // Supplier filter
                if ($supplierId) {
                    $query->where('supplier_ledgers.supplier_id', $supplierId);
                }

                // Payment mode filter
                $modes = ['1' => 'Cash', '2' => 'Cheque', '3' => 'RTGS', '4' => 'NEFT'];
                if ($paymentMode != '5' && isset($modes[$paymentMode])) {
                    $query->where('supplier_ledgers.payment_mode', $modes[$paymentMode]);
                }

                // Sort by
                if ($sortBy == 'date') {
                    $query->orderBy('supplier_ledgers.transaction_date');
                } elseif ($sortBy == 'amount') {
                    $query->orderBy('supplier_ledgers.amount', 'desc');
                } else {
                    $query->orderBy('suppliers.name');
                }

                $payments = $query->select(
                    'supplier_ledgers.*',
                    'suppliers.name as supplier_name',
                    'suppliers.code as supplier_code'
                )->get();

                foreach ($payments as $payment) {
                    $trnDate = Carbon::parse($payment->transaction_date);
                    $reportData[] = [
                        'code' => $payment->supplier_code ?? $payment->supplier_id,
                        'party_name' => $payment->supplier_name ?? 'Unknown',
                        'trn_date' => $trnDate->format('d-M-Y'),
                        'trn_no' => $payment->reference_no ?? $payment->id,
                        'amount' => abs($payment->amount ?? 0),
                        'mode' => $payment->payment_mode ?? 'Cash',
                        'days' => $payment->bill_date ? $trnDate->diffInDays(Carbon::parse($payment->bill_date)) : '',
                        'bill_date' => $payment->bill_date ? Carbon::parse($payment->bill_date)->format('d-M-Y') : '',
                        'bill_no' => $payment->bill_no ?? '',
                        'bill_amount' => abs($payment->bill_amount ?? 0),
                    ];
                }
            } catch (\Exception $e) {}

            if ($request->has('print')) {
                return view('admin.reports.receipt-payment-reports.payment-history-print', compact('reportData', 'request'));
            }

            if ($request->has('excel')) {
                return $this->exportPaymentHistoryExcel($reportData, $request);
            }
        }

        return view('admin.reports.receipt-payment-reports.payment-history', compact('suppliers', 'reportData'));
    }

    // Export Payment History to Excel
    private function exportPaymentHistoryExcel($reportData, $request)
    {
        $filename = 'Payment_History_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Code', 'Party Name', 'Trn. Date', 'Trn.No', 'Amount', 'P.Mode', 'Days', 'Bill Date', 'Bill No', 'Bill Amt']);
            
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['code'],
                    $row['party_name'],
                    $row['trn_date'],
                    $row['trn_no'],
                    number_format($row['amount'], 2),
                    $row['mode'],
                    $row['days'],
                    $row['bill_date'],
                    $row['bill_no'],
                    number_format($row['bill_amount'], 2)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
