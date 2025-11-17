@extends('layouts.admin')

@section('title', 'Create Sales Man')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Sales Man</h3>
                    <a href="{{ route('admin.sales-men.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sales-men.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Address -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Telephone -->
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Telephone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                       id="telephone" name="telephone" value="{{ old('telephone') }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- City -->
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Mobile -->
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                       id="mobile" name="mobile" value="{{ old('mobile') }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- E-Mail -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-Mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- PIN -->
                            <div class="col-md-6 mb-3">
                                <label for="pin" class="form-label">PIN</label>
                                <input type="text" class="form-control @error('pin') is-invalid @enderror" 
                                       id="pin" name="pin" value="{{ old('pin') }}">
                                @error('pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control @error('status') is-invalid @enderror" 
                                       id="status" name="status" value="{{ old('status') }}">
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Sales Man / Collection Boy / Both -->
                            <div class="col-md-6 mb-3">
                                <label for="sales_type" class="form-label">S(ales Man) / C(oll. Boy) / B(oth)</label>
                                <select class="form-select @error('sales_type') is-invalid @enderror" id="sales_type" name="sales_type">
                                    <option value="B" {{ old('sales_type', 'B') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="S" {{ old('sales_type') == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="C" {{ old('sales_type') == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                                @error('sales_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Sales Man / Delivery Man / Both -->
                            <div class="col-md-6 mb-3">
                                <label for="delivery_type" class="form-label">S(ales Man) / D(elivery Man) / B(oth)</label>
                                <select class="form-select @error('delivery_type') is-invalid @enderror" id="delivery_type" name="delivery_type">
                                    <option value="B" {{ old('delivery_type', 'B') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="S" {{ old('delivery_type') == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="D" {{ old('delivery_type') == 'D' ? 'selected' : '' }}>D</option>
                                </select>
                                @error('delivery_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Area Manager Code -->
                            <div class="col-md-3 mb-3">
                                <label for="area_mgr_code" class="form-label">Area Mgr.</label>
                                <input type="text" class="form-control @error('area_mgr_code') is-invalid @enderror" 
                                       id="area_mgr_code" name="area_mgr_code" value="{{ old('area_mgr_code', '00') }}">
                                @error('area_mgr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Area Manager Name -->
                            <div class="col-md-3 mb-3">
                                <label for="area_mgr_name" class="form-label">&nbsp;</label>
                                <input type="text" class="form-control @error('area_mgr_name') is-invalid @enderror" 
                                       id="area_mgr_name" name="area_mgr_name" value="{{ old('area_mgr_name', 'DIRECT') }}">
                                @error('area_mgr_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Monthly Target -->
                            <div class="col-md-6 mb-3">
                                <label for="monthly_target" class="form-label">Monthly Target</label>
                                <input type="number" class="form-control @error('monthly_target') is-invalid @enderror" 
                                       id="monthly_target" name="monthly_target" value="{{ old('monthly_target', '0.00') }}" 
                                       step="0.01" min="0">
                                @error('monthly_target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Create Sales Man
                                </button>
                                <a href="{{ route('admin.sales-men.index') }}" class="btn btn-secondary ms-2">
                                    <i class="bi bi-x-lg"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
