<?php $__env->startSection('title', 'Breakage/Expiry Transaction'); ?>

<?php $__env->startSection('content'); ?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    input:focus {
        box-shadow: none !important;
    }

    .header-sectionSR {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }

    .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .inner-card-sr {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }

    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    /* Focus ring (blue border) */
    input:focus,
    select:focus,
    .kb-focus {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        background-color: inherit !important;
        outline: none !important;
    }

    /* Keep ring visible for keyboard focus */
    input:focus-visible,
    select:focus-visible {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        background-color: inherit !important;
        outline: none !important;
    }

    .table-compact {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    .table-compact th,
    .table-compact td {
        padding: 4px;
        vertical-align: middle;
        height: 45px;
    }
    
    .table-compact th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        height: 40px;
    }
    
    .table-compact input {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }

    /* Custom searchable dropdown styles */
    .custom-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5ff;
    }
    
    .custom-dropdown-menu .dropdown-item:active {
        background-color: #e3ebff;
    }

    .custom-dropdown-menu .dropdown-item.active {
        background-color: #cfe2ff;
    }

    /* Keyboard-selected rows in modals */
    .kb-row-active,
    .kb-row-active td {
        background-color: #cfe2ff !important;
    }

    .credit-note-options button.kb-active,
    .credit-note-options button:focus,
    .credit-note-options button:focus-visible {
        outline: 2px solid #0d6efd !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    }

    .adjustment-input.kb-active,
    .adjustment-input:focus,
    .adjustment-input:focus-visible {
        outline: 2px solid #0d6efd !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    }

    /* Item modal keyboard selection row */
    .item-row-selected,
    .item-row-selected td {
        background-color: #1976d2 !important;
        color: white !important;
    }
    .item-row-selected button {
        background-color: white !important;
        color: #1976d2 !important;
    }

    .item-modal-backdrop, .batch-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    .item-modal-backdrop.show, .batch-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .item-modal, .batch-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 57%;
        transform: translate(-50%, -50%) scale(0.7);
        width: calc(100% - 200px);
        max-width: 800px;
        z-index: 1055;
        opacity: 0;
        animation: zoomIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        margin-left: 0;
    }

    .item-modal.show, .batch-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .item-modal-content, .batch-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .item-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .batch-modal-header {
        padding: 1rem 1.5rem;
        background: #17a2b8;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-modal-title, .batch-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .btn-close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    .item-modal-body, .batch-modal-body {
        padding: 1rem;
        max-height: 350px;
        overflow-y: auto;
    }

    .batch-modal-body {
        padding: 0;
    }

    .item-modal-footer, .batch-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes zoomIn {
        from {
            transform: translate(-50%, -50%) scale(0.7);
            opacity: 0;
        }
        to {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    @keyframes zoomOut {
        from {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        to {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }
    }

    /* Batch Not Exist Modal Styles */
    .batch-not-exist-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1055;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .batch-not-exist-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .batch-not-exist-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        z-index: 1060;
        width: 90%;
        max-width: 400px;
        opacity: 0;
        animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .batch-not-exist-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .batch-not-exist-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .batch-not-exist-modal-header {
        padding: 1rem 1.5rem;
        background: #ffc107;
        color: #212529;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e0a800;
    }

    .batch-not-exist-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .batch-not-exist-modal-body {
        padding: 1.5rem;
        background: #fff;
        text-align: center;
    }

    .batch-not-exist-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    /* Create Batch Modal Styles */
    .create-batch-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1060;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .create-batch-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .create-batch-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        z-index: 1065;
        width: 90%;
        max-width: 600px;
        opacity: 0;
        animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .create-batch-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .create-batch-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .create-batch-modal-header {
        padding: 1rem 1.5rem;
        background: #007bff;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0056b3;
    }

    .create-batch-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .create-batch-modal-body {
        padding: 1.5rem;
        background: #fff;
    }

    .create-batch-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        text-align: right;
    }

    /* Alert Animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    /* Credit Note Modal Styles */
    .credit-note-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .credit-note-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .credit-note-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        z-index: 10001;
        width: 400px;
        opacity: 0;
        animation: bounceIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .credit-note-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .credit-note-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .credit-note-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0b5ed7;
    }

    .credit-note-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .credit-note-modal-body {
        padding: 1.5rem;
        background: #fff;
        text-align: center;
    }

    .credit-note-options {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .credit-note-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    /* Adjustment Modal Styles (similar to Sale Return) */
    .adjustment-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .adjustment-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .adjustment-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        z-index: 10001;
        width: 80%;
        max-width: 800px;
        opacity: 0;
        animation: bounceIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .adjustment-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .adjustment-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .adjustment-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0b5ed7;
    }

    .adjustment-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .adjustment-modal-body {
        padding: 1rem;
        background: #fff;
    }

    .adjustment-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    /* Bounce animations */
    @keyframes bounceIn {
        0% {
            transform: translate(-50%, -50%) scale(0.3);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.05);
        }
        70% {
            transform: translate(-50%, -50%) scale(0.9);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    @keyframes bounceOut {
        0% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(0.3);
            opacity: 0;
        }
    }


</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cart-plus me-2"></i> Breakage/Expiry Transaction</h4>
        <div class="text-muted small">Create new breakage/expiry transaction</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="breakageExpiryTransactionForm" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>

            <!-- Header Section -->
            <div class="header-sectionSR">
                <!-- Row 1 -->
                <div class="header-row">
                    <div class="field-group">
                        <label>Series:</label>
                        <input type="text" class="form-control" name="series" id="seriesInput" style="width: 60px;" value="BE">
                    </div>

                    <div class="field-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" name="transaction_date" id="transactionDate" style="width: 140px;" value="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <div class="field-group">
                        <label>End Date:</label>
                        <input type="date" class="form-control" name="end_date" id="endDate" style="width: 140px;">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="d-flex gap-3">

                    <!-- Right Side - Inner Card SR -->
                    <div class="inner-card-sr flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group" style="position: relative;">
                                    <label style="width: 100px;">Name</label>
                                    <div class="custom-dropdown-wrapper" style="width: 100%; position: relative;">
                                        <input type="text" 
                                               class="form-control no-select2" 
                                               id="customerSearchInput" 
                                               placeholder="Type to search customer..."
                                               autocomplete="off"
                                               style="width: 100%;">
                                        <select class="form-control no-select2" name="customer_id" id="customerSelect" autocomplete="off" style="display: none;">
                                            <option value="">Select Customer</option>
                                            <?php $__currentLoopData = $customers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($customer->id); ?>" data-name="<?php echo e($customer->name); ?>"><?php echo e($customer->code ?? ''); ?> - <?php echo e($customer->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        
                                        <div id="customerDropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                                            <div class="dropdown-header" style="padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-weight: 600; font-size: 13px;">
                                                Select Customer
                                            </div>
                                            <div id="customerList" class="dropdown-list">
                                                <?php $__currentLoopData = $customers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="dropdown-item" 
                                                         data-id="<?php echo e($customer->id); ?>" 
                                                         data-name="<?php echo e($customer->name); ?>"
                                                         data-code="<?php echo e($customer->code ?? ''); ?>"
                                                         style="padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                                        <?php echo e($customer->code ?? ''); ?> - <?php echo e($customer->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>GST Vno:</label>
                                    <input type="text" class="form-control" name="gst_vno" id="gstVno" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>R(epl.) / C(redit) Note:</label>
                                    <input type="text" class="form-control" name="note_type" id="noteType" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>With GST[Y/N]:</label>
                                    <input type="text" class="form-control" name="with_gst" id="withGst" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-1">
                            <div class="col-md-5">
                                <div class="field-group" style="position: relative;">
                                    <label style="width: 100px;">Sales Man</label>
                                    <div class="custom-dropdown-wrapper" style="width: 100%; position: relative;">
                                        <input type="text" 
                                               class="form-control no-select2" 
                                               id="salesmanSearchInput" 
                                               placeholder="Type to search salesman..."
                                               autocomplete="off"
                                               style="width: 100%;">
                                        <select class="form-control no-select2" name="salesman_id" id="salesmanSelect" autocomplete="off" style="display: none;">
                                            <option value="">Select Salesman</option>
                                            <?php $__currentLoopData = $salesmen ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($salesman->id); ?>" data-name="<?php echo e($salesman->name); ?>"><?php echo e($salesman->code ?? ''); ?> - <?php echo e($salesman->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        
                                        <div id="salesmanDropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                                            <div class="dropdown-header" style="padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-weight: 600; font-size: 13px;">
                                                Select Salesman
                                            </div>
                                            <div id="salesmanList" class="dropdown-list">
                                                <?php $__currentLoopData = $salesmen ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="dropdown-item" 
                                                         data-id="<?php echo e($salesman->id); ?>" 
                                                         data-name="<?php echo e($salesman->name); ?>"
                                                         data-code="<?php echo e($salesman->code ?? ''); ?>"
                                                         style="padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                                        <?php echo e($salesman->code ?? ''); ?> - <?php echo e($salesman->name); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Inc.</label>
                                    <input type="text" class="form-control" name="inc" id="inc" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Rev.Charge</label>
                                    <input type="text" class="form-control" name="rev_charge" id="revCharge" value="Y" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            

                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label>To be Adjusted?[Y/N],&lt;X&gt; for Imm. Posting</label>
                                    <input type="text" class="form-control" name="adjusted" id="adjustedFlag" value="X" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 80px;">Dis. Rpl:</label>
                                    <input type="text" class="form-control" name="dis_rpl" id="disRpl">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 100px;">Brk. :</label>
                                    <input type="text" class="form-control" name="brk" id="brk">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Exp. :</label>
                                    <input type="text" class="form-control" name="exp" id="expField">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div style="width: 200px;">
                        <div class="field-group mb-2">
                            <label style="width: 150px;">Sr. No.:</label>
                            <input type="text" class="form-control readonly-field" name="sr_no" value="<?php echo e($nextSrNo ?? '1'); ?>" readonly>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-info" id="insertOrdersBtn" style="width: 100%;" onclick="openItemSelectionModal()">
                                <i class="bi bi-list-check"></i> Insert Orders
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Exp.</th>
                                <th style="width: 60px;">Br/Ex.</th>
                                <th style="width: 60px;">Qty</th>
                                <th style="width: 80px;">F.Qty.</th>
                                <th style="width: 80px;">MRP</th>
                                <th style="width: 80px;">Scm.%</th>   
                                <th style="width: 80px;">Dis.%</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" id="addRowBtn" onclick="addNewRow()">
                        <i class="fas fa-plus-circle"></i> Add Row
                    </button>
                </div>
            </div>


            <!-- Calculation Section -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!--  -->
                    <div class="d-flex flex-column gap-2">
                        <!-- HSN Code -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>HSN Code:</strong></label>
                            <input type="text" id="calc_hsn_code" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="---" readonly>
                        </div>

                        <!-- CGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>CGST(%):</strong></label>
                            <input type="text" id="calc_cgst_percent" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0" readonly>
                        </div>

                        <!-- CGST Amount -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>CGST Amt:</strong></label>
                            <input type="text" id="calc_cgst_amount" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0.00" readonly>
                        </div>


                    </div>

                    <!-- Right Side Fields (2 Columns) -->
                    <div class="d-flex gap-3">
                        <!-- Column 1 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- SC -->
                            <!-- SGST(%) -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>SGST(%):</strong></label>
                                <input type="text" id="calc_sgst_percent" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0" readonly>
                            </div>

                            <!-- SGST Amount -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>SGST Amt:</strong></label>
                                <input type="text" id="calc_sgst_amount" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0.00" readonly>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="d-flex flex-column gap-2">
                            <!--  -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 65px;"><strong>TAX %</strong></label>
                                <input type="number" id="calc_tax_percent" class="form-control readonly-field" readonly style="width: 80px; height: 28px;" value="0">
                            </div> 

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 65px;"><strong>P.Rate</strong></label>
                                <input type="number" id="calc_prate" class="form-control readonly-field" readonly style="width: 80px; height: 28px;" value="0.00">
                            </div>

                        </div>

                        <!-- Column 3 -->
                        <div class="d-flex flex-column gap-2">
                            <!--  -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>S.Rate</strong></label>
                                <input type="number" id="calc_srate" class="form-control readonly-field" readonly style="width: 70px; height: 28px;" value="0.00">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>MRP</strong></label>
                                <input type="text" id="calc_mrp" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="0.00">
                            </div>

                        </div>

                        <!-- Column 4 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- Excise -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>Pack</strong></label>
                                <input type="text" id="calc_pack" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>Disallow</strong></label>
                                <input type="text" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="N">
                            </div>

                        </div>
                        <!-- Column 4 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- TSR. -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 80px;"><strong>Payable Amt</strong></label>
                                <input type="text" id="calc_payable_amount" class="form-control text-center readonly-field" readonly style="width: 80px; height: 28px;" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Summary Section -->
            <div class="bg-white border rounded p-2 mb-2">
                <!-- Row 1: 6 fields -->
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">MRP Value</label>
                        <input type="number" id="summary_mrp_value" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #fff3cd;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Gross</label>
                        <input type="number" id="summary_gross" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" id="summary_discount" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" id="summary_scheme" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" id="summary_tax" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Net</label>
                        <input type="number" id="summary_net" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>

            </div>

            <!-- Additional Fields (same as original) -->
            <div class="col-12 mb-4 bg-white border rounded p-2 mb-2">
                <div class="row gx-3" style="font-size: 11px; gap: 10px;">
                    <!-- col 1 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Packing</label>
                                    <input type="number" id="detail_packing" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Unit</label>
                                    <input type="number" id="detail_unit" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Cl. Qty.</label>
                                    <input type="number" id="detail_cl_qty" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 2 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Gross</label>
                                    <input type="number" id="detail_gross" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Scm. Amt.</label>
                                    <input type="number" id="detail_scm_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Dis.Amt.</label>
                                    <input type="number" id="detail_dis_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 3 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Sub Tot.</label>
                                    <input type="number" id="detail_subtotal" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Tax Amt.</label>
                                    <input type="number" id="detail_tax_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Net Amt.</label>
                                    <input type="number" id="detail_net_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col-4 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Less Pcnt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col-5 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Inv.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Srino</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">SCM.</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="height: 26px; background: #ffcccc;" value="0.00">
                                        <label class="form-label mx-1 mb-0 fs-4 fw-bold">+</label>
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="height: 26px; background: #ffcccc;" value="0.00">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="saveTransactionBtn" onclick="saveTransaction()">
                    <i class="bi bi-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='<?php echo e(route('admin.dashboard')); ?>'">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="addNewRow()">
                    <i class="bi bi-plus-circle"></i> Add Row
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modals will be dynamically created by JavaScript -->

<script>
let currentRowIndex = 0;

function getNextRowIndex() {
    const rows = document.querySelectorAll('#itemsTableBody tr[id^="row-"]');
    let maxExisting = -1;

    rows.forEach(row => {
        const n = parseInt((row.id || '').replace('row-', ''), 10);
        if (!Number.isNaN(n) && n > maxExisting) {
            maxExisting = n;
        }
    });

    if (currentRowIndex <= maxExisting) {
        currentRowIndex = maxExisting + 1;
    }

    return currentRowIndex++;
}
let itemsData = [];
let currentItemForBatch = null;

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertTypes = {
        'success': { bg: '#28a745', icon: '✓' },
        'error': { bg: '#dc3545', icon: '✕' },
        'info': { bg: '#17a2b8', icon: 'ℹ' },
        'warning': { bg: '#ffc107', icon: '⚠' }
    };
    
    const alertConfig = alertTypes[type] || alertTypes['info'];
    
    const alertHTML = `
        <div class="custom-alert" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${alertConfig.bg};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; font-weight: bold;">${alertConfig.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
}

// Pagination state for items
let itemsCurrentPage = 1;
let itemsPerPage = 50;
let itemsHasMore = true;
let itemsLoading = false;

// Open Item Selection Modal
function openItemSelectionModal() {
    // Check if customer is selected
    const customerId = document.querySelector('select[name="customer_id"]').value;
    
    if (!customerId) {
        showAlert('error', 'Please select a customer first.');
        return;
    }
    
    // Use reusable item selection modal
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else if (typeof showItemModal === 'function') {
        showItemModal();
    } else {
        showAlert('error', 'Item selection modal not initialized. Please reload the page.');
    }
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from modal:', item);
    console.log('Batch selected from modal:', batch);
    
    // Reuse first empty row if available, otherwise create a new row.
    const tbody = document.getElementById('itemsTableBody');
    const reusableRow = Array.from(tbody.querySelectorAll('tr')).find(r => {
        const codeVal = r.querySelector('input[name*="[code]"]')?.value?.trim() || '';
        const nameVal = r.querySelector('input[name*="[name]"]')?.value?.trim() || '';
        return codeVal === '' && nameVal === '';
    });

    let rowIndex;
    let row;
    if (reusableRow) {
        row = reusableRow;
        const parsed = parseInt((row.id || '').replace('row-', ''), 10);
        rowIndex = Number.isNaN(parsed) ? getNextRowIndex() : parsed;
        row.id = `row-${rowIndex}`;
    } else {
        rowIndex = getNextRowIndex();
        row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        tbody.appendChild(row);
    }
    
    // Format expiry date
    let expiryDisplay = '';
    if (batch.expiry_date) {
        const expiryDate = new Date(batch.expiry_date);
        expiryDisplay = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
    }
    
    row.innerHTML = `
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly>
        </td>
        <td>
            <select class="form-control no-select2" name="items[${rowIndex}][br_ex]" style="width: 60px;">
                <option value="B">B</option>
                <option value="E">E</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="0" step="1" 
                   onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'f_qty'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][f_qty]" value="0" step="1"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][mrp]" value="${parseFloat(batch.mrp || 0).toFixed(2)}" step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})" readonly>
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][scm_percent]" value="0" step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="0" step="0.01" 
                   onchange="handleDiscountChange(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndAddRow(${rowIndex}); return false; }">
        </td>
        <td>
            <input type="number" class="form-control readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
        <input type="hidden" name="items[${rowIndex}][s_rate]" value="${batch.s_rate || 0}">
        <input type="hidden" name="items[${rowIndex}][p_rate]" value="${batch.p_rate || batch.pur_rate || 0}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="${item.cgst_percent || 0}">
        <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="${item.sgst_percent || 0}">
    `;
    // Store batch and item data for calculations
    row.dataset.batchData = JSON.stringify(batch);
    row.dataset.sRate = batch.s_rate || 0;
    row.dataset.pRate = batch.p_rate || batch.pur_rate || 0;
    row.dataset.mrp = batch.mrp || 0;
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id || '';

    // Update calculation and details immediately for selected item
    updateCalculationSection(batch, item);
    updateAdditionalDetails(row, item);

    // Ensure row click selects it for calculation display
    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });

    bindDisEnterHandler(row, rowIndex);

    // Focus on qty input
    setTimeout(() => {
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
            qtyInput.focus();
        }
    }, 100);
    
    showAlert('success', 'Item added! Enter quantity.');
    calculateRowAmount(rowIndex);
    updateAllCalculations();
};

// Load Items from Database with Pagination
function loadPaginatedItems(page, isInitial = false) {
    if (itemsLoading || (!itemsHasMore && !isInitial)) return;
    
    itemsLoading = true;
    
    // Show loading indicator if not initial
    if (!isInitial) {
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';
    }
    
    const url = `<?php echo e(route("admin.items.all")); ?>?page=${page}&per_page=${itemsPerPage}`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        itemsLoading = false;
        
        // Hide loading indicator
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        const items = data.items || [];
        
        // Check if we have pagination info
        if (data.pagination) {
            itemsHasMore = data.pagination.has_more || (page < data.pagination.last_page);
        } else {
            // Legacy: no pagination, all items returned
            itemsHasMore = false;
        }
        
        // Store loaded items
        itemsData = itemsData.concat(items);
        
        if (isInitial) {
            // Show modal with initial items
            showPaginatedItemModal(items);
        } else {
            // Append items to existing table
            appendItemsToTable(items);
        }
        
        // Update records info
        updateItemsRecordsInfo();
        
        itemsCurrentPage++;
    })
    .catch(error => {
        itemsLoading = false;
        console.error('Error loading items:', error);
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        if (isInitial) {
            showAlert('error', 'Error loading items. Please try again.');
        }
    });
}

// Display Items in Modal (legacy - still used for search filtering)
function displayItems(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!tbody) return;
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No items found</td></tr>';
        return;
    }
    
    let html = '';
    items.forEach((item, index) => {
        html += `
            <tr style="padding: 2px;">
                <td style="padding: 4px;">${item.id || ''}</td>
                <td style="padding: 4px;">${item.name || ''}</td>
                <td style="padding: 4px;">${item.packing || ''}</td>
                <td class="text-end" style="padding: 4px;">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 4px;">${parseFloat(item.total_qty || 0).toFixed(2)}</td>
                <td class="text-center" style="padding: 4px;">
                    <button type="button" class="btn btn-sm btn-primary" style="padding: 2px 6px; font-size: 10px;" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check-circle"></i> Select
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Append items to the modal table (for infinite scroll)
function appendItemsToTable(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!tbody) return;
    
    items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.style.padding = '2px';
        row.innerHTML = `
            <td style="padding: 4px;">${item.id || ''}</td>
            <td style="padding: 4px;">${item.name || ''}</td>
            <td style="padding: 4px;">${item.packing || ''}</td>
            <td class="text-end" style="padding: 4px;">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td class="text-end" style="padding: 4px;">${parseFloat(item.total_qty || 0).toFixed(2)}</td>
            <td class="text-center" style="padding: 4px;">
                <button type="button" class="btn btn-sm btn-primary" style="padding: 2px 6px; font-size: 10px;" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                    <i class="bi bi-check-circle"></i> Select
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update items records info
function updateItemsRecordsInfo() {
    const infoEl = document.getElementById('itemsRecordsInfo');
    if (!infoEl) return;
    
    const loadedCount = itemsData.length;
    if (itemsHasMore) {
        infoEl.textContent = `Showing ${loadedCount} items (scroll for more)`;
    } else {
        infoEl.textContent = `Showing all ${loadedCount} items`;
    }
}

// Setup infinite scroll for items modal
function setupItemsInfiniteScroll() {
    const scrollContainer = document.getElementById('itemsScrollContainer');
    if (!scrollContainer) return;
    
    scrollContainer.addEventListener('scroll', function() {
        // Check if scrolled near bottom
        if (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 50) {
            // Load more items if available
            if (itemsHasMore && !itemsLoading) {
                loadPaginatedItems(itemsCurrentPage, false);
            }
        }
    });
}

// Show Item Selection Modal with Pagination
function showPaginatedItemModal(items) {
    const totalInfo = itemsHasMore ? `Showing first ${items.length} items (scroll for more)` : `Showing all ${items.length} items`;
    
    const modalHTML = `
        <div class="item-modal-backdrop" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-content">
                <div class="item-modal-header">
                    <h5 class="item-modal-title"><i class="bi bi-box-seam"></i> Select Item</h5>
                    <button type="button" class="btn-close-modal" onclick="closeItemModal()">&times;</button>
                </div>
                <div class="item-modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by item name or code..." onkeyup="filterItems()">
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="itemsScrollContainer">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 11px; margin-bottom: 0;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #e9ecef;">
                                <tr>
                                    <th style="width: 50px; padding: 4px;">Code</th>
                                    <th style="width: 100px; padding: 4px;">Item Name</th>
                                    <th style="width: 40px; padding: 4px;">Packing</th>
                                    <th style="width: 40px; padding: 4px;">MRP</th>
                                    <th style="width: 40px; padding: 4px;">Stock</th>
                                    <th style="width: 80px; padding: 4px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsListBody"></tbody>
                        </table>
                        <!-- Loading indicator -->
                        <div id="itemsLoadingIndicator" style="display: none; text-align: center; padding: 15px; color: #6c757d;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Loading more items...</span>
                        </div>
                    </div>
                </div>
                <div class="item-modal-footer">
                    <small class="text-muted me-auto" id="itemsRecordsInfo">${totalInfo}</small>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('itemModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('itemModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    setTimeout(() => {
        document.getElementById('itemModalBackdrop').classList.add('show');
        document.getElementById('itemModal').classList.add('show');
        
        // Setup infinite scroll
        setupItemsInfiniteScroll();
        
        // Setup keyboard navigation for items modal
        setupItemModalKeyboardNav();
        
        // Focus search input
        const searchInput = document.getElementById('itemSearchInput');
        if (searchInput) searchInput.focus();
    }, 10);
    
    displayItems(items);
}

