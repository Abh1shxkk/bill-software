<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StateController extends Controller
{
    use CrudNotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = State::query();

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

        $states = $query->orderBy('name')->paginate(10);

        // For AJAX requests, return rendered HTML
        if ($request->ajax()) {
            return view('admin.states.index', compact('states'))->render();
        }

        return view('admin.states.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.states.create');
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
            $state = State::create([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyCreated($state->name);
            return redirect()->route('admin.states.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating state: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        return view('admin.states.edit', compact('state'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alter_code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:100',
        ]);

        try {
            $state->update([
                'name' => $request->name,
                'alter_code' => $request->alter_code,
                'status' => $request->status,
            ]);
            
            $this->notifyUpdated($state->name);
            return redirect()->route('admin.states.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating state: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        try {
            // Log the delete attempt
            Log::info('Attempting to permanently delete state: ' . $state->id . ' - ' . $state->name);
            
            // Store name before deletion for log
            $stateName = $state->name;
            
            // Permanently delete from database
            $state->delete();
            
            // Log successful deletion
            Log::info('State permanently deleted: ' . $state->id . ' - ' . $stateName);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'State deleted successfully!'
                ]);
            }

            $this->notifyDeleted($stateName);
            return redirect()->route('admin.states.index');
        } catch (\Exception $e) {
            Log::error('Error deleting state: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting state: ' . $e->getMessage()
                ], 500);
            }

            $this->notifyError('Error deleting state: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete multiple states
     */
    public function multipleDelete(Request $request)
    {
        // Accept fallback from item_ids to support older client scripts
        $request->merge([
            'state_ids' => $request->input('state_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'state_ids' => 'required|array|min:1',
            'state_ids.*' => 'required|integer|exists:states,id'
        ]);

        try {
            $ids = $request->state_ids;
            $states = State::whereIn('id', $ids)->get();

            if ($states->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No states found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($states as $st) {
                $st->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 state deleted successfully.'
                : $deletedCount . ' states deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple states deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete states. Please try again.'
            ], 500);
        }
    }
}
