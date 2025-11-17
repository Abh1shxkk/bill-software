@extends('layouts.admin')
@section('title', 'Edit Company')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Company</h2>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Companies
                </a>
            </div>

            <form action="{{ route('admin.companies.update', $company) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name', $company->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Short Name</label>
                                <input type="text" class="form-control" name="short_name"
                                    value="{{ old('short_name', $company->short_name) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Alter Code</label>
                                <input type="text" class="form-control" name="alter_code"
                                    value="{{ old('alter_code', $company->alter_code) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $company->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" class="form-control" name="website"
                                    value="{{ old('website', $company->website) }}" placeholder="https://">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location"
                                    value="{{ old('location', $company->location) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" name="address"
                                    rows="2">{{ old('address', $company->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                        rows="3"
                                        placeholder="Add any additional notes or remarks here...">{{ old('notes', $company->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person I</label>
                                <input type="text" class="form-control" name="contact_person_1"
                                    value="{{ old('contact_person_1', $company->contact_person_1) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile 1</label>
                                <input type="text" class="form-control" name="mobile_1"
                                    value="{{ old('mobile_1', $company->mobile_1) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Telephone</label>
                                <input type="text" class="form-control" name="telephone"
                                    value="{{ old('telephone', $company->telephone) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person II</label>
                                <input type="text" class="form-control" name="contact_person_2"
                                    value="{{ old('contact_person_2', $company->contact_person_2) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile 2</label>
                                <input type="text" class="form-control" name="mobile_2"
                                    value="{{ old('mobile_2', $company->mobile_2) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Financial Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pur S.C.</label>
                                <input type="number" step="0.01" class="form-control" name="pur_sc"
                                    value="{{ old('pur_sc', $company->pur_sc ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sale S.C.</label>
                                <input type="number" step="0.01" class="form-control" name="sale_sc"
                                    value="{{ old('sale_sc', $company->sale_sc ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pur Tax</label>
                                <input type="number" step="0.01" class="form-control" name="pur_tax"
                                    value="{{ old('pur_tax', $company->pur_tax ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sale Tax</label>
                                <input type="number" step="0.01" class="form-control" name="sale_tax"
                                    value="{{ old('sale_tax', $company->sale_tax ?? '0.00') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dis. on Sale %</label>
                                <input type="number" step="0.01" class="form-control" name="dis_on_sale_percent"
                                    value="{{ old('dis_on_sale_percent', $company->dis_on_sale_percent ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Min GP</label>
                                <input type="number" step="0.01" class="form-control" name="min_gp"
                                    value="{{ old('min_gp', $company->min_gp ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">VAT %</label>
                                <input type="number" step="0.01" class="form-control" name="vat_percent"
                                    value="{{ old('vat_percent', $company->vat_percent ?? '0.00') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Discount (Locked)</label>
                                <input type="number" step="0.01" class="form-control" name="discount"
                                    value="{{ old('discount', $company->discount ?? '0.00') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>Business Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Expiry (Y/N)</label>
                                <select class="form-select @error('expiry') is-invalid @enderror" name="expiry">
                                    <option value="n" {{ old('expiry', strtolower($company->expiry ?? 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('expiry', strtolower($company->expiry ?? 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Generic (Y/N)</label>
                                <select class="form-select @error('generic') is-invalid @enderror" name="generic">
                                    <option value="n" {{ old('generic', strtolower($company->generic ?? 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('generic', strtolower($company->generic ?? 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('generic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">D(irect) / I(ndirect)</label>
                                <select class="form-select @error('direct_indirect') is-invalid @enderror"
                                    name="direct_indirect">
                                    @php($di = strtolower(old('direct_indirect', $company->direct_indirect)))
                                    <option value="d" {{ $di === 'd' ? 'selected' : '' }}>D</option>
                                    <option value="i" {{ $di === 'i' ? 'selected' : '' }}>I</option>
                                </select>
                                @error('direct_indirect')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Invoice Print Order</label>
                                <input type="number" class="form-control" name="invoice_print_order"
                                    value="{{ old('invoice_print_order', $company->invoice_print_order) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Disallow Expiry After (months)</label>
                                <input type="number" class="form-control" name="disallow_expiry_after_months"
                                    value="{{ old('disallow_expiry_after_months', $company->disallow_expiry_after_months ?? '0') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Flag</label>
                                <input type="text" class="form-control" name="flag"
                                    value="{{ old('flag', $company->flag) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">F(ixed) / M(aximum)</label>
                                <select class="form-select @error('fixed_maximum') is-invalid @enderror"
                                    name="fixed_maximum" id="fixed_maximum">
                                    @php($fm = strtolower(old('fixed_maximum', $company->fixed_maximum ?? 'f')))
                                    <option value="f" {{ $fm === 'f' ? 'selected' : '' }}>F</option>
                                    <option value="m" {{ $fm === 'm' ? 'selected' : '' }}>M</option>
                                </select>
                                @error('fixed_maximum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Surcharge After Dis. (Y/N)</label>
                                <select class="form-select @error('surcharge_after_dis_yn') is-invalid @enderror"
                                    name="surcharge_after_dis_yn" id="surcharge_after_dis_yn">
                                    <option value="n" {{ old('surcharge_after_dis_yn', ($company->surcharge_after_dis_yn ? 'y' : 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('surcharge_after_dis_yn', ($company->surcharge_after_dis_yn ? 'y' : 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('surcharge_after_dis_yn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Add Surcharge (Y/N)</label>
                                <select class="form-select @error('add_surcharge_yn') is-invalid @enderror"
                                    name="add_surcharge_yn" id="add_surcharge_yn">
                                    <option value="n" {{ old('add_surcharge_yn', ($company->add_surcharge_yn ? 'y' : 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('add_surcharge_yn', ($company->add_surcharge_yn ? 'y' : 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('add_surcharge_yn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Inclusive (Y/N)</label>
                                <select class="form-select @error('inclusive_yn') is-invalid @enderror"
                                    name="inclusive_yn" id="inclusive_yn">
                                    <option value="n" {{ old('inclusive_yn', ($company->inclusive_yn ? 'y' : 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('inclusive_yn', ($company->inclusive_yn ? 'y' : 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('inclusive_yn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Lock AIOCD (Y/N)</label>
                                <select class="form-select @error('lock_aiocd') is-invalid @enderror" name="lock_aiocd">
                                    <option value="n" {{ old('lock_aiocd', strtolower($company->lock_aiocd ?? 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('lock_aiocd', strtolower($company->lock_aiocd ?? 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('lock_aiocd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Lock IMS (Y/N)</label>
                                <select class="form-select @error('lock_ims') is-invalid @enderror" name="lock_ims">
                                    <option value="n" {{ old('lock_ims', strtolower($company->lock_ims ?? 'n')) == 'n' ? 'selected' : '' }}>N</option>
                                    <option value="y" {{ old('lock_ims', strtolower($company->lock_ims ?? 'n')) == 'y' ? 'selected' : '' }}>Y</option>
                                </select>
                                @error('lock_ims')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status (max 5 chars)</label>
                                <input type="text" maxlength="5" class="form-control" name="status" id="status"
                                    value="{{ old('status', $company->status) }}" placeholder="e.g. ACTV">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection