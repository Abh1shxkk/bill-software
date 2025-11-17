<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralManager;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralManagerController extends Controller
{
    use CrudNotificationTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GeneralManager::query();

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
                      ->orWhere('dc_mgr', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status', $request->get('status_filter'));
        }

        $generalManagers = $query->orderBy('name')->paginate(10);

        // For AJAX requests, return JSON data for modal
        if ($request->ajax()) {
            // If it's a modal request, return JSON data
            if ($request->get('modal') === 'true') {
                return response()->json($generalManagers->items());
            }
            // Otherwise return rendered HTML for search
            return view('admin.general-managers.index', compact('generalManagers'))->render();
        }

        return view('admin.general-managers.index', compact('generalManagers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.general-managers.create');
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
            'dc_mgr' => 'nullable|string|max:255',
        ]);

        try {
            $generalManager = GeneralManager::create([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'dc_mgr' => $request->dc_mgr,
            ]);

            $this->notifyCreated($generalManager->name ?? 'General Manager');
            return redirect()->route('admin.general-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating general manager: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralManager $generalManager)
    {
        if (request()->ajax()) {
            return response()->json([
                'id' => $generalManager->id,
                'name' => $generalManager->name,
                'code' => $generalManager->code,
                'address' => $generalManager->address,
                'telephone' => $generalManager->telephone,
                'mobile' => $generalManager->mobile,
                'email' => $generalManager->email,
                'status' => $generalManager->status,
                'dc_mgr' => $generalManager->dc_mgr,
                'created_at' => $generalManager->created_at ? $generalManager->created_at->format('M d, Y h:i A') : null,
                'updated_at' => $generalManager->updated_at ? $generalManager->updated_at->format('M d, Y h:i A') : null,
            ]);
        }

        return redirect()->route('admin.general-managers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralManager $generalManager)
    {
        return view('admin.general-managers.edit', compact('generalManager'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneralManager $generalManager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'telephone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:100',
            'dc_mgr' => 'nullable|string|max:255',
        ]);

        try {
            $generalManager->update([
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'status' => $request->status,
                'dc_mgr' => $request->dc_mgr,
            ]);

            $this->notifyUpdated($generalManager->name ?? 'General Manager');
            return redirect()->route('admin.general-managers.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating general manager: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralManager $generalManager)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete general manager: ' . $generalManager->id . ' - ' . $generalManager->name);
            
            // Store name before deletion for log
            $generalManagerName = $generalManager->name;
            
            // Permanently delete from database
            $generalManager->delete();
            
            // Log successful deletion
            Log::info('General Manager permanently deleted: ' . $generalManager->id . ' - ' . $generalManagerName);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'General Manager deleted successfully!'
                ]);
            }

            $this->notifyDeleted($generalManagerName);
            return redirect()->route('admin.general-managers.index');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting general manager: ' . $e->getMessage());
            
            $this->notifyError('Error deleting general manager: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting general manager: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Delete multiple general managers
     */
    public function multipleDelete(Request $request)
    {
        $request->merge([
            'general_manager_ids' => $request->input('general_manager_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'general_manager_ids' => 'required|array|min:1',
            'general_manager_ids.*' => 'required|integer|exists:general_managers,id'
        ]);

        try {
            $ids = $request->general_manager_ids;
            $generalManagers = GeneralManager::whereIn('id', $ids)->get();

            if ($generalManagers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No general managers found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($generalManagers as $gm) {
                $gm->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 general manager deleted successfully.'
                : $deletedCount . ' general managers deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple general managers deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete general managers. Please try again.'
            ], 500);
        }
    }
}
