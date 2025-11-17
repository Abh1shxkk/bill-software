<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaManager;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaManagerController extends Controller
{
    use CrudNotificationTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AreaManager::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $searchField = $request->get('search_field', 'all');
            
            if ($searchField === 'all') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhere('reg_mgr', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status', $request->get('status_filter'));
        }

        $areaManagers = $query->orderBy('name')->paginate(10);

        // For AJAX requests, return rendered HTML
        if ($request->ajax()) {
            return view('admin.area-managers.index', compact('areaManagers'))->render();
        }

        return view('admin.area-managers.index', compact('areaManagers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.area-managers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'telephone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:100',
            'reg_mgr' => 'nullable|string|max:255',
        ]);

        try {
            $areaManager = AreaManager::create([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'reg_mgr' => $request->reg_mgr,
            ]);

            $this->notifyCreated($areaManager->name ?? 'Area Manager');
            return redirect()->route('admin.area-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating area manager: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AreaManager $areaManager)
    {
        if (request()->ajax()) {
            return response()->json([
                'id' => $areaManager->id,
                'name' => $areaManager->name,
                'code' => $areaManager->code,
                'address' => $areaManager->address,
                'telephone' => $areaManager->telephone,
                'mobile' => $areaManager->mobile,
                'email' => $areaManager->email,
                'status' => $areaManager->status,
                'reg_mgr' => $areaManager->reg_mgr,
                'created_at' => $areaManager->created_at ? $areaManager->created_at->format('M d, Y h:i A') : null,
                'updated_at' => $areaManager->updated_at ? $areaManager->updated_at->format('M d, Y h:i A') : null,
            ]);
        }

        return redirect()->route('admin.area-managers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AreaManager $areaManager)
    {
        return view('admin.area-managers.edit', compact('areaManager'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AreaManager $areaManager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'telephone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:100',
            'reg_mgr' => 'nullable|string|max:255',
        ]);

        try {
            $areaManager->update([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'reg_mgr' => $request->reg_mgr,
            ]);

            $this->notifyUpdated($areaManager->name ?? 'Area Manager');
            return redirect()->route('admin.area-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating area manager: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AreaManager $areaManager)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete area manager: ' . $areaManager->id . ' - ' . $areaManager->name);
            
            // Store name before deletion for log
            $areaManagerName = $areaManager->name;
            
            // Permanently delete from database
            $areaManager->delete();
            
            // Log successful deletion
            Log::info('Area Manager permanently deleted: ' . $areaManager->id . ' - ' . $areaManagerName);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area Manager deleted successfully!'
                ]);
            }

            $this->notifyDeleted($areaManagerName);
            return redirect()->route('admin.area-managers.index');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting area manager: ' . $e->getMessage());
            
            $this->notifyError('Error deleting area manager: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting area manager: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Delete multiple area managers
     */
    public function multipleDelete(Request $request)
    {
        $request->merge([
            'area_manager_ids' => $request->input('area_manager_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'area_manager_ids' => 'required|array|min:1',
            'area_manager_ids.*' => 'required|integer|exists:area_managers,id'
        ]);

        try {
            $ids = $request->area_manager_ids;
            $areaManagers = AreaManager::whereIn('id', $ids)->get();

            if ($areaManagers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No area managers found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($areaManagers as $am) {
                $am->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 area manager deleted successfully.'
                : $deletedCount . ' area managers deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple area managers deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete area managers. Please try again.'
            ], 500);
        }
    }
}