// Item modal keyboard navigation state
let itemModalSelectedIndex = -1;

// Setup keyboard navigation for items modal
function setupItemModalKeyboardNav() {
    const searchInput = document.getElementById('itemSearchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('keydown', handleItemModalKeyDown);
}

// Handle keyboard navigation in items modal
function handleItemModalKeyDown(e) {
    const tbody = document.getElementById('itemsListBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('tr');
    if (rows.length === 0) return;
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // If nothing selected, start at first row
        if (itemModalSelectedIndex < 0) {
            itemModalSelectedIndex = 0;
        } else if (itemModalSelectedIndex < rows.length - 1) {
            itemModalSelectedIndex++;
        }
        highlightItemModalRow(rows, itemModalSelectedIndex);
        
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        if (itemModalSelectedIndex > 0) {
            itemModalSelectedIndex--;
            highlightItemModalRow(rows, itemModalSelectedIndex);
        }
        
    } else if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        console.log('[KB-ItemModal-BE] Enter pressed', { selectedIndex: itemModalSelectedIndex, rowCount: rows.length });
        
        // Select the highlighted row, or first row if nothing highlighted
        let targetIndex = itemModalSelectedIndex >= 0 ? itemModalSelectedIndex : 0;
        
        if (rows[targetIndex]) {
            const selectBtn = rows[targetIndex].querySelector('button');
            if (selectBtn) {
                selectBtn.click();
            }
        }
        
    } else if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeItemModal();
    }
}

// Highlight row in items modal
function highlightItemModalRow(rows, index) {
    // Clear all highlights
    rows.forEach(r => r.classList.remove('item-row-selected'));
    
    if (rows[index]) {
        rows[index].classList.add('item-row-selected');
        rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
}

// Show Item Modal (legacy - kept for backward compatibility)
function showItemModal() {
    // Use paginated modal with current data
    showPaginatedItemModal(itemsData);
}

// Load Items (legacy - kept for backward compatibility)
function loadItems() {
    // Reset and load paginated
    itemsCurrentPage = 1;
    itemsHasMore = true;
    itemsLoading = false;
    itemsData = [];
    loadPaginatedItems(itemsCurrentPage, true);
}

// Close Item Modal
function closeItemModal() {
    const modal = document.getElementById('itemModal');
    const backdrop = document.getElementById('itemModalBackdrop');
    
    if (modal) modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    if (backdrop) backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Filter Items
function filterItems() {
    const searchTerm = document.getElementById('itemSearchInput').value.toLowerCase();
    const filteredItems = itemsData.filter(item => {
        const name = (item.name || '').toLowerCase();
        const code = (item.id || '').toString().toLowerCase();
        return name.includes(searchTerm) || code.includes(searchTerm);
    });
    // Reset keyboard selection when filtering
    itemModalSelectedIndex = -1;
    displayItems(filteredItems);
}

// Select Item and Add to Table
function selectItem(item) {
    closeItemModal();
    addNewRowWithItem(item);
}

// Add New Row with Item
function addNewRowWithItem(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = getNextRowIndex();
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" 
                   onblur="checkBatchExists(${rowIndex}, ${item.id}, this.value)"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); checkBatchExists(${rowIndex}, ${item.id}, this.value); return false; }"
                   placeholder="Enter batch no.">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'br_ex'); return false; }" 
                   placeholder="MM/YY">
        </td>
        <td>
            <select class="form-control form-control-sm no-select2" name="items[${rowIndex}][br_ex]"
                    onchange="moveToNextField(${rowIndex}, 'qty')"
                    onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'qty'); return false; }">
                <option value="">Select</option>
                <option value="B">Breakage</option>
                <option value="E">Expiry</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" 
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'f_qty'); return false; }" 
                   placeholder="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][f_qty]" 
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" 
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'scm_percent'); return false; }" 
                   value="">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][scm_percent]" 
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][dis_percent]" 
                   step="0.01" onchange="handleDiscountChange(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndAddRow(${rowIndex}); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" 
                   step="0.01" readonly value="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Store item data for later use
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    
    // Add click event to row to update calculation section
    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });

    bindDisEnterHandler(row, rowIndex);
    
    // Focus on batch input
    setTimeout(() => {
        const batchInput = row.querySelector('input[name*="[batch]"]');
        if (batchInput) {
            batchInput.focus();
        }
    }, 100);
}

