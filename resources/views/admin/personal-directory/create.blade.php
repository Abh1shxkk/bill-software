@extends('layouts.admin')

@section('title', 'Add Personal Directory Entry')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-person-lines-fill me-2"></i>Add Personal Directory Entry
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.personal-directory.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.personal-directory.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" placeholder="Enter name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="alt_code" class="form-label">Alt. Code</label>
                        <input type="text" class="form-control @error('alt_code') is-invalid @enderror" 
                               id="alt_code" name="alt_code" placeholder="Enter alt code" value="{{ old('alt_code') }}">
                        @error('alt_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address Office -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address_office" class="form-label">Address (O)</label>
                        <textarea class="form-control @error('address_office') is-invalid @enderror" 
                                  id="address_office" name="address_office" rows="3" placeholder="Enter office address">{{ old('address_office') }}</textarea>
                        @error('address_office')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label for="tel_office" class="form-label">Tel (O)</label>
                            <input type="text" class="form-control @error('tel_office') is-invalid @enderror" 
                                   id="tel_office" name="tel_office" placeholder="Office telephone" value="{{ old('tel_office') }}">
                        </div>
                        <div class="mb-2">
                            <label for="tel_residence" class="form-label">Tel (R)</label>
                            <input type="text" class="form-control @error('tel_residence') is-invalid @enderror" 
                                   id="tel_residence" name="tel_residence" placeholder="Residence telephone" value="{{ old('tel_residence') }}">
                        </div>
                        <div class="mb-2">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                   id="mobile" name="mobile" placeholder="Mobile number" value="{{ old('mobile') }}">
                        </div>
                    </div>
                </div>

                <!-- Address Residence -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address_residence" class="form-label">Address (R)</label>
                        <textarea class="form-control @error('address_residence') is-invalid @enderror" 
                                  id="address_residence" name="address_residence" rows="3" placeholder="Enter residence address">{{ old('address_residence') }}</textarea>
                        @error('address_residence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label for="fax" class="form-label">Fax</label>
                            <input type="text" class="form-control @error('fax') is-invalid @enderror" 
                                   id="fax" name="fax" placeholder="Fax number" value="{{ old('fax') }}">
                        </div>
                        <div class="mb-2">
                            <label for="email" class="form-label">E-Mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" placeholder="Email address" value="{{ old('email') }}">
                        </div>
                        <div class="mb-2">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control @error('status') is-invalid @enderror" 
                                   id="status" name="status" placeholder="Status" value="{{ old('status') }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Personal & Family Details -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                               id="contact_person" name="contact_person" placeholder="Contact person name" value="{{ old('contact_person') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control @error('birthday') is-invalid @enderror" 
                               id="birthday" name="birthday" value="{{ old('birthday') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="anniversary" class="form-label">Anniversary</label>
                        <input type="date" class="form-control @error('anniversary') is-invalid @enderror" 
                               id="anniversary" name="anniversary" value="{{ old('anniversary') }}">
                    </div>
                </div>

                <!-- Spouse Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="spouse" class="form-label">Spouse</label>
                        <input type="text" class="form-control @error('spouse') is-invalid @enderror" 
                               id="spouse" name="spouse" placeholder="Spouse name" value="{{ old('spouse') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="spouse_dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('spouse_dob') is-invalid @enderror" 
                               id="spouse_dob" name="spouse_dob" value="{{ old('spouse_dob') }}">
                    </div>
                </div>

                <!-- Child 1 Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="child_1" class="form-label">Child I</label>
                        <input type="text" class="form-control @error('child_1') is-invalid @enderror" 
                               id="child_1" name="child_1" placeholder="Child 1 name" value="{{ old('child_1') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="child_1_dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('child_1_dob') is-invalid @enderror" 
                               id="child_1_dob" name="child_1_dob" value="{{ old('child_1_dob') }}">
                    </div>
                </div>

                <!-- Child 2 Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="child_2" class="form-label">Child II</label>
                        <input type="text" class="form-control @error('child_2') is-invalid @enderror" 
                               id="child_2" name="child_2" placeholder="Child 2 name" value="{{ old('child_2') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="child_2_dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('child_2_dob') is-invalid @enderror" 
                               id="child_2_dob" name="child_2_dob" value="{{ old('child_2_dob') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Save Entry
                        </button>
                        <a href="{{ route('admin.personal-directory.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
