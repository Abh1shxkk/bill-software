<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsnCode;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class HsnCodeController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of HSN codes
     */
    public function index()
    {
        $search = request('search');
        $status = request('status');
        $all = request('all'); // For AJAX requests to get all records

        $query = HsnCode::query()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('hsn_code', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function($query) use ($status) {
                $query->where('is_inactive', $status === 'inactive' ? 1 : 0);
            })
            ->orderByDesc('created_at');

        // If AJAX request with 'all=1', return all active HSN codes as JSON
        if (request()->ajax() && $all == '1') {
            $hsnCodes = $query->where('is_inactive', 0)->get();
            return response()->json($hsnCodes);
        }

        $hsnCodes = $query->paginate(15)->withQueryString();

        return view('admin.hsn-codes.index', compact('hsnCodes', 'search', 'status'));
    }

    /**
     * Show the form for creating a new HSN code
     */
    public function create()
    {
        return view('admin.hsn-codes.create');
    }

    /**
     * Store a newly created HSN code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'cgst_percent' => 'nullable|numeric|min:0|max:100',
            'sgst_percent' => 'nullable|numeric|min:0|max:100',
            'igst_percent' => 'nullable|numeric|min:0|max:100',
            'total_gst_percent' => 'nullable|numeric|min:0|max:100',
            'is_inactive' => 'nullable|boolean',
            'is_service' => 'nullable|boolean',
        ]);

        // Convert checkbox values
        $validated['is_inactive'] = $request->has('is_inactive') ? true : false;
        $validated['is_service'] = $request->has('is_service') ? true : false;

        $hsnCode = HsnCode::create($validated);
        $this->notifyCreated($hsnCode->name);
        return redirect()->route('admin.hsn-codes.index');
    }

    /**
     * Show the form for editing the specified HSN code
     */
    public function edit(HsnCode $hsnCode)
    {
        return view('admin.hsn-codes.edit', compact('hsnCode'));
    }

    /**
     * Update the specified HSN code
     */
    public function update(Request $request, HsnCode $hsnCode)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'cgst_percent' => 'nullable|numeric|min:0|max:100',
            'sgst_percent' => 'nullable|numeric|min:0|max:100',
            'igst_percent' => 'nullable|numeric|min:0|max:100',
            'total_gst_percent' => 'nullable|numeric|min:0|max:100',
            'is_inactive' => 'nullable|boolean',
            'is_service' => 'nullable|boolean',
        ]);

        // Convert checkbox values
        $validated['is_inactive'] = $request->has('is_inactive') ? true : false;
        $validated['is_service'] = $request->has('is_service') ? true : false;

        $hsnCode->update($validated);
        $this->notifyUpdated($hsnCode->name);
        return redirect()->route('admin.hsn-codes.index');
    }

    /**
     * Remove the specified HSN code
     */
    public function destroy(HsnCode $hsnCode)
    {
        $hsnCodeName = $hsnCode->name;
        $hsnCode->delete();
        $this->notifyDeleted($hsnCodeName);
        return back();
    }

    /**
     * Delete multiple HSN codes
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'hsn_codes_ids' => $request->input('hsn_codes_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'hsn_codes_ids' => 'required|array|min:1',
            'hsn_codes_ids.*' => 'required|integer|exists:hsn_codes,id'
        ]);

        try {
            $hsnCodeIds = $request->hsn_codes_ids;
            $hsnCodes = HsnCode::whereIn('id', $hsnCodeIds)->get();
            
            if ($hsnCodes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No HSN codes found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            $hsnCodeNames = [];

            foreach ($hsnCodes as $hsnCode) {
                $hsnCodeNames[] = $hsnCode->name;
                $hsnCode->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1 
                ? "HSN code '{$hsnCodeNames[0]}' deleted successfully."
                : "{$deletedCount} HSN codes deleted successfully.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple HSN code deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete HSN codes. Please try again.'
            ], 500);
        }
    }
}
