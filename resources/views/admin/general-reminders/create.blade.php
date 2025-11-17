@extends('layouts.admin')

@section('title', 'Create Reminder')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-bell me-2"></i> Create Reminder</h4>
    <div class="text-muted small">Add a new reminder</div>
  </div>
  <a href="{{ route('admin.general-reminders.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.general-reminders.store') }}" method="POST">
      @csrf

      <div class="row">
        <!-- Name -->
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" 
                 id="name" name="name" value="{{ old('name') }}" placeholder="Enter name">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Code -->
        <div class="col-md-6 mb-3">
          <label for="code" class="form-label">Code</label>
          <input type="text" class="form-control @error('code') is-invalid @enderror" 
                 id="code" name="code" value="{{ old('code') }}" placeholder="Enter code">
          @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- Due Date -->
        <div class="col-md-6 mb-3">
          <label for="due_date" class="form-label">Due Date <small class="text-muted">(eg. 25 JAN)</small></label>
          <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                 id="due_date" name="due_date" value="{{ old('due_date') }}">
          @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control @error('status') is-invalid @enderror" 
                 id="status" name="status" value="{{ old('status') }}" placeholder="Enter status">
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.general-reminders.index') }}" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Create Reminder
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
