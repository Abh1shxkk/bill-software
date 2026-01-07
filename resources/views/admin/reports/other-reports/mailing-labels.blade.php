{{-- Mailing Labels Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">Mailing Labels</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">C(ustomer) / S(upplier) / P(ersonal):</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="list_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('list_type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Sales Man:</label>
                    </div>
                    <div class="col-3">
                        <select name="salesman" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" {{ request('salesman') == $sm->id ? 'selected' : '' }}>
                                    {{ $sm->id }} - {{ $sm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Area:</label>
                    </div>
                    <div class="col-3">
                        <select name="area" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                                    {{ $area->id }} - {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Route:</label>
                    </div>
                    <div class="col-3">
                        <select name="route" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                    {{ $route->id }} - {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Category:</label>
                    </div>
                    <div class="col-3">
                        <select name="category" class="form-select form-select-sm">
                            <option value="00">00</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->id }} - {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                        <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    @if(request('list_type', 'C') == 'C')
                        Customer
                    @elseif(request('list_type') == 'S')
                        Supplier
                    @else
                        Personal Directory
                    @endif
                    Mailing Labels - {{ $reportData->count() }} Records
                </h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print Labels</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($reportData as $record)
                <div class="col-4 mb-3">
                    <div class="border p-2" style="min-height: 120px; font-size: 0.8rem;">
                        <strong>{{ $record->name }}</strong><br>
                        @if(request('list_type', 'C') == 'P')
                            {{ $record->address_office }}<br>
                            @if($record->tel_office)Tel: {{ $record->tel_office }}<br>@endif
                        @else
                            {{ $record->address }}<br>
                            @if($record->city){{ $record->city }}<br>@endif
                        @endif
                        @if($record->mobile)Mob: {{ $record->mobile }}@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">No records found matching the criteria.</div>
    @endif
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.other.mailing-labels") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
