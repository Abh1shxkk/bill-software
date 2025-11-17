@extends('layouts.admin')
@section('title','Edit Cash/Bank Book')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cash-stack me-2"></i> Edit Cash/Bank Book</h4>
    <div class="text-muted small">Update cash/bank book details</div>
  </div>
  <a href="{{ route('admin.cash-bank-books.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form action="{{ route('admin.cash-bank-books.update', $cashBankBook) }}" method="POST">
      @csrf
      @method('PUT')
      
      <!-- Basic Information Section -->
      <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name', $cashBankBook->name) }}" placeholder="FG" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="alter_code" class="form-label">Alter Code</label>
            <input type="text" class="form-control @error('alter_code') is-invalid @enderror" 
                   id="alter_code" name="alter_code" value="{{ old('alter_code', $cashBankBook->alter_code) }}">
            @error('alter_code')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="under" class="form-label">Under</label>
            <input type="text" class="form-control @error('under') is-invalid @enderror" 
                   id="under" name="under" value="{{ old('under', $cashBankBook->under) }}">
            @error('under')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-3 mb-3">
            <label for="opening_balance" class="form-label">Opening Balance</label>
            <input type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" 
                   id="opening_balance" name="opening_balance" value="{{ old('opening_balance', $cashBankBook->opening_balance ?? 0) }}">
            @error('opening_balance')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-3 mb-3">
            <label for="opening_balance_type" class="form-label">Dr / Cr</label>
            <select class="form-select @error('opening_balance_type') is-invalid @enderror" 
                    id="opening_balance_type" name="opening_balance_type">
              <option value="D" {{ old('opening_balance_type', $cashBankBook->opening_balance_type ?? 'D') == 'D' ? 'selected' : '' }}>Dr</option>
              <option value="C" {{ old('opening_balance_type', $cashBankBook->opening_balance_type ?? 'D') == 'C' ? 'selected' : '' }}>Cr</option>
            </select>
            @error('opening_balance_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="credit_card" class="form-label">Credit Card [Y/N]/W(allet)</label>
            <select class="form-select @error('credit_card') is-invalid @enderror" 
                    id="credit_card" name="credit_card">
              <option value="">-- Select --</option>
              <option value="Y" {{ old('credit_card', $cashBankBook->credit_card) == 'Y' ? 'selected' : '' }}>Y</option>
              <option value="N" {{ old('credit_card', $cashBankBook->credit_card ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
              <option value="W" {{ old('credit_card', $cashBankBook->credit_card) == 'W' ? 'selected' : '' }}>W</option>
            </select>
            @error('credit_card')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4 mb-3">
            <label for="bank_charges" class="form-label">Bank Charges</label>
            <input type="number" step="0.01" class="form-control @error('bank_charges') is-invalid @enderror" 
                   id="bank_charges" name="bank_charges" value="{{ old('bank_charges', $cashBankBook->bank_charges ?? 0) }}">
            @error('bank_charges')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4 mb-3">
            <label for="flag" class="form-label">Flag</label>
            <input type="text" class="form-control @error('flag') is-invalid @enderror" 
                   id="flag" name="flag" value="{{ old('flag', $cashBankBook->flag) }}">
            @error('flag')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- Address & Contact Section -->
      <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2"></i>Address & Contact Details</h5>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" 
                      id="address" name="address" rows="2">{{ old('address', $cashBankBook->address) }}</textarea>
            @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="telephone" class="form-label">Telephone</label>
            <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                   id="telephone" name="telephone" value="{{ old('telephone', $cashBankBook->telephone) }}">
            @error('telephone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="address1" class="form-label">Address 1</label>
            <textarea class="form-control @error('address1') is-invalid @enderror" 
                      id="address1" name="address1" rows="2">{{ old('address1', $cashBankBook->address1) }}</textarea>
            @error('address1')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $cashBankBook->email) }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="birth_day" class="form-label">B' Day</label>
            <input type="date" class="form-control @error('birth_day') is-invalid @enderror" 
                   id="birth_day" name="birth_day" value="{{ old('birth_day', $cashBankBook->birth_day) }}">
            @error('birth_day')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-3 mb-3">
            <label for="anniversary_day" class="form-label">A' Day</label>
            <input type="date" class="form-control @error('anniversary_day') is-invalid @enderror" 
                   id="anniversary_day" name="anniversary_day" value="{{ old('anniversary_day', $cashBankBook->anniversary_day) }}">
            @error('anniversary_day')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="fax" class="form-label">Fax</label>
            <input type="text" class="form-control @error('fax') is-invalid @enderror" 
                   id="fax" name="fax" value="{{ old('fax', $cashBankBook->fax) }}">
            @error('fax')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="contact_person_1" class="form-label">Contact Person I</label>
            <input type="text" class="form-control @error('contact_person_1') is-invalid @enderror" 
                   id="contact_person_1" name="contact_person_1" value="{{ old('contact_person_1', $cashBankBook->contact_person_1) }}">
            @error('contact_person_1')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="mobile_1" class="form-label">Mobile</label>
            <input type="text" class="form-control @error('mobile_1') is-invalid @enderror" 
                   id="mobile_1" name="mobile_1" value="{{ old('mobile_1', $cashBankBook->mobile_1) }}">
            @error('mobile_1')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="contact_person_2" class="form-label">Contact Person II</label>
            <input type="text" class="form-control @error('contact_person_2') is-invalid @enderror" 
                   id="contact_person_2" name="contact_person_2" value="{{ old('contact_person_2', $cashBankBook->contact_person_2) }}">
            @error('contact_person_2')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="mobile_2" class="form-label">Mobile</label>
            <input type="text" class="form-control @error('mobile_2') is-invalid @enderror" 
                   id="mobile_2" name="mobile_2" value="{{ old('mobile_2', $cashBankBook->mobile_2) }}">
            @error('mobile_2')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- Account Details Section -->
      <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-bank me-2"></i>Account Details</h5>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="account_no" class="form-label">Account No</label>
            <input type="text" class="form-control @error('account_no') is-invalid @enderror" 
                   id="account_no" name="account_no" value="{{ old('account_no', $cashBankBook->account_no) }}">
            @error('account_no')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label for="report_no" class="form-label">Report No</label>
            <input type="text" class="form-control @error('report_no') is-invalid @enderror" 
                   id="report_no" name="report_no" value="{{ old('report_no', $cashBankBook->report_no) }}">
            @error('report_no')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- GST & Cheque Section -->
      <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-receipt me-2"></i>GST & Cheque Settings</h5>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="input_gst_purchase" name="input_gst_purchase" 
                     value="1" {{ old('input_gst_purchase', $cashBankBook->input_gst_purchase) ? 'checked' : '' }}>
              <label class="form-check-label" for="input_gst_purchase">
                Input GST(Purchase)
              </label>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="output_gst_income" name="output_gst_income" 
                     value="1" {{ old('output_gst_income', $cashBankBook->output_gst_income) ? 'checked' : '' }}>
              <label class="form-check-label" for="output_gst_income">
                Output GST (Income)
              </label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-3">
            <label class="form-label">Cheque Clearance Method</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" id="cheque_pis" name="cheque_clearance_method" 
                     value="P" {{ old('cheque_clearance_method', $cashBankBook->cheque_clearance_method ?? 'P') == 'P' ? 'checked' : '' }}>
              <label class="form-check-label" for="cheque_pis">
                Pis. No.
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" id="cheque_individual" name="cheque_clearance_method" 
                     value="I" {{ old('cheque_clearance_method', $cashBankBook->cheque_clearance_method ?? 'P') == 'I' ? 'checked' : '' }}>
              <label class="form-check-label" for="cheque_individual">
                Individual Cheques
              </label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-3">
            <label class="form-label">Receipts</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" id="receipts_summary" name="receipts" 
                     value="S" {{ old('receipts', $cashBankBook->receipts ?? 'S') == 'S' ? 'checked' : '' }}>
              <label class="form-check-label" for="receipts_summary">
                Summary
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" id="receipts_individual" name="receipts" 
                     value="I" {{ old('receipts', $cashBankBook->receipts ?? 'S') == 'I' ? 'checked' : '' }}>
              <label class="form-check-label" for="receipts_individual">
                Individual
              </label>
            </div>
          </div>
        </div>
      </div>


      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i> Update
        </button>
        <a href="{{ route('admin.cash-bank-books.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