// Check if Batch Exists
function checkBatchExists(rowIndex, itemId, batchNo) {
    if (!batchNo || batchNo.trim() === '') {
        return;
    }
    
    // Show loading indicator
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    fetch(`<?php echo e(route('admin.batches.check-batch')); ?>?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Batch check response:', data);
        if (data.exists && data.batches && data.batches.length > 0) {
            // Show batch details modal
            currentItemForBatch = {
                rowIndex: rowIndex,
                itemId: itemId,
                batchNo: batchNo,
                itemName: row.querySelector('input[name*="[name]"]').value,
                packing: data.item_packing || ''
            };
            console.log('Showing batch modal with batches:', data.batches);
            showBatchDetailsModal(data.batches, data.item_name, data.item_packing);
        } else {
            // Batch doesn't exist, show confirmation modal
            console.log('Batch not found or no batches returned');
            currentItemForBatch = {
                rowIndex: rowIndex,
                itemId: itemId,
                batchNo: batchNo,
                itemName: row.querySelector('input[name*="[name]"]').value,
                packing: data.item_packing || ''
            };
            showBatchNotExistModal();
        }
    })
    .catch(error => {
        console.error('Error checking batch:', error);
        if (window.crudNotification) {
            crudNotification.showToast('error', 'Error', 'Error checking batch: ' + error.message);
        }
    });
}

// Show Batch Details Modal
function showBatchDetailsModal(batches, itemName, packing) {
    let tableRows = '';
    
    batches.forEach(batch => {
        const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}).replace(/ /g, '-') : '';
        const mfgDate = batch.manufacturing_date ? new Date(batch.manufacturing_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}).replace(/ /g, '-') : '';
        
        tableRows += `
            <tr style="background-color: ${batch.total_qty > 0 ? '#ffcccc' : '#ffffff'}; padding: 2px;">
                <td style="padding: 3px;"><strong>${batch.batch_no || ''}</strong></td>
                <td style="padding: 3px;">${mfgDate}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;"><strong>${parseFloat(batch.mrp || 0).toFixed(2)}</strong></td>
                <td class="text-end" style="padding: 3px;"><strong>${parseFloat(batch.total_qty || 0).toFixed(0)}</strong></td>
                <td style="padding: 3px;"><strong>${expiryDate}</strong></td>
                <td style="padding: 3px;">${batch.item_code || ''}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.cost_gst || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.sale_scheme || 0).toFixed(2)}</td>
                <td class="text-center" style="padding: 3px;">
                    <button type="button" class="btn btn-sm btn-success" style="padding: 2px 6px; font-size: 10px;" onclick='selectBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check-circle"></i> Select
                    </button>
                </td>
            </tr>
        `;
    });
    
    const modalHTML = `
        <div class="batch-modal-backdrop" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
        <div class="batch-modal" id="batchModal">
            <div class="batch-modal-content">
                <div class="batch-modal-header">
                    <h5 class="batch-modal-title">Batch Details - ${itemName || ''} | Packing: ${packing || ''}</h5>
                    <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
                </div>
                <div class="batch-modal-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 10px; margin-bottom: 0;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #e9ecef;">
                                <tr>
                                    <th style="width: 60px; padding: 3px;">BATCH</th>
                                    <th style="width: 70px; padding: 3px;">DATE</th>
                                    <th style="width: 50px; padding: 3px;">RATE</th>
                                    <th style="width: 50px; padding: 3px;">P.RATE</th>
                                    <th style="width: 50px; padding: 3px;">MRP</th>
                                    <th style="width: 45px; padding: 3px;">QTY.</th>
                                    <th style="width: 60px; padding: 3px;">EXP.</th>
                                    <th style="width: 50px; padding: 3px;">CODE</th>
                                    <th style="width: 70px; padding: 3px;">Cost+GST</th>
                                    <th style="width: 50px; padding: 3px;">SCM</th>
                                    <th style="width: 60px; padding: 3px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                </div>
                <div class="batch-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('batchModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('batchModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Ensure modal elements exist before adding show class
    setTimeout(() => {
        const backdrop = document.getElementById('batchModalBackdrop');
        const modal = document.getElementById('batchModal');
        if (backdrop) backdrop.classList.add('show');
        if (modal) modal.classList.add('show');
    }, 50);
}

// Close Batch Modal
function closeBatchModal() {
    const modal = document.getElementById('batchModal');
    const backdrop = document.getElementById('batchModalBackdrop');
    
    if (modal) modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    if (backdrop) backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Select Batch and Fill Row
function selectBatch(batch) {
    if (!currentItemForBatch) return;
    
    const rowIndex = currentItemForBatch.rowIndex;
    const row = document.getElementById(`row-${rowIndex}`);
    
    if (row) {
        // Fill expiry date
        const expiryInput = row.querySelector('input[name*="[expiry]"]');
        if (expiryInput && batch.expiry_date) {
            const expiryDate = new Date(batch.expiry_date);
            const formattedExpiry = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
            expiryInput.value = formattedExpiry;
        }
        
        // Fill MRP
        const mrpInput = row.querySelector('input[name*="[mrp]"]');
        if (mrpInput) {
            mrpInput.value = parseFloat(batch.mrp || 0).toFixed(2);
        }
        
        // Store batch data including rates
        row.dataset.batchData = JSON.stringify(batch);
        row.dataset.sRate = batch.s_rate || 0;
        row.dataset.pRate = batch.pur_rate || 0;
        row.dataset.mrp = batch.mrp || 0;
        
        // Get item data from row
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        
        // Don't show HSN details yet - will show when row is completed
        // Just highlight the row as active
        row.style.backgroundColor = '#e7f3ff';
    }
    
    // Close modal
    closeBatchModal();
    
    // Recalculate all amounts
    calculateRowAmount(rowIndex);
    updateAllCalculations();
    
    // Move cursor to expiry field and select it
    setTimeout(() => {
        const expiryInput = row.querySelector('input[name*="[expiry]"]');
        if (expiryInput) {
            expiryInput.focus();
        }
    }, 150);
    
    currentItemForBatch = null;
}

// Move to Next Field
function moveToNextField(rowIndex, nextFieldName) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const nextField = row.querySelector(`[name*="[${nextFieldName}]"]`);
    if (nextField) {
        setTimeout(() => {
            nextField.focus();
        }, 100);
    }
}

// Handle Discount and Add New Row - Called when Enter is pressed on Dis% field
function handleDiscountAndAddRow(rowIndex) {
    console.log('handleDiscountAndAddRow called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found:', rowIndex);
        return;
    }
    
    console.log('Marking row as completed');
    // Mark row as completed
    markRowAsCompleted(rowIndex);
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
    
    // Clear HSN section (will show next row's data when selected)
    clearCalculationSection();
    
    // Remove focus from current field
    document.activeElement.blur();

    // Auto create a new row and focus code field.
    console.log('[KB-BE] Dis% Enter -> addNewRow');
    setTimeout(() => {
        addNewRow();
    }, 80);
}

// Ensure Dis% input has a direct Enter handler (extra safety)
function bindDisEnterHandler(row, rowIndex) {
    if (!row) return;
    const disInput = row.querySelector('input[name*="[dis_percent]"]');
    if (!disInput || disInput.dataset.kbBeBound === '1') return;
    disInput.dataset.kbBeBound = '1';
    disInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;
        console.log('[KB-BE] Dis% Enter (direct bind)', { rowIndex });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        handleDiscountAndAddRow(rowIndex);
    });
}

// Handle Discount Change - Highlights row and triggers calculations
function handleDiscountChange(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
}

// Mark row as completed (green background)
function markRowAsCompleted(rowIndex) {
    console.log('markRowAsCompleted called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found in markRowAsCompleted:', rowIndex);
        return;
    }
    
    console.log('Setting green background');
    // Set green background
    row.style.backgroundColor = '#d4edda';
    row.dataset.completed = 'true';
    
    // Apply to all cells
    const cells = row.querySelectorAll('td');
    cells.forEach(cell => {
        cell.style.backgroundColor = '#d4edda';
    });
    
    console.log('Row marked as completed, background:', row.style.backgroundColor);
}

// Clear calculation section
function clearCalculationSection() {
    document.getElementById('calc_hsn_code').value = '---';
    document.getElementById('calc_cgst_percent').value = '0';
    document.getElementById('calc_sgst_percent').value = '0';
    document.getElementById('calc_cgst_amount').value = '0.00';
    document.getElementById('calc_sgst_amount').value = '0.00';
    document.getElementById('calc_tax_percent').value = '0';
    document.getElementById('calc_srate').value = '0.00';
    document.getElementById('calc_prate').value = '0.00';
    document.getElementById('calc_mrp').value = '0.00';
    document.getElementById('calc_pack').value = '';
    
    // Clear additional details
    document.getElementById('detail_packing').value = '0.00';
    document.getElementById('detail_unit').value = '0.00';
    document.getElementById('detail_cl_qty').value = '0.00';
    document.getElementById('detail_gross').value = '0.00';
    document.getElementById('detail_scm_amt').value = '0.00';
    document.getElementById('detail_dis_amt').value = '0.00';
    document.getElementById('detail_subtotal').value = '0.00';
    document.getElementById('detail_tax_amt').value = '0.00';
    document.getElementById('detail_net_amt').value = '0.00';
}

// Calculate Row Amount
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]').value) || 0;
    const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]').value) || 0;
    const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]').value) || 0;
    
    // Calculate amount
    const grossAmount = qty * mrp;
    const schemeAmount = grossAmount * (scmPercent / 100);
    const afterScheme = grossAmount - schemeAmount;
    const discountAmount = afterScheme * (disPercent / 100);
    const amount = afterScheme - discountAmount;
    
    // Update amount field
    const amountInput = row.querySelector('input[name*="[amount]"]');
    if (amountInput) {
        amountInput.value = amount.toFixed(2);
    }
    
    // Update calculation section and additional details if this row is selected
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    const batchData = JSON.parse(row.dataset.batchData || '{}');
    
    // Check if this row is currently selected (has light blue background)
    if (row.style.backgroundColor === 'rgb(231, 243, 255)' || row.style.backgroundColor === '#e7f3ff') {
        updateAdditionalDetails(row, itemData);
    }
    
    // Recalculate summary totals
    updateAllCalculations();
}

// Select Row for Calculation - Updates calculation section with selected row's data
function selectRowForCalculation(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Check if row is completed (green)
    const isCompleted = row.dataset.completed === 'true';
    
    // Remove highlight from all rows except completed ones
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    allRows.forEach(r => {
        if (r.dataset.completed !== 'true') {
            r.style.backgroundColor = '';
        }
    });
    
    // Highlight selected row (light blue) if not completed
    if (!isCompleted) {
        row.style.backgroundColor = '#e7f3ff';
    }
    
    // Always show HSN details when item data is present
    let itemData = {};
    let batchData = {};

    try {
        const rawItem = row.dataset.itemData;
        if (rawItem && rawItem !== 'undefined') {
            itemData = JSON.parse(rawItem) || {};
        }
    } catch (err) {
        console.warn('[KB-BE] itemData parse failed', err);
        itemData = {};
    }

    try {
        const rawBatch = row.dataset.batchData;
        if (rawBatch && rawBatch !== 'undefined') {
            batchData = JSON.parse(rawBatch) || {};
        }
    } catch (err) {
        console.warn('[KB-BE] batchData parse failed', err);
        batchData = {};
    }

    // If item data missing, try to resolve from itemsData using item_id/code
    const itemId =
        row.dataset.itemId ||
        row.querySelector('input[name*="[item_id]"]')?.value ||
        row.querySelector('input[name*="[code]"]')?.value;

    if ((!itemData || Object.keys(itemData).length === 0) && itemId && Array.isArray(itemsData)) {
        const found = itemsData.find(i => String(i.id) === String(itemId) || String(i.bar_code) === String(itemId));
        if (found) {
            itemData = found;
            row.dataset.itemData = JSON.stringify(found);
        }
    }

    // Fill minimal batch data from row inputs if missing
    if (!batchData || Object.keys(batchData).length === 0) {
        const mrpVal = row.querySelector('input[name*="[mrp]"]')?.value;
        const sRateVal = row.querySelector('input[name*="[s_rate]"]')?.value;
        const pRateVal = row.querySelector('input[name*="[p_rate]"]')?.value;
        batchData = {
            mrp: mrpVal || 0,
            s_rate: sRateVal || 0,
            p_rate: pRateVal || 0
        };
        row.dataset.batchData = JSON.stringify(batchData);
    }

    // Backfill tax/packing from hidden inputs if needed
    if (itemData && Object.keys(itemData).length > 0) {
        const cgstHidden = row.querySelector('input[name*="[cgst_percent]"]')?.value;
        const sgstHidden = row.querySelector('input[name*="[sgst_percent]"]')?.value;
        const packHidden = row.querySelector('input[name*="[packing]"]')?.value;
        if (cgstHidden && !itemData.cgst_percent) itemData.cgst_percent = cgstHidden;
        if (sgstHidden && !itemData.sgst_percent) itemData.sgst_percent = sgstHidden;
        if (packHidden && !itemData.packing) itemData.packing = packHidden;
        row.dataset.itemData = JSON.stringify(itemData);
    }

    const hasItem = itemData && Object.keys(itemData).length > 0;

    if (hasItem) {
        updateCalculationSection(batchData, itemData);
        updateAdditionalDetails(row, itemData);
    } else {
        clearCalculationSection();
    }
}

