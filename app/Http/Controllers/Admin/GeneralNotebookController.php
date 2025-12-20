<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralNotebook;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralNotebookController extends Controller
{
    use CrudNotificationTrait;

    public function index(Request $request)
    {
        $query = GeneralNotebook::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $searchField = $request->search_field ?? 'all';
            
            if ($searchField === 'all') {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }
        
        $notebooks = $query->orderBy('id', 'desc')->paginate(15);
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.general-notebook.index', compact('notebooks'))->render();
        }
        
        return view('admin.general-notebook.index', compact('notebooks'));
    }

    public function create()
    {
        return view('admin.general-notebook.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Add validation rules as you specify fields
        ]);

        try {
            $generalNotebook = GeneralNotebook::create($validated);
            $this->notifyCreated($generalNotebook->title ?? 'General Notebook Entry');
            return redirect()->route('admin.general-notebook.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating note: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(GeneralNotebook $generalNotebook)
    {
        return view('admin.general-notebook.show', compact('generalNotebook'));
    }

    public function edit(GeneralNotebook $generalNotebook)
    {
        return view('admin.general-notebook.edit', compact('generalNotebook'));
    }

    public function update(Request $request, GeneralNotebook $generalNotebook)
    {
        $validated = $request->validate([
            // Add validation rules as you specify fields
        ]);

        try {
            $generalNotebook->update($validated);
            $this->notifyUpdated($generalNotebook->title ?? 'General Notebook Entry');
            return redirect()->route('admin.general-notebook.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating note: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(GeneralNotebook $generalNotebook)
    {
        try {
            $generalNotebookName = $generalNotebook->title ?? 'General Notebook Entry';
            $generalNotebook->delete();
            $this->notifyDeleted($generalNotebookName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting note: ' . $e->getMessage());
            return back();
        }
    }

    public function multipleDelete(Request $request)
    {
        $request->merge([
            'general_notebook_ids' => $request->input('general_notebook_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'general_notebook_ids' => 'required|array|min:1',
            'general_notebook_ids.*' => 'required|integer|exists:general_notebooks,id'
        ]);

        try {
            $ids = $request->general_notebook_ids;
            $notebooks = GeneralNotebook::whereIn('id', $ids)->get();

            if ($notebooks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No general notebook entries found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($notebooks as $notebook) {
                $notebook->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 general notebook entry deleted successfully.'
                : $deletedCount . ' general notebook entries deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple general notebook entries deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete general notebook entries. Please try again.'
            ], 500);
        }
    }
}
