<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotkey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HotkeyController extends Controller
{
    /**
     * Display a listing of hotkeys.
     */
    public function index(Request $request)
    {
        $categories = Hotkey::getCategories();
        $scopes = Hotkey::getScopes();

        return view('admin.administration.hotkeys.index', compact('categories', 'scopes'));
    }

    /**
     * Get hotkeys data for AJAX infinite scroll.
     */
    public function getData(Request $request)
    {
        $query = Hotkey::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by scope
        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('module_name', 'like', "%{$search}%")
                    ->orWhere('key_combination', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 30);
        $total = $query->count();
        
        $hotkeys = $query->orderBy('category')
            ->orderBy('module_name')
            ->paginate($perPage);

        return response()->json([
            'hotkeys' => $hotkeys->items(),
            'total' => $total,
            'has_more' => $hotkeys->hasMorePages(),
            'current_page' => $hotkeys->currentPage(),
            'last_page' => $hotkeys->lastPage()
        ]);
    }

    /**
     * Show the form for editing the specified hotkey.
     */
    public function edit(Hotkey $hotkey)
    {
        $categories = Hotkey::getCategories();
        $scopes = Hotkey::getScopes();
        
        return view('admin.administration.hotkeys.edit', compact('hotkey', 'categories', 'scopes'));
    }

    /**
     * Update the specified hotkey.
     */
    public function update(Request $request, Hotkey $hotkey)
    {
        $validated = $request->validate([
            'key_combination' => 'required|string|max:50',
            'module_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Normalize key combination to lowercase
        $keyCombination = strtolower($validated['key_combination']);

        // Check if key is already in use by another hotkey
        if (Hotkey::isKeyInUse($keyCombination, $hotkey->id)) {
            return back()->withErrors([
                'key_combination' => 'This key combination is already assigned to another module.'
            ])->withInput();
        }

        $hotkey->update([
            'key_combination' => $keyCombination,
            'module_name' => $validated['module_name'],
            'description' => $validated['description'] ?? $hotkey->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.administration.hotkeys.index')
            ->with('success', 'Hotkey updated successfully!');
    }

    /**
     * Toggle hotkey status.
     */
    public function toggleStatus(Hotkey $hotkey)
    {
        $hotkey->update(['is_active' => !$hotkey->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $hotkey->is_active,
            'message' => $hotkey->is_active ? 'Hotkey activated' : 'Hotkey deactivated'
        ]);
    }

    /**
     * Check if key combination is available.
     */
    public function checkKey(Request $request)
    {
        $key = strtolower($request->key);
        $excludeId = $request->exclude_id;

        $inUse = Hotkey::isKeyInUse($key, $excludeId);

        if ($inUse) {
            $existingHotkey = Hotkey::where('key_combination', $key)
                ->where('is_active', true)
                ->first();

            return response()->json([
                'available' => false,
                'message' => "This key is already assigned to: {$existingHotkey->module_name}"
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Key combination is available'
        ]);
    }

    /**
     * Reset all hotkeys to default.
     */
    public function resetToDefault()
    {
        // Re-run the seeder
        $seeder = new \Database\Seeders\HotkeySeeder();
        $seeder->run();

        return redirect()->route('admin.administration.hotkeys.index')
            ->with('success', 'All hotkeys have been reset to default!');
    }

    /**
     * Get hotkeys JSON for JavaScript (used by keyboard-shortcuts.js)
     */
    public function getHotkeysJson()
    {
        $hotkeys = Hotkey::where('is_active', true)
            ->where('scope', 'global')
            ->get();

        $shortcuts = [];
        foreach ($hotkeys as $hotkey) {
            // Skip special action hotkeys (those starting with #)
            if (str_starts_with($hotkey->route_name, '#')) {
                continue;
            }

            // Check if route exists
            if (Route::has($hotkey->route_name)) {
                $shortcuts[$hotkey->key_combination] = [
                    'url' => route($hotkey->route_name),
                    'description' => $hotkey->module_name
                ];
            }
        }

        return response()->json($shortcuts);
    }
}