function getGstPercents(itemData) {
    const cgstRaw = parseFloat(itemData.cgst_percent ?? itemData.cgst ?? 0) || 0;
    const sgstRaw = parseFloat(itemData.sgst_percent ?? itemData.sgst ?? 0) || 0;
    let gstRaw = parseFloat(itemData.gst_percent ?? 0) || 0;

    let cgst = cgstRaw;
    let sgst = sgstRaw;
    let gst = gstRaw;

    if ((!cgst && !sgst) && gst) {
        cgst = gst / 2;
        sgst = gst / 2;
    }

    if (!gst && (cgst || sgst)) {
        gst = cgst + sgst;
    }

    return { gst, cgst, sgst };
}

// Update Calculation Section with Batch and Item Data
function updateCalculationSection(batch, item) {
    console.log('Updating calculation section with:', { batch, item });
    
    // Fill from batch
    const sRate = parseFloat(batch.s_rate ?? batch.sale_rate ?? batch.sRate ?? 0) || 0;
    const pRate = parseFloat(batch.pur_rate ?? batch.p_rate ?? batch.pRate ?? 0) || 0;
    const mrp = parseFloat(batch.mrp ?? batch.MRP ?? 0) || 0;

    document.getElementById('calc_srate').value = sRate.toFixed(2);
    document.getElementById('calc_prate').value = pRate.toFixed(2);
    document.getElementById('calc_mrp').value = mrp.toFixed(2);
    
    // Fill from item
    document.getElementById('calc_hsn_code').value = item.hsn_code || item.hsn || '---';
    document.getElementById('calc_pack').value = item.packing || '';
    
    const { gst, cgst, sgst } = getGstPercents(item);

    console.log('Tax calculations:', { gst, cgst, sgst });

    document.getElementById('calc_cgst_percent').value = cgst.toFixed(2);
    document.getElementById('calc_sgst_percent').value = sgst.toFixed(2);
    document.getElementById('calc_tax_percent').value = gst.toFixed(2);
}

// Update Additional Details Section with selected row's data
function updateAdditionalDetails(row, itemData) {
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0;
    const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
    const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    
    // Calculate individual row values
    const grossAmount = qty * mrp;
    const schemeAmount = grossAmount * (scmPercent / 100);
    const discountAmount = (grossAmount - schemeAmount) * (disPercent / 100);
    const subtotal = grossAmount - schemeAmount - discountAmount;
    
    // Get tax from item data (fallback if calc section is empty)
    const taxFromCalcCgst = parseFloat(document.getElementById('calc_cgst_percent').value) || 0;
    const taxFromCalcSgst = parseFloat(document.getElementById('calc_sgst_percent').value) || 0;
    const calcSum = taxFromCalcCgst + taxFromCalcSgst;
    const { gst, cgst, sgst } = getGstPercents(itemData);
    const cgstPercent = calcSum > 0 ? taxFromCalcCgst : cgst;
    const sgstPercent = calcSum > 0 ? taxFromCalcSgst : sgst;
    const taxPercent = cgstPercent + sgstPercent;
    
    // Calculate CGST and SGST amounts separately
    const cgstAmount = subtotal * (cgstPercent / 100);
    const sgstAmount = subtotal * (sgstPercent / 100);
    const taxAmount = cgstAmount + sgstAmount;
    const netAmount = subtotal + taxAmount;
    
    // Update CGST and SGST amounts in calculation section
    document.getElementById('calc_cgst_amount').value = cgstAmount.toFixed(2);
    document.getElementById('calc_sgst_amount').value = sgstAmount.toFixed(2);

    // Ensure percent fields are populated if they were empty
    if (calcSum === 0) {
        document.getElementById('calc_cgst_percent').value = cgstPercent.toFixed(2);
        document.getElementById('calc_sgst_percent').value = sgstPercent.toFixed(2);
        document.getElementById('calc_tax_percent').value = taxPercent.toFixed(2);
    }
    
    // Get closing quantity from item data (total of all batches)
    const closingQty = parseFloat(itemData.total_qty || 0);
    
    // Update additional details section
    document.getElementById('detail_packing').value = parseFloat(itemData.packing || 0).toFixed(2);
    document.getElementById('detail_unit').value = parseFloat(itemData.unit || 0).toFixed(2);
    document.getElementById('detail_cl_qty').value = closingQty.toFixed(2);
    document.getElementById('detail_gross').value = grossAmount.toFixed(2);
    document.getElementById('detail_scm_amt').value = schemeAmount.toFixed(2);
    document.getElementById('detail_dis_amt').value = discountAmount.toFixed(2);
    document.getElementById('detail_subtotal').value = subtotal.toFixed(2);
    document.getElementById('detail_tax_amt').value = taxAmount.toFixed(2);
    document.getElementById('detail_net_amt').value = netAmount.toFixed(2);
}

// Update All Calculations - Updates SUMMARY section with totals from all rows
function updateAllCalculations() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let totalMrpValue = 0;
    let totalGross = 0;
    let totalDiscount = 0;
    let totalScheme = 0;
    let totalQty = 0;
    let totalTaxAmount = 0;
    
    // Calculate totals from all rows
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0;
        const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
        const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
        
        totalQty += qty;
        
        // Calculate for this row
        const grossAmount = qty * mrp;
        const schemeAmount = grossAmount * (scmPercent / 100);
        const discountAmount = (grossAmount - schemeAmount) * (disPercent / 100);
        const subtotal = grossAmount - schemeAmount - discountAmount;
        
        // Get tax percentage from item data
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        const { gst } = getGstPercents(itemData);
        const taxAmount = subtotal * (gst / 100);
        
        totalMrpValue += grossAmount;
        totalGross += subtotal;
        totalScheme += schemeAmount;
        totalDiscount += discountAmount;
        totalTaxAmount += taxAmount;
    });
    
    const totalNet = totalGross + totalTaxAmount;
    
    // Update SUMMARY section only (cumulative totals)
    document.getElementById('summary_mrp_value').value = totalMrpValue.toFixed(2);
    document.getElementById('summary_gross').value = totalGross.toFixed(2);
    document.getElementById('summary_discount').value = totalDiscount.toFixed(2);
    document.getElementById('summary_scheme').value = totalScheme.toFixed(2);
    document.getElementById('summary_tax').value = totalTaxAmount.toFixed(2);
    document.getElementById('summary_net').value = totalNet.toFixed(2);
    
    // Update payable amount in calculation section
    document.getElementById('calc_payable_amount').value = totalNet.toFixed(2);
}

// Remove Row
function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        updateAllCalculations();
    }
}

// Add New Row - create empty row and focus code field
function addNewRow() {
    // Check if customer is selected
    const customerId = document.querySelector('select[name="customer_id"]')?.value;
    
    if (!customerId) {
        showAlert('error', 'Please select a customer first.');
        return;
    }

    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = getNextRowIndex();

    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value=""
                   data-custom-enter="true"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); openItemSelectionModal(); return false; }"
                   placeholder="">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="" readonly>
        </td>
        <td>
            <select class="form-control form-control-sm no-select2" name="items[${rowIndex}][br_ex]"
                    onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'qty'); return false; }">
                <option value="B">B</option>
                <option value="E">E</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="0" step="1"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'f_qty'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][f_qty]" value="0" step="1"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" value="0.00" step="0.01"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'scm_percent'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][scm_percent]" value="0.00" step="0.01"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][dis_percent]" value="0.00" step="0.01"
                   onchange="handleDiscountChange(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndAddRow(${rowIndex}); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);

    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });

    bindDisEnterHandler(row, rowIndex);

    setTimeout(() => {
        const codeInput = row.querySelector('input[name*="[code]"]');
        if (codeInput) {
            codeInput.focus();
        }
    }, 80);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Breakage/Expiry Transaction page loaded');

    // Ensure Select2 is not applied to custom dropdowns or Br/Ex selects
    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        const disableSelect2 = (el) => {
            const $el = jQuery(el);
            if ($el.data('select2')) {
                $el.select2('destroy');
            }
        };
        document.querySelectorAll('select.no-select2, select[name*="[br_ex]"]').forEach(disableSelect2);
    }
    
    // Handle form submission
    document.getElementById('breakageExpiryTransactionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveTransaction();
    });

    // When adjustment modal is open, ESC should close it and Ctrl+S should submit it.
    document.addEventListener('keydown', function(e) {
        const modalOpen = !!document.getElementById('adjustmentModal') && document.getElementById('adjustmentModal').classList.contains('show');
        if (!modalOpen) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeAdjustmentModal();
            return;
        }

        const isCtrlS = (e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey);
        if (isCtrlS) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            saveAdjustment();
            return;
        }
    }, true);

    // Prevent Enter from bubbling out of native selects (so it selects option, not next field)
    document.addEventListener('keydown', function(e) {
        const el = document.activeElement;
        if (!el || el.tagName !== 'SELECT') return;
        if (e.key === 'Enter') {
            e.stopPropagation();
        }
    }, true);

    // Ctrl+S / Cmd+S -> trigger Save flow
    document.addEventListener('keydown', function(e) {
        const isSaveShortcut = (e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S');
        if (!isSaveShortcut) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const saveBtn = document.getElementById('saveTransactionBtn');
        if (saveBtn) {
            saveBtn.click();
        } else {
            saveTransaction();
        }
    }, true);
});

// Save Transaction
function saveTransaction() {
    // Validate first
    const customerId = document.querySelector('select[name="customer_id"]')?.value;
    if (!customerId) {
        showAlert('error', 'Please select a customer');
        return;
    }
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let hasItems = false;
    rows.forEach(row => {
        const code = row.querySelector('input[name*="[code]"]')?.value;
        const name = row.querySelector('input[name*="[name]"]')?.value;
        if (code && name) hasItems = true;
    });
    
    if (!hasItems) {
        showAlert('error', 'Please add at least one item');
        return;
    }
    
    // Show Credit Note option modal
    showCreditNoteModal();
}

