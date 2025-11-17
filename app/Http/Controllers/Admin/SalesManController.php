<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesMan;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;

class SalesManController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalesMan::where('is_deleted', '!=', 1);

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $searchField = $request->get('search_field', 'all');

            if ($searchField === 'all') {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('mobile', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('telephone', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('city', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('area_mgr_name', 'LIKE', "%{$searchTerm}%");
                });
            } else {
                $query->where($searchField, 'LIKE', "%{$searchTerm}%");
            }
        }

        $salesMen = $query->orderBy('created_at', 'desc')->paginate(10);

        // Handle AJAX requests
        if ($request->ajax()) {
            return view('admin.sales-men.index', compact('salesMen'))->render();
        }
            
        return view('admin.sales-men.index', compact('salesMen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sales-men.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:sales_men,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'pin' => 'nullable|string|max:10',
            'sales_type' => 'nullable|in:S,C,B',
            'delivery_type' => 'nullable|in:S,D,B',
            'area_mgr_code' => 'nullable|string|max:255',
            'area_mgr_name' => 'nullable|string|max:255',
            'monthly_target' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        $validated['created_date'] = now();

        $salesMan = SalesMan::create($validated);
        $this->notifyCreated($salesMan->name);

        return redirect()->route('admin.sales-men.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesMan $salesMan)
    {
        return view('admin.sales-men.edit', compact('salesMan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesMan $salesMan)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:sales_men,code,' . $salesMan->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pin' => 'nullable|string|max:10',
            'sales_type' => 'nullable|in:S,C,B',
            'delivery_type' => 'nullable|in:S,D,B',
            'area_mgr_code' => 'nullable|string|max:255',
            'area_mgr_name' => 'nullable|string|max:255',
            'monthly_target' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        $salesMan->update($validated);
        $this->notifyUpdated($salesMan->name);
        return redirect()->route('admin.sales-men.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesMan $salesMan)
    {
        $salesManName = $salesMan->name;
        
        // Soft delete by setting is_deleted = 1
        $salesMan->update([
            'is_deleted' => 1,
            'modified_date' => now()
        ]);

        $this->notifyDeleted($salesManName);
        return redirect()->route('admin.sales-men.index');
    }

    /**
     * Delete multiple sales men (soft delete)
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'sales_man_ids' => $request->input('sales_man_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'sales_man_ids' => 'required|array|min:1',
            'sales_man_ids.*' => 'required|integer|exists:sales_men,id'
        ]);

        try {
            $ids = $request->sales_man_ids;
            $records = SalesMan::whereIn('id', $ids)->where('is_deleted', '!=', 1)->get();

            if ($records->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sales men found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($records as $rec) {
                $rec->update([
                    'is_deleted' => 1,
                    'modified_date' => now()
                ]);
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 sales man deleted successfully.'
                : $deletedCount . ' sales men deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Multiple sales men deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sales men. Please try again.'
            ], 500);
        }
    }
}
