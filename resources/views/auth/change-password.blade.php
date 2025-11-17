@extends('layouts.admin')
@section('title','Change Password')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Change Password</h5>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('password.change') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Current password</label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">New password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <button class="btn btn-primary">Update Password</button>
        </form>
      </div>
    </div>
  </div>
  </div>
@endsection


