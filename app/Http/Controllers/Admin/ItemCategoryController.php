<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemCategoryController extends Controller
{
    use CrudNotificationTrait;

    public function index(Request $request)
    {
        $categories = ItemCategory::orderBy('id', 'desc')->paginate(10);
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.item-category.index', compact('categories'))->render();
        }
        
        return view('admin.item-category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.item-category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'alter_code' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        try {
            $itemCategory = ItemCategory::create($validated);
            $this->notifyCreated($itemCategory->name ?? 'Item Category');
            return redirect()->route('admin.item-category.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating category: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit(ItemCategory $itemCategory)
    {
        return view('admin.item-category.edit', compact('itemCategory'));
    }

    public function update(Request $request, ItemCategory $itemCategory)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'alter_code' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        try {
            $itemCategory->update($validated);
            $this->notifyUpdated($itemCategory->name ?? 'Item Category');
            return redirect()->route('admin.item-category.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating category: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(ItemCategory $itemCategory)
    {
        try {
            $itemCategoryName = $itemCategory->name ?? 'Item Category';
            $itemCategory->delete();
            $this->notifyDeleted($itemCategoryName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting category: ' . $e->getMessage());
            return back();
        }
    }

    public function multipleDelete(Request $request)
    {
        $request->merge([
            'item_category_ids' => $request->input('item_category_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'item_category_ids' => 'required|array|min:1',
            'item_category_ids.*' => 'required|integer|exists:item_categories,id'
        ]);

        try {
            $ids = $request->item_category_ids;
            $categories = ItemCategory::whereIn('id', $ids)->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No item categories found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($categories as $category) {
                $category->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 item category deleted successfully.'
                : $deletedCount . ' item categories deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple item categories deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item categories. Please try again.'
            ], 500);
        }
    }
}
