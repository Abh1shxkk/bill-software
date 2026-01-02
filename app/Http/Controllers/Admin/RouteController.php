<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RouteController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Route::query();

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

        $routes = $query->orderBy('name')->paginate(10);

        // For AJAX requests, return rendered HTML
        if ($request->ajax()) {
            return view('admin.routes.index', compact('routes'))->render();
        }

        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.routes.create');
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
            $route = Route::create([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyCreated($route->name);
            return redirect()->route('admin.routes.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating route: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:100',
        ]);

        try {
            $route->update([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyUpdated($route->name);
            return redirect()->route('admin.routes.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating route: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete route: ' . $route->id . ' - ' . $route->name);
            
            // Store name before deletion for log
            $routeName = $route->name;
            
            // Permanently delete from database
            $route->delete();
            
            // Log successful deletion
            Log::info('Route permanently deleted: ' . $route->id . ' - ' . $routeName);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Route deleted successfully!'
                ]);
            }

            $this->notifyDeleted($routeName);
            return redirect()->route('admin.routes.index');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting route: ' . $e->getMessage());
            
            $this->notifyError('Error deleting route: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting route: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Delete multiple routes
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'route_ids' => $request->input('route_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'route_ids' => 'required|array|min:1',
            'route_ids.*' => 'required|integer|exists:routes,id'
        ]);

        try {
            $ids = $request->route_ids;
            $routes = Route::whereIn('id', $ids)->get();

            if ($routes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No routes found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($routes as $rt) {
                $rt->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 route deleted successfully.'
                : $deletedCount . ' routes deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple routes deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete routes. Please try again.'
            ], 500);
        }
    }

    /**
     * Search route by code (AJAX)
     */
    public function search(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return response()->json(['name' => '']);
        }

        $route = Route::where('code', $code)->first();

        return response()->json([
            'id' => $route->id ?? null,
            'name' => $route->name ?? '',
            'code' => $route->code ?? ''
        ]);
    }
}
