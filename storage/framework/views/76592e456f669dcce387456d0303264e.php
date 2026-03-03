<?php $__env->startSection('title', 'Sale Book'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALE BOOK</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_sale" value="1" <?php echo e(($reportType ?? '1') == '1' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_sale">1. Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_return" value="2" <?php echo e(($reportType ?? '') == '2' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_return">2. Sale Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_debit" value="3" <?php echo e(($reportType ?? '') == '3' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_debit">3. Debit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_credit" value="4" <?php echo e(($reportType ?? '') == '4' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_credit">4. Credit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_consolidated" value="5" <?php echo e(($reportType ?? '') == '5' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_consolidated">5. Consolidated</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_all_cn_dn" value="6" <?php echo e(($reportType ?? '') == '6' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_all_cn_dn">6. All CN_DN</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="<?php echo e(route('admin.reports.sales.sales-book')); ?>">
                <input type="hidden" name="report_type" id="hidden_report_type" value="<?php echo e($reportType ?? '1'); ?>">
                
                <div class="row g-2">
                    <!-- Row 1: Date Range & Basic Options -->
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">From</span>
                                    <input type="date" name="date_from" class="form-control" value="<?php echo e($dateFrom); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">To</span>
                                    <input type="date" name="date_to" class="form-control" value="<?php echo e($dateTo); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">VAT ROff [DN/CN]</span>
                            <select name="vat_roff" class="form-select">
                                <option value="Y" <?php echo e(($vatRoff ?? 'Y') == 'Y' ? 'selected' : ''); ?>>Y</option>
                                <option value="N" <?php echo e(($vatRoff ?? '') == 'N' ? 'selected' : ''); ?>>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">T(ax)/R(etail)</span>
                            <select name="tax_retail" class="form-select">
                                <option value="">All</option>
                                <option value="T" <?php echo e(($taxRetail ?? '') == 'T' ? 'selected' : ''); ?>>Tax</option>
                                <option value="R" <?php echo e(($taxRetail ?? '') == 'R' ? 'selected' : ''); ?>>Retail</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Report Format & Options -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Format</span>
                            <select name="report_format" class="form-select">
                                <option value="D" <?php echo e(($reportFormat ?? 'D') == 'D' ? 'selected' : ''); ?>>D(etailed)</option>
                                <option value="S" <?php echo e(($reportFormat ?? '') == 'S' ? 'selected' : ''); ?>>S(ummarised-Day wise)</option>
                                <option value="M" <?php echo e(($reportFormat ?? '') == 'M' ? 'selected' : ''); ?>>M(onthly)</option>
                                <option value="G" <?php echo e(($reportFormat ?? '') == 'G' ? 'selected' : ''); ?>>G(roup)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cancelled</span>
                            <select name="cancelled" class="form-select">
                                <option value="N" <?php echo e(($cancelled ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($cancelled ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Br.Exp</span>
                            <select name="with_br_exp" class="form-select">
                                <option value="N" <?php echo e(($withBrExp ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($withBrExp ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                                <option value="A" <?php echo e(($withBrExp ?? '') == 'A' ? 'selected' : ''); ?>>A(ll)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Day Total</span>
                            <select name="day_wise_total" class="form-select">
                                <option value="N" <?php echo e(($dayWiseTotal ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($dayWiseTotal ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Series</span>
                            <select name="series" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $seriesList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e(($series ?? '') == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: User & Party Filters -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">User</span>
                            <select name="user_id" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->user_id); ?>" <?php echo e(($userId ?? '') == $user->user_id ? 'selected' : ''); ?>><?php echo e($user->full_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">F/L User</span>
                            <select name="first_last_user" class="form-select">
                                <option value="F" <?php echo e(($firstLastUser ?? 'F') == 'F' ? 'selected' : ''); ?>>F</option>
                                <option value="L" <?php echo e(($firstLastUser ?? '') == 'L' ? 'selected' : ''); ?>>L</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Code</span>
                            <input type="text" name="party_code" class="form-control text-uppercase" value="<?php echo e($partyCode ?? ''); ?>" placeholder="00">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Name</span>
                            <select name="customer_id" class="form-select" id="customerSelect">
                                <option value="">All Customers</option>
                                <?php $__currentLoopData = $customers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>" data-code="<?php echo e($customer->code); ?>" <?php echo e(($customerId ?? '') == $customer->id ? 'selected' : ''); ?>>
                                        <?php echo e($customer->code); ?> - <?php echo e($customer->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Location & Type Filters -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B/E</span>
                            <select name="local_central" class="form-select">
                                <option value="B" <?php echo e(($localCentral ?? 'B') == 'B' ? 'selected' : ''); ?>>B(oth)</option>
                                <option value="L" <?php echo e(($localCentral ?? '') == 'L' ? 'selected' : ''); ?>>L(ocal)</option>
                                <option value="C" <?php echo e(($localCentral ?? '') == 'C' ? 'selected' : ''); ?>>C(entral)</option>
                                <option value="E" <?php echo e(($localCentral ?? '') == 'E' ? 'selected' : ''); ?>>E(xport)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sale Type</span>
                            <select name="business_type" class="form-select">
                                <option value="">All</option>
                                <option value="W" <?php echo e(($businessType ?? '') == 'W' ? 'selected' : ''); ?>>W(holesale)</option>
                                <option value="R" <?php echo e(($businessType ?? '') == 'R' ? 'selected' : ''); ?>>R(etail)</option>
                                <option value="I" <?php echo e(($businessType ?? '') == 'I' ? 'selected' : ''); ?>>I(nstitution)</option>
                                <option value="D" <?php echo e(($businessType ?? '') == 'D' ? 'selected' : ''); ?>>D(ept. Store)</option>
                                <option value="O" <?php echo e(($businessType ?? '') == 'O' ? 'selected' : ''); ?>>O(thers)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GSTN</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" <?php echo e(($gstnFilter ?? '3') == '3' ? 'selected' : ''); ?>>All</option>
                                <option value="1" <?php echo e(($gstnFilter ?? '') == '1' ? 'selected' : ''); ?>>With</option>
                                <option value="2" <?php echo e(($gstnFilter ?? '') == '2' ? 'selected' : ''); ?>>Without</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Credit Card</span>
                            <select name="credit_card" class="form-select">
                                <option value="Y" <?php echo e(($creditCard ?? 'Y') == 'Y' ? 'selected' : ''); ?>>Y</option>
                                <option value="N" <?php echo e(($creditCard ?? '') == 'N' ? 'selected' : ''); ?>>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S.Man Master</span>
                            <select name="sman_from_master" class="form-select">
                                <option value="N" <?php echo e(($smanFromMaster ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($smanFromMaster ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Sales Man, Area, Route, State -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sales Man</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $salesmen ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($salesman->id); ?>" <?php echo e(($salesmanId ?? '') == $salesman->id ? 'selected' : ''); ?>>
                                        <?php echo e($salesman->code ?? ''); ?> - <?php echo e($salesman->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area</span>
                            <select name="area_id" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $areas ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($area->id); ?>" <?php echo e(($areaId ?? '') == $area->id ? 'selected' : ''); ?>><?php echo e($area->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route</span>
                            <select name="route_id" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $routes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($route->id); ?>" <?php echo e(($routeId ?? '') == $route->id ? 'selected' : ''); ?>><?php echo e($route->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">State</span>
                            <select name="state_id" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $states ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state->id); ?>" <?php echo e(($stateId ?? '') == $state->id ? 'selected' : ''); ?>><?php echo e($state->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Display Options (Checkboxes) -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <span class="fw-bold small">Display Options:</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_gst_details" id="showGstDetails" value="1" <?php echo e(($showGstDetails ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="showGstDetails">GST Details</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_gr_details" id="showGrDetails" value="1" <?php echo e(($showGrDetails ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="showGrDetails">GR Details</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_cash_credit" id="showCashCredit" value="1" <?php echo e(($showCashCredit ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="showCashCredit">Cash/Credit Card</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_salesman" id="showSalesman" value="1" <?php echo e(($showSalesman ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="showSalesman">Show Sales Man</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="order_by_customer" id="orderByCustomer" value="1" <?php echo e(($orderByCustomer ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="orderByCustomer">Order by Customer</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="deduct_add_less" id="deductAddLess" value="1" <?php echo e(($deductAddLess ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="deductAddLess">Deduct Add Less Bill Amt</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_area" id="showArea" value="1" <?php echo e(($showArea ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="showArea">Show AREA</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="with_address" id="withAddress" value="1" <?php echo e(($withAddress ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="withAddress">With Address</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="stateWiseSale()">
                            State Wise
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="<?php echo e(route('admin.reports.sales')); ?>" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    <?php if(request()->has('view') && isset($sales) && $sales->count() > 0): ?>
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0"><?php echo e(number_format($totals['count'] ?? 0)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Gross Amount</small>
                    <h6 class="mb-0">₹<?php echo e(number_format($totals['nt_amount'] ?? 0, 2)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2">
                    <small>Discount</small>
                    <h6 class="mb-0">₹<?php echo e(number_format($totals['dis_amount'] ?? 0, 2)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Tax Amount</small>
                    <h6 class="mb-0">₹<?php echo e(number_format($totals['tax_amount'] ?? 0, 2)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Sch. Amount</small>
                    <h6 class="mb-0">₹<?php echo e(number_format($totals['scm_amount'] ?? 0, 2)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹<?php echo e(number_format($totals['net_amount'] ?? 0, 2)); ?></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Party Name</th>
                            <?php if($showArea ?? false): ?>
                            <th>Area</th>
                            <?php endif; ?>
                            <?php if($showSalesman ?? false): ?>
                            <th>Salesman</th>
                            <?php endif; ?>
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Sch Amt</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                            <?php if($withAddress ?? false): ?>
                            <th>Address</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $currentDate = null; $dayTotal = 0; $dayCount = 0; ?>
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(($dayWiseTotal ?? 'N') == 'Y' && $currentDate !== null && $currentDate != $sale->sale_date->format('Y-m-d')): ?>
                                <tr class="table-warning fw-bold">
                                    <td colspan="<?php echo e(4 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0)); ?>" class="text-end">
                                        Day Total (<?php echo e(\Carbon\Carbon::parse($currentDate)->format('d-m-Y')); ?>): <?php echo e($dayCount); ?> Bills
                                    </td>
                                    <td class="text-end" colspan="4"></td>
                                    <td class="text-end">₹<?php echo e(number_format($dayTotal, 2)); ?></td>
                                    <?php if($withAddress ?? false): ?><td></td><?php endif; ?>
                                </tr>
                                <?php $dayTotal = 0; $dayCount = 0; ?>
                            <?php endif; ?>
                            <?php 
                                $currentDate = $sale->sale_date->format('Y-m-d'); 
                                $dayTotal += $sale->net_amount;
                                $dayCount++;
                            ?>
                        <tr>
                            <td class="text-center"><?php echo e($index + 1); ?></td>
                            <td><?php echo e($sale->sale_date->format('d-m-Y')); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.sale.show', $sale->id)); ?>" class="text-decoration-none fw-bold">
                                    <?php echo e($sale->series); ?><?php echo e($sale->invoice_no); ?>

                                </a>
                            </td>
                            <td>
                                <span class="text-muted small"><?php echo e($sale->customer->code ?? ''); ?></span>
                                <?php echo e($sale->customer->name ?? 'N/A'); ?>

                            </td>
                            <?php if($showArea ?? false): ?>
                            <td><?php echo e($sale->customer->area_name ?? '-'); ?></td>
                            <?php endif; ?>
                            <?php if($showSalesman ?? false): ?>
                            <td><?php echo e($sale->salesman->name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td class="text-end"><?php echo e(number_format($sale->nt_amount ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($sale->dis_amount ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($sale->scm_amount ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($sale->tax_amount ?? 0, 2)); ?></td>
                            <td class="text-end fw-bold"><?php echo e(number_format($sale->net_amount ?? 0, 2)); ?></td>
                            <?php if($withAddress ?? false): ?>
                            <td class="small"><?php echo e($sale->customer->address ?? ''); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if(($dayWiseTotal ?? 'N') == 'Y' && $sales->count() > 0): ?>
                            <tr class="table-warning fw-bold">
                                <td colspan="<?php echo e(4 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0)); ?>" class="text-end">
                                    Day Total (<?php echo e(\Carbon\Carbon::parse($currentDate)->format('d-m-Y')); ?>): <?php echo e($dayCount); ?> Bills
                                </td>
                                <td class="text-end" colspan="4"></td>
                                <td class="text-end">₹<?php echo e(number_format($dayTotal, 2)); ?></td>
                                <?php if($withAddress ?? false): ?><td></td><?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="<?php echo e(4 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0)); ?>" class="text-end">
                                Grand Total: <?php echo e(number_format($totals['count'] ?? 0)); ?> Bills
                            </td>
                            <td class="text-end"><?php echo e(number_format($totals['nt_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['dis_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['scm_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['tax_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['net_amount'] ?? 0, 2)); ?></td>
                            <?php if($withAddress ?? false): ?><td></td><?php endif; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Sync report type radio buttons with hidden field
document.querySelectorAll('input[name="report_type_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_type').value = this.value;
    });
});

// Party code to customer select sync
document.querySelector('input[name="party_code"]').addEventListener('change', function() {
    const code = this.value.toUpperCase();
    const select = document.getElementById('customerSelect');
    for (let option of select.options) {
        if (option.dataset.code === code) {
            select.value = option.value;
            break;
        }
    }
});

// Customer select to party code sync
document.getElementById('customerSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    document.querySelector('input[name="party_code"]').value = selectedOption.dataset.code || '';
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('<?php echo e(route("admin.reports.sales.sales-book")); ?>?' + params.toString(), '_blank');
}

function stateWiseSale() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('group_by', 'state');
    window.location.href = '<?php echo e(route("admin.reports.sales.sales-book")); ?>?' + params.toString();
}

function printReport() {
    window.open('<?php echo e(route("admin.reports.sales.sales-book")); ?>?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '<?php echo e(route("admin.reports.sales")); ?>';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/reports/sale-report/sale-book/sales-book.blade.php ENDPATH**/ ?>