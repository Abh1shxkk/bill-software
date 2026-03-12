<?php $__env->startSection('title', 'Edit Organization Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="<?php echo e(route('admin.organization.update-profile')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-building me-2"></i>Edit Organization Profile
                        </h5>
                        <a href="<?php echo e(route('admin.organization.settings')); ?>" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <label class="form-label">Logo</label>
                                <div class="mb-2">
                                    <?php if($organization->logo_path): ?>
                                        <img src="<?php echo e(Storage::url($organization->logo_path)); ?>" 
                                             alt="Logo" class="img-fluid rounded" style="max-height: 100px;" id="logoPreview">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center mx-auto"
                                             style="width: 100px; height: 100px; font-size: 2rem;" id="logoPlaceholder">
                                            <?php echo e(strtoupper(substr($organization->name, 0, 2))); ?>

                                        </div>
                                        <img src="" alt="Logo" class="img-fluid rounded d-none" style="max-height: 100px;" id="logoPreview">
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="logo" id="logoInput" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Max 2MB, JPG/PNG</small>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('name', $organization->name)); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo e(old('email', $organization->email)); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" 
                                               value="<?php echo e(old('phone', $organization->phone)); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Address -->
                        <h6 class="mb-3">Address</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Street Address</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $organization->address)); ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" 
                                       value="<?php echo e(old('city', $organization->city)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" 
                                       value="<?php echo e(old('state', $organization->state)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">PIN Code</label>
                                <input type="text" name="pin_code" class="form-control" 
                                       value="<?php echo e(old('pin_code', $organization->pin_code)); ?>">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Tax & Licenses -->
                        <h6 class="mb-3">Tax & Licenses</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">GST Number</label>
                                <input type="text" name="gst_no" class="form-control" 
                                       value="<?php echo e(old('gst_no', $organization->gst_no)); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_no" class="form-control" 
                                       value="<?php echo e(old('pan_no', $organization->pan_no)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Drug License No. 1</label>
                                <input type="text" name="dl_no" class="form-control" 
                                       value="<?php echo e(old('dl_no', $organization->dl_no)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Drug License No. 2</label>
                                <input type="text" name="dl_no_1" class="form-control" 
                                       value="<?php echo e(old('dl_no_1', $organization->dl_no_1)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Food License</label>
                                <input type="text" name="food_license" class="form-control" 
                                       value="<?php echo e(old('food_license', $organization->food_license)); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-1"></i>Save Changes
                        </button>
                        <a href="<?php echo e(route('admin.organization.settings')); ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreview').classList.remove('d-none');
            const placeholder = document.getElementById('logoPlaceholder');
            if (placeholder) placeholder.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/organization/edit-profile.blade.php ENDPATH**/ ?>