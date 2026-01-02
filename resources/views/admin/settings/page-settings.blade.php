@extends('layouts.admin')

@section('title', 'Page Content Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-file-earmark-text me-2"></i>Page Content Settings</h4>
            <p class="text-muted mb-0">Manage content displayed on Privacy Policy, Terms, Support & Documentation pages</p>
        </div>
        <div>
            <a href="{{ route('pages.privacy') }}" target="_blank" class="btn btn-outline-primary btn-sm me-1">
                <i class="bi bi-eye me-1"></i>Preview Pages
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.page-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="bi bi-building me-1"></i>General Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy" type="button" role="tab">
                    <i class="bi bi-shield-lock me-1"></i>Privacy Policy
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab">
                    <i class="bi bi-file-text me-1"></i>Terms of Service
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">
                    <i class="bi bi-headset me-1"></i>Support
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab">
                    <i class="bi bi-question-circle me-1"></i>FAQ
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="settingsTabContent">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>General Information</h5>
                        <small class="text-muted">This information appears across all pages</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($generalSettings as $setting)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if($setting->type == 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                    @endif
                                    <small class="text-muted">Key: {{ $setting->key }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy Policy Settings -->
            <div class="tab-pane fade" id="privacy" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Privacy Policy Content</h5>
                            <small class="text-muted">Customize Privacy Policy page content</small>
                        </div>
                        <a href="{{ route('pages.privacy') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>View Page
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($privacySettings as $setting)
                                <div class="col-md-{{ $setting->type == 'textarea' ? '12' : '6' }}">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if($setting->type == 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="4">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms Settings -->
            <div class="tab-pane fade" id="terms" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-file-text me-2 text-primary"></i>Terms of Service Content</h5>
                            <small class="text-muted">Customize Terms of Service page content</small>
                        </div>
                        <a href="{{ route('pages.terms') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>View Page
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($termsSettings as $setting)
                                <div class="col-md-{{ $setting->type == 'textarea' ? '12' : '6' }}">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if($setting->type == 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="4">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Settings -->
            <div class="tab-pane fade" id="support" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-headset me-2 text-primary"></i>Support Page Content</h5>
                            <small class="text-muted">Customize Support page content</small>
                        </div>
                        <a href="{{ route('pages.support') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>View Page
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($supportSettings as $setting)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                    @if($setting->type == 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Settings -->
            <div class="tab-pane fade" id="faq" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-question-circle me-2 text-primary"></i>FAQ Content</h5>
                            <small class="text-muted">Customize FAQ questions and answers on Support page</small>
                        </div>
                        <a href="{{ route('pages.support') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>View Page
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $faqGroups = $faqSettings->groupBy(function($item) {
                                return preg_replace('/_(question|answer)$/', '', $item->key);
                            });
                        @endphp
                        
                        @foreach($faqGroups as $faqKey => $items)
                            <div class="card mb-3 border">
                                <div class="card-header bg-light py-2">
                                    <strong><i class="bi bi-chat-square-text me-1"></i>FAQ {{ str_replace('faq_', '', $faqKey) }}</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($items as $setting)
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">{{ $setting->label }}</label>
                                                @if($setting->type == 'textarea')
                                                    <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="2">{{ $setting->value }}</textarea>
                                                @else
                                                    <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <span class="text-muted"><i class="bi bi-info-circle me-1"></i>Changes will be reflected on all static pages immediately after saving.</span>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Save All Changes
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .nav-tabs .nav-link {
        color: #64748b;
        border: none;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
    }
    .nav-tabs .nav-link:hover {
        color: #667eea;
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: #667eea;
        border: none;
        border-bottom: 3px solid #667eea;
        background: transparent;
    }
    .card-header h5 {
        font-size: 1.1rem;
    }
</style>
@endsection
