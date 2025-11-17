<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBankBook;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class CashBankBookController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = CashBankBook::query();

        // Search functionality
        $search = request('search');
        $searchField = request('search_field', 'all');

        if ($search) {
            if ($searchField === 'all') {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('under', 'like', "%{$search}%")
                    ->orWhere('account_no', 'like', "%{$search}%");
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }

        $books = $query->orderByDesc('id')->paginate(15);
        
        if (request()->ajax()) {
            return view('admin.cash-bank-books.index', compact('books'));
        }
        
        return view('admin.cash-bank-books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cash-bank-books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:255',
            'under' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_type' => 'nullable|string|in:D,C',
            'credit_card' => 'nullable|string|in:Y,N,W',
            'bank_charges' => 'nullable|numeric',
            'flag' => 'nullable|string|max:255',
            
            // Contact Information
            'address' => 'nullable|string',
            'address1' => 'nullable|string',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:255',
            'birth_day' => 'nullable|date',
            'anniversary_day' => 'nullable|date',
            'contact_person_1' => 'nullable|string|max:255',
            'contact_person_2' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:255',
            
            // Account Details
            'account_no' => 'nullable|string|max:255',
            'report_no' => 'nullable|string|max:255',
            
            // GST & Settings
            'input_gst_purchase' => 'nullable|boolean',
            'output_gst_income' => 'nullable|boolean',
            'cheque_clearance_method' => 'nullable|string|in:P,I',
            'receipts' => 'nullable|string|in:S,I',
        ]);

        // Handle boolean fields
        $validated['input_gst_purchase'] = $request->has('input_gst_purchase');
        $validated['output_gst_income'] = $request->has('output_gst_income');

        $cashBankBook = CashBankBook::create($validated);
        $this->notifyCreated($cashBankBook->name);
        return redirect()->route('admin.cash-bank-books.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CashBankBook $cashBankBook)
    {
        return view('admin.cash-bank-books.show', compact('cashBankBook'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashBankBook $cashBankBook)
    {
        return view('admin.cash-bank-books.edit', compact('cashBankBook'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashBankBook $cashBankBook)
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:255',
            'under' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_type' => 'nullable|string|in:D,C',
            'credit_card' => 'nullable|string|in:Y,N,W',
            'bank_charges' => 'nullable|numeric',
            'flag' => 'nullable|string|max:255',
            
            // Contact Information
            'address' => 'nullable|string',
            'address1' => 'nullable|string',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:255',
            'birth_day' => 'nullable|date',
            'anniversary_day' => 'nullable|date',
            'contact_person_1' => 'nullable|string|max:255',
            'contact_person_2' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:255',
            
            // Account Details
            'account_no' => 'nullable|string|max:255',
            'report_no' => 'nullable|string|max:255',
            
            // GST & Settings
            'input_gst_purchase' => 'nullable|boolean',
            'output_gst_income' => 'nullable|boolean',
            'cheque_clearance_method' => 'nullable|string|in:P,I',
            'receipts' => 'nullable|string|in:S,I',
        ]);

        // Handle boolean fields
        $validated['input_gst_purchase'] = $request->has('input_gst_purchase');
        $validated['output_gst_income'] = $request->has('output_gst_income');

        $cashBankBook->update($validated);
        $this->notifyUpdated($cashBankBook->name);
        return redirect()->route('admin.cash-bank-books.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashBankBook $cashBankBook)
    {
        $cashBankBookName = $cashBankBook->name;
        $cashBankBook->delete();
        $this->notifyDeleted($cashBankBookName);
        return redirect()->route('admin.cash-bank-books.index');
    }

    /**
     * Delete multiple cash bank books
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'cash_bank_books_ids' => $request->input('cash_bank_books_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'cash_bank_books_ids' => 'required|array|min:1',
            'cash_bank_books_ids.*' => 'required|integer|exists:cash_bank_books,id'
        ]);

        try {
            $bookIds = $request->cash_bank_books_ids;
            $books = CashBankBook::whereIn('id', $bookIds)->get();
            
            if ($books->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cash bank book entries found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $bookNames = [];

            foreach ($books as $book) {
                $bookNames[] = $book->name;
                $book->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1 
                ? "Cash bank book entry '{$bookNames[0]}' deleted successfully."
                : "{$deletedCount} cash bank book entries deleted successfully.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple cash bank book deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash bank book entries. Please try again.'
            ], 500);
        }
    }
}
