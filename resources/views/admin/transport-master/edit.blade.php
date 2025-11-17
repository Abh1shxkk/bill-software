@extends('layouts.admin')

@section('title', 'Edit Transport Master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-truck me-2"></i> Edit Transport Master</h4>
    <div class="text-muted small">Update transport details</div>
  </div>
  <a href="{{ route('admin.transport-master.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.transport-master.update', $transportMaster) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row">
        <!-- Name -->
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" 
                 id="name" name="name" value="{{ old('name', $transportMaster->name) }}" placeholder="Enter name">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Alter. code -->
        <div class="col-md-6 mb-3">
          <label for="alter_code" class="form-label">Alter. code</label>
          <input type="text" class="form-control @error('alter_code') is-invalid @enderror" 
                 id="alter_code" name="alter_code" value="{{ old('alter_code', $transportMaster->alter_code) }}" placeholder="Enter alter code">
          @error('alter_code')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- Address -->
        <div class="col-md-6 mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea class="form-control @error('address') is-invalid @enderror" 
                    id="address" name="address" rows="3" placeholder="Enter address">{{ old('address', $transportMaster->address) }}</textarea>
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Telephone -->
        <div class="col-md-6 mb-3">
          <label for="telephone" class="form-label">Telephone</label>
          <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                 id="telephone" name="telephone" value="{{ old('telephone', $transportMaster->telephone) }}" placeholder="Enter telephone">
          @error('telephone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- E-Mail -->
        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">E-Mail</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" 
                 id="email" name="email" value="{{ old('email', $transportMaster->email) }}" placeholder="Enter email">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Mobile -->
        <div class="col-md-6 mb-3">
          <label for="mobile" class="form-label">Mobile</label>
          <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                 id="mobile" name="mobile" value="{{ old('mobile', $transportMaster->mobile) }}" placeholder="Enter mobile">
          @error('mobile')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- GST No. -->
        <div class="col-md-6 mb-3">
          <label for="gst_no" class="form-label">GST No.</label>
          <input type="text" class="form-control @error('gst_no') is-invalid @enderror" 
                 id="gst_no" name="gst_no" value="{{ old('gst_no', $transportMaster->gst_no) }}" placeholder="Enter GST number">
          @error('gst_no')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control @error('status') is-invalid @enderror" 
                 id="status" name="status" value="{{ old('status', $transportMaster->status) }}" placeholder="Enter status">
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <!-- Vehicle No. -->
        <div class="col-md-6 mb-3">
          <label for="vehicle_no" class="form-label">Vehicle No.</label>
          <input type="text" class="form-control @error('vehicle_no') is-invalid @enderror" 
                 id="vehicle_no" name="vehicle_no" value="{{ old('vehicle_no', $transportMaster->vehicle_no) }}" placeholder="Enter vehicle number">
          @error('vehicle_no')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Trans Mode -->
        <div class="col-md-6 mb-3">
          <label for="trans_mode" class="form-label">Trans Mode</label>
          <select class="form-select @error('trans_mode') is-invalid @enderror" id="trans_mode" name="trans_mode">
            <option value="">Select mode</option>
            <option value="Road" {{ old('trans_mode', $transportMaster->trans_mode) == 'Road' ? 'selected' : '' }}>Road</option>
            <option value="Rail" {{ old('trans_mode', $transportMaster->trans_mode) == 'Rail' ? 'selected' : '' }}>Rail</option>
            <option value="Air" {{ old('trans_mode', $transportMaster->trans_mode) == 'Air' ? 'selected' : '' }}>Air</option>
            <option value="Ship" {{ old('trans_mode', $transportMaster->trans_mode) == 'Ship' ? 'selected' : '' }}>Ship</option>
          </select>
          @error('trans_mode')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.transport-master.index') }}" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Update Transport
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
