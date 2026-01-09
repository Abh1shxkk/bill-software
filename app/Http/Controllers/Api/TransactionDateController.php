<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionDateValidator;
use Illuminate\Http\Request;

class TransactionDateController extends Controller
{
    /**
     * Validate a transaction date via AJAX
     * 
     * POST /api/validate-transaction-date
     * Body: { transaction_type: 'sale', date: '2026-01-08', exclude_id: null }
     */
    public function validate(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required|string',
            'date' => 'required|date',
            'exclude_id' => 'nullable|integer'
        ]);

        $result = TransactionDateValidator::validate(
            $request->transaction_type,
            $request->date,
            $request->exclude_id
        );

        return response()->json($result, $result['valid'] ? 200 : 422);
    }

    /**
     * Get allowed date range for a transaction type
     * 
     * GET /api/transaction-date-range/{type}
     */
    public function getDateRange(string $type)
    {
        $range = TransactionDateValidator::getAllowedDateRange($type);
        
        return response()->json([
            'success' => true,
            ...$range
        ]);
    }

    /**
     * Get last transaction date for a type
     * 
     * GET /api/last-transaction-date/{type}
     */
    public function getLastDate(string $type)
    {
        $lastDate = TransactionDateValidator::getLastTransactionDate($type);
        
        return response()->json([
            'success' => true,
            'last_date' => $lastDate,
            'last_date_formatted' => $lastDate ? \Carbon\Carbon::parse($lastDate)->format('d-m-Y') : null
        ]);
    }

    /**
     * Get all supported transaction types
     * 
     * GET /api/transaction-types
     */
    public function getTypes()
    {
        return response()->json([
            'success' => true,
            'types' => TransactionDateValidator::getSupportedTypes()
        ]);
    }
}
