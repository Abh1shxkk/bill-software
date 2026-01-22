{{-- 
    Receipt OCR Preview Partial
    Features: Popup Preview, Zoom, Area Selection, OCR Text Extraction, Item Matching
    Version: 1.0.0
    
    Include this partial in any blade file that needs OCR functionality:
    @include('admin.sale.partials.receipt-ocr-preview')
--}}

<script>
/**
 * Receipt OCR Preview Module
 * Features: Popup Preview, Zoom, Area Selection, OCR Text Extraction, Item Matching
 */

class ReceiptOCRPreview {
    constructor(options = {}) {
        this.options = {
            ocrApiUrl: options.ocrApiUrl || '{{ route("admin.api.ocr.extract") }}',
            itemSearchUrl: options.itemSearchUrl || '{{ route("admin.api.ocr.search-items") }}',
            batchApiUrl: options.batchApiUrl || '{{ url("admin/api/item-batches") }}',
            csrfToken: options.csrfToken || '{{ csrf_token() }}',
            ...options
        };

        this.modal = null;
        this.canvas = null;
        this.ctx = null;
        this.image = null;
        this.imageData = null;

        // Zoom state
        this.scale = 1;
        this.minScale = 0.5;
        this.maxScale = 4;
        this.offsetX = 0;
        this.offsetY = 0;

        // Selection state
        this.isSelecting = false;
        this.selectionStart = { x: 0, y: 0 };
        this.selectionEnd = { x: 0, y: 0 };
        this.currentSelection = null;

        // Pan state
        this.isPanning = false;
        this.panStart = { x: 0, y: 0 };
        this.mode = 'select'; // 'select' or 'pan'

        // Extracted items
        this.extractedItems = [];
        
        // Batch data for items
        this.itemBatches = {}; // { itemId: [batches] }
        this.selectedBatches = {}; // { itemIndex: batchData }

        this.init();
    }

    init() {
        this.createModal();
        this.bindEvents();
    }

