@extends('layouts.admin')

@section('title', 'Edit Marketing Manager')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Marketing Manager</h2>
                <a href="{{ route('admin.marketing-managers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Marketing Managers
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Marketing Manager Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marketing-managers.update', $marketingManager) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $marketingManager->name) }}" 
                                           placeholder="Enter marketing manager name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $marketingManager->code) }}" 
                                           placeholder="Enter code">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" 
                                              placeholder="Enter address">{{ old('address', $marketingManager->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Telephone</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $marketingManager->telephone) }}" 
                                           placeholder="Enter telephone number">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                           id="mobile" name="mobile" value="{{ old('mobile', $marketingManager->mobile) }}" 
                                           placeholder="Enter mobile number">
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-Mail</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $marketingManager->email) }}" 
                                           placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control @error('status') is-invalid @enderror" 
                                           id="status" name="status" value="{{ old('status', $marketingManager->status) }}" 
                                           placeholder="Enter status">
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gen_mgr" class="form-label">Gen.mgr. <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('gen_mgr') is-invalid @enderror" 
                                               id="gen_mgr" name="gen_mgr" value="{{ old('gen_mgr', $marketingManager->gen_mgr) }}" 
                                               placeholder="Select general manager" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="openGeneralManagerModal()">
                                            <i class="bi bi-search me-2"></i>Select
                                        </button>
                                        @error('gen_mgr')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Update Marketing Manager
                            </button>
                            <a href="{{ route('admin.marketing-managers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- General Manager Modal -->
<div id="generalManagerModal" class="manager-modal">
    <div class="manager-modal-content">
        <div class="manager-modal-header">
            <h5 class="manager-modal-title">
                <i class="bi bi-people me-2"></i>Select General Manager
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeGeneralManagerModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="manager-modal-body" id="generalManagerModalBody">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<div id="generalManagerModalBackdrop" class="manager-modal-backdrop"></div>

<script>
function openGeneralManagerModal() {
    const modal = document.getElementById('generalManagerModal');
    const backdrop = document.getElementById('generalManagerModalBackdrop');
    const modalBody = document.getElementById('generalManagerModalBody');
    backdrop.style.display = 'block';
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
    modalBody.innerHTML = `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
    fetch('{{ route("admin.general-managers.index") }}?modal=true', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => populateGeneralManagerModal(data))
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load general managers. Please try again.</div>`;
        });
}
function populateGeneralManagerModal(managers) {
    const modalBody = document.getElementById('generalManagerModalBody');
    if (!managers || managers.length === 0) {
        modalBody.innerHTML = `<div class="text-center py-5"><i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i><p class="text-muted mt-3">No general managers found</p><a href="{{ route('admin.general-managers.create') }}" class="btn btn-sm btn-primary mt-2"><i class="bi bi-plus-lg me-2"></i>Add New General Manager</a></div>`;
        return;
    }
    let html = '<div class="row g-2">';
    managers.forEach(manager => {
        html += `<div class="col-12"><div class="manager-card p-3" onclick="selectGeneralManager('${manager.name} (${manager.code})', '${manager.id}')"><div class="d-flex justify-content-between align-items-start"><div><h6 class="mb-1 fw-600">${manager.name}</h6><small class="text-muted">Code: ${manager.code}</small></div><i class="bi bi-chevron-right text-primary"></i></div><div class="mt-2 small text-muted"><div><i class="bi bi-telephone me-2"></i>${manager.mobile || 'N/A'}</div><div><i class="bi bi-envelope me-2"></i>${manager.email || 'N/A'}</div></div></div></div>`;
    });
    html += '</div>';
    modalBody.innerHTML = html;
}
function selectGeneralManager(name, id) {
    document.getElementById('gen_mgr').value = name;
    closeGeneralManagerModal();
}
function closeGeneralManagerModal() {
    const modal = document.getElementById('generalManagerModal');
    const backdrop = document.getElementById('generalManagerModalBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    setTimeout(() => { backdrop.style.display = 'none'; }, 300);
}
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'generalManagerModalBackdrop') closeGeneralManagerModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('generalManagerModal');
        if (modal && modal.classList.contains('show')) closeGeneralManagerModal();
    }
});
</script>
<style>
.manager-modal { position: fixed; top: 70px; right: 0; width: 400px; height: calc(100vh - 70px); z-index: 999999; transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); visibility: hidden; opacity: 0; }
.manager-modal.show { transform: translateX(0); visibility: visible; opacity: 1; }
.manager-modal-content { background: white; height: 100%; box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2); display: flex; flex-direction: column; }
.manager-modal-header { padding: 1rem 1.25rem; border-bottom: 2px solid #dee2e6; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
.manager-modal-title { margin: 0; font-size: 1.1rem; font-weight: 600; color: #ffffff; }
.btn-close-modal { background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; cursor: pointer; font-size: 1rem; }
.btn-close-modal:hover { background: rgba(255, 255, 255, 0.3); transform: rotate(90deg); }
.manager-modal-body { padding: 1rem; overflow-y: auto; flex: 1; background: #f8f9fa; }
.manager-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); z-index: 999998; opacity: 0; transition: all 0.3s ease; }
.manager-modal-backdrop.show { opacity: 1; }
.manager-card { cursor: pointer; transition: all 0.2s ease; border: 1px solid #dee2e6; }
.manager-card:hover { border-color: #0d6efd; box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15); transform: translateY(-1px); }
@media (max-width: 768px) { .manager-modal { width: 100%; } }
</style>
@endsection
