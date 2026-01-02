@extends('layouts.admin')

@section('title', 'Remove Batch Hold Status')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">REMOVE BATCH HOLD STATUS</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="POST" id="filterForm" action="">
                @csrf
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Item :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="item_id" id="item_id" class="form-select form-select-sm" required>
                            <option value="">Select Item</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->id }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Batch No :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="batch_id" id="batch_id" class="form-select form-select-sm" required>
                            <option value="">Select Batch</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 offset-md-6 text-end">
                        <button type="submit" class="btn btn-danger border px-4 fw-bold shadow-sm me-2">Remove Hold</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
