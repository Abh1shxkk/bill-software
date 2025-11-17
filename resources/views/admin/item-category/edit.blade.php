@extends('layouts.admin')

@section('title', 'Edit Item Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-tag me-2"></i> Edit Item Category</h4>
    <div class="text-muted small">Update category details</div>
  </div>
  <a href="{{ route('admin.item-category.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.item-category.update', $itemCategory) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row">
        <!-- Name -->
        <div class="col-md-12 mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" 
                 id="name" name="name" value="{{ old('name', $itemCategory->name) }}" placeholder="Enter category name">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- Alter. code -->
        <div class="col-md-6 mb-3">
          <label for="alter_code" class="form-label">Alter. code</label>
          <input type="text" class="form-control @error('alter_code') is-invalid @enderror" 
                 id="alter_code" name="alter_code" value="{{ old('alter_code', $itemCategory->alter_code) }}" placeholder="Enter alter code">
          @error('alter_code')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control @error('status') is-invalid @enderror" 
                 id="status" name="status" value="{{ old('status', $itemCategory->status) }}" placeholder="Enter status">
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.item-category.index') }}" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Update Category
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