    createModal() {
        // Remove existing modal if any
        const existingModal = document.getElementById('receiptOCRModal');
        if (existingModal) existingModal.remove();

        const modalHTML = `
            <div id="receiptOCRModal" class="receipt-ocr-modal" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999999 !important; isolation: isolate !important;">
                <div class="receipt-ocr-backdrop" style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 1 !important; background: rgba(0,0,0,0.9) !important;"></div>
                <div class="receipt-ocr-container" style="position: relative !important; z-index: 2 !important;">
                    <!-- Header -->
                    <div class="receipt-ocr-header">
                        <div class="receipt-ocr-title">
                            <i class="bi bi-receipt-cutoff"></i>
                            <span>Receipt OCR Preview</span>
                        </div>
                        <div class="receipt-ocr-toolbar">
                            <div class="toolbar-group">
                                <button type="button" class="toolbar-btn" id="ocrZoomOut" title="Zoom Out">
                                    <i class="bi bi-zoom-out"></i>
                                </button>
                                <span class="zoom-level" id="ocrZoomLevel">100%</span>
                                <button type="button" class="toolbar-btn" id="ocrZoomIn" title="Zoom In">
                                    <i class="bi bi-zoom-in"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrZoomFit" title="Fit to Screen">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="toolbar-btn active" id="ocrModeSelect" title="Selection Mode">
                                    <i class="bi bi-bounding-box"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrModePan" title="Pan Mode">
                                    <i class="bi bi-arrows-move"></i>
                                </button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="toolbar-btn" id="ocrRotateLeft15" title="Rotate Left 15°">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrRotateLeft5" title="Rotate Left 5°">
                                    <i class="bi bi-arrow-counterclockwise" style="font-size:12px;"></i>
                                </button>
                                <span class="rotation-angle-display" id="ocrRotationDisplay" title="Current Rotation" style="display: inline-block; min-width: 40px; text-align: center; font-size: 13px; font-weight: 500; color: #6c757d; padding: 0 8px;">0°</span>
                                <button type="button" class="toolbar-btn" id="ocrRotateRight5" title="Rotate Right 5°">
                                    <i class="bi bi-arrow-clockwise" style="font-size:12px;"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrRotateRight15" title="Rotate Right 15°">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrRotateReset" title="Reset Rotation">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="toolbar-btn" id="ocrAutoStraighten" title="Auto Straighten (Python/OpenCV)">
                                    <i class="bi bi-magic"></i>
                                </button>
                                <button type="button" class="toolbar-btn" id="ocrApplyStraighten" title="Apply Straightening">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="receipt-ocr-close" id="ocrCloseBtn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="receipt-ocr-body">
                        <!-- Left: Image Preview -->
                        <div class="receipt-ocr-preview-area">
                            <div class="receipt-ocr-canvas-container" id="ocrCanvasContainer">
                                <canvas id="ocrCanvas"></canvas>
                                <div class="selection-overlay" id="ocrSelectionOverlay" style="display: none;"></div>
                                <div class="ocr-processing-overlay" id="ocrProcessingOverlay" style="display: none;">
                                    <div class="processing-content">
                                        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"></div>
                                        <p id="ocrProcessingText">Straightening receipt...</p>
                                        <small id="ocrProcessingSubtext">Detecting tilt angle</small>
                                    </div>
                                </div>
                            </div>
                            <div class="receipt-ocr-instructions">
                                <i class="bi bi-info-circle"></i>
                                <span>Draw a rectangle around the text you want to extract. Use scroll wheel or +/- buttons to zoom.</span>
                            </div>
                        </div>
                        
                        <!-- Right: OCR Results -->
                        <div class="receipt-ocr-results-area">
                            <div class="ocr-results-panel">
                                <div class="panel-header">
                                    <h6><i class="bi bi-text-paragraph"></i> Extracted Text</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="ocrExtractBtn" disabled>
                                        <i class="bi bi-cpu"></i> Extract Text
                                    </button>
                                </div>
                                <div class="extracted-text-area" id="ocrExtractedText">
                                    <div class="empty-state">
                                        <i class="bi bi-textarea-resize"></i>
                                        <p>Select an area on the receipt to extract text</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ocr-items-panel">
                                <div class="panel-header">
                                    <h6><i class="bi bi-box-seam"></i> Matched Items</h6>
                                    <span class="badge bg-secondary" id="ocrMatchedCount">0</span>
                                </div>
                                <div class="ocr-search-box" style="padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                    <div style="position: relative;">
                                        <input type="text" id="ocrItemSearch" class="form-control form-control-sm" placeholder="🔍 Search in matched items..." style="padding-left: 10px; border-radius: 20px; font-size: 12px;">
                                        <button type="button" id="ocrClearSearch" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: none; background: transparent; color: #adb5bd; cursor: pointer; display: none;" title="Clear search">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="matched-items-area" id="ocrMatchedItems">
                                    <div class="empty-state">
                                        <i class="bi bi-search"></i>
                                        <p>Items matching extracted text will appear here</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ocr-actions">
                                <button type="button" class="btn btn-secondary" id="ocrCancelBtn">
                                    <i class="bi bi-x"></i> Cancel
                                </button>
                                <button type="button" class="btn btn-success" id="ocrAddItemsBtn" disabled>
                                    <i class="bi bi-plus-circle"></i> Add Selected Items
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('receiptOCRModal');
        this.canvas = document.getElementById('ocrCanvas');
        this.ctx = this.canvas.getContext('2d');

        this.injectStyles();
    }

    injectStyles() {
        if (document.getElementById('receiptOCRStyles')) return;

        const styles = `
            <style id="receiptOCRStyles">
                .receipt-ocr-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 2147483647 !important;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                
                .receipt-ocr-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.85);
                    backdrop-filter: blur(5px);
                }
                
                .receipt-ocr-container {
                    position: relative;
                    width: 95%;
                    height: 95%;
                    max-width: 1400px;
                    margin: 2.5% auto;
                    background: #f8f9fa;
                    border-radius: 12px;
                    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                }
                
                .receipt-ocr-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 20px;
                    background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                    color: white;
                    border-bottom: 1px solid rgba(255,255,255,0.1);
                }
                
                .receipt-ocr-title {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 18px;
                    font-weight: 600;
                }
                
                .receipt-ocr-title i {
                    font-size: 22px;
                }
                
                .receipt-ocr-toolbar {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: rgba(255,255,255,0.15);
                    padding: 6px 12px;
                    border-radius: 8px;
                }
                
                .toolbar-group {
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }
                
                .toolbar-btn {
                    width: 36px;
                    height: 36px;
                    border: none;
                    background: transparent;
                    color: white;
                    border-radius: 6px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    transition: all 0.2s ease;
                }
                
                .toolbar-btn:hover {
                    background: rgba(255,255,255,0.2);
                }
                
                .toolbar-btn.active {
                    background: rgba(255,255,255,0.3);
                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
                }
                
                .zoom-level {
                    min-width: 50px;
                    text-align: center;
                    font-size: 12px;
                    font-weight: 600;
                }
                
                .toolbar-divider {
                    width: 1px;
                    height: 24px;
                    background: rgba(255,255,255,0.3);
                    margin: 0 8px;
                }
                
                .receipt-ocr-close {
                    width: 36px;
                    height: 36px;
                    border: none;
                    background: rgba(255,255,255,0.1);
                    color: white;
                    border-radius: 50%;
                    cursor: pointer;
                    font-size: 18px;
                    transition: all 0.2s ease;
                }
                
                .receipt-ocr-close:hover {
                    background: #dc3545;
                    transform: rotate(90deg);
                }
                
                .receipt-ocr-body {
                    flex: 1;
                    display: flex;
                    overflow: hidden;
                }
                
                .receipt-ocr-preview-area {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    background: #1a1a2e;
                    position: relative;
                }
                
                .receipt-ocr-canvas-container {
                    flex: 1;
                    overflow: hidden;
                    position: relative;
                    cursor: crosshair;
                }
                
                .receipt-ocr-canvas-container.pan-mode {
                    cursor: grab;
                }
                
                .receipt-ocr-canvas-container.pan-mode:active {
                    cursor: grabbing;
                }
                
                #ocrCanvas {
                    display: block;
                }
                
                .selection-overlay {
                    position: absolute;
                    border: 2px dashed #00ff88;
                    background: rgba(0, 255, 136, 0.1);
                    box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
                    pointer-events: none;
                }
                
                .receipt-ocr-instructions {
                    padding: 10px 20px;
                    background: rgba(0,0,0,0.5);
                    color: #aaa;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .receipt-ocr-results-area {
                    width: 380px;
                    display: flex;
                    flex-direction: column;
                    background: white;
                    border-left: 1px solid #dee2e6;
                }
                
                .ocr-results-panel, .ocr-items-panel {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    border-bottom: 1px solid #dee2e6;
                    overflow: hidden;
                }
                
                .panel-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 16px;
                    background: #f8f9fa;
                    border-bottom: 1px solid #dee2e6;
                }
                
                .panel-header h6 {
                    margin: 0;
                    font-size: 14px;
                    font-weight: 600;
                    color: #333;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .extracted-text-area, .matched-items-area {
                    flex: 1;
                    overflow-y: auto;
                    padding: 12px;
                }
                
                .empty-state {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: #aaa;
                    text-align: center;
                    padding: 20px;
                }
                
                .empty-state i {
                    font-size: 48px;
                    margin-bottom: 12px;
                    opacity: 0.5;
                }
                
                .empty-state p {
                    margin: 0;
                    font-size: 13px;
                }
                
                .extracted-text-content {
                    background: #f8f9fa;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 12px;
                    font-family: 'Consolas', 'Monaco', monospace;
                    font-size: 12px;
                    line-height: 1.6;
                    white-space: pre-wrap;
                    word-break: break-word;
                    max-height: 100%;
                    overflow-y: auto;
                }
                
                .matched-item-card {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    padding: 10px 12px;
                    background: #fff;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    margin-bottom: 8px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                
                .matched-item-card:hover {
                    border-color: #6f42c1;
                    background: #f8f5fc;
                }
                
                .matched-item-card.selected {
                    border-color: #28a745;
                    background: #f0fff4;
                    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
                }
                
                .matched-item-checkbox {
                    width: 20px;
                    height: 20px;
                    border-radius: 4px;
                    cursor: pointer;
                }
                
                .matched-item-info {
                    flex: 1;
                    min-width: 0;
                }
                
                .matched-item-name {
                    font-size: 13px;
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 2px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                
                .matched-item-details {
                    font-size: 11px;
                    color: #666;
                    display: flex;
                    gap: 8px;
                    flex-wrap: wrap;
                }
                
                .matched-item-price {
                    font-size: 14px;
                    font-weight: 700;
                    color: #28a745;
                    white-space: nowrap;
                }
                
                .ocr-actions {
                    padding: 12px 16px;
                    background: #f8f9fa;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                
                .ocr-loading {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: #6f42c1;
                }
                
                .ocr-loading .spinner-border {
                    width: 40px;
                    height: 40px;
                    margin-bottom: 12px;
                }
                
                .ocr-loading p {
                    margin: 0;
                    font-size: 14px;
                }
                
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
                
                .selection-active .selection-overlay {
                    animation: pulse 1s infinite;
                }
                
                /* Batch Selection Styles */
                .batch-selection-wrapper {
                    margin-top: 8px;
                    padding: 8px;
                    background: #f0f4ff;
                    border-radius: 6px;
                    border: 1px solid #d0d8ff;
                }
                
                .batch-selection-label {
                    font-size: 11px;
                    font-weight: 600;
                    color: #555;
                    margin-bottom: 4px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }
                
                .batch-select-dropdown {
                    width: 100%;
                    padding: 6px 10px;
                    font-size: 12px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    background: white;
                    cursor: pointer;
                }
                
                .batch-select-dropdown:focus {
                    border-color: #6f42c1;
                    outline: none;
                    box-shadow: 0 0 0 2px rgba(111, 66, 193, 0.2);
                }
                
                .batch-info-display {
                    margin-top: 6px;
                    padding: 6px 8px;
                    background: white;
                    border-radius: 4px;
                    font-size: 11px;
                    color: #444;
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 4px;
                }
                
                .batch-info-item {
                    display: flex;
                    gap: 4px;
                }
                
                .batch-info-item label {
                    color: #888;
                    font-weight: 500;
                }
                
                .batch-info-item span {
                    font-weight: 600;
                    color: #333;
                }
                
                .batch-loading {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    color: #6f42c1;
                    font-size: 11px;
                    padding: 8px;
                }
                
                .batch-loading .spinner-border-sm {
                    width: 14px;
                    height: 14px;
                }
                
                .no-batches-warning {
                    color: #dc3545;
                    font-size: 11px;
                    padding: 6px 8px;
                    background: #fff5f5;
                    border-radius: 4px;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                
                .matched-item-card.needs-batch {
                    border-color: #ffc107;
                    background: #fffef0;
                }
                
                .matched-item-card.batch-selected {
                    border-color: #28a745;
                    background: #f0fff4;
                }
                
                /* Processing Overlay Styles */
                .ocr-processing-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(26, 26, 46, 0.95);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 100;
                }
                
                .ocr-processing-overlay .processing-content {
                    text-align: center;
                    color: white;
                }
                
                .ocr-processing-overlay .processing-content p {
                    margin: 16px 0 8px 0;
                    font-size: 16px;
                    font-weight: 600;
                }
                
                .ocr-processing-overlay .processing-content small {
                    color: #aaa;
                    font-size: 12px;
                }
                
                .toolbar-btn.processing {
                    animation: spin 1s linear infinite;
                }
                
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                
                .straighten-success-toast {
                    position: absolute;
                    bottom: 60px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    padding: 10px 20px;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
                    animation: slideUp 0.3s ease, fadeOut 0.3s ease 2.5s forwards;
                    z-index: 200;
                }
                
                @keyframes slideUp {
                    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
                    to { opacity: 1; transform: translateX(-50%) translateY(0); }
                }
                
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindEvents() {
        // Close buttons
        document.getElementById('ocrCloseBtn').addEventListener('click', () => this.close());
        document.getElementById('ocrCancelBtn').addEventListener('click', () => this.close());
        document.querySelector('.receipt-ocr-backdrop').addEventListener('click', () => this.close());

        // Zoom controls
        document.getElementById('ocrZoomIn').addEventListener('click', () => this.zoom(0.25));
        document.getElementById('ocrZoomOut').addEventListener('click', () => this.zoom(-0.25));
        document.getElementById('ocrZoomFit').addEventListener('click', () => this.fitToScreen());

        // Mode controls
        document.getElementById('ocrModeSelect').addEventListener('click', () => this.setMode('select'));
        document.getElementById('ocrModePan').addEventListener('click', () => this.setMode('pan'));

        // Clear selection
        document.getElementById('ocrClearSelection')?.addEventListener('click', () => this.clearSelection());

        // Manual rotation buttons
        document.getElementById('ocrRotateLeft15')?.addEventListener('click', () => this.rotateImage(15));
        document.getElementById('ocrRotateLeft5')?.addEventListener('click', () => this.rotateImage(5));
        document.getElementById('ocrRotateRight5')?.addEventListener('click', () => this.rotateImage(-5));
        document.getElementById('ocrRotateRight15')?.addEventListener('click', () => this.rotateImage(-15));
        document.getElementById('ocrRotateReset')?.addEventListener('click', () => this.resetRotation());
        
        // Auto-straighten button (if exists)
        document.getElementById('ocrAutoStraighten')?.addEventListener('click', () => this.straightenImage());
        
        // Apply straightening button - saves current rotation
        document.getElementById('ocrApplyStraighten')?.addEventListener('click', () => this.applyStraightening());

        // Extract text button
        document.getElementById('ocrExtractBtn').addEventListener('click', () => this.extractText());

        // Add items button
        document.getElementById('ocrAddItemsBtn').addEventListener('click', () => this.addSelectedItems());

        // Canvas events
        const container = document.getElementById('ocrCanvasContainer');

        container.addEventListener('mousedown', (e) => this.handleMouseDown(e));
        container.addEventListener('mousemove', (e) => this.handleMouseMove(e));
        container.addEventListener('mouseup', (e) => this.handleMouseUp(e));
        container.addEventListener('mouseleave', (e) => this.handleMouseUp(e));
        container.addEventListener('wheel', (e) => this.handleWheel(e), { passive: false });

        // Touch events for mobile
        container.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: false });
        container.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
        container.addEventListener('touchend', (e) => this.handleTouchEnd(e));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (this.modal.style.display === 'none') return;

            if (e.key === 'Escape') {
                this.close();
            } else if (e.key === '+' || e.key === '=') {
                this.zoom(0.25);
            } else if (e.key === '-') {
                this.zoom(-0.25);
            } else if (e.key === '0') {
                this.fitToScreen();
            }
        });

        // Live search in matched items
        const searchInput = document.getElementById('ocrItemSearch');
        const clearBtn = document.getElementById('ocrClearSearch');
        let searchTimeout = null;
        
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();
            
            // Show/hide clear button
            clearBtn.style.display = query.length > 0 ? 'block' : 'none';
            
            // Debounce for better performance
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filterMatchedItems(query);
            }, 150);
        });
        
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            this.filterMatchedItems('');
            searchInput.focus();
        });
    }

    open(imageData) {
        this.imageData = imageData;
        this.modal.style.display = 'block';

        // Reset state
        this.scale = 1;
        this.offsetX = 0;
        this.offsetY = 0;
        this.clearSelection();
        this.resetResults();

        // Load image
        this.loadImage(imageData);
    }

    loadImage(imageData, skipStraighten = false) {
        // Store original image data for re-processing
        this.originalImageData = imageData;
        
        // Convert to proper format first
        let imageSrc = imageData;
        if (typeof imageData === 'string') {
            if (!imageData.startsWith('data:')) {
                imageSrc = 'data:image/jpeg;base64,' + imageData;
            }
        } else if (imageData instanceof Blob || imageData instanceof File) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.loadImage(e.target.result, skipStraighten);
            };
            reader.readAsDataURL(imageData);
            return;
        }

        // Reset rotation tracking
        this.currentRotation = 0;
        this.updateRotationDisplay();

        // Auto-crop receipt by default (unless explicitly skipped)
        if (!skipStraighten) {
            this.autoStraightenImage(imageSrc);
        } else {
            // Load image directly without processing
            this.loadImageDirect(imageSrc);
        }
    }

    /**
     * Auto-straighten image on initial load
     */
    async autoStraightenImage(imageData) {
        const overlay = document.getElementById('ocrProcessingOverlay');
        const processingText = document.getElementById('ocrProcessingText');
        const processingSubtext = document.getElementById('ocrProcessingSubtext');
        
        try {
            // Show processing overlay
            overlay.style.display = 'flex';
            processingText.textContent = 'Analyzing receipt...';
            processingSubtext.textContent = 'Detecting tilt angle';
            
            // Call the straighten API
            const response = await fetch('{{ route("admin.api.ocr.straighten-image") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ image: imageData })
            });

            const result = await response.json();
            
            if (result.success && result.processed_image) {
                // Update processing text
                processingText.textContent = 'Processing complete!';
                processingSubtext.textContent = result.was_processed 
                    ? `Straightened by ${Math.abs(result.tilt_angle || 0).toFixed(1)}°`
                    : 'Image is already straight';
                
                // Short delay to show the message
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Load the processed image
                this.image = new Image();
                this.image.onload = () => {
                    overlay.style.display = 'none';
                    this.fitToScreen();
                    this.render();
                    
                    // Show success toast if image was straightened
                    if (result.was_processed) {
                        this.showStraightenToast(result.tilt_angle);
                    }
                };
                this.image.onerror = () => {
                    console.error('Failed to load processed image');
                    overlay.style.display = 'none';
                    // Fallback to original
                    this.loadImageDirect(imageData);
                };
                this.image.src = result.processed_image;
            } else {
                console.warn('Straightening failed, using original image:', result.message);
                overlay.style.display = 'none';
                this.loadImageDirect(imageData);
            }
            
        } catch (error) {
            console.error('Auto-straighten error:', error);
            overlay.style.display = 'none';
            // Fallback to original image
            this.loadImageDirect(imageData);
        }
    }

    /**
     * Load image directly without processing
     */
    loadImageDirect(imageData) {
        this.image = new Image();
        this.image.onload = () => {
            this.fitToScreen();
            this.render();
        };
        this.image.src = imageData;
    }

    /**
     * Manual straighten button handler
     */
    async straightenImage() {
        if (!this.originalImageData) return;
        
        const btn = document.getElementById('ocrAutoStraighten');
        const icon = btn.querySelector('i');
        
        // Start spinning animation
        btn.classList.add('processing');
        btn.disabled = true;
        
        // Get current image as base64
        let imageData = this.originalImageData;
        if (typeof imageData === 'string' && !imageData.startsWith('data:')) {
            imageData = 'data:image/jpeg;base64,' + imageData;
        }
        
        await this.autoStraightenImage(imageData);
        
        // Stop spinning
        btn.classList.remove('processing');
        btn.disabled = false;
    }

    /**
     * Show success toast after straightening
     */
    showStraightenToast(angle) {
        const container = document.getElementById('ocrCanvasContainer');
        const existingToast = container.querySelector('.straighten-success-toast');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = 'straighten-success-toast';
        toast.innerHTML = `
            <i class="bi bi-check-circle-fill"></i>
            <span>Image rotated by ${Math.abs(angle || 0).toFixed(1)}°</span>
        `;
        container.appendChild(toast);
        
        // Remove after animation
        setTimeout(() => toast.remove(), 3000);
    }

    /**
     * Manual rotation - rotate image by specified degrees
     * Positive = counter-clockwise, Negative = clockwise
     */
    rotateImage(degrees) {
        if (!this.image) return;
        
        // Track cumulative rotation
        this.currentRotation = (this.currentRotation || 0) + degrees;
        
        // Update rotation display
        this.updateRotationDisplay();
        
        // Create a canvas to rotate the image
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Calculate new dimensions after rotation
        const radians = (Math.abs(this.currentRotation) * Math.PI) / 180;
        const sin = Math.abs(Math.sin(radians));
        const cos = Math.abs(Math.cos(radians));
        const newWidth = this.image.naturalWidth * cos + this.image.naturalHeight * sin;
        const newHeight = this.image.naturalWidth * sin + this.image.naturalHeight * cos;
        
        canvas.width = newWidth;
        canvas.height = newHeight;
        
        // Fill with white background
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Move to center, rotate, draw image
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate((this.currentRotation * Math.PI) / 180);
        ctx.drawImage(this.image, -this.image.naturalWidth / 2, -this.image.naturalHeight / 2);
        
        // Create new image from rotated canvas
        const rotatedImage = new Image();
        rotatedImage.onload = () => {
            this.image = rotatedImage;
            this.fitToScreen();
            this.render();
            this.showStraightenToast(degrees);
        };
        rotatedImage.src = canvas.toDataURL('image/jpeg', 0.95);
    }

    /**
     * Update rotation angle display
     */
    updateRotationDisplay() {
        const display = document.getElementById('ocrRotationDisplay');
        if (display) {
            const angle = this.currentRotation || 0;
            display.textContent = `${angle.toFixed(1)}°`;
            display.style.color = angle === 0 ? '#6c757d' : '#0d6efd';
            display.style.fontWeight = angle === 0 ? 'normal' : 'bold';
        }
    }

    /**
     * Reset rotation to original image
     */
    resetRotation() {
        this.currentRotation = 0;
        this.updateRotationDisplay();
        if (this.originalImageData) {
            // Reload original image without straightening
            this.loadImage(this.originalImageData, true);
        }
    }

    /**
     * Apply current rotation permanently
     * This makes the current rotated image the new "original"
     */
    applyStraightening() {
        if (!this.image || !this.currentRotation || this.currentRotation === 0) {
            this.showToast('No rotation to apply', 'warning');
            return;
        }

        // Convert current canvas to data URL
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = this.image.naturalWidth;
        canvas.height = this.image.naturalHeight;
        ctx.drawImage(this.image, 0, 0);
        
        const newImageData = canvas.toDataURL('image/jpeg', 0.95);
        
        // Update original image data
        this.originalImageData = newImageData;
        
        // Reset rotation counter
        const appliedRotation = this.currentRotation;
        this.currentRotation = 0;
        this.updateRotationDisplay();
        
        this.showToast(`Applied ${appliedRotation.toFixed(1)}° rotation`, 'success');
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed`;
        toast.style.cssText = 'top: 80px; right: 20px; z-index: 99999; min-width: 250px; animation: slideInRight 0.3s;';
        toast.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            ${message}
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    close() {
        this.modal.style.display = 'none';
        this.clearSelection();
        this.resetResults();
    }

    setMode(mode) {
        this.mode = mode;
        const container = document.getElementById('ocrCanvasContainer');
        const selectBtn = document.getElementById('ocrModeSelect');
        const panBtn = document.getElementById('ocrModePan');

        if (mode === 'select') {
            container.classList.remove('pan-mode');
            selectBtn.classList.add('active');
            panBtn.classList.remove('active');
        } else {
            container.classList.add('pan-mode');
            selectBtn.classList.remove('active');
            panBtn.classList.add('active');
        }
    }

    zoom(delta) {
        const newScale = Math.max(this.minScale, Math.min(this.maxScale, this.scale + delta));

        // Zoom toward center
        const container = document.getElementById('ocrCanvasContainer');
        const centerX = container.clientWidth / 2;
        const centerY = container.clientHeight / 2;

        const factor = newScale / this.scale;
        this.offsetX = centerX - (centerX - this.offsetX) * factor;
        this.offsetY = centerY - (centerY - this.offsetY) * factor;

        this.scale = newScale;
        this.updateZoomLevel();
        this.render();
    }

    fitToScreen() {
        if (!this.image) return;

        const container = document.getElementById('ocrCanvasContainer');
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        const scaleX = containerWidth / this.image.width;
        const scaleY = containerHeight / this.image.height;
        this.scale = Math.min(scaleX, scaleY) * 0.95;

        // Center the image
        this.offsetX = (containerWidth - this.image.width * this.scale) / 2;
        this.offsetY = (containerHeight - this.image.height * this.scale) / 2;

        this.updateZoomLevel();
        this.render();
    }

    updateZoomLevel() {
        document.getElementById('ocrZoomLevel').textContent = Math.round(this.scale * 100) + '%';
    }

    render() {
        if (!this.image || !this.canvas) return;

        const container = document.getElementById('ocrCanvasContainer');
        this.canvas.width = container.clientWidth;
        this.canvas.height = container.clientHeight;

        // Clear canvas
        this.ctx.fillStyle = '#1a1a2e';
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw image
        this.ctx.save();
        this.ctx.translate(this.offsetX, this.offsetY);
        this.ctx.scale(this.scale, this.scale);
        this.ctx.drawImage(this.image, 0, 0);
        this.ctx.restore();

        // Draw selection if exists
        if (this.currentSelection) {
            this.drawSelection();
        }
    }

    drawSelection() {
        if (!this.currentSelection) return;

        const { x, y, width, height } = this.currentSelection;

        // Convert to canvas coordinates
        const canvasX = x * this.scale + this.offsetX;
        const canvasY = y * this.scale + this.offsetY;
        const canvasWidth = width * this.scale;
        const canvasHeight = height * this.scale;

        // Draw selection rectangle
        this.ctx.strokeStyle = '#00ff88';
        this.ctx.lineWidth = 2;
        this.ctx.setLineDash([8, 4]);
        this.ctx.strokeRect(canvasX, canvasY, canvasWidth, canvasHeight);

        // Fill with semi-transparent
        this.ctx.fillStyle = 'rgba(0, 255, 136, 0.1)';
        this.ctx.fillRect(canvasX, canvasY, canvasWidth, canvasHeight);

        // Draw corner handles
        this.ctx.setLineDash([]);
        this.ctx.fillStyle = '#00ff88';
        const handleSize = 8;

        // Top-left
        this.ctx.fillRect(canvasX - handleSize / 2, canvasY - handleSize / 2, handleSize, handleSize);
        // Top-right
        this.ctx.fillRect(canvasX + canvasWidth - handleSize / 2, canvasY - handleSize / 2, handleSize, handleSize);
        // Bottom-left
        this.ctx.fillRect(canvasX - handleSize / 2, canvasY + canvasHeight - handleSize / 2, handleSize, handleSize);
        // Bottom-right
        this.ctx.fillRect(canvasX + canvasWidth - handleSize / 2, canvasY + canvasHeight - handleSize / 2, handleSize, handleSize);
    }

    handleMouseDown(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        if (this.mode === 'pan') {
            this.isPanning = true;
            this.panStart = { x: x - this.offsetX, y: y - this.offsetY };
        } else {
            this.isSelecting = true;
            // Convert to image coordinates
            this.selectionStart = {
                x: (x - this.offsetX) / this.scale,
                y: (y - this.offsetY) / this.scale
            };
            this.selectionEnd = { ...this.selectionStart };
        }
    }

    handleMouseMove(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        if (this.isPanning) {
            this.offsetX = x - this.panStart.x;
            this.offsetY = y - this.panStart.y;
            this.render();
        } else if (this.isSelecting) {
            this.selectionEnd = {
                x: (x - this.offsetX) / this.scale,
                y: (y - this.offsetY) / this.scale
            };

            // Update current selection
            this.currentSelection = {
                x: Math.min(this.selectionStart.x, this.selectionEnd.x),
                y: Math.min(this.selectionStart.y, this.selectionEnd.y),
                width: Math.abs(this.selectionEnd.x - this.selectionStart.x),
                height: Math.abs(this.selectionEnd.y - this.selectionStart.y)
            };

            this.render();
        }
    }

    handleMouseUp(e) {
        if (this.isSelecting && this.currentSelection) {
            // Enable extract button if selection is valid
            if (this.currentSelection.width > 10 && this.currentSelection.height > 10) {
                document.getElementById('ocrExtractBtn').disabled = false;
            }
        }

        this.isPanning = false;
        this.isSelecting = false;
    }

    handleWheel(e) {
        e.preventDefault();

        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newScale = Math.max(this.minScale, Math.min(this.maxScale, this.scale + delta));

        // Zoom toward mouse position
        const factor = newScale / this.scale;
        this.offsetX = mouseX - (mouseX - this.offsetX) * factor;
        this.offsetY = mouseY - (mouseY - this.offsetY) * factor;

        this.scale = newScale;
        this.updateZoomLevel();
        this.render();
    }

    handleTouchStart(e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            this.handleMouseDown({ clientX: touch.clientX, clientY: touch.clientY });
        }
    }

    handleTouchMove(e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            this.handleMouseMove({ clientX: touch.clientX, clientY: touch.clientY });
        }
    }

    handleTouchEnd(e) {
        this.handleMouseUp(e);
    }

    clearSelection() {
        this.currentSelection = null;
        this.selectionStart = { x: 0, y: 0 };
        this.selectionEnd = { x: 0, y: 0 };
        document.getElementById('ocrExtractBtn').disabled = true;
        this.render();
    }

    resetResults() {
        document.getElementById('ocrExtractedText').innerHTML = `
            <div class="empty-state">
                <i class="bi bi-textarea-resize"></i>
                <p>Select an area on the receipt to extract text</p>
            </div>
        `;
        document.getElementById('ocrMatchedItems').innerHTML = `
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <p>Items matching extracted text will appear here</p>
            </div>
        `;
        document.getElementById('ocrMatchedCount').textContent = '0';
        document.getElementById('ocrAddItemsBtn').disabled = true;
        this.extractedItems = [];
    }

    async extractText() {
        if (!this.currentSelection) return;

        const extractedTextArea = document.getElementById('ocrExtractedText');
        const extractBtn = document.getElementById('ocrExtractBtn');

        // Show loading
        extractedTextArea.innerHTML = `
            <div class="ocr-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p>Extracting text...</p>
            </div>
        `;
        extractBtn.disabled = true;

        try {
            // Get cropped image data
            const croppedData = this.getCroppedImageData();

            // Call OCR API
            const response = await fetch(this.options.ocrApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    image: croppedData,
                    selection: this.currentSelection
                })
            });

            const result = await response.json();

            if (result.success && result.text) {
                this.displayExtractedText(result.text);
                await this.searchItems(result.text);
            } else {
                throw new Error(result.message || 'OCR extraction failed');
            }

        } catch (error) {
            console.error('OCR Error:', error);
            extractedTextArea.innerHTML = `
                <div class="empty-state" style="color: #dc3545;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to extract text: ${error.message}</p>
                </div>
            `;
        }

        extractBtn.disabled = false;
    }

    getCroppedImageData() {
        if (!this.currentSelection || !this.image) return null;

        const { x, y, width, height } = this.currentSelection;

        // Create temporary canvas for cropping
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = width;
        tempCanvas.height = height;
        const tempCtx = tempCanvas.getContext('2d');

        // Draw cropped portion
        tempCtx.drawImage(this.image, x, y, width, height, 0, 0, width, height);

        // Return as base64
        return tempCanvas.toDataURL('image/jpeg', 0.9);
    }

    displayExtractedText(text) {
        document.getElementById('ocrExtractedText').innerHTML = `
            <div class="extracted-text-content">${this.escapeHtml(text)}</div>
        `;
    }

    async searchItems(text) {
        const matchedItemsArea = document.getElementById('ocrMatchedItems');
        const matchedCount = document.getElementById('ocrMatchedCount');

        matchedItemsArea.innerHTML = `
            <div class="ocr-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p>Searching for matching items...</p>
            </div>
        `;

        try {
            // Extract potential item names from text
            const searchTerms = this.extractSearchTerms(text);

            if (searchTerms.length === 0) {
                matchedItemsArea.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-info-circle"></i>
                        <p>No recognizable item names found in the extracted text</p>
                    </div>
                `;
                return;
            }

            // Search for items
            const response = await fetch(this.options.itemSearchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    search_terms: searchTerms
                    // No limit - show ALL matched items
                })
            });

            const result = await response.json();

            if (result.success && result.items && result.items.length > 0) {
                this.displayMatchedItems(result.items);
                // Show "30 of X" format if there are more matches
                if (result.total_matches && result.total_matches > result.items.length) {
                    matchedCount.innerHTML = `<span style="font-weight: bold;">${result.items.length}</span> <span style="font-size: 10px; color: #6c757d;">of ${result.total_matches}</span>`;
                } else {
                    matchedCount.textContent = result.items.length;
                }
            } else {
                matchedItemsArea.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <p>No matching items found in your inventory</p>
                    </div>
                `;
            }

        } catch (error) {
            console.error('Search Error:', error);
            matchedItemsArea.innerHTML = `
                <div class="empty-state" style="color: #dc3545;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to search items: ${error.message}</p>
                </div>
            `;
        }
    }

    extractSearchTerms(text) {
        console.log('Extracting search terms from:', text);

        // Split text into lines and words
        const lines = text.split(/[\n\r]+/)
            .map(line => line.trim())
            .filter(line => line.length > 1);

        const terms = [];

        for (const line of lines) {
            // First, add the whole line as a search term (if it looks like text)
            if (line.length > 2 && /[a-zA-Z]{2,}/.test(line)) {
                // Clean up but keep the main text
                let cleaned = line
                    .replace(/[^\w\s.-]/g, ' ') // Remove special chars except . and -
                    .replace(/\s+/g, ' ')
                    .trim();

                if (cleaned.length > 2) {
                    terms.push(cleaned);
                }
            }

            // Also split line into individual words for broader matching
            const words = line.split(/\s+/).filter(word => {
                // Keep words that are 3+ characters and contain letters
                return word.length >= 3 && /[a-zA-Z]{2,}/.test(word);
            });

            for (const word of words) {
                // Clean the word
                let cleanWord = word.replace(/[^\w.-]/g, '').trim();
                // Match items with 4+ characters for better medicine name matching
                if (cleanWord.length >= 4 && /^[a-zA-Z]/.test(cleanWord)) {
                    terms.push(cleanWord);
                }
            }
        }

        // Remove duplicates and return
        const uniqueTerms = [...new Set(terms)];
        console.log('Extracted search terms:', uniqueTerms);
        return uniqueTerms;
    }

    displayMatchedItems(items) {
        this.extractedItems = items;
        this.itemBatches = {};
        this.selectedBatches = {};

        const html = items.map((item, index) => {
            // Color code based on match score
            const score = item.match_score || 0;
            let scoreColor = '#6c757d'; // gray
            let scoreBg = '#f8f9fa';
            if (score >= 70) {
                scoreColor = '#198754'; // green
                scoreBg = '#d1e7dd';
            } else if (score >= 50) {
                scoreColor = '#ffc107'; // yellow
                scoreBg = '#fff3cd';
            } else if (score >= 30) {
                scoreColor = '#fd7e14'; // orange
                scoreBg = '#ffe5d0';
            }
            
            return `
            <div class="matched-item-card" data-item-index="${index}" data-item-id="${item.id}" data-match-score="${score}">
                <input type="checkbox" class="matched-item-checkbox" data-item-id="${item.id}" onclick="event.stopPropagation(); receiptOCR.toggleItemSelection(${index})">
                <div class="matched-item-info" onclick="receiptOCR.toggleItemSelection(${index})">
                    <div class="matched-item-name" title="${this.escapeHtml(item.name)}">
                        ${this.escapeHtml(item.name)}
                        ${score > 0 ? `<span style="display: inline-block; padding: 1px 6px; margin-left: 5px; font-size: 10px; border-radius: 10px; background: ${scoreBg}; color: ${scoreColor}; font-weight: bold;">${score}%</span>` : ''}
                    </div>
                    <div class="matched-item-details">
                        ${item.packing ? `<span><i class="bi bi-box"></i> ${item.packing}</span>` : ''}
                        ${item.company_short_name ? `<span><i class="bi bi-building"></i> ${item.company_short_name}</span>` : ''}
                        ${item.matched_term ? `<span style="color: #0d6efd; font-size: 10px;"><i class="bi bi-search"></i> "${item.matched_term}"</span>` : ''}
                    </div>
                    <div class="selected-batch-info" id="selectedBatchInfo_${index}" style="display: none;">
                        <span class="batch-badge"><i class="bi bi-layers"></i> <span id="batchBadgeText_${index}"></span></span>
                    </div>
                </div>
                <div class="matched-item-actions">
                    <button type="button" class="btn-select-batch" id="selectBatchBtn_${index}" onclick="event.stopPropagation(); receiptOCR.openBatchModal(${index})" title="Select Batch for this item">
                        <i class="bi bi-layers"></i>
                    </button>
                    <div class="matched-item-price">₹${parseFloat(item.mrp || item.s_rate || 0).toFixed(2)}</div>
                </div>
            </div>
        `}).join('');

        document.getElementById('ocrMatchedItems').innerHTML = html;
        this.updateAddItemsButton();
    }

    // Live search filter for matched items
    filterMatchedItems(query) {
        const cards = document.querySelectorAll('.matched-item-card');
        let visibleCount = 0;
        
        cards.forEach((card, index) => {
            const item = this.extractedItems[index];
            if (!item) return;
            
            const itemName = (item.name || '').toLowerCase();
            const packing = (item.packing || '').toLowerCase();
            const company = (item.company_short_name || '').toLowerCase();
            const matchedTerm = (item.matched_term || '').toLowerCase();
            const barcode = (item.bar_code || '').toLowerCase();
            
            // Check if any field matches the query
            const matches = query === '' || 
                itemName.includes(query) || 
                packing.includes(query) || 
                company.includes(query) ||
                matchedTerm.includes(query) ||
                barcode.includes(query);
            
            if (matches) {
                card.style.display = '';
                visibleCount++;
                
                // Highlight matching text if there's a query
                if (query) {
                    const nameEl = card.querySelector('.matched-item-name');
                    if (nameEl) {
                        const originalName = item.name;
                        const regex = new RegExp(`(${this.escapeRegex(query)})`, 'gi');
                        const highlightedName = originalName.replace(regex, '<mark style="background: #ffeb3b; padding: 0 2px; border-radius: 2px;">$1</mark>');
                        
                        // Get the score badge part
                        const scoreBadge = nameEl.querySelector('span');
                        const scoreBadgeHTML = scoreBadge ? scoreBadge.outerHTML : '';
                        nameEl.innerHTML = highlightedName + ' ' + scoreBadgeHTML;
                    }
                }
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update visible count display
        const matchedCount = document.getElementById('ocrMatchedCount');
        if (query) {
            matchedCount.innerHTML = `<span style="font-weight: bold;">${visibleCount}</span> <span style="font-size: 10px; color: #6c757d;">filtered</span>`;
        } else {
            // Reset to original count
            matchedCount.textContent = this.extractedItems.length;
        }
    }
    
    // Helper to escape regex special characters
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    toggleItemSelection(index) {
        const card = document.querySelector(`.matched-item-card[data-item-index="${index}"]`);
        const checkbox = card.querySelector('.matched-item-checkbox');

        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);

        this.updateAddItemsButton();
    }

    async openBatchModal(index) {
        const item = this.extractedItems[index];
        this.currentBatchItemIndex = index;

        // Create batch modal if not exists
        this.createBatchModal();
        
        const modal = document.getElementById('ocrBatchModal');
        const modalTitle = document.getElementById('ocrBatchModalTitle');
        const modalContent = document.getElementById('ocrBatchModalContent');
        
        modalTitle.textContent = item.name;
        modalContent.innerHTML = `
            <div class="batch-loading">
                <span class="spinner-border spinner-border-sm"></span>
                Loading batches...
            </div>
        `;
        
        modal.style.display = 'flex';
        
        // Fetch batches
        await this.fetchAndDisplayBatches(index);
    }

    createBatchModal() {
        if (document.getElementById('ocrBatchModal')) return;

        const modalHTML = `
            <div id="ocrBatchModal" class="ocr-batch-modal" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 2147483649 !important;">
                <div class="ocr-batch-modal-backdrop" onclick="receiptOCR.closeBatchModal()" style="position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background: rgba(0,0,0,0.7) !important; z-index: 1 !important;"></div>
                <div class="ocr-batch-modal-content" style="position: relative !important; z-index: 2 !important;">
                    <div class="ocr-batch-modal-header">
                        <h5><i class="bi bi-layers"></i> Select Batch</h5>
                        <button type="button" class="btn-close-modal" onclick="receiptOCR.closeBatchModal()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="ocr-batch-modal-item">
                        <strong id="ocrBatchModalTitle"></strong>
                    </div>
                    <div class="ocr-batch-modal-body" id="ocrBatchModalContent">
                    </div>
                    <div class="ocr-batch-modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="receiptOCR.closeBatchModal()">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="receiptOCR.skipBatchSelection()">
                            <i class="bi bi-skip-forward"></i> Skip (No Batch)
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Append to the OCR modal container for proper stacking
        const ocrModal = document.getElementById('receiptOCRModal');
        if (ocrModal) {
            ocrModal.insertAdjacentHTML('beforeend', modalHTML);
        } else {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        this.injectBatchModalStyles();
    }

    injectBatchModalStyles() {
        if (document.getElementById('ocrBatchModalStyles')) return;

        const styles = `
            <style id="ocrBatchModalStyles">
                .ocr-batch-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    z-index: 2147483648;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .ocr-batch-modal-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                }
                
                .ocr-batch-modal-content {
                    position: relative;
                    background: white;
                    border-radius: 12px;
                    width: 90%;
                    max-width: 500px;
                    max-height: 80vh;
                    display: flex;
                    flex-direction: column;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
                }
                
                .ocr-batch-modal-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 20px;
                    background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                    color: white;
                    border-radius: 12px 12px 0 0;
                }
                
                .ocr-batch-modal-header h5 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .btn-close-modal {
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .btn-close-modal:hover {
                    background: rgba(255,255,255,0.3);
                }
                
                .ocr-batch-modal-item {
                    padding: 12px 20px;
                    background: #f8f9fa;
                    border-bottom: 1px solid #e9ecef;
                    font-size: 14px;
                }
                
                .ocr-batch-modal-body {
                    flex: 1;
                    overflow-y: auto;
                    padding: 16px 20px;
                    max-height: 400px;
                }
                
                .ocr-batch-modal-footer {
                    padding: 16px 20px;
                    background: #f8f9fa;
                    border-top: 1px solid #e9ecef;
                    border-radius: 0 0 12px 12px;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                
                .batch-option-card {
                    padding: 12px 16px;
                    border: 2px solid #e9ecef;
                    border-radius: 8px;
                    margin-bottom: 10px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                
                .batch-option-card:hover {
                    border-color: #6f42c1;
                    background: #f8f5fc;
                }
                
                .batch-option-card.selected {
                    border-color: #28a745;
                    background: #f0fff4;
                }
                
                .batch-option-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 8px;
                }
                
                .batch-option-name {
                    font-weight: 600;
                    font-size: 14px;
                    color: #333;
                }
                
                .batch-option-qty {
                    font-size: 12px;
                    padding: 2px 8px;
                    background: #e9ecef;
                    border-radius: 4px;
                }
                
                .batch-option-qty.low-stock {
                    background: #fff3cd;
                    color: #856404;
                }
                
                .batch-option-details {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                    font-size: 12px;
                    color: #666;
                }
                
                .batch-option-detail label {
                    color: #888;
                }
                
                .batch-option-detail span {
                    font-weight: 600;
                    color: #333;
                }
                
                .btn-select-batch {
                    background: linear-gradient(135deg, #6f42c1, #5a32a3);
                    border: none;
                    color: white;
                    width: 36px;
                    height: 36px;
                    border-radius: 8px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    transition: all 0.2s ease;
                    position: relative;
                    box-shadow: 0 2px 6px rgba(111, 66, 193, 0.3);
                }
                
                .btn-select-batch::after {
                    content: '';
                    position: absolute;
                    top: -2px;
                    right: -2px;
                    width: 10px;
                    height: 10px;
                    background: #ffc107;
                    border-radius: 50%;
                    border: 2px solid white;
                }
                
                .btn-select-batch:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.5);
                }
                
                .btn-select-batch.has-batch {
                    background: linear-gradient(135deg, #28a745, #1e7e34);
                    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
                }
                
                .btn-select-batch.has-batch::after {
                    background: #28a745;
                    content: '✓';
                    color: white;
                    font-size: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .btn-select-batch.has-batch:hover {
                    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5);
                }
                
                .matched-item-actions {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex-shrink: 0;
                }
                
                .selected-batch-info {
                    margin-top: 4px;
                }
                
                .batch-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    font-size: 11px;
                    padding: 3px 10px;
                    background: #d4edda;
                    color: #155724;
                    border-radius: 12px;
                    font-weight: 500;
                }
                
                .no-batches-message {
                    text-align: center;
                    padding: 20px;
                    color: #666;
                }
                
                .no-batches-message i {
                    font-size: 40px;
                    color: #ccc;
                    margin-bottom: 10px;
                    display: block;
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    closeBatchModal() {
        const modal = document.getElementById('ocrBatchModal');
        if (modal) {
            modal.style.display = 'none';
        }
        this.currentBatchItemIndex = null;
    }

    async fetchAndDisplayBatches(index) {
        const item = this.extractedItems[index];
        const itemId = item.id;
        const modalContent = document.getElementById('ocrBatchModalContent');

        // Check if already fetched
        if (this.itemBatches[itemId]) {
            this.displayBatchOptions(this.itemBatches[itemId]);
            return;
        }

        try {
            const response = await fetch(`${this.options.batchApiUrl}/${itemId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            console.log('Batches for item', item.name, ':', result);

            if (result.success && result.batches && result.batches.length > 0) {
                this.itemBatches[itemId] = result.batches;
                this.displayBatchOptions(result.batches);
            } else {
                modalContent.innerHTML = `
                    <div class="no-batches-message">
                        <i class="bi bi-inbox"></i>
                        <p>No batches available for this item</p>
                        <p class="text-muted">Click "Skip (No Batch)" to add without batch</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error fetching batches:', error);
            modalContent.innerHTML = `
                <div class="no-batches-message">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>Error loading batches</p>
                </div>
            `;
        }
    }

    displayBatchOptions(batches) {
        const modalContent = document.getElementById('ocrBatchModalContent');
        const availableBatches = batches.filter(b => parseFloat(b.qty || 0) > 0);

        if (availableBatches.length === 0) {
            modalContent.innerHTML = `
                <div class="no-batches-message">
                    <i class="bi bi-inbox"></i>
                    <p>No batches with available stock</p>
                    <p class="text-muted">Click "Skip (No Batch)" to add without batch</p>
                </div>
            `;
            return;
        }

        const html = availableBatches.map((batch, idx) => {
            const qty = parseFloat(batch.qty || 0);
            const isLowStock = qty <= 5;
            return `
                <div class="batch-option-card" data-batch-idx="${idx}" onclick="receiptOCR.selectBatchFromModal(${idx})">
                    <div class="batch-option-header">
                        <span class="batch-option-name">${batch.batch_no}</span>
                        <span class="batch-option-qty ${isLowStock ? 'low-stock' : ''}">Qty: ${qty}</span>
                    </div>
                    <div class="batch-option-details">
                        <div class="batch-option-detail">
                            <label>Expiry:</label>
                            <span>${batch.expiry_display || '---'}</span>
                        </div>
                        <div class="batch-option-detail">
                            <label>MRP:</label>
                            <span>₹${parseFloat(batch.mrp || 0).toFixed(2)}</span>
                        </div>
                        <div class="batch-option-detail">
                            <label>S.Rate:</label>
                            <span>₹${parseFloat(batch.s_rate || 0).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        modalContent.innerHTML = html;
        this.currentBatches = availableBatches;
    }

    selectBatchFromModal(batchIdx) {
        const index = this.currentBatchItemIndex;
        const batch = this.currentBatches[batchIdx];
        
        // Store selected batch
        this.selectedBatches[index] = batch;
        
        // Update UI
        const card = document.querySelector(`.matched-item-card[data-item-index="${index}"]`);
        const checkbox = card.querySelector('.matched-item-checkbox');
        const batchBtn = document.getElementById(`selectBatchBtn_${index}`);
        const batchInfo = document.getElementById(`selectedBatchInfo_${index}`);
        const batchText = document.getElementById(`batchBadgeText_${index}`);
        
        // Auto-check the item
        if (!checkbox.checked) {
            checkbox.checked = true;
            card.classList.add('selected');
        }
        
        // Update batch button style
        batchBtn.classList.add('has-batch');
        
        // Show batch info
        batchInfo.style.display = 'block';
        batchText.textContent = `${batch.batch_no} | Exp: ${batch.expiry_display || '---'}`;
        
        this.closeBatchModal();
        this.updateAddItemsButton();
    }

    skipBatchSelection() {
        const index = this.currentBatchItemIndex;
        
        // Mark as selected without batch
        this.selectedBatches[index] = { isEmpty: true };
        
        // Update UI
        const card = document.querySelector(`.matched-item-card[data-item-index="${index}"]`);
        const checkbox = card.querySelector('.matched-item-checkbox');
        const batchInfo = document.getElementById(`selectedBatchInfo_${index}`);
        const batchText = document.getElementById(`batchBadgeText_${index}`);
        
        // Auto-check the item
        if (!checkbox.checked) {
            checkbox.checked = true;
            card.classList.add('selected');
        }
        
        // Show no batch info
        batchInfo.style.display = 'block';
        batchText.textContent = 'No Batch';
        
        this.closeBatchModal();
        this.updateAddItemsButton();
    }

    updateAddItemsButton() {
        const selectedCheckboxes = document.querySelectorAll('.matched-item-checkbox:checked');
        const addBtn = document.getElementById('ocrAddItemsBtn');
        
        const totalSelected = selectedCheckboxes.length;
        
        if (totalSelected === 0) {
            addBtn.disabled = true;
            addBtn.innerHTML = `<i class="bi bi-plus-circle"></i> Add Selected Items`;
        } else {
            addBtn.disabled = false;
            addBtn.innerHTML = `<i class="bi bi-plus-circle"></i> Add ${totalSelected} Item(s)`;
        }
    }

    addSelectedItems() {
        const selectedCheckboxes = document.querySelectorAll('.matched-item-checkbox:checked');
        const selectedItems = [];

        selectedCheckboxes.forEach(checkbox => {
            const card = checkbox.closest('.matched-item-card');
            const index = card.dataset.itemIndex;
            const item = this.extractedItems[index];
            const batch = this.selectedBatches[index] || null;

            selectedItems.push({
                item: item,
                batch: (batch && !batch.isEmpty) ? batch : null
            });
        });

        if (selectedItems.length === 0) return;

        console.log('Adding items with batches:', selectedItems);

        // Trigger callback or event with both item and batch data
        if (typeof this.options.onItemsSelected === 'function') {
            this.options.onItemsSelected(selectedItems);
        }

        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('ocrItemsSelected', {
            detail: { items: selectedItems }
        }));

        this.close();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Create global instance
let receiptOCR = null;

// Helper function to open OCR preview
function openReceiptOCRPreview(imageData, options = {}) {
    if (!receiptOCR) {
        receiptOCR = new ReceiptOCRPreview(options);
    }
    receiptOCR.open(imageData);
}

console.log('📷 Receipt OCR Preview Module Loaded');
</script>
