@extends('layouts.admin')

@section('title', 'White-Label Branding')

@section('content')
<div class="container-fluid py-4">
    <form action="{{ route('admin.organization.branding.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <!-- Colors -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-palette me-2"></i>Brand Colors
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Primary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           value="{{ $organization->primary_color }}" 
                                           onchange="document.getElementById('primaryColorText').value = this.value">
                                    <input type="text" name="primary_color" id="primaryColorText" 
                                           class="form-control" value="{{ $organization->primary_color }}"
                                           pattern="^#[A-Fa-f0-9]{6}$" maxlength="7">
                                </div>
                                <small class="text-muted">Buttons, links, accents</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secondary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           value="{{ $organization->secondary_color }}"
                                           onchange="document.getElementById('secondaryColorText').value = this.value">
                                    <input type="text" name="secondary_color" id="secondaryColorText" 
                                           class="form-control" value="{{ $organization->secondary_color }}"
                                           pattern="^#[A-Fa-f0-9]{6}$" maxlength="7">
                                </div>
                                <small class="text-muted">Gradients, highlights</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Accent Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           value="{{ $organization->accent_color }}"
                                           onchange="document.getElementById('accentColorText').value = this.value">
                                    <input type="text" name="accent_color" id="accentColorText" 
                                           class="form-control" value="{{ $organization->accent_color }}"
                                           pattern="^#[A-Fa-f0-9]{6}$" maxlength="7">
                                </div>
                                <small class="text-muted">Success states, badges</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sidebar Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           value="{{ $organization->sidebar_color }}"
                                           onchange="document.getElementById('sidebarColorText').value = this.value">
                                    <input type="text" name="sidebar_color" id="sidebarColorText" 
                                           class="form-control" value="{{ $organization->sidebar_color }}"
                                           pattern="^#[A-Fa-f0-9]{6}$" maxlength="7">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Header Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           value="{{ $organization->header_color }}"
                                           onchange="document.getElementById('headerColorText').value = this.value">
                                    <input type="text" name="header_color" id="headerColorText" 
                                           class="form-control" value="{{ $organization->header_color }}"
                                           pattern="^#[A-Fa-f0-9]{6}$" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- App Identity -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-type me-2"></i>App Identity
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">App Name</label>
                                <input type="text" name="app_name" class="form-control" 
                                       value="{{ $organization->app_name }}"
                                       placeholder="MediBill">
                                <small class="text-muted">Leave empty for default "MediBill"</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="tagline" class="form-control" 
                                       value="{{ $organization->tagline }}"
                                       placeholder="Pharmacy Management Made Easy">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Footer Text</label>
                                <input type="text" name="footer_text" class="form-control" 
                                       value="{{ $organization->footer_text }}"
                                       placeholder="© 2026 Your Company Name. All rights reserved.">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Branding -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>Invoice Branding
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Invoice Header HTML</label>
                            <textarea name="invoice_header_html" class="form-control" rows="3"
                                      placeholder="Custom HTML for invoice header">{{ $organization->invoice_header_html }}</textarea>
                            <small class="text-muted">HTML displayed at the top of invoices</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Invoice Footer HTML</label>
                            <textarea name="invoice_footer_html" class="form-control" rows="3"
                                      placeholder="Custom HTML for invoice footer">{{ $organization->invoice_footer_html }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="invoice_terms" class="form-control" rows="4"
                                      placeholder="Terms and conditions to appear on invoices">{{ $organization->invoice_terms }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-code me-2"></i>Custom CSS
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="custom_css" class="form-control font-monospace" rows="6"
                                  placeholder="/* Add custom CSS here */">{{ $organization->custom_css }}</textarea>
                        <small class="text-muted">Advanced: Add custom CSS to override default styles</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Assets -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-image me-2"></i>Brand Assets
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Favicon</label>
                            @if($organization->favicon_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($organization->favicon_path) }}" 
                                         alt="Favicon" style="max-height: 32px;">
                                </div>
                            @endif
                            <input type="file" name="favicon" class="form-control form-control-sm" 
                                   accept=".ico,.png">
                            <small class="text-muted">ICO or PNG, max 256KB</small>
                        </div>
                        <div>
                            <label class="form-label">Login Background</label>
                            @if($organization->login_background_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($organization->login_background_path) }}" 
                                         alt="Background" class="img-fluid rounded" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="login_background" class="form-control form-control-sm" 
                                   accept="image/*">
                            <small class="text-muted">JPEG/PNG, max 2MB</small>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-toggles me-2"></i>Options
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="show_powered_by" class="form-check-input" 
                                   id="showPoweredBy" value="1"
                                   {{ $organization->show_powered_by ? 'checked' : '' }}>
                            <label class="form-check-label" for="showPoweredBy">
                                Show "Powered by MediBill"
                            </label>
                        </div>
                        <small class="text-muted">Display branding in footer</small>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-eye me-2"></i>Color Preview
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="colorPreview" style="height: 120px; display: flex;">
                            <div style="width: 60px; background: {{ $organization->sidebar_color }};" id="previewSidebar"></div>
                            <div style="flex: 1;">
                                <div style="height: 30px; background: {{ $organization->header_color }};" id="previewHeader"></div>
                                <div style="padding: 1rem;">
                                    <span class="badge" id="previewPrimary" 
                                          style="background: {{ $organization->primary_color }};">Primary</span>
                                    <span class="badge" id="previewSecondary" 
                                          style="background: {{ $organization->secondary_color }};">Secondary</span>
                                    <span class="badge" id="previewAccent" 
                                          style="background: {{ $organization->accent_color }};">Accent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>Save Branding
                    </button>
                    <a href="{{ route('admin.organization.branding.reset') }}" class="btn btn-outline-secondary"
                       onclick="return confirm('Reset all branding to defaults?')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to Defaults
                    </a>
                    <a href="{{ route('admin.organization.settings') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Live preview color updates
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', function() {
        const id = this.nextElementSibling.id;
        if (id === 'primaryColorText') {
            document.getElementById('previewPrimary').style.background = this.value;
        } else if (id === 'secondaryColorText') {
            document.getElementById('previewSecondary').style.background = this.value;
        } else if (id === 'accentColorText') {
            document.getElementById('previewAccent').style.background = this.value;
        } else if (id === 'sidebarColorText') {
            document.getElementById('previewSidebar').style.background = this.value;
        } else if (id === 'headerColorText') {
            document.getElementById('previewHeader').style.background = this.value;
        }
    });
});
</script>
@endsection