// Show Credit Note Modal
function showCreditNoteModal() {
    const modalHTML = `
        <div class="credit-note-modal-backdrop" id="creditNoteModalBackdrop"></div>
        <div class="credit-note-modal" id="creditNoteModal">
            <div class="credit-note-modal-content">
                <div class="credit-note-modal-header">
                    <h5 class="credit-note-modal-title">Save Transaction</h5>
                    <button type="button" class="credit-note-close-btn" onclick="closeCreditNoteModal()">&times;</button>
                </div>
                <div class="credit-note-modal-body">
                    <p>How would you like to save this transaction?</p>
                    <div class="credit-note-options">
                        <button type="button" class="btn btn-secondary" onclick="saveWithoutCreditNote()">
                            Save Without Credit Note
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveWithCreditNote()">
                            Save With Credit Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('creditNoteModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('creditNoteModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('creditNoteModalBackdrop').classList.add('show');
        document.getElementById('creditNoteModal').classList.add('show');
        initCreditNoteModalKeyboard();
    }, 10);
    
}

// Close Credit Note Modal
function closeCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    const backdrop = document.getElementById('creditNoteModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'bounceOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        
        setTimeout(() => {
            modal.remove();
            backdrop.remove();
        }, 300);
    }
    
    teardownCreditNoteModalKeyboard();
}

// Save Without Credit Note
function saveWithoutCreditNote() {
    closeCreditNoteModal();
    submitTransaction(false);
}

// Save With Credit Note
function saveWithCreditNote() {
    closeCreditNoteModal();
    
    // Get customer ID and Payable Amount (net amount after all calculations)
    const customerId = document.querySelector('select[name="customer_id"]')?.value;
    const payableAmount = parseFloat(document.getElementById('calc_payable_amount').value || 0);
    
    if (payableAmount <= 0) {
        showAlert('error', 'MRP value must be greater than 0 for credit note adjustment');
        return;
    }
    
    // Fetch customer's past sales for adjustment
    fetchCustomerSales(customerId, payableAmount);
}

// Credit Note Modal Keyboard Navigation
function initCreditNoteModalKeyboard() {
    if (window.__kbBeCreditNoteKeyBound) return;

    function getButtons() {
        const modal = document.getElementById('creditNoteModal');
        if (!modal) return [];
        return Array.from(modal.querySelectorAll('.credit-note-options button'));
    }

    function setActive(index) {
        const buttons = getButtons();
        buttons.forEach(btn => btn.classList.remove('kb-active'));
        if (buttons[index]) {
            buttons[index].classList.add('kb-active');
            buttons[index].focus();
        }
    }

    window.__kbBeCreditNoteIndex = 0;
    setActive(window.__kbBeCreditNoteIndex);

    window.__kbBeCreditNoteKeyHandler = function(e) {
        const modal = document.getElementById('creditNoteModal');
        if (!modal || !modal.classList.contains('show')) return;

        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const buttons = getButtons();
            if (buttons.length === 0) return;
            if (e.key === 'ArrowRight') {
                window.__kbBeCreditNoteIndex = (window.__kbBeCreditNoteIndex + 1) % buttons.length;
            } else {
                window.__kbBeCreditNoteIndex = (window.__kbBeCreditNoteIndex - 1 + buttons.length) % buttons.length;
            }
            setActive(window.__kbBeCreditNoteIndex);
        } else if (e.key === 'Enter') {
            const buttons = getButtons();
            if (buttons[window.__kbBeCreditNoteIndex]) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                buttons[window.__kbBeCreditNoteIndex].click();
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeCreditNoteModal();
        }
    };

    document.addEventListener('keydown', window.__kbBeCreditNoteKeyHandler, true);
    window.__kbBeCreditNoteKeyBound = true;
}

function teardownCreditNoteModalKeyboard() {
    if (window.__kbBeCreditNoteKeyBound && window.__kbBeCreditNoteKeyHandler) {
        document.removeEventListener('keydown', window.__kbBeCreditNoteKeyHandler, true);
    }
    window.__kbBeCreditNoteKeyBound = false;
    window.__kbBeCreditNoteKeyHandler = null;
}

// Submit Transaction (actual save)
function submitTransaction(withCreditNote = false, adjustments = []) {
    const form = document.getElementById('breakageExpiryTransactionForm');
    const formData = new FormData(form);
    
    // Get all form data
    const data = {
        _token: formData.get('_token'),
        series: formData.get('series'),
        transaction_date: formData.get('transaction_date'),
        end_date: formData.get('end_date'),
        customer_id: formData.get('customer_id'),
        salesman_id: formData.get('salesman_id'),
        gst_vno: formData.get('gst_vno'),
        note_type: formData.get('note_type'),
        with_gst: formData.get('with_gst'),
        inc: formData.get('inc'),
        rev_charge: formData.get('rev_charge'),
        adjusted: formData.get('adjusted'),
        dis_rpl: formData.get('dis_rpl'),
        brk: formData.get('brk'),
        exp: formData.get('exp'),
        
        // Summary values
        summary_mrp_value: document.getElementById('summary_mrp_value').value,
        summary_gross: document.getElementById('summary_gross').value,
        summary_discount: document.getElementById('summary_discount').value,
        summary_scheme: document.getElementById('summary_scheme').value,
        summary_tax: document.getElementById('summary_tax').value,
        summary_net: document.getElementById('summary_net').value,
        
        // Detail values
        detail_packing: document.getElementById('detail_packing').value,
        detail_unit: document.getElementById('detail_unit').value,
        detail_cl_qty: document.getElementById('detail_cl_qty').value,
        detail_scm_amt: document.getElementById('detail_scm_amt').value,
        detail_dis_amt: document.getElementById('detail_dis_amt').value,
        detail_subtotal: document.getElementById('detail_subtotal').value,
        detail_tax_amt: document.getElementById('detail_tax_amt').value,
        detail_net_amt: document.getElementById('detail_net_amt').value,
        
        // Credit note data
        with_credit_note: withCreditNote,
        adjustments: adjustments,
        
        // Items
        items: []
    };
    
    // Get all items from table
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        const item = {
            code: row.querySelector('input[name*="[code]"]')?.value || '',
            name: row.querySelector('input[name*="[name]"]')?.value || '',
            batch: row.querySelector('input[name*="[batch]"]')?.value || '',
            expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
            br_ex: row.querySelector('select[name*="[br_ex]"]')?.value || '',
            qty: row.querySelector('input[name*="[qty]"]')?.value || 0,
            f_qty: row.querySelector('input[name*="[f_qty]"]')?.value || 0,
            mrp: row.querySelector('input[name*="[mrp]"]')?.value || 0,
            scm_percent: row.querySelector('input[name*="[scm_percent]"]')?.value || 0,
            dis_percent: row.querySelector('input[name*="[dis_percent]"]')?.value || 0,
            amount: row.querySelector('input[name*="[amount]"]')?.value || 0
        };
        
        // Only add if item has code and name
        if (item.code && item.name) {
            data.items.push(item);
        }
    });
    
    // Show loading
    showAlert('info', 'Saving transaction...');
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    // Submit
    fetch('<?php echo e(route("admin.breakage-expiry.transaction.store")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Transaction saved successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message || 'Error saving transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error saving transaction: ' + error.message);
    });
}



// Open Create Batch Modal
function openCreateBatchModal() {
    // Close the batch not exist modal first
    closeBatchNotExistModal();
    
    if (!currentItemForBatch) return;
    
    const { itemId, batchNo, rowIndex, itemName, packing } = currentItemForBatch;
    const row = document.getElementById(`row-${rowIndex}`);
    
    if (!row) return;
    
    // Get item data from the row
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    
    // Fetch item details to pre-populate the form
    fetch(`<?php echo e(url('/admin/items')); ?>/${itemId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const item = data.item;
                
                // Pre-populate the form
                document.getElementById('batchItemName').value = item.name || itemName || '';
                document.getElementById('batchPack').value = item.packing || packing || '';
                document.getElementById('batchMrp').value = parseFloat(item.mrp || 0).toFixed(2);
                document.getElementById('batchSRate').value = parseFloat(item.s_rate || 0).toFixed(2);
                document.getElementById('batchPRate').value = parseFloat(item.pur_rate || 0).toFixed(2);
                document.getElementById('batchExpiry').value = '';
                
                // Store hidden values
                document.getElementById('batchItemId').value = itemId;
                document.getElementById('batchNumber').value = batchNo;
                document.getElementById('batchRowIndex').value = rowIndex;
                
                // Show the create batch modal
                setTimeout(() => {
                    const backdrop = document.getElementById('createBatchModalBackdrop');
                    const modal = document.getElementById('createBatchModal');
                    
                    if (backdrop && modal) {
                        backdrop.style.display = 'block';
                        modal.style.display = 'block';
                        
                        setTimeout(() => {
                            backdrop.classList.add('show');
                            modal.classList.add('show');
                        }, 10);
                    }
                }, 10);
            } else {
                showAlert('error', 'Error loading item details');
            }
        })
        .catch(error => {
            console.error('Error loading item details:', error);
            showAlert('error', 'Error loading item details: ' + error.message);
        });
}

// Save Batch
function saveBatch() {
    const form = document.getElementById('createBatchForm');
    
    const batchData = {
        item_id: document.getElementById('batchItemId').value,
        batch_no: document.getElementById('batchNumber').value,
        mrp: parseFloat(document.getElementById('batchMrp').value) || 0,
        s_rate: parseFloat(document.getElementById('batchSRate').value) || 0,
        pur_rate: parseFloat(document.getElementById('batchPRate').value) || 0,
        expiry_date: document.getElementById('batchExpiry').value || null,
        total_qty: 0 // Default quantity for new batch
    };
    
    // Validate required fields
    if (!batchData.mrp || !batchData.s_rate || !batchData.pur_rate) {
        showAlert('error', 'Please fill all required fields (MRP, S.Rate, P.Rate)');
        return;
    }
    
    // Validate item_id and batch_no
    if (!batchData.item_id || !batchData.batch_no) {
        showAlert('error', 'Missing item or batch information');
        return;
    }
    
    // Save batch to database
    fetch(`<?php echo e(route('admin.batches.store')); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(batchData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Batch data sent:', batchData);
        if (!response.ok) {
            // Clone the response to read it as text for error logging
            return response.text().then(text => {
                console.error('=== BATCH CREATION ERROR ===');
                console.error('Status:', response.status);
                console.error('Status Text:', response.statusText);
                console.error('Response Body:', text);
                try {
                    const jsonError = JSON.parse(text);
                    console.error('Parsed Error:', jsonError);
                    throw new Error(jsonError.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Batch creation response:', data);
        if (data.success) {
            // Close the modal
            closeCreateBatchModal();
            
            // Reopen the batch selection modal for the newly created batch
            const rowIndex = document.getElementById('batchRowIndex').value;
            const itemId = document.getElementById('batchItemId').value;
            const batchNo = batchData.batch_no;
            
            // Use checkBatchExists to fetch the batch list (which will now include the new batch)
            // and open the "Batch Details" modal
            checkBatchExists(rowIndex, itemId, batchNo);
            
            showAlert('success', 'Batch created successfully');
            // currentItemForBatch is NOT cleared here, so it persists for the selectBatch call
        } else {
            console.error('Batch creation failed:', data);
            showAlert('error', data.message || 'Error creating batch');
        }
    })
    .catch(error => {
        console.error('=== CATCH BLOCK ERROR ===');
        console.error('Error creating batch:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        showAlert('error', 'Error creating batch: ' + error.message);
    });
}

// Show Batch Not Exist Modal
function showBatchNotExistModal() {
    setTimeout(() => {
        const backdrop = document.getElementById('batchNotExistModalBackdrop');
        const modal = document.getElementById('batchNotExistModal');
        
        if (backdrop && modal) {
            backdrop.style.display = 'block';
            modal.style.display = 'block';
            
            setTimeout(() => {
                backdrop.classList.add('show');
                modal.classList.add('show');
            }, 10);
        }
    }, 10);
}

// Close Batch Not Exist Modal
function closeBatchNotExistModal() {
    const modal = document.getElementById('batchNotExistModal');
    const backdrop = document.getElementById('batchNotExistModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.style.display = 'none';
        }
    }, 300);
}

// Close Create Batch Modal
function closeCreateBatchModal() {
    const modal = document.getElementById('createBatchModal');
    const backdrop = document.getElementById('createBatchModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.style.display = 'none';
        }
    }, 300);
}

// Show alert function (like sale return)
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertTypes = {
        'success': { bg: '#28a745', icon: '✓' },
        'error': { bg: '#dc3545', icon: '✕' },
        'info': { bg: '#17a2b8', icon: 'ℹ' },
        'warning': { bg: '#ffc107', icon: '⚠' }
    };
    
    const alertConfig = alertTypes[type] || alertTypes['info'];
    
    const alertHTML = `
        <div class="custom-alert" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${alertConfig.bg};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; font-weight: bold;">${alertConfig.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
}

// Fetch Customer Sales for Adjustment
function fetchCustomerSales(customerId, netAmount) {
    showAlert('info', 'Loading customer sales...');
    
    fetch(`<?php echo e(url('/admin/customers')); ?>/${customerId}/sales`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.sales.length > 0) {
            showAdjustmentModal(data.sales, netAmount);
        } else {
            // No sales found, save without adjustment
            if (confirm('No outstanding sales found for this customer. Save transaction without credit note adjustment?')) {
                submitTransaction(true, []);
            }
        }
    })
    .catch(error => {
        console.error('Error fetching customer sales:', error);
        showAlert('error', 'Error loading customer sales. Saving without adjustment.');
        submitTransaction(false, []);
    });
}

