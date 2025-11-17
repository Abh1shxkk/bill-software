@extends('layouts.admin')
@section('title', 'Item Details')
@section('content')
@php
    // Use latest batch data if available, otherwise fallback to item master
    $useLatestBatch = isset($latestBatch) && $latestBatch !== null;
    
    // Get rates from latest batch or item master
    $sRate = $useLatestBatch ? ($latestBatch->s_rate ?? 0) : ($item->s_rate ?? 0);
    $wsRate = $useLatestBatch ? ($latestBatch->ws_rate ?? 0) : ($item->ws_rate ?? 0);
    $splRate = $useLatestBatch ? ($latestBatch->spl_rate ?? 0) : ($item->spl_rate ?? 0);
    $purRate = $useLatestBatch ? ($latestBatch->pur_rate ?? 0) : ($item->pur_rate ?? 0);
    $mrp = $useLatestBatch ? ($latestBatch->mrp ?? 0) : ($item->mrp ?? 0);
    $cost = $useLatestBatch ? ($latestBatch->cost ?? 0) : ($item->cost ?? 0);
    
    // Calculate GST percentage from latest batch or HSN code fields
    if ($useLatestBatch) {
        $gstPercent = ($latestBatch->cgst_percent ?? 0) + ($latestBatch->sgst_percent ?? 0) + ($latestBatch->cess_percent ?? 0);
    } else {
        $gstPercent = ($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0) + ($item->cess_percent ?? 0);
        // If IGST is used instead of CGST+SGST, use IGST
        if (($item->igst_percent ?? 0) > 0) {
            $gstPercent = ($item->igst_percent ?? 0) + ($item->cess_percent ?? 0);
        }
    }
    
    // Calculate F.T. Rates using formula: FT = Rate × (1 + GST/100)
    $ftRateSRate = $sRate * (1 + ($gstPercent / 100));
    $ftRateWSRate = $wsRate * (1 + ($gstPercent / 100));
    $ftRateSPLRate = $splRate * (1 + ($gstPercent / 100));
    $ftRatePurRate = $purRate * (1 + ($gstPercent / 100));
    
    // Calculate Cost + GST
    $costPlusGst = $cost * (1 + ($gstPercent / 100));
    
    // Calculate Margin % and Markup %
    $marginPercent = 0;
    $markupPercent = 0;
    if ($sRate > 0 && $cost > 0) {
        $marginPercent = (($sRate - $cost) / $sRate) * 100;
        $markupPercent = (($sRate - $cost) / $cost) * 100;
    }
    
    // Calculate Total Units from batches table (actual available stock)
    // This shows current available quantity after sales
    $totalUnits = $item->getTotalQuantity(); // Uses batches table: sum(total_qty) where is_deleted = 0
    $packUnits = $totalUnits; // Pack Units same as Total Units
    $looseUnits = 0; // Loose Units (can be calculated if needed)
    $unitsInPack = 1; // Always 1 as per requirement
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted small">Detailed item record</div>
            <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-seam me-2"></i> Item Details - {{ $item->name }}</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.batches.index', ['item_id' => $item->id]) }}" class="btn btn-info">
                <i class="bi bi-boxes me-1"></i>Batches (F5)
            </a>
            <a href="{{ route('admin.items.stock-ledger-complete', $item->id) }}" class="btn btn-warning">
                <i class="bi bi-graph-up me-1"></i>Stock Ledger (F10)
            </a>
            <a href="{{ route('admin.items.pending-orders', $item->id) }}" class="btn btn-outline-warning">
                <i class="bi bi-hourglass-split me-1"></i>Pending Order (F7)
            </a>
            <a href="{{ route('admin.items.godown-expiry', $item->id) }}" class="btn btn-outline-danger">
                <i class="bi bi-calendar-x me-1"></i>Godown Expiry
            </a>
            <a href="{{ route('admin.items.expiry-ledger', $item->id) }}" class="btn btn-outline-info">
                <i class="bi bi-file-text me-1"></i>Expiry Ledger (F5)
            </a>
            <a href="{{ route('admin.batches.all', ['item_id' => $item->id]) }}" class="btn btn-outline-info">
                <i class="bi bi-collection me-1"></i>All Batches
            </a>
            <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit Item
            </a>
            <a href="{{ route('admin.items.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>Back to Items
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-card mb-5">
                        <div class="section-header mb-4">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            <h5 class="mb-0">Header Section</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Name:</label>
                                    <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Company:</label>
                                    <div class="fw-semibold">{{ $item->company->name ?? '-' }} ({{ $item->company->short_name ?? '-' }})</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Company Short Name:</label>
                                    <div class="fw-semibold">{{ $item->company_short_name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Packing:</label>
                                    <div class="fw-semibold">{{ $item->packing ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Unit:</label>
                                    <div class="fw-semibold">{{ $item->unit ?? '1' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Unit Type:</label>
                                    <div class="fw-semibold">{{ $item->unit_type ?? 'Unit' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Mfg. By:</label>
                                    <div class="fw-semibold">{{ $item->mfg_by ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Location:</label>
                                    <div class="fw-semibold">{{ $item->location ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Status:</label>
                                    <div class="fw-semibold">{{ $item->status ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Schedule:</label>
                                    <div class="fw-semibold">{{ $item->schedule ?? '00' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Division:</label>
                                    <div class="fw-semibold">{{ $item->division ?? '00' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Box Qty:</label>
                                    <div class="fw-semibold">{{ $item->box_qty ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Case Qty:</label>
                                    <div class="fw-semibold">{{ $item->case_qty ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Min. Level:</label>
                                    <div class="fw-semibold">{{ $item->min_level ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Max. Level:</label>
                                    <div class="fw-semibold">{{ $item->max_level ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Bar Code:</label>
                                    <div class="fw-semibold">{{ $item->bar_code ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Flag:</label>
                                    <div class="fw-semibold">{{ $item->flag ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Narcotic:</label>
                                    <div>
                                        <span class="badge {{ $item->narcotic_flag == 'Y' ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                            {{ $item->narcotic_flag == 'Y' ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Details Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-card mb-5">
                        <div class="section-header mb-4">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            <h5 class="mb-0">Sale Details</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">S. Rate:</label>
                                    <div class="fw-semibold text-success">₹{{ number_format($sRate, 2) }}</div>
                                    <div class="fw-semibold text-primary small mt-1">F.T. Rate: ₹{{ number_format($ftRateSRate, 2) }}</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">MRP (Net):</label>
                                    <div class="fw-semibold text-success">₹{{ number_format($mrp, 2) }}</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Min.GP:</label>
                                    <div class="fw-semibold">{{ number_format($item->min_gp ?? 0, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">W.S.Rate:</label>
                                    <div class="fw-semibold text-success">
                                        ₹{{ number_format($wsRate, 2) }}
                                        <span class="badge {{ $item->ws_net_toggle == 'Y' ? 'bg-success' : 'bg-secondary' }} ms-2 px-2 py-1 rounded-pill">
                                            {{ $item->ws_net_toggle ?? 'Y' }}
                                        </span>
                                    </div>
                                    <div class="fw-semibold text-primary small mt-1">F.T. Rate: ₹{{ number_format($ftRateWSRate, 2) }} N</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Spl.Rate:</label>
                                    <div class="fw-semibold text-success">
                                        ₹{{ number_format($splRate, 2) }}
                                        <span class="badge {{ $item->spl_net_toggle == 'Y' ? 'bg-success' : 'bg-secondary' }} ms-2 px-2 py-1 rounded-pill">
                                            {{ $item->spl_net_toggle ?? 'Y' }}
                                        </span>
                                    </div>
                                    <div class="fw-semibold text-primary small mt-1">F.T. Rate: ₹{{ number_format($ftRateSPLRate, 2) }} N</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Scheme:</label>
                                    <div class="fw-semibold">{{ $item->scheme_plus ?? 0 }} + {{ $item->scheme_minus ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Details Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-card mb-5">
                        <div class="section-header mb-4">
                            <i class="bi bi-cart text-warning me-2"></i>
                            <h5 class="mb-0">Purchase Details</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Pur. Rate:</label>
                                    <div class="fw-semibold text-warning">₹{{ number_format($purRate, 2) }}</div>
                                    <div class="fw-semibold text-primary small mt-1">F.T. Rate: ₹{{ number_format($ftRatePurRate, 2) }}</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Cost:</label>
                                    <div class="fw-semibold text-warning">₹{{ number_format($cost, 2) }}</div>
                                    @if($useLatestBatch)
                                        <div class="text-muted small">From Latest Batch</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">NR:</label>
                                    <div class="fw-semibold text-warning">₹{{ number_format($item->nr ?? 0, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Scheme:</label>
                                    <div class="fw-semibold">{{ $item->pur_scheme_plus ?? 0 }} + {{ $item->pur_scheme_minus ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GST Details Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-card mb-5">
                        <div class="section-header mb-4">
                            <i class="bi bi-percent text-danger me-2"></i>
                            <h5 class="mb-0">GST Details</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">HSN Code:</label>
                                    <div class="fw-semibold">{{ $item->hsn_code ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">CGST (%):</label>
                                    <div class="fw-semibold">{{ number_format($item->cgst_percent ?? 0, 2) }}%</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">SGST (%):</label>
                                    <div class="fw-semibold">{{ number_format($item->sgst_percent ?? 0, 2) }}%</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">IGST (%):</label>
                                    <div class="fw-semibold">{{ number_format($item->igst_percent ?? 0, 2) }}%</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Cess (%):</label>
                                    <div class="fw-semibold">{{ number_format($item->cess_percent ?? 0, 2) }}%</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Cost + GST:</label>
                                    <div class="fw-semibold text-danger">₹{{ number_format($costPlusGst, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Margin (%):</label>
                                    <div class="fw-semibold text-danger">{{ number_format($marginPercent, 2) }}%</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Markup (%):</label>
                                    <div class="fw-semibold text-danger">{{ number_format($markupPercent, 2) }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-card mb-5">
                        <div class="section-header mb-4">
                            <i class="bi bi-box text-dark me-2"></i>
                            <h5 class="mb-0">Bottom Section</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Commodity:</label>
                                    <div class="fw-semibold">{{ $item->commodity ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Current Scheme:</label>
                                    <div>
                                        <span class="badge {{ $item->current_scheme_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                            {{ $item->current_scheme_flag ?? 'N' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Scheme:</label>
                                    <div class="fw-semibold">{{ $item->scheme_plus_value ?? 0 }} + {{ $item->scheme_minus_value ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">From:</label>
                                    <div class="fw-semibold">{{ $item->from_date ? \Carbon\Carbon::parse($item->from_date)->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">To:</label>
                                    <div class="fw-semibold">{{ $item->to_date ? \Carbon\Carbon::parse($item->to_date)->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Category:</label>
                                    <div class="fw-semibold">{{ $item->category ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">Category 2:</label>
                                    <div class="fw-semibold">{{ $item->category_2 ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1">UPC:</label>
                                    <div class="fw-semibold">{{ $item->upc ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Other Details Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-header mb-4">
                        <i class="bi bi-sliders text-primary me-2"></i>
                        <h5 class="mb-0">Other Details</h5>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label class="text-muted small mb-1">VAT(%):</label>
                            <div class="fw-semibold">{{ number_format($item->vat_percent ?? 0, 2) }}%</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">GST(%):</label>
                            <div class="fw-semibold text-primary">{{ number_format($gstPercent, 3) }}%</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Fixed Dis. (Y/N/M):</label>
                            <div class="fw-semibold">{{ $item->fixed_dis ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Fixed Dis %:</label>
                            <div class="fw-semibold">{{ number_format($item->fixed_dis_percent ?? 0, 2) }}%</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Fixed Dis Type:</label>
                            <div class="fw-semibold">{{ $item->fixed_dis_type ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Expiry:</label>
                            <div>
                                <span class="badge {{ $item->expiry_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->expiry_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Inclusive:</label>
                            <div>
                                <span class="badge {{ $item->inclusive_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->inclusive_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Generic:</label>
                            <div>
                                <span class="badge {{ $item->generic_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->generic_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Bar Code:</label>
                            <div>
                                <span class="badge {{ $item->bar_code_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->bar_code_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">H.Scm:</label>
                            <div>
                                <span class="badge {{ $item->h_scm_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->h_scm_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Q.Scm:</label>
                            <div>
                                <span class="badge {{ $item->q_scm_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->q_scm_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Def. Qty:</label>
                            <div>
                                <span class="badge {{ $item->def_qty_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->def_qty_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Comp.Name (BC):</label>
                            <div>
                                <span class="badge {{ $item->comp_name_bc_new == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->comp_name_bc_new ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">DPC Item:</label>
                            <div>
                                <span class="badge {{ $item->dpc_item_flag == 'Y' ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->dpc_item_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Lock Sale:</label>
                            <div>
                                <span class="badge {{ $item->lock_sale_flag == 'Y' ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                                    {{ $item->lock_sale_flag ?? 'N' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Locks:</label>
                            <div class="fw-semibold">{{ $item->locks_flag ?? 'S' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">1.(Max) 2.(Min):</label>
                            <div class="fw-semibold">{{ $item->max_min_flag ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Max Inv.Qty:</label>
                            <div class="fw-semibold">{{ number_format($item->max_inv_qty_value ?? 0, 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Max Inv. Qty Type:</label>
                            <div class="fw-semibold">{{ $item->max_inv_qty_new ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Weight:</label>
                            <div class="fw-semibold">{{ number_format($item->weight_new ?? 0, 3) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Volume:</label>
                            <div class="fw-semibold">{{ number_format($item->volume_new ?? 0, 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">MRP for Sale:</label>
                            <div class="fw-semibold text-success">₹{{ number_format($item->mrp_for_sale_new ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Units Section -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="section-header mb-4">
                        <i class="bi bi-box-seam text-info me-2"></i>
                        <h5 class="mb-0">Units</h5>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Total Units:</label>
                            <div class="fw-semibold text-purple">{{ number_format($totalUnits, 0) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Pack Units:</label>
                            <div class="fw-semibold text-purple">{{ number_format($packUnits, 0) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Loose Units:</label>
                            <div class="fw-semibold text-purple">{{ number_format($looseUnits, 0) }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Units In Pack:</label>
                            <div class="fw-semibold text-purple">{{ number_format($unitsInPack, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-4">
                    <div class="section-header mb-4">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        <h5 class="mb-0">System Information</h5>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Created At:</label>
                            <div class="fw-semibold">{{ $item->created_at ? $item->created_at->format('M d, Y H:i:s') : '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Updated At:</label>
                            <div class="fw-semibold">{{ $item->updated_at ? $item->updated_at->format('M d, Y H:i:s') : '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted small mb-1">Status:</label>
                            <div>
                                <span class="badge {{ $item->is_deleted == 0 ? 'bg-success' : 'bg-danger' }} px-2 py-1 rounded-pill">
                                    {{ $item->is_deleted == 0 ? 'Active' : 'Deleted' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .section-card {
        position: relative;
    }

    .section-header {
        display: flex;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }

    .detail-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item label {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        color: #6c757d;
    }

    .detail-item .fw-semibold {
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .detail-grid .detail-item {
        padding: 0.5rem 0;
    }

    .rounded-lg {
        border-radius: 0.75rem !important;
    }

    .card {
        border: none;
    }

    .text-purple {
        color: #6f42c1 !important;
    }
</style>
@endsection