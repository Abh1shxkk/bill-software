<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Area::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $searchField = $request->get('search_field', 'all');
            
            if ($searchField === 'all') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('alter_code', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status', $request->get('status_filter'));
        }

        $areas = $query->orderBy('name')->paginate(10);

        // For AJAX requests, return rendered HTML
        if ($request->ajax()) {
            return view('admin.areas.index', compact('areas'))->render();
        }

        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:100',
        ]);

        try {
            $area = Area::create([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyCreated($area->name);
            return redirect()->route('admin.areas.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating area: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:100',
        ]);

        try {
            $area->update([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyUpdated($area->name);
            return redirect()->route('admin.areas.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating area: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete area: ' . $area->id . ' - ' . $area->name);
            
            // Store name before deletion for log
            $areaName = $area->name;
            
            // Permanently delete from database
            $area->delete();
            
            // Log successful deletion
            Log::info('Area permanently deleted: ' . $area->id . ' - ' . $areaName);

            $this->notifyDeleted($areaName);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area deleted successfully!'
                ]);
            }

            return redirect()->route('admin.areas.index');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting area: ' . $e->getMessage());
            
            $this->notifyError('Error deleting area: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting area: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Delete multiple areas
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'area_ids' => $request->input('area_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'area_ids' => 'required|array|min:1',
            'area_ids.*' => 'required|integer|exists:areas,id'
        ]);

        try {
            $ids = $request->area_ids;
            $areas = Area::whereIn('id', $ids)->get();

            if ($areas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No areas found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($areas as $ar) {
                $ar->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 area deleted successfully.'
                : $deletedCount . ' areas deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple areas deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete areas. Please try again.'
            ], 500);
        }
    }
}