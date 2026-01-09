<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransaction;
use App\Models\CashBankBook;
use App\Traits\ValidatesTransactionDate;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    use ValidatesTransactionDate;
    public function index(Request $request)
    {
        $query = BankTransaction::with('bank');
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_no', 'like', "%{$request->search}%")
                  ->orWhere('bank_name', 'like', "%{$request->search}%")
                  ->orWhere('cheque_no', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->filled('from_date')) $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('transaction_date', '<=', $request->to_date);
        
        $transactions = $query->orderByDesc('transaction_date')->orderByDesc('transaction_no')->paginate(15);
        return view('admin.bank-transaction.index', compact('transactions'));
    }

    public function transaction()
    {
        // Get all cash/bank books - filter by name containing 'bank' or get all
        $banks = CashBankBook::orderBy('name')->get();
        $nextTransactionNo = BankTransaction::getNextTransactionNo();
        return view('admin.bank-transaction.transaction', compact('banks', 'nextTransactionNo'));
    }

    public function store(Request $request)
    {
        // Validate transaction date (no backdating, max 1 day future)
        $dateError = $this->validateTransactionDate($request, 'cash_bank', 'transaction_date');
        if ($dateError) {
            return $this->dateValidationErrorResponse($dateError);
        }
        
        $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:D,W',
            'bank_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $bank = CashBankBook::find($request->bank_id);
        
        $transaction = BankTransaction::create([
            'transaction_date' => $request->transaction_date,
            'transaction_no' => BankTransaction::getNextTransactionNo(),
            'transaction_type' => $request->transaction_type,
            'bank_id' => $request->bank_id,
            'bank_name' => $bank ? $bank->name : null,
            'cheque_no' => $request->cheque_no,
            'amount' => $request->amount,
            'narration' => $request->narration,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaction saved successfully',
            'transaction_no' => $transaction->transaction_no,
        ]);
    }

    public function show($id)
    {
        $transaction = BankTransaction::with('bank')->findOrFail($id);
        return view('admin.bank-transaction.show', compact('transaction'));
    }

    public function destroy($id)
    {
        $transaction = BankTransaction::findOrFail($id);
        $transactionNo = $transaction->transaction_no;
        $transaction->delete();
        return response()->json(['success' => true, 'message' => "Transaction #{$transactionNo} deleted"]);
    }
}
