<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\RegionalManager;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class RegionalManagerController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RegionalManager::query();
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
                      ->orWhere('mkt_mgr', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status', $request->get('status_filter'));
        }

        $regionalManagers = $query->orderBy('name')->paginate(10);
        // For AJAX requests, return JSON data for modal
        if ($request->ajax()) {
            // If it's a modal request, return JSON data
            if ($request->get('modal') === 'true') {
                return response()->json($regionalManagers->items());
            }
            // Otherwise return rendered HTML for search
            return view('admin.regional-managers.index', compact('regionalManagers'))->render();
        }

        return view('admin.regional-managers.index', compact('regionalManagers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.regional-managers.create');
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
            'mkt_mgr' => 'nullable|string|max:255',
        ]);
        try {
            $regionalManager = RegionalManager::create([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'mkt_mgr' => $request->mkt_mgr,
            ]);
            $this->notifyCreated($regionalManager->name ?? 'Regional Manager');
            return redirect()->route('admin.regional-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RegionalManager $regionalManager)
    {
        if (request()->ajax()) {
            return response()->json([
                'id' => $regionalManager->id,
                'name' => $regionalManager->name,
                'code' => $regionalManager->code,
                'address' => $regionalManager->address,
                'telephone' => $regionalManager->telephone,
                'mobile' => $regionalManager->mobile,
                'email' => $regionalManager->email,
                'status' => $regionalManager->status,
                'mkt_mgr' => $regionalManager->mkt_mgr,
                'created_at' => $regionalManager->created_at ? $regionalManager->created_at->format('M d, Y h:i A') : null,
                'updated_at' => $regionalManager->updated_at ? $regionalManager->updated_at->format('M d, Y h:i A') : null,
            ]);
        }

        return redirect()->route('admin.regional-managers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegionalManager $regionalManager)
    {
        return view('admin.regional-managers.edit', compact('regionalManager'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegionalManager $regionalManager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'telephone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:100',
            'mkt_mgr' => 'nullable|string|max:255',
        ]);
        try {
            $regionalManager->update([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'mkt_mgr' => $request->mkt_mgr,
            ]);
            $this->notifyUpdated($regionalManager->name ?? 'Regional Manager');
            return redirect()->route('admin.regional-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegionalManager $regionalManager)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete regional manager: ' . $regionalManager->id . ' - ' . $regionalManager->name);
            // Store name before deletion for log
            $regionalManagerName = $regionalManager->name;
            // Permanently delete from database
            $regionalManager->delete();
            // Log successful deletion
            Log::info('Regional Manager permanently deleted: ' . $regionalManager->id . ' - ' . $regionalManagerName);
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Regional Manager deleted successfully!'
                ]);
            }

            $this->notifyDeleted($regionalManagerName);
            return redirect()->route('admin.regional-managers.index');
        } catch (\Exception $e) {
            Log::error('Error deleting regional manager: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting regional manager: ' . $e->getMessage()
                ], 500);
            }

            $this->notifyError('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete multiple regional managers
     */
    public function multipleDelete(Request $request)
    {
        $request->merge([
            'regional_manager_ids' => $request->input('regional_manager_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'regional_manager_ids' => 'required|array|min:1',
            'regional_manager_ids.*' => 'required|integer|exists:regional_managers,id'
        ]);

        try {
            $ids = $request->regional_manager_ids;
            $regionalManagers = RegionalManager::whereIn('id', $ids)->get();

            if ($regionalManagers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No regional managers found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($regionalManagers as $rm) {
                $rm->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 regional manager deleted successfully.'
                : $deletedCount . ' regional managers deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple regional managers deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete regional managers. Please try again.'
            ], 500);
        }
    }
}
