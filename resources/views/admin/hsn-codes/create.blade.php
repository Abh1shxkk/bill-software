@extends('layouts.admin')
@section('title', 'Add HSN Code')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add New HSN Code</h2>
        <a href="{{ route('admin.hsn-codes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to HSN Codes
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-upc-scan me-2"></i>HSN Code Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hsn-codes.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" placeholder="Enter HSN code name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- HSN Code -->
                        <div class="mb-3">
                            <label class="form-label">HSN Code</label>
                            <input type="text" class="form-control @error('hsn_code') is-invalid @enderror" 
                                   name="hsn_code" value="{{ old('hsn_code') }}" placeholder="Enter HSN code number">
                            @error('hsn_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- GST Percentages Row -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">CGST %</label>
                                <input type="number" step="0.01" class="form-control @error('cgst_percent') is-invalid @enderror" 
                                       name="cgst_percent" value="{{ old('cgst_percent', '0.00') }}" placeholder="0.00">
                                @error('cgst_percent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">SGST %</label>
                                <input type="number" step="0.01" class="form-control @error('sgst_percent') is-invalid @enderror" 
                                       name="sgst_percent" value="{{ old('sgst_percent', '0.00') }}" placeholder="0.00">
                                @error('sgst_percent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">IGST %</label>
                                <input type="number" step="0.01" class="form-control @error('igst_percent') is-invalid @enderror" 
                                       name="igst_percent" value="{{ old('igst_percent', '0.00') }}" placeholder="0.00">
                                @error('igst_percent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total GST %</label>
                                <input type="number" step="0.01" class="form-control @error('total_gst_percent') is-invalid @enderror" 
                                       name="total_gst_percent" value="{{ old('total_gst_percent', '0.00') }}" placeholder="0.00">
                                @error('total_gst_percent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Checkboxes Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_inactive" 
                                           id="is_inactive" value="1" {{ old('is_inactive') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_inactive">
                                        Inactive
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_service" 
                                           id="is_service" value="1" {{ old('is_service') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_service">
                                        Service
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.hsn-codes.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save HSN Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
