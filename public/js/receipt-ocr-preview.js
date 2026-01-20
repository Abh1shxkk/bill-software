/**
 * Receipt OCR Preview Module
 * Features: Popup Preview, Zoom, Area Selection, OCR Text Extraction, Item Matching
 * Version: 1.0.0
 */

class ReceiptOCRPreview {
    constructor(options = {}) {
        this.options = {
            ocrApiUrl: options.ocrApiUrl || '/api/ocr/extract',
            itemSearchUrl: options.itemSearchUrl || '/admin/items/search',
            csrfToken: options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
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
                                <button type="button" class="toolbar-btn" id="ocrClearSelection" title="Clear Selection">
                                    <i class="bi bi-x-circle"></i>
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
        document.getElementById('ocrClearSelection').addEventListener('click', () => this.clearSelection());

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

    loadImage(imageData) {
        this.image = new Image();
        this.image.onload = () => {
            this.fitToScreen();
            this.render();
        };

        // Handle different image data formats
        if (typeof imageData === 'string') {
            if (imageData.startsWith('data:')) {
                this.image.src = imageData;
            } else {
                // Assume base64
                this.image.src = 'data:image/jpeg;base64,' + imageData;
            }
        } else if (imageData instanceof Blob || imageData instanceof File) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.image.src = e.target.result;
            };
            reader.readAsDataURL(imageData);
        }
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
                    search_terms: searchTerms,
                    limit: 20
                })
            });

            const result = await response.json();

            if (result.success && result.items && result.items.length > 0) {
                this.displayMatchedItems(result.items);
                matchedCount.textContent = result.items.length;
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
                if (cleanWord.length >= 3 && /^[a-zA-Z]/.test(cleanWord)) {
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

        const html = items.map((item, index) => `
            <div class="matched-item-card" data-item-index="${index}" onclick="receiptOCR.toggleItemSelection(${index})">
                <input type="checkbox" class="matched-item-checkbox" data-item-id="${item.id}">
                <div class="matched-item-info">
                    <div class="matched-item-name" title="${this.escapeHtml(item.name)}">${this.escapeHtml(item.name)}</div>
                    <div class="matched-item-details">
                        ${item.packing ? `<span><i class="bi bi-box"></i> ${item.packing}</span>` : ''}
                        ${item.company_short_name ? `<span><i class="bi bi-building"></i> ${item.company_short_name}</span>` : ''}
                    </div>
                </div>
                <div class="matched-item-price">₹${parseFloat(item.mrp || item.s_rate || 0).toFixed(2)}</div>
            </div>
        `).join('');

        document.getElementById('ocrMatchedItems').innerHTML = html;
        this.updateAddItemsButton();
    }

    toggleItemSelection(index) {
        const card = document.querySelector(`.matched-item-card[data-item-index="${index}"]`);
        const checkbox = card.querySelector('.matched-item-checkbox');

        checkbox.checked = !checkbox.checked;
        card.classList.toggle('selected', checkbox.checked);

        this.updateAddItemsButton();
    }

    updateAddItemsButton() {
        const selectedCount = document.querySelectorAll('.matched-item-checkbox:checked').length;
        const addBtn = document.getElementById('ocrAddItemsBtn');
        addBtn.disabled = selectedCount === 0;
        addBtn.innerHTML = `<i class="bi bi-plus-circle"></i> Add ${selectedCount > 0 ? selectedCount + ' ' : ''}Selected Items`;
    }

    addSelectedItems() {
        const selectedCheckboxes = document.querySelectorAll('.matched-item-checkbox:checked');
        const selectedItems = [];

        selectedCheckboxes.forEach(checkbox => {
            const index = checkbox.closest('.matched-item-card').dataset.itemIndex;
            selectedItems.push(this.extractedItems[index]);
        });

        if (selectedItems.length === 0) return;

        // Trigger callback or event
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

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Will be initialized with options when needed
});

// Helper function to open OCR preview
function openReceiptOCRPreview(imageData, options = {}) {
    if (!receiptOCR) {
        receiptOCR = new ReceiptOCRPreview(options);
    }
    receiptOCR.open(imageData);
}
