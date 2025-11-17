@extends('layouts.admin')
@section('title','Edit General Ledger Account')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-journal-text me-2"></i> Edit General Ledger Account</h4>
    <div class="text-muted small">Update ledger account details</div>
  </div>
  <a href="{{ route('admin.general-ledger.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.general-ledger.update', $generalLedger) }}" method="POST">
      @csrf
      @method('PUT')
      
      <!-- Row 1: Name, Alter Code -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="account_name" class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                 id="account_name" name="account_name" value="{{ old('account_name', $generalLedger->account_name) }}" required>
          @error('account_name')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="alter_code" class="form-label">Alter Code</label>
          <input type="text" class="form-control" 
                 id="alter_code" name="alter_code" value="{{ old('alter_code', $generalLedger->alter_code) }}">
        </div>
      </div>

      <!-- Row 2: Under, Opening Balance, Dr/Cr -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="under" class="form-label">Under</label>
          <input type="text" class="form-control" 
                 id="under" name="under" value="{{ old('under', $generalLedger->under) }}" placeholder="e.g., CURRENT LIABILITIES">
        </div>

        <div class="col-md-3 mb-3">
          <label for="opening_balance" class="form-label">Opening Bal.</label>
          <input type="number" step="0.01" class="form-control" 
                 id="opening_balance" name="opening_balance" value="{{ old('opening_balance', $generalLedger->opening_balance) }}" placeholder="0.00">
        </div>

        <div class="col-md-3 mb-3">
          <label for="balance_type" class="form-label">Dr / Cr</label>
          <select class="form-select" 
                  id="balance_type" name="balance_type">
            <option value="C" {{ old('balance_type', $generalLedger->balance_type) == 'C' ? 'selected' : '' }}>Cr</option>
            <option value="D" {{ old('balance_type', $generalLedger->balance_type) == 'D' ? 'selected' : '' }}>Dr</option>
          </select>
        </div>
      </div>

      <!-- Row 3: GST Checkboxes -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="input_gst_purchase" name="input_gst_purchase" 
                   value="1" {{ old('input_gst_purchase', $generalLedger->input_gst_purchase) ? 'checked' : '' }}>
            <label class="form-check-label" for="input_gst_purchase">
              Input GST(Purchase)
            </label>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="output_gst_income" name="output_gst_income" 
                   value="1" {{ old('output_gst_income', $generalLedger->output_gst_income) ? 'checked' : '' }}>
            <label class="form-check-label" for="output_gst_income">
              Output GST (Income)
            </label>
          </div>
        </div>
      </div>

      <!-- Row 4: Flag -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="flag" class="form-label">Flag</label>
          <input type="text" class="form-control" 
                 id="flag" name="flag" value="{{ old('flag', $generalLedger->flag) }}">
        </div>
      </div>

      <!-- Row 5: Address Section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea class="form-control" 
                    id="address" name="address" rows="2">{{ old('address', $generalLedger->address) }}</textarea>
        </div>

        <div class="col-md-6 mb-3">
          <label for="address_line2" class="form-label">Address Line 2</label>
          <input type="text" class="form-control" 
                 id="address_line2" name="address_line2" value="{{ old('address_line2', $generalLedger->address_line2) }}">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="address_line3" class="form-label">Address Line 3</label>
          <input type="text" class="form-control" 
                 id="address_line3" name="address_line3" value="{{ old('address_line3', $generalLedger->address_line3) }}">
        </div>

        <div class="col-md-3 mb-3">
          <label for="birth_day" class="form-label">B' Day</label>
          <input type="text" class="form-control" 
                 id="birth_day" name="birth_day" value="{{ old('birth_day', $generalLedger->birth_day) }}" placeholder="DD/MM">
        </div>

        <div class="col-md-3 mb-3">
          <label for="anniversary_day" class="form-label">A' Day</label>
          <input type="text" class="form-control" 
                 id="anniversary_day" name="anniversary_day" value="{{ old('anniversary_day', $generalLedger->anniversary_day) }}" placeholder="DD/MM">
        </div>
      </div>

      <!-- Row 6: Contact Person I & Mobile 1 -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="contact_person_1" class="form-label">Contact Person I</label>
          <input type="text" class="form-control" 
                 id="contact_person_1" name="contact_person_1" value="{{ old('contact_person_1', $generalLedger->contact_person_1) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile_1" class="form-label">Mobile 1</label>
          <input type="text" class="form-control" 
                 id="mobile_1" name="mobile_1" value="{{ old('mobile_1', $generalLedger->mobile_1) }}">
        </div>
      </div>

      <!-- Row 6b: Contact Person II & Mobile 2 -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="contact_person_2" class="form-label">Contact Person II</label>
          <input type="text" class="form-control" 
                 id="contact_person_2" name="contact_person_2" value="{{ old('contact_person_2', $generalLedger->contact_person_2) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile_2" class="form-label">Mobile 2</label>
          <input type="text" class="form-control" 
                 id="mobile_2" name="mobile_2" value="{{ old('mobile_2', $generalLedger->mobile_2) }}">
        </div>
      </div>

      <!-- Row 7: Telephone, Email, Fax -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="telephone" class="form-label">Telephone</label>
          <input type="text" class="form-control" 
                 id="telephone" name="telephone" value="{{ old('telephone', $generalLedger->telephone) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">E-Mail</label>
          <input type="email" class="form-control" 
                 id="email" name="email" value="{{ old('email', $generalLedger->email) }}">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="fax" class="form-label">Fax</label>
          <input type="text" class="form-control" 
                 id="fax" name="fax" value="{{ old('fax', $generalLedger->fax) }}">
        </div>

        <div class="col-md-6 mb-3">
          <label for="contact_person_1_mobile" class="form-label">Mobile</label>
          <input type="text" class="form-control" 
                 id="contact_person_1_mobile" name="contact_person_1_mobile" value="{{ old('contact_person_1_mobile', $generalLedger->contact_person_1_mobile) }}">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="contact_person_2_mobile" class="form-label">Mobile (Additional)</label>
          <input type="text" class="form-control" 
                 id="contact_person_2_mobile" name="contact_person_2_mobile" value="{{ old('contact_person_2_mobile', $generalLedger->contact_person_2_mobile) }}">
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle me-1"></i> Update
        </button>
        <a href="{{ route('admin.general-ledger.index') }}" class="btn btn-secondary">
          <i class="bi bi-x-circle me-1"></i> Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  
  // Add Enter key listener to all input fields
  const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], textarea, select');
  
  inputs.forEach(input => {
    input.addEventListener('keypress', function(e) {
      if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.submit();
      }
    });
  });
});
</script>
@endsection
