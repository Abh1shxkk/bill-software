@extends('layouts.admin')
@section('title','Edit Sale Ledger Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cart-check me-2"></i> Edit Sale Ledger Entry</h4>
    <div class="text-muted small">Update sale ledger entry details</div>
  </div>
  <a href="{{ route('admin.sale-ledger.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.sale-ledger.update', $saleLedger) }}" method="POST">
      @csrf
      @method('PUT')
      
      <!-- Ledger Information Section -->
      <div class="card-header bg-primary text-white mb-3">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ledger Information</h5>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="ledger_name" class="form-label">Ledger Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('ledger_name') is-invalid @enderror" 
                 id="ledger_name" name="ledger_name" value="{{ old('ledger_name', $saleLedger->ledger_name) }}" placeholder="e.g., SALE - TAXPAID" required>
          @error('ledger_name')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="form_type" class="form-label">Form Type</label>
          <input type="text" class="form-control @error('form_type') is-invalid @enderror" 
                 id="form_type" name="form_type" value="{{ old('form_type', $saleLedger->form_type) }}" placeholder="Form Type">
          @error('form_type')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label for="sale_tax" class="form-label">Sale Tax</label>
          <input type="number" step="0.01" class="form-control @error('sale_tax') is-invalid @enderror" 
                 id="sale_tax" name="sale_tax" value="{{ old('sale_tax', $saleLedger->sale_tax ?? 0) }}" placeholder="0.00">
          @error('sale_tax')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
          <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            <option value="">-- Select Type --</option>
            <option value="L" {{ old('type', $saleLedger->type) == 'L' ? 'selected' : '' }}>L (Ledger)</option>
            <option value="C" {{ old('type', $saleLedger->type) == 'C' ? 'selected' : '' }}>C (Credit)</option>
          </select>
          @error('type')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control @error('status') is-invalid @enderror" 
                 id="status" name="status" value="{{ old('status', $saleLedger->status) }}" placeholder="Status">
          @error('status')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="alter_code" class="form-label">Alter Code</label>
          <input type="text" class="form-control @error('alter_code') is-invalid @enderror" 
                 id="alter_code" name="alter_code" value="{{ old('alter_code', $saleLedger->alter_code) }}" placeholder="Alternate Code">
          @error('alter_code')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="under" class="form-label">Under</label>
          <input type="text" class="form-control @error('under') is-invalid @enderror" 
                 id="under" name="under" value="{{ old('under', $saleLedger->under) }}" placeholder="Under which group/ledger">
          @error('under')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label for="opening_balance" class="form-label">Opening Balance</label>
          <input type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" 
                 id="opening_balance" name="opening_balance" value="{{ old('opening_balance', $saleLedger->opening_balance ?? 0) }}" placeholder="0.00">
          @error('opening_balance')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label for="form_required" class="form-label">Form Required</label>
          <select class="form-select @error('form_required') is-invalid @enderror" id="form_required" name="form_required">
            <option value="">-- Select --</option>
            <option value="Y" {{ old('form_required', $saleLedger->form_required) == 'Y' ? 'selected' : '' }}>Y</option>
            <option value="N" {{ old('form_required', $saleLedger->form_required ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
          </select>
          @error('form_required')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label for="charges" class="form-label">Charges</label>
          <input type="number" step="0.01" class="form-control @error('charges') is-invalid @enderror" 
                 id="charges" name="charges" value="{{ old('charges', $saleLedger->charges ?? 0) }}" placeholder="0.00">
          @error('charges')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <hr class="my-4">

      <!-- Contact Information Section -->
      <div class="card-header bg-success text-white mb-3">
        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Contact Information</h5>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea class="form-control @error('address') is-invalid @enderror" 
                    id="address" name="address" rows="2" placeholder="Address">{{ old('address', $saleLedger->address) }}</textarea>
          @error('address')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3 mb-3">
          <label for="birth_day" class="form-label">Birth Day</label>
          <input type="date" class="form-control @error('birth_day') is-invalid @enderror" 
                 id="birth_day" name="birth_day" value="{{ old('birth_day', $saleLedger->birth_day?->format('Y-m-d')) }}">
          @error('birth_day')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3 mb-3">
          <label for="anniversary" class="form-label">Anniversary</label>
          <input type="date" class="form-control @error('anniversary') is-invalid @enderror" 
                 id="anniversary" name="anniversary" value="{{ old('anniversary', $saleLedger->anniversary?->format('Y-m-d')) }}">
          @error('anniversary')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 mb-3">
          <label for="telephone" class="form-label">Telephone</label>
          <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                 id="telephone" name="telephone" value="{{ old('telephone', $saleLedger->telephone) }}" placeholder="Telephone">
          @error('telephone')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3 mb-3">
          <label for="fax" class="form-label">Fax</label>
          <input type="text" class="form-control @error('fax') is-invalid @enderror" 
                 id="fax" name="fax" value="{{ old('fax', $saleLedger->fax) }}" placeholder="Fax">
          @error('fax')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3 mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" 
                 id="email" name="email" value="{{ old('email', $saleLedger->email) }}" placeholder="Email">
          @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3 mb-3">
          <label for="desc" class="form-label">Description</label>
          <input type="text" class="form-control @error('desc') is-invalid @enderror" 
                 id="desc" name="desc" value="{{ old('desc', $saleLedger->desc) }}" placeholder="Description">
          @error('desc')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="contact_1" class="form-label">Contact 1</label>
          <input type="text" class="form-control @error('contact_1') is-invalid @enderror" 
                 id="contact_1" name="contact_1" value="{{ old('contact_1', $saleLedger->contact_1) }}" placeholder="Contact Person 1">
          @error('contact_1')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile_1" class="form-label">Mobile 1</label>
          <input type="text" class="form-control @error('mobile_1') is-invalid @enderror" 
                 id="mobile_1" name="mobile_1" value="{{ old('mobile_1', $saleLedger->mobile_1) }}" placeholder="Mobile 1">
          @error('mobile_1')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="contact_2" class="form-label">Contact 2</label>
          <input type="text" class="form-control @error('contact_2') is-invalid @enderror" 
                 id="contact_2" name="contact_2" value="{{ old('contact_2', $saleLedger->contact_2) }}" placeholder="Contact Person 2">
          @error('contact_2')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile_2" class="form-label">Mobile 2</label>
          <input type="text" class="form-control @error('mobile_2') is-invalid @enderror" 
                 id="mobile_2" name="mobile_2" value="{{ old('mobile_2', $saleLedger->mobile_2) }}" placeholder="Mobile 2">
          @error('mobile_2')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>


      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i> Update Sale Ledger Entry
        </button>
        <a href="{{ route('admin.sale-ledger.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
