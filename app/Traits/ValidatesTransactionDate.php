<?php

namespace App\Traits;

use App\Services\TransactionDateValidator;
use Illuminate\Http\Request;

trait ValidatesTransactionDate
{
    /**
     * Validate transaction date in controller
     * 
     * @param Request $request
     * @param string $transactionType
     * @param string $dateField - The request field name containing the date
     * @param int|null $excludeId - Exclude this ID when validating (for updates)
     * @return array|null - Returns error response array if invalid, null if valid
     */
    protected function validateTransactionDate(Request $request, string $transactionType, string $dateField = 'date', ?int $excludeId = null): ?array
    {
        $date = $request->input($dateField);
        
        // Also check nested fields like header.bill_date
        if (!$date && str_contains($dateField, '.')) {
            $parts = explode('.', $dateField);
            $data = $request->input($parts[0]);
            $date = $data[$parts[1]] ?? null;
        }

        if (!$date) {
            return [
                'success' => false,
                'message' => 'Date is required',
                'error_type' => 'date_validation'
            ];
        }

        $result = TransactionDateValidator::validate($transactionType, $date, $excludeId);

        if (!$result['valid']) {
            return [
                'success' => false,
                'message' => $result['message'],
                'error_type' => 'date_validation',
                'last_date' => $result['last_date'] ?? null,
                'last_date_formatted' => $result['last_date_formatted'] ?? null,
                'max_date' => $result['max_date'] ?? null
            ];
        }

        return null; // Valid
    }

    /**
     * Return JSON error response for invalid date
     */
    protected function dateValidationErrorResponse(array $error)
    {
        return response()->json($error, 422);
    }
}
