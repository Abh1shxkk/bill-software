<?php $__env->startSection('title', 'Access Denied'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger mt-5">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-x text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="text-danger mb-3">Access Denied</h2>
                    <p class="text-muted mb-4">
                        You do not have permission to access this module.<br>
                        Please contact your administrator if you believe this is an error.
                    </p>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/errors/403.blade.php ENDPATH**/ ?>