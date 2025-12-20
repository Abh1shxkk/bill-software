@extends('layouts.admin')

@section('title', 'Edit Hotkey')

@section('content')
<style>
    .key-preview {
        font-family: 'Consolas', 'Monaco', monospace;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: inline-block;
    }
    
    .key-input-container {
        position: relative;
    }
    
    .key-status {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .key-available { color: #10b981; }
    .key-unavailable { color: #ef4444; }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Hotkey</h4>
                    <small class="text-muted">Modify keyboard shortcut settings</small>
                </div>
                <a href="{{ route('admin.administration.hotkeys.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Hotkeys
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-keyboard me-2"></i>Hotkey Details</span>
                        <span class="key-preview" id="keyPreview">{{ strtoupper(str_replace('+', ' + ', $hotkey->key_combination)) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.administration.hotkeys.update', $hotkey) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Key Combination <span class="text-danger">*</span></label>
                                <div class="key-input-container">
                                    <input type="text" class="form-control @error('key_combination') is-invalid @enderror" 
                                           name="key_combination" id="keyCombination"
                                           value="{{ old('key_combination', $hotkey->key_combination) }}"
                                           placeholder="e.g., ctrl+f1, alt+s"
                                           required>
                                    <span class="key-status" id="keyStatus"></span>
                                </div>
                                <small class="text-muted">Use lowercase with + separator (e.g., ctrl+shift+f1)</small>
                                @error('key_combination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Module Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('module_name') is-invalid @enderror" 
                                       name="module_name" 
                                       value="{{ old('module_name', $hotkey->module_name) }}"
                                       required>
                                @error('module_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <input type="text" class="form-control" value="{{ $categories[$hotkey->category] ?? $hotkey->category }}" disabled>
                                <small class="text-muted">Category cannot be changed</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Scope</label>
                                <input type="text" class="form-control" value="{{ $scopes[$hotkey->scope] ?? $hotkey->scope }}" disabled>
                                <small class="text-muted">Scope cannot be changed</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Route Name</label>
                            <input type="text" class="form-control" value="{{ $hotkey->route_name }}" disabled>
                            <small class="text-muted">Route cannot be changed (system defined)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="2">{{ old('description', $hotkey->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                       {{ old('is_active', $hotkey->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    <strong>Active</strong> - Enable this hotkey
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.administration.hotkeys.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Hotkey
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Key Capture Helper -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-lightbulb me-2"></i>Key Capture Helper
                </div>
                <div class="card-body">
                    <p class="mb-2">Click the button below and press your desired key combination:</p>
                    <button type="button" class="btn btn-outline-info" id="captureKeyBtn">
                        <i class="bi bi-keyboard me-1"></i> Capture Key Combination
                    </button>
                    <div id="capturedKey" class="mt-3" style="display: none;">
                        <span class="text-muted">Captured: </span>
                        <span class="key-preview" id="capturedKeyDisplay"></span>
                        <button type="button" class="btn btn-sm btn-success ms-2" id="useKeyBtn">Use This Key</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const keyInput = document.getElementById('keyCombination');
    const keyPreview = document.getElementById('keyPreview');
    const keyStatus = document.getElementById('keyStatus');
    const captureBtn = document.getElementById('captureKeyBtn');
    const capturedKeyDiv = document.getElementById('capturedKey');
    const capturedKeyDisplay = document.getElementById('capturedKeyDisplay');
    const useKeyBtn = document.getElementById('useKeyBtn');
    
    let isCapturing = false;
    let capturedKeyCombination = '';
    
    // Update preview on input change
    keyInput.addEventListener('input', function() {
        const value = this.value.toLowerCase().trim();
        keyPreview.textContent = value.toUpperCase().replace(/\+/g, ' + ');
        checkKeyAvailability(value);
    });
    
    // Check key availability
    function checkKeyAvailability(key) {
        if (!key) {
            keyStatus.innerHTML = '';
            return;
        }
        
        fetch('{{ route("admin.administration.hotkeys.check-key") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                key: key,
                exclude_id: {{ $hotkey->id }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                keyStatus.innerHTML = '<i class="bi bi-check-circle-fill key-available"></i>';
                keyStatus.title = 'Available';
            } else {
                keyStatus.innerHTML = '<i class="bi bi-x-circle-fill key-unavailable"></i>';
                keyStatus.title = data.message;
            }
        });
    }
    
    // Key capture functionality
    captureBtn.addEventListener('click', function() {
        isCapturing = true;
        this.textContent = 'Press your key combination...';
        this.classList.add('btn-warning');
        this.classList.remove('btn-outline-info');
    });
    
    document.addEventListener('keydown', function(e) {
        if (!isCapturing) return;
        
        e.preventDefault();
        
        const parts = [];
        if (e.ctrlKey) parts.push('ctrl');
        if (e.shiftKey) parts.push('shift');
        if (e.altKey) parts.push('alt');
        
        let key = e.key.toLowerCase();
        if (key === 'control' || key === 'shift' || key === 'alt') return;
        
        if (key === ' ') key = 'space';
        if (key === 'arrowup') key = 'arrowup';
        if (key === 'arrowdown') key = 'arrowdown';
        if (key === 'arrowleft') key = 'arrowleft';
        if (key === 'arrowright') key = 'arrowright';
        
        parts.push(key);
        
        capturedKeyCombination = parts.join('+');
        capturedKeyDisplay.textContent = capturedKeyCombination.toUpperCase().replace(/\+/g, ' + ');
        capturedKeyDiv.style.display = 'block';
        
        captureBtn.textContent = 'Capture Key Combination';
        captureBtn.classList.remove('btn-warning');
        captureBtn.classList.add('btn-outline-info');
        isCapturing = false;
    });
    
    // Use captured key
    useKeyBtn.addEventListener('click', function() {
        keyInput.value = capturedKeyCombination;
        keyPreview.textContent = capturedKeyCombination.toUpperCase().replace(/\+/g, ' + ');
        checkKeyAvailability(capturedKeyCombination);
        capturedKeyDiv.style.display = 'none';
    });
    
    // Initial check
    checkKeyAvailability(keyInput.value);
});
</script>
@endpush
@endsection
