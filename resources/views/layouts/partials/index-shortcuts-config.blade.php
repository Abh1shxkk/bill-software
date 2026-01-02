{{-- Index Page Shortcuts Configuration - Loaded separately from global shortcuts --}}
@php
    use App\Models\Hotkey;
    
    $indexShortcuts = [];
    $indexCategoryHotkeys = [];
    $hasIndexHotkeys = false;
    
    try {
        if (class_exists(Hotkey::class) && \Schema::hasTable('hotkeys')) {
            $hotkeys = Hotkey::where('is_active', true)
                ->where('scope', 'index')
                ->orderBy('category')
                ->orderBy('module_name')
                ->get();
            
            if ($hotkeys->count() > 0) {
                $hasIndexHotkeys = true;
                
                foreach ($hotkeys as $hotkey) {
                    // Build the shortcut data
                    $shortcutData = [
                        'description' => $hotkey->module_name,
                        'category' => $hotkey->category
                    ];
                    
                    // Handle special action hotkeys (e.g., #add, #delete, #edit)
                    if (str_starts_with($hotkey->route_name, '#')) {
                        $shortcutData['action'] = str_replace('#', '', $hotkey->route_name);
                    } elseif (\Route::has($hotkey->route_name)) {
                        $shortcutData['url'] = route($hotkey->route_name);
                    } else {
                        // For index shortcuts, the route might be dynamic (e.g., admin.items.create)
                        // Store the route name to be resolved at runtime
                        $shortcutData['routeName'] = $hotkey->route_name;
                    }
                    
                    $indexShortcuts[$hotkey->key_combination] = $shortcutData;
                    
                    // Group by category for help panel
                    if (!isset($indexCategoryHotkeys[$hotkey->category])) {
                        $indexCategoryHotkeys[$hotkey->category] = [];
                    }
                    $indexCategoryHotkeys[$hotkey->category][] = [
                        'key' => $hotkey->key_combination,
                        'name' => $hotkey->module_name
                    ];
                }
            }
        }
    } catch (\Exception $e) {
        // Silently fall back
        $hasIndexHotkeys = false;
    }
    
    // Index shortcut category config
    $indexCategoryConfig = [
        'index' => ['name' => 'Index Page Actions', 'icon' => 'bi-list-ul', 'color' => 'text-info'],
    ];
@endphp
<script>
    window.INDEX_SHORTCUTS_CONFIG = {
        // Flag to indicate if using database hotkeys
        isDynamic: {{ $hasIndexHotkeys ? 'true' : 'false' }},
        
        // Category configuration for help panel
        categories: @json($indexCategoryConfig),
        
        // Hotkeys grouped by category for help panel
        categoryHotkeys: @json($indexCategoryHotkeys),
        
        // All index shortcuts
        @if($hasIndexHotkeys)
        shortcuts: @json($indexShortcuts)
        @else
        // Fallback to static index shortcuts if database not available
        shortcuts: {
            'insert': {
                action: 'add',
                description: 'Add New Record',
                category: 'index'
            },
            'delete': {
                action: 'delete',
                description: 'Delete Selected',
                category: 'index'
            },
            'ctrl+e': {
                action: 'edit',
                description: 'Edit Selected',
                category: 'index'
            },
            'ctrl+p': {
                action: 'print',
                description: 'Print',
                category: 'index'
            },
            'ctrl+shift+f': {
                action: 'search',
                description: 'Focus Search',
                category: 'index'
            },
            'ctrl+r': {
                action: 'refresh',
                description: 'Refresh Data',
                category: 'index'
            }
        }
        @endif
    };
</script>