// Show Adjustment Modal (similar to Sale Return)
function showAdjustmentModal(sales, netAmount) {
    window.netAmount = netAmount; // Store for calculations
    
    // Check if we're editing an existing transaction
    const transactionId = window.currentTransactionId || null;
    
    // Function to render modal with optional pre-filled adjustments
    const renderModal = (existingAdjustments = {}) => {
    const modalHTML = `
        <div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop"></div>
        <div class="adjustment-modal" id="adjustmentModal">
            <div class="adjustment-modal-content">
                <div class="adjustment-modal-header">
                    <h5 class="adjustment-modal-title">Credit Note Adjustment</h5>
                </div>
                <div class="adjustment-modal-body">
                    <div style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered" style="font-size: 11px; margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                                <tr>
                                    <th style="text-align: center;">SR.NO.</th>
                                    <th style="text-align: center;">TRANS NO.</th>
                                    <th style="text-align: center;">DATE</th>
                                    <th style="text-align: center;">BILL AMT.</th>
                                    <th style="text-align: center;">ADJUST AMT.</th>
                                    <th style="text-align: center;">REMAINING BAL.</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${sales.map((sale, index) => {
                                    // Use bill_amount which is actually net_amount from backend
                                    const netAmount = parseFloat(sale.bill_amount || 0);
                                    const currentBalance = parseFloat(sale.balance || sale.bill_amount || 0);
                                    const preFilledAmount = parseFloat(existingAdjustments[sale.id] || 0);
                                    
                                    // Calculate original bill amount: current balance + previous adjustment
                                    const originalBillAmount = currentBalance + preFilledAmount;

                                    return `
                                    <tr>
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td style="text-align: center;">${sale.trans_no}</td>
                                        <td style="text-align: center;">${sale.date}</td>
                                        <td style="text-align: right; font-weight: bold; color: #0d6efd;">${originalBillAmount.toFixed(2)}</td>
                                        <td style="text-align: center;">
                                            <input type="number" class="form-control form-control-sm adjustment-input" 
                                                   id="adj_${sale.id}" 
                                                   data-sale-id="${sale.id}"
                                                   data-balance="${originalBillAmount}"
                                                   data-bill-amount="${originalBillAmount}"
                                                   max="${originalBillAmount}"
                                                   step="0.01" 
                                                   style="width: 90px; font-size: 10px;"
                                                   value="${preFilledAmount > 0 ? preFilledAmount.toFixed(2) : ''}"
                                                   placeholder="0.00">
                                        </td>
                                        <td style="text-align: right; font-weight: bold;" id="remaining_${sale.id}">
                                            <span style="color: #28a745;">${currentBalance.toFixed(2)}</span>
                                        </td>
                                    </tr>
                                `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" style="background: #f8f9fa; padding: 10px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: bold; color: #dc3545;">EXIT : &lt;ESC &gt;</span>
                            <span style="font-weight: bold; font-size: 16px; color: #0d6efd;">
                                ADJUST AMOUNT (Rs) : <span id="adjustmentBalance">${netAmount.toFixed(2)}</span>
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <label style="font-weight: bold; color: #495057;">Auto Adjust Amount:</label>
                            <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" 
                                   style="width: 120px;" step="0.01" placeholder="Enter amount"
                                   onchange="autoDistributeAmount()">
                            <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                                <i class="bi bi-magic me-1"></i>Auto Distribute
                            </button>
                        </div>
                        <div class="mt-2" style="text-align: right;">
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="saveAdjustment()">
                                <i class="bi bi-check-circle me-1"></i>Save & Submit
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('adjustmentModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('adjustmentModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add event listeners using event delegation for adjustment inputs
    const adjustmentModal = document.getElementById('adjustmentModal');
    if (adjustmentModal) {
        // Event delegation for all adjustment inputs
        adjustmentModal.addEventListener('input', function(e) {
            if (e.target.classList.contains('adjustment-input')) {
                const saleId = e.target.getAttribute('data-sale-id');
                console.log('Input event triggered for sale ID:', saleId);
                updateRemainingBalance(saleId);
            }
        });
        
        adjustmentModal.addEventListener('change', function(e) {
            if (e.target.classList.contains('adjustment-input')) {
                const saleId = e.target.getAttribute('data-sale-id');
                console.log('Change event triggered for sale ID:', saleId);
                updateRemainingBalance(saleId);
            }
        });
    }
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('adjustmentModalBackdrop').classList.add('show');
        document.getElementById('adjustmentModal').classList.add('show');
        
        // Update remaining balance if pre-filled
        if (Object.keys(existingAdjustments).length > 0) {
            // Trigger update for all pre-filled inputs
            Object.keys(existingAdjustments).forEach(saleId => {
                updateRemainingBalance(saleId);
            });
        } else {
            prefillAdjustmentFirstRow();
        }

        initAdjustmentModalKeyboard();
        if (!window.__kbBeAdjustmentEscBound) {
            document.addEventListener('keydown', handleAdjustmentEsc, true);
            window.__kbBeAdjustmentEscBound = true;
        }
    }, 10);
    
    };
    
    // If editing existing transaction, fetch existing adjustments
    if (transactionId) {
        fetch(`<?php echo e(url('/admin/breakage-expiry/transaction')); ?>/${transactionId}/adjustments`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.adjustments) {
                // Create a map of sale_transaction_id => adjusted_amount
                const adjustmentsMap = {};
                data.adjustments.forEach(adj => {
                    adjustmentsMap[adj.sale_transaction_id] = adj.adjusted_amount;
                });
                renderModal(adjustmentsMap);
            } else {
                renderModal();
            }
        })
        .catch(error => {
            console.error('Error fetching existing adjustments:', error);
            renderModal(); // Show modal anyway without pre-filled data
        });
    } else {
        // New transaction, no pre-filled data
        renderModal();
    }
}

// Auto-fill first row with adjustment amount
function autoFillFirstRow(netAmount, sales) {
    if (sales && sales.length > 0) {
        const firstSale = sales[0];
        const firstInput = document.getElementById(`adj_${firstSale.id}`);
        const firstBalance = parseFloat(firstSale.balance || firstSale.bill_amount || 0);
        
        if (firstInput) {
            // If net amount is less than first row balance, fill exact amount
            // Otherwise fill the balance and cascade will handle rest
            const amountToFill = Math.min(netAmount, firstBalance);
            firstInput.value = amountToFill.toFixed(2);
            updateRemainingBalance(firstSale.id);
            
            // If there's remaining amount, cascade to next rows
            if (netAmount > firstBalance) {
                cascadeRemainingAmount(netAmount - firstBalance, 1, sales);
            }
            
            // Focus on first input
            firstInput.focus();
        }
    }
}

function prefillAdjustmentFirstRow() {
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    if (!inputs.length) return;

    const anyValue = inputs.some(input => parseFloat(input.value || 0) > 0);
    if (anyValue) return;

    const firstInput = inputs[0];
    const balance = parseFloat(firstInput.getAttribute('data-balance') || 0);
    const desired = parseFloat(window.netAmount || 0);
    const prefill = Math.min(desired, balance);

    if (prefill > 0) {
        firstInput.value = prefill.toFixed(2);
    }
    firstInput.placeholder = desired.toFixed(2);
    updateAdjustmentBalance();
}

function getRemainingAdjustment() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    inputs.forEach(input => {
        totalAdjusted += parseFloat(input.value || 0);
    });
    return Math.max(0, (parseFloat(window.netAmount || 0) - totalAdjusted));
}

function initAdjustmentModalKeyboard() {
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    if (!inputs.length) return;

    const setActive = (input) => {
        inputs.forEach(i => i.classList.remove('kb-active'));
        if (input) {
            input.classList.add('kb-active');
            input.focus();
        }
    };

    inputs.forEach((input, idx) => {
        input.addEventListener('focus', () => setActive(input));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = inputs[idx + 1];
                if (next) {
                    const currentValue = parseFloat(input.value || 0);
                    const remaining = getRemainingAdjustment();
                    const nextBalance = parseFloat(next.getAttribute('data-balance') || 0);

                    if (remaining <= 0 && currentValue > 0) {
                        const moveValue = Math.min(currentValue, nextBalance);
                        next.value = moveValue.toFixed(2);
                        input.value = '0.00';
                    } else {
                        const nextValue = Math.min(remaining, nextBalance);
                        next.value = nextValue.toFixed(2);
                    }

                    updateAdjustmentBalance();
                    setActive(next);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = inputs[idx - 1];
                if (prev) {
                    const currentValue = parseFloat(input.value || 0);
                    const prevBalance = parseFloat(prev.getAttribute('data-balance') || 0);

                    if (currentValue > 0) {
                        const moveValue = Math.min(currentValue, prevBalance);
                        prev.value = moveValue.toFixed(2);
                        input.value = '0.00';
                        updateAdjustmentBalance();
                    }

                    setActive(prev);
                }
            }
        });
    });

    setActive(inputs[0]);
}

function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeAdjustmentModal();
        return;
    }
    const isCtrlS = (e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey);
    if (isCtrlS) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveAdjustment();
    }
}

// Handle Enter key to cascade remaining amount
function handleAdjustmentEnter(event, saleId, currentIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        
        const currentInput = document.getElementById(`adj_${saleId}`);
        const currentValue = parseFloat(currentInput.value || 0);
        const currentBalance = parseFloat(currentInput.dataset.balance || 0);
        
        // Get all adjustment inputs
        const allInputs = document.querySelectorAll('.adjustment-input');
        const salesData = Array.from(allInputs).map(input => ({
            id: input.dataset.saleId,
            balance: parseFloat(input.dataset.balance || 0)
        }));
        
        // If current value exceeds balance, cascade the excess
        if (currentValue > currentBalance) {
            const excess = currentValue - currentBalance;
            currentInput.value = currentBalance.toFixed(2);
            updateRemainingBalance(saleId);
            
            // Cascade excess to next row
            cascadeRemainingAmount(excess, currentIndex + 1, salesData);
        }
        
        // Move focus to next row
        if (currentIndex + 1 < allInputs.length) {
            allInputs[currentIndex + 1].focus();
        }
    }
}

// Cascade remaining amount to next rows
function cascadeRemainingAmount(remainingAmount, startIndex, salesData) {
    const allInputs = document.querySelectorAll('.adjustment-input');
    
    let amountLeft = remainingAmount;
    for (let i = startIndex; i < allInputs.length && amountLeft > 0; i++) {
        const input = allInputs[i];
        const balance = parseFloat(input.dataset.balance || 0);
        const saleId = input.dataset.saleId;
        
        const amountToFill = Math.min(amountLeft, balance);
        input.value = amountToFill.toFixed(2);
        updateRemainingBalance(saleId);
        
        amountLeft -= amountToFill;
    }
}

// Auto Distribute Amount Across Transactions
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        showAlert('warning', 'Please enter a valid amount to distribute');
        return;
    }
    
    // Clear all existing adjustments first
    document.querySelectorAll('.adjustment-input').forEach(input => {
        input.value = '';
    });
    
    // Get all transactions sorted by balance (highest first for better distribution)
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    const transactions = inputs.map(input => ({
        input: input,
        saleId: input.getAttribute('data-sale-id'),
        balance: parseFloat(input.getAttribute('data-balance'))
    })).filter(t => t.balance > 0).sort((a, b) => b.balance - a.balance);
    
    let remainingAmount = totalAmount;
    
    // Distribute amount across transactions
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        
        const adjustAmount = Math.min(remainingAmount, transaction.balance);
        transaction.input.value = adjustAmount.toFixed(2);
        remainingAmount -= adjustAmount;
    });
    
    // If still amount remaining, show warning
    if (remainingAmount > 0) {
        showAlert('warning', `₹${remainingAmount.toFixed(2)} could not be distributed. Insufficient outstanding balance.`);
    } else {
        showAlert('success', `₹${totalAmount.toFixed(2)} distributed successfully across ${transactions.length} transaction(s)`);
    }
    
    // Update the balance display
    updateAdjustmentBalance();
}

// Update Remaining Balance for Individual Row
function updateRemainingBalance(saleId) {
    // Convert to string for consistent ID matching
    const id = String(saleId);
    const input = document.getElementById(`adj_${id}`);
    const remainingCell = document.getElementById(`remaining_${id}`);
    
    console.log('updateRemainingBalance called for ID:', id);
    console.log('Input element:', input);
    console.log('Remaining cell:', remainingCell);
    
    if (!input) {
        console.error('Input not found for ID:', id);
        return;
    }
    if (!remainingCell) {
        console.error('Remaining cell not found for ID:', id);
        return;
    }
    
    const currentBalance = parseFloat(input.getAttribute('data-balance') || 0);
    const adjustedAmount = parseFloat(input.value || 0);
    
    console.log('Current balance:', currentBalance, 'Adjusted amount:', adjustedAmount);
    
    // Validate adjustment amount
    if (adjustedAmount > currentBalance) {
        input.value = currentBalance.toFixed(2);
        showAlert('warning', 'Adjustment amount cannot exceed current balance');
        return;
    }
    
    const remainingBalance = currentBalance - adjustedAmount;
    console.log('New remaining balance:', remainingBalance);
    
    // Update remaining balance with color coding
    let color = '#28a745'; // Green for positive
    if (remainingBalance === 0) {
        color = '#6c757d'; // Gray for zero
    } else if (remainingBalance < currentBalance * 0.3) {
        color = '#ffc107'; // Yellow for low balance
    }
    
    remainingCell.innerHTML = `<span style="color: ${color};">${remainingBalance.toFixed(2)}</span>`;
    
    // Also update total adjustment balance
    updateAdjustmentBalance();
}

// Update adjustment balance
function updateAdjustmentBalance() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        const adjustedAmount = parseFloat(input.value || 0);
        const saleId = input.getAttribute('data-sale-id');
        const originalBalance = parseFloat(input.getAttribute('data-balance') || 0);
        
        totalAdjusted += adjustedAmount;
        
        // Update remaining balance cell (use 'remaining_' not 'balance_')
        const newBalance = originalBalance - adjustedAmount;
        const remainingCell = document.getElementById(`remaining_${saleId}`);
        
        if (remainingCell) {
            // Color code the balance based on value
            let color = '#28a745'; // Green for positive
            if (newBalance <= 0) {
                color = '#6c757d'; // Gray for zero
            } else if (newBalance < originalBalance * 0.3) {
                color = '#ffc107'; // Yellow for low balance
            }
            remainingCell.innerHTML = `<span style="color: ${color}; font-weight: bold;">${newBalance.toFixed(2)}</span>`;
        }
    });
    
    // Update remaining adjustment balance at the bottom
    const adjustmentBalanceEl = document.getElementById('adjustmentBalance');
    if (adjustmentBalanceEl && typeof window.netAmount !== 'undefined') {
        const remainingBalance = window.netAmount - totalAdjusted;
        adjustmentBalanceEl.textContent = remainingBalance.toFixed(2);
        
        // Change color based on remaining balance
        const balanceSpan = adjustmentBalanceEl.parentElement;
        if (balanceSpan) {
            if (remainingBalance < 0) {
                balanceSpan.style.color = '#dc3545'; // Red for over-adjusted
            } else if (remainingBalance === 0) {
                balanceSpan.style.color = '#28a745'; // Green for fully adjusted
            } else {
                balanceSpan.style.color = '#0d6efd'; // Blue for partial
            }
        }
    }
    
    console.log('Total adjusted:', totalAdjusted, 'Net amount:', window.netAmount);
}

// Close Adjustment Modal
function closeAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    const backdrop = document.getElementById('adjustmentModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'bounceOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        
        setTimeout(() => {
            modal.remove();
            backdrop.remove();
        }, 300);
    }

    if (window.__kbBeAdjustmentEscBound) {
        document.removeEventListener('keydown', handleAdjustmentEsc, true);
        window.__kbBeAdjustmentEscBound = false;
    }
    
}

// Save Adjustment and Submit Form
function saveAdjustment() {
    console.log('=== saveAdjustment called ===');
    
    // Collect adjustment data
    const inputs = document.querySelectorAll('.adjustment-input');
    const adjustments = [];
    
    console.log('Found adjustment inputs:', inputs.length);
    
    inputs.forEach(input => {
        const adjusted = parseFloat(input.value || 0);
        const saleId = input.getAttribute('data-sale-id');
        console.log(`Input for sale ${saleId}: value = ${adjusted}`);
        
        if (adjusted > 0) {
            adjustments.push({
                sale_id: saleId,
                adjusted_amount: adjusted
            });
        }
    });
    
    console.log('Collected adjustments:', JSON.stringify(adjustments));
    
    // Check if fully adjusted
    const remainingBalance = parseFloat(document.getElementById('adjustmentBalance').textContent);
    console.log('Remaining balance:', remainingBalance);
    
    if (remainingBalance != 0) {
        if (!confirm(`Balance remaining is Rs ${remainingBalance.toFixed(2)}. Do you want to continue?`)) {
            return;
        }
    }
    
    // Close modal and submit
    closeAdjustmentModal();
    
    console.log('Calling submitTransaction with withCreditNote=true, adjustments:', adjustments);
    submitTransaction(true, adjustments);
}

// ==========================================
// CUSTOM DROPDOWN AND KEYBOARD NAVIGATION
// ==========================================

// Customer Dropdown State
let customerDropdownIndex = -1;

// Salesman Dropdown State
let salesmanDropdownIndex = -1;

// Guard to prevent Enter handlers from other scopes from hijacking dropdown selection
let kbBeDropdownSelecting = false;

function setKbBeDropdownSelecting(value) {
    kbBeDropdownSelecting = value;
    if (value) {
        setTimeout(() => {
            kbBeDropdownSelecting = false;
        }, 180);
    }
}

function isKbBeDropdownOpen(dropdownEl) {
    if (!dropdownEl) return false;
    const computed = window.getComputedStyle(dropdownEl);
    return computed.display !== 'none' && computed.visibility !== 'hidden';
}

function getKbBePreferredDropdownItem(listContainer, index) {
    if (!listContainer) return null;

    const allItems = Array.from(listContainer.querySelectorAll('.dropdown-item'));
    const visibleItems = allItems.filter(item => item.style.display !== 'none');
    if (visibleItems.length === 0) return null;

    // Prefer actively highlighted row
    const activeItem = visibleItems.find(item => item.classList.contains('active'));
    if (activeItem) return activeItem;

    // Then prefer keyboard index
    if (index >= 0 && visibleItems[index]) return visibleItems[index];

    // Fallback: first valid option (skip placeholders like "-")
    const firstValid = visibleItems.find(item => (item.dataset.id || '').trim() !== '');
    return firstValid || visibleItems[0];
}

function selectKbBeDropdownItem(kind) {
    const isCustomer = kind === 'customer';
    const searchInput = document.getElementById(isCustomer ? 'customerSearchInput' : 'salesmanSearchInput');
    const dropdown = document.getElementById(isCustomer ? 'customerDropdown' : 'salesmanDropdown');
    const listContainer = document.getElementById(isCustomer ? 'customerList' : 'salesmanList');

    if (!searchInput || !dropdown || !listContainer) return false;

    const allItems = Array.from(listContainer.querySelectorAll('.dropdown-item'));
    const visibleItems = allItems.filter(item => item.style.display !== 'none');

    if (!visibleItems.length) {
        console.log(`[KB-BE][${isCustomer ? 'Customer' : 'Salesman'}] no visible items to select`);
        return false;
    }

    const index = isCustomer ? customerDropdownIndex : salesmanDropdownIndex;
    let selectedItem = getKbBePreferredDropdownItem(listContainer, index);

    if (!selectedItem && visibleItems.length) {
        if (isCustomer) {
            customerDropdownIndex = 0;
            updateCustomerDropdownHighlight();
            selectedItem = visibleItems[0];
        } else {
            salesmanDropdownIndex = 0;
            updateSalesmanDropdownHighlight();
            selectedItem = visibleItems[0];
        }
    }

    if (!selectedItem) {
        console.log(`[KB-BE][${isCustomer ? 'Customer' : 'Salesman'}] selected item not found`);
        return false;
    }

    console.log(`[KB-BE][${isCustomer ? 'Customer' : 'Salesman'}] selecting item via Enter`, {
        text: selectedItem.textContent?.trim(),
        id: selectedItem.dataset.id || ''
    });

    setKbBeDropdownSelecting(true);

    // Directly select to avoid Enter bubbling to header handlers.
    const selected = isCustomer ? selectCustomer(selectedItem) : selectSalesman(selectedItem);
    console.log(`[KB-BE][${isCustomer ? 'Customer' : 'Salesman'}] selection result`, {
        selected,
        text: selectedItem.textContent?.trim(),
        id: selectedItem.dataset.id || ''
    });
    if (selected) {
        const nextField = document.getElementById(isCustomer ? 'gstVno' : 'inc');
        if (nextField) {
            nextField.focus();
        }
        return true;
    }

    return false;
}

// Initialize Customer Dropdown
function initCustomerDropdown() {
    const searchInput = document.getElementById('customerSearchInput');
    const selectElement = document.getElementById('customerSelect');
    const dropdown = document.getElementById('customerDropdown');
    const listContainer = document.getElementById('customerList');
    
    if (!searchInput || !selectElement || !dropdown || !listContainer) return;
    
    const allItems = listContainer.querySelectorAll('.dropdown-item');
    
    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        showCustomerDropdown();
    });
    
    // Filter on input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;
        
        allItems.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const code = (item.dataset.code || '').toLowerCase();
            const text = item.textContent.toLowerCase();
            
            if (name.includes(searchTerm) || code.includes(searchTerm) || text.includes(searchTerm)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Reset selection index
        customerDropdownIndex = -1;
        updateCustomerDropdownHighlight();
        
        showCustomerDropdown();
    });
    
    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
        const dropdownVisible = isKbBeDropdownOpen(dropdown);
        console.log('[KB-BE][Customer] keydown', {
            key: e.key,
            dropdownVisible,
            index: customerDropdownIndex,
            visibleCount: visibleItems.length
        });
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (customerDropdownIndex < visibleItems.length - 1) {
                customerDropdownIndex++;
            } else {
                customerDropdownIndex = 0;
            }
            updateCustomerDropdownHighlight();
            scrollIntoViewIfNeeded(visibleItems[customerDropdownIndex], listContainer);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (customerDropdownIndex > 0) {
                customerDropdownIndex--;
            } else {
                customerDropdownIndex = visibleItems.length - 1;
            }
            updateCustomerDropdownHighlight();
            scrollIntoViewIfNeeded(visibleItems[customerDropdownIndex], listContainer);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // If dropdown is closed, first Enter only opens it (does not skip field)
            if (!dropdownVisible) {
                showCustomerDropdown();
                if (visibleItems.length > 0) {
                    customerDropdownIndex = customerDropdownIndex >= 0 ? customerDropdownIndex : 0;
                    updateCustomerDropdownHighlight();
                }
                console.log('[KB-BE][Customer] Enter opened dropdown');
                return;
            }
            selectKbBeDropdownItem('customer');
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            hideCustomerDropdown();
        } else if (e.key === 'Tab') {
            hideCustomerDropdown();
        }
    });
    
    // Click on dropdown item
    allItems.forEach(item => {
        item.addEventListener('click', function() {
            console.log('[KB-BE][Customer] selecting item via click', {
                text: this.textContent?.trim()
            });
            selectCustomer(this);
            const nextField = document.getElementById('gstVno');
            if (nextField) {
                nextField.focus();
            }
        });
        
        item.addEventListener('mouseenter', function() {
            const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
            customerDropdownIndex = visibleItems.indexOf(this);
            updateCustomerDropdownHighlight();
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-wrapper')) {
            hideCustomerDropdown();
        }
    });
}

function showCustomerDropdown() {
    const dropdown = document.getElementById('customerDropdown');
    if (dropdown) dropdown.style.display = 'block';
}

function hideCustomerDropdown() {
    const dropdown = document.getElementById('customerDropdown');
    if (dropdown) dropdown.style.display = 'none';
    customerDropdownIndex = -1;
}

function updateCustomerDropdownHighlight() {
    const listContainer = document.getElementById('customerList');
    if (!listContainer) return;
    
    const allItems = listContainer.querySelectorAll('.dropdown-item');
    const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
    
    allItems.forEach(item => item.classList.remove('active'));
    
    if (customerDropdownIndex >= 0 && visibleItems[customerDropdownIndex]) {
        visibleItems[customerDropdownIndex].classList.add('active');
    }
}

function selectCustomer(item) {
    const searchInput = document.getElementById('customerSearchInput');
    const selectElement = document.getElementById('customerSelect');
    if (!item || !searchInput || !selectElement) return false;
    
    const id = (item.dataset.id || '').trim();
    const name = item.dataset.name;
    const code = item.dataset.code || '';

    // Ignore placeholder row on Enter selection.
    if (!id) {
        console.log('[KB-BE][Customer] skip placeholder selection');
        return false;
    }

    console.log('[KB-BE][Customer] selectCustomer', { id, name, code });
    
    searchInput.value = code ? `${code} - ${name}` : name;
    selectElement.value = id;
    selectElement.dispatchEvent(new Event('change', { bubbles: true }));
    
    hideCustomerDropdown();
    return true;
}

// Initialize Salesman Dropdown
function initSalesmanDropdown() {
    const searchInput = document.getElementById('salesmanSearchInput');
    const selectElement = document.getElementById('salesmanSelect');
    const dropdown = document.getElementById('salesmanDropdown');
    const listContainer = document.getElementById('salesmanList');
    
    if (!searchInput || !selectElement || !dropdown || !listContainer) return;
    
    const allItems = listContainer.querySelectorAll('.dropdown-item');
    
    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        showSalesmanDropdown();
    });
    
    // Filter on input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;
        
        allItems.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const code = (item.dataset.code || '').toLowerCase();
            const text = item.textContent.toLowerCase();
            
            if (name.includes(searchTerm) || code.includes(searchTerm) || text.includes(searchTerm)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Reset selection index
        salesmanDropdownIndex = -1;
        updateSalesmanDropdownHighlight();
        
        showSalesmanDropdown();
    });
    
    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
        const dropdownVisible = isKbBeDropdownOpen(dropdown);
        console.log('[KB-BE][Salesman] keydown', {
            key: e.key,
            dropdownVisible,
            index: salesmanDropdownIndex,
            visibleCount: visibleItems.length
        });
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (salesmanDropdownIndex < visibleItems.length - 1) {
                salesmanDropdownIndex++;
            } else {
                salesmanDropdownIndex = 0;
            }
            updateSalesmanDropdownHighlight();
            scrollIntoViewIfNeeded(visibleItems[salesmanDropdownIndex], listContainer);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (salesmanDropdownIndex > 0) {
                salesmanDropdownIndex--;
            } else {
                salesmanDropdownIndex = visibleItems.length - 1;
            }
            updateSalesmanDropdownHighlight();
            scrollIntoViewIfNeeded(visibleItems[salesmanDropdownIndex], listContainer);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // If dropdown is closed, first Enter only opens it (does not skip field)
            if (!dropdownVisible) {
                showSalesmanDropdown();
                if (visibleItems.length > 0) {
                    salesmanDropdownIndex = salesmanDropdownIndex >= 0 ? salesmanDropdownIndex : 0;
                    updateSalesmanDropdownHighlight();
                }
                console.log('[KB-BE][Salesman] Enter opened dropdown');
                return;
            }
            selectKbBeDropdownItem('salesman');
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            hideSalesmanDropdown();
        } else if (e.key === 'Tab') {
            hideSalesmanDropdown();
        }
    });
    
    // Click on dropdown item
    allItems.forEach(item => {
        item.addEventListener('click', function() {
            console.log('[KB-BE][Salesman] selecting item via click', {
                text: this.textContent?.trim()
            });
            selectSalesman(this);
            const nextField = document.getElementById('inc');
            if (nextField) {
                nextField.focus();
            }
        });
        
        item.addEventListener('mouseenter', function() {
            const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
            salesmanDropdownIndex = visibleItems.indexOf(this);
            updateSalesmanDropdownHighlight();
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-wrapper')) {
            hideSalesmanDropdown();
        }
    });
}

function showSalesmanDropdown() {
    const dropdown = document.getElementById('salesmanDropdown');
    if (dropdown) dropdown.style.display = 'block';
}

function hideSalesmanDropdown() {
    const dropdown = document.getElementById('salesmanDropdown');
    if (dropdown) dropdown.style.display = 'none';
    salesmanDropdownIndex = -1;
}

function updateSalesmanDropdownHighlight() {
    const listContainer = document.getElementById('salesmanList');
    if (!listContainer) return;
    
    const allItems = listContainer.querySelectorAll('.dropdown-item');
    const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
    
    allItems.forEach(item => item.classList.remove('active'));
    
    if (salesmanDropdownIndex >= 0 && visibleItems[salesmanDropdownIndex]) {
        visibleItems[salesmanDropdownIndex].classList.add('active');
    }
}

function selectSalesman(item) {
    const searchInput = document.getElementById('salesmanSearchInput');
    const selectElement = document.getElementById('salesmanSelect');
    if (!item || !searchInput || !selectElement) return false;
    
    const id = (item.dataset.id || '').trim();
    const name = item.dataset.name;
    const code = item.dataset.code || '';

    if (!id) {
        console.log('[KB-BE][Salesman] skip placeholder selection');
        return false;
    }

    console.log('[KB-BE][Salesman] selectSalesman', { id, name, code });
    
    searchInput.value = code ? `${code} - ${name}` : name;
    selectElement.value = id;
    selectElement.dispatchEvent(new Event('change', { bubbles: true }));
    
    hideSalesmanDropdown();
    return true;
}

// Capture Enter before global/header handlers so dropdown selection always wins.
function initDropdownEnterCapture() {
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;

        const active = document.activeElement;
        if (!active) return;

        const customerInput = document.getElementById('customerSearchInput');
        const customerDropdown = document.getElementById('customerDropdown');
        const customerList = document.getElementById('customerList');
        const salesmanInput = document.getElementById('salesmanSearchInput');
        const salesmanDropdown = document.getElementById('salesmanDropdown');
        const salesmanList = document.getElementById('salesmanList');

        const customerOpen = isKbBeDropdownOpen(customerDropdown);
        const salesmanOpen = isKbBeDropdownOpen(salesmanDropdown);
        const customerFocus = !!(customerInput && (active === customerInput || customerDropdown?.contains(active)));
        const salesmanFocus = !!(salesmanInput && (active === salesmanInput || salesmanDropdown?.contains(active)));

        console.log('[KB-BE][Capture][Global] Enter', {
            activeId: active.id || null,
            activeName: active.name || null,
            customerOpen,
            salesmanOpen,
            customerFocus,
            salesmanFocus,
            customerIndex: customerDropdownIndex,
            salesmanIndex: salesmanDropdownIndex
        });

        if (
            customerInput &&
            customerDropdown &&
            customerList &&
            (customerOpen || customerFocus)
        ) {
            const allItems = customerList.querySelectorAll('.dropdown-item');
            const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
            const selectedItem = getKbBePreferredDropdownItem(customerList, customerDropdownIndex);

            console.log('[KB-BE][Capture][Customer] Enter', {
                index: customerDropdownIndex,
                visibleCount: visibleItems.length,
                hasSelectedItem: !!selectedItem
            });

            if (selectedItem) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                setKbBeDropdownSelecting(true);
                selectKbBeDropdownItem('customer');
            } else {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                showCustomerDropdown();
            }
            return;
        }

        if (
            salesmanInput &&
            salesmanDropdown &&
            salesmanList &&
            (salesmanOpen || salesmanFocus)
        ) {
            const allItems = salesmanList.querySelectorAll('.dropdown-item');
            const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
            const selectedItem = getKbBePreferredDropdownItem(salesmanList, salesmanDropdownIndex);

            console.log('[KB-BE][Capture][Salesman] Enter', {
                index: salesmanDropdownIndex,
                visibleCount: visibleItems.length,
                hasSelectedItem: !!selectedItem
            });

            if (selectedItem) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                setKbBeDropdownSelecting(true);
                selectKbBeDropdownItem('salesman');
            } else {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                showSalesmanDropdown();
            }
        }
    }, true);

    // Also block keyup Enter from global handlers when dropdown is open
    document.addEventListener('keyup', function(e) {
        if (e.key !== 'Enter') return;
        const customerDropdown = document.getElementById('customerDropdown');
        const salesmanDropdown = document.getElementById('salesmanDropdown');
        const customerOpen = isKbBeDropdownOpen(customerDropdown);
        const salesmanOpen = isKbBeDropdownOpen(salesmanDropdown);
        if (customerOpen || salesmanOpen || kbBeDropdownSelecting) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        }
    }, true);
}

// Ensure Enter always selects highlighted row inside reusable item/batch modals.
function initReusableModalEnterCapture() {
    if (window.__kbBeModalEnterBound) return;

    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;

        const chooseModal = document.getElementById('chooseItemsModal');
        const batchModal = document.getElementById('batchSelectionModal');
        const chooseOpen = !!(chooseModal && chooseModal.classList.contains('show'));
        const batchOpen = !!(batchModal && batchModal.classList.contains('show'));

        if (!chooseOpen && !batchOpen) return;

        function resolveSelectedRow(modalEl, kind) {
            const selectors = kind === 'batch'
                ? [
                    'tbody tr.batch-row.row-selected',
                    'tbody tr.batch-row.item-row-selected',
                    'tbody tr.batch-row.table-active',
                    'tbody tr.batch-row.selected',
                    'tbody tr.row-selected',
                    'tbody tr.batch-row'
                ]
                : [
                    'tbody tr.item-row.row-selected',
                    'tbody tr.item-row.item-row-selected',
                    'tbody tr.item-row.table-active',
                    'tbody tr.item-row.selected',
                    'tbody tr.row-selected',
                    'tbody tr.item-row'
                ];

            for (const s of selectors) {
                const found = modalEl.querySelector(s);
                if (found) return found;
            }
            return null;
        }

        // If batch modal is open, Enter should select batch.
        if (batchOpen && typeof window.selectBatch_batchSelectionModal === 'function') {
            const selectedRow = resolveSelectedRow(batchModal, 'batch');
            const index = selectedRow
                ? parseInt(selectedRow.getAttribute('data-index') || '0', 10)
                : -1;

            console.log('[KB-BE][ModalCapture] Batch Enter', {
                hasSelectedRow: !!selectedRow,
                selectedRowClass: selectedRow?.className || null,
                index
            });

            if (index >= 0) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window.selectBatch_batchSelectionModal(index);
            } else if (selectedRow) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                selectedRow.dispatchEvent(new MouseEvent('dblclick', { bubbles: true }));
            }
            return;
        }

        // If item modal is open, Enter should select item.
        if (chooseOpen && typeof window.selectItem_chooseItemsModal === 'function') {
            const selectedRow = resolveSelectedRow(chooseModal, 'item');
            const index = selectedRow
                ? parseInt(selectedRow.getAttribute('data-index') || '0', 10)
                : -1;

            console.log('[KB-BE][ModalCapture] Item Enter', {
                hasSelectedRow: !!selectedRow,
                selectedRowClass: selectedRow?.className || null,
                index
            });

            if (index >= 0) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window.selectItem_chooseItemsModal(index);
            } else if (selectedRow) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                selectedRow.dispatchEvent(new MouseEvent('dblclick', { bubbles: true }));
            }
        }
    }, true);

    window.__kbBeModalEnterBound = true;
}

// Fallback: Ensure Enter on table Code field always opens item selection modal.
function initTableCodeEnterCapture() {
    if (window.__kbBeCodeEnterCaptureBound) return;

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;

        const active = document.activeElement;
        if (!active) return;

        // Target only code fields inside items table that are not readonly
        const isCodeField =
            active.matches &&
            active.matches('#itemsTableBody input[name*="[code]"]') &&
            !active.readOnly;
        if (!isCodeField) return;

        console.log('[KB-BE][Capture][Table] Code Enter -> openItemSelectionModal');

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        openItemSelectionModal();
    }, true);

    window.__kbBeCodeEnterCaptureBound = true;
}

// Fallback: Ensure Enter on table Dis% always creates next row.
function initTableDiscountEnterCapture() {
    if (window.__kbBeDisEnterCaptureBound) return;

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;

        const active = document.activeElement;
        if (!active) return;

        // Target only Dis% fields inside items table
        const isDisField =
            active.matches &&
            active.matches('#itemsTableBody input[name*="[dis_percent]"]');
        if (!isDisField) return;

        const rowEl = active.closest('tr[id^="row-"]');
        let rowIndex = -1;

        if (rowEl && rowEl.id) {
            const parsed = parseInt(rowEl.id.replace('row-', ''), 10);
            if (!Number.isNaN(parsed)) rowIndex = parsed;
        }

        if (rowIndex < 0) {
            const name = active.getAttribute('name') || '';
            const m = name.match(/items\[(\d+)\]\[dis_percent\]/);
            if (m) rowIndex = parseInt(m[1], 10);
        }

        console.log('[KB-BE][Capture][Table] Dis% Enter fallback', {
            activeName: active.name || null,
            rowId: rowEl ? rowEl.id : null,
            rowIndex
        });

        if (rowIndex >= 0) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            handleDiscountAndAddRow(rowIndex);
        }
    }, true);

    document.addEventListener('keypress', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;

        const active = document.activeElement;
        if (!active) return;

        const isDisField =
            active.matches &&
            active.matches('#itemsTableBody input[name*="[dis_percent]"]');
        if (!isDisField) return;

        const rowEl = active.closest('tr[id^="row-"]');
        let rowIndex = -1;

        if (rowEl && rowEl.id) {
            const parsed = parseInt(rowEl.id.replace('row-', ''), 10);
            if (!Number.isNaN(parsed)) rowIndex = parsed;
        }

        if (rowIndex < 0) {
            const name = active.getAttribute('name') || '';
            const m = name.match(/items\[(\d+)\]\[dis_percent\]/);
            if (m) rowIndex = parseInt(m[1], 10);
        }

        console.log('[KB-BE][Capture][Table] Dis% Enter keypress fallback', {
            activeName: active.name || null,
            rowId: rowEl ? rowEl.id : null,
            rowIndex
        });

        if (rowIndex >= 0) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            handleDiscountAndAddRow(rowIndex);
        }
    }, true);

    document.addEventListener('keyup', function(e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;

        const active = document.activeElement;
        if (!active) return;

        const isDisField =
            active.matches &&
            active.matches('#itemsTableBody input[name*="[dis_percent]"]');
        if (!isDisField) return;

        const rowEl = active.closest('tr[id^="row-"]');
        let rowIndex = -1;

        if (rowEl && rowEl.id) {
            const parsed = parseInt(rowEl.id.replace('row-', ''), 10);
            if (!Number.isNaN(parsed)) rowIndex = parsed;
        }

        if (rowIndex < 0) {
            const name = active.getAttribute('name') || '';
            const m = name.match(/items\[(\d+)\]\[dis_percent\]/);
            if (m) rowIndex = parseInt(m[1], 10);
        }

        console.log('[KB-BE][Capture][Table] Dis% Enter keyup fallback', {
            activeName: active.name || null,
            rowId: rowEl ? rowEl.id : null,
            rowIndex
        });

        if (rowIndex >= 0) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            handleDiscountAndAddRow(rowIndex);
        }
    }, true);

    window.__kbBeDisEnterCaptureBound = true;
}

// Utility: Scroll into view if needed
function scrollIntoViewIfNeeded(element, container) {
    if (!element || !container) return;
    
    const elementRect = element.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();
    
    if (elementRect.bottom > containerRect.bottom) {
        element.scrollIntoView({ block: 'end', behavior: 'smooth' });
    } else if (elementRect.top < containerRect.top) {
        element.scrollIntoView({ block: 'start', behavior: 'smooth' });
    }
}

// Initialize Header Field Keyboard Navigation
function initHeaderKeyboardNavigation() {
    function isBlockingModalOpen() {
        return !!document.querySelector(
            '.item-modal.show, .batch-modal.show, .credit-note-modal.show, .adjustment-modal.show, #alertModal.show, .alert-modal.show'
        );
    }

    function triggerInsertOrders(source = 'exp-field') {
        if (isBlockingModalOpen()) {
            console.log('[KB-BE] Insert Orders skipped (modal open)', { source });
            return;
        }

        const insertBtn = document.getElementById('insertOrdersBtn');
        const customerId = document.querySelector('select[name="customer_id"]')?.value || '';
        console.log('[KB-BE] triggerInsertOrders', { source, hasInsertBtn: !!insertBtn, customerId });

        // Prefer direct function call (same as sale-return flow reliability).
        if (typeof openItemSelectionModal === 'function') {
            openItemSelectionModal();
            return;
        }

        if (insertBtn) {
            insertBtn.click();
        } else {
            console.warn('[KB-BE] Insert Orders trigger failed: button/function not found');
        }
    }

    // Exp field: trigger Insert Orders only when Enter is pressed inside Exp field.
    const expInput = document.getElementById('expField');
    if (expInput && !expInput.dataset.kbBeEnterBound) {
        expInput.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) return;
            console.log('[KB-BE] Exp Enter (direct field)', {
                value: expInput.value || ''
            });
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            triggerInsertOrders('expField-direct');
        }, true);

        // Fallback: if some global handler swallows keydown before target, keyup still triggers.
        expInput.addEventListener('keyup', function(e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) return;
            console.log('[KB-BE] Exp Enter (keyup fallback)', {
                value: expInput.value || ''
            });
            e.preventDefault();
            e.stopPropagation();
            triggerInsertOrders('expField-keyup-fallback');
        }, true);

        // Extra capture fallback at document level for exp focus.
        if (!window.__kbBeExpDocCaptureBound) {
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter' && e.keyCode !== 13) return;
                const active = document.activeElement;
                if (!active || active.id !== 'expField') return;
                console.log('[KB-BE] Exp Enter (document capture fallback)', {
                    activeId: active.id,
                    value: active.value || ''
                });
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                triggerInsertOrders('expField-doc-capture-fallback');
            }, true);
            window.__kbBeExpDocCaptureBound = true;
        }

        expInput.dataset.kbBeEnterBound = '1';
    }

    // Define the field navigation flow
    const headerFields = [
        { id: 'seriesInput', next: 'transactionDate' },
        { id: 'transactionDate', next: 'endDate' },
        { id: 'endDate', next: 'customerSearchInput' },
        // customerSearchInput handled by dropdown logic
        { id: 'gstVno', next: 'noteType' },
        { id: 'noteType', next: 'withGst' },
        { id: 'withGst', next: 'salesmanSearchInput' },
        // salesmanSearchInput handled by dropdown logic
        { id: 'inc', next: 'revCharge' },
        { id: 'revCharge', next: 'adjustedFlag' },
        { id: 'adjustedFlag', next: 'disRpl' },
        { id: 'disRpl', next: 'brk' },
        { id: 'brk', next: 'expField' }
    ];
    
    headerFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element) return;
        
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const customerDropdown = document.getElementById('customerDropdown');
                const salesmanDropdown = document.getElementById('salesmanDropdown');
                const customerOpen = isKbBeDropdownOpen(customerDropdown);
                const salesmanOpen = isKbBeDropdownOpen(salesmanDropdown);
                console.log('[KB-BE][Header] Enter keydown', {
                    field: field.id,
                    customerOpen,
                    salesmanOpen,
                    activeId: document.activeElement?.id || null
                });

                if (customerOpen || salesmanOpen) {
                    e.preventDefault();
                    return;
                }

                if (kbBeDropdownSelecting) {
                    console.log('[KB-BE][Header] Enter ignored due dropdown selection lock', {
                        field: field.id
                    });
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                
                // Move to next field
                if (field.next) {
                    const nextElement = document.getElementById(field.next);
                    if (nextElement) {
                        // If it's a custom dropdown search input, the dropdown will show on focus
                        nextElement.focus();
                    }
                }
            }
        });
    });
}

// Initialize all keyboard handlers on DOM ready
(function() {
    // Wait for DOM content to be loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeKeyboardHandlers);
    } else {
        initializeKeyboardHandlers();
    }
    
    function initializeKeyboardHandlers() {
        initCustomerDropdown();
        initSalesmanDropdown();
        initReusableModalEnterCapture();
        initDropdownEnterCapture();
        initTableCodeEnterCapture();
        initTableDiscountEnterCapture();
        initHeaderKeyboardNavigation();
        
        // Set initial focus on Series field
        setTimeout(() => {
            const seriesInput = document.getElementById('seriesInput');
            if (seriesInput) {
                seriesInput.focus();
            }
        }, 100);
    }
})();
</script>

<!-- Batch Does Not Exist Confirmation Modal -->
<div class="batch-not-exist-modal-backdrop" id="batchNotExistModalBackdrop" onclick="closeBatchNotExistModal()" style="display: none;"></div>
<div class="batch-not-exist-modal" id="batchNotExistModal" style="display: none;">
    <div class="batch-not-exist-modal-content">
        <div class="batch-not-exist-modal-header">
            <h5 class="batch-not-exist-modal-title">
                <i class="bi bi-question-circle me-2"></i>Batch Does not Exists Create New Batch
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeBatchNotExistModal()">&times;</button>
        </div>
        <div class="batch-not-exist-modal-body">
            <p class="mb-3">The batch number you entered does not exist. Would you like to create a new batch?</p>
        </div>
        <div class="batch-not-exist-modal-footer">
            <button type="button" class="btn btn-success btn-sm" onclick="openCreateBatchModal()">Yes</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchNotExistModal()">No</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="closeBatchNotExistModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Create New Batch Modal -->
<div class="create-batch-modal-backdrop" id="createBatchModalBackdrop" onclick="closeCreateBatchModal()" style="display: none;"></div>
<div class="create-batch-modal" id="createBatchModal" style="display: none;">
    <div class="create-batch-modal-content">
        <div class="create-batch-modal-header">
            <h5 class="create-batch-modal-title">
                <i class="bi bi-plus-circle me-2"></i>Create New Batch
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeCreateBatchModal()">&times;</button>
        </div>
        <div class="create-batch-modal-body">
            <form id="createBatchForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Item Name:</label>
                            <input type="text" class="form-control form-control-sm" id="batchItemName" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pack:</label>
                            <input type="text" class="form-control form-control-sm" id="batchPack" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">MRP:</label>
                            <input type="number" class="form-control form-control-sm" id="batchMrp" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">S.Rate:</label>
                            <input type="number" class="form-control form-control-sm" id="batchSRate" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">P.Rate:</label>
                            <input type="number" class="form-control form-control-sm" id="batchPRate" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Expiry:</label>
                            <input type="text" class="form-control form-control-sm" id="batchExpiry" placeholder="MM/YY">
                        </div>
                    </div>
                </div>
                <input type="hidden" id="batchItemId">
                <input type="hidden" id="batchNumber">
                <input type="hidden" id="batchRowIndex">
            </form>
        </div>
        <div class="create-batch-modal-footer">
            <button type="button" class="btn btn-primary btn-sm" onclick="saveBatch()">
                <i class="bi bi-check-circle me-1"></i>OK
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'breakage-expiry',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'breakage-expiry',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/breakage-expiry/transaction.blade.php ENDPATH**/ ?>