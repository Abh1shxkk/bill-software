@props(['module', 'icon', 'label', 'createRoute' => null, 'indexRoute' => null])

@if(auth()->user()->hasPermission($module, 'view'))
<div class="mt-2">
    <button class="btn btn-sm w-100 text-start text-white d-flex align-items-center px-2"
        data-bs-toggle="collapse" data-bs-target="#menu{{ Str::studly($module) }}" style="background:transparent;">
        <i class="{{ $icon }} me-2"></i> <span class="label">{{ $label }}</span>
    </button>
    <div class="collapse" id="menu{{ Str::studly($module) }}">
        @if($createRoute && auth()->user()->hasPermission($module, 'create'))
            <a class="nav-link ms-3 d-flex align-items-center" href="{{ route($createRoute) }}">
                <span class="label">Add {{ Str::singular($label) }}</span>
            </a>
        @endif
        @if($indexRoute)
            <a class="nav-link ms-3 d-flex align-items-center" href="{{ route($indexRoute) }}">
                <span class="label">All {{ $label }}</span>
            </a>
        @endif
        {{ $slot }}
    </div>
</div>
@endif
