# Receipt OCR Feature Documentation

## Overview

The Receipt OCR feature allows users to scan/upload receipts, preview them with zoom functionality, select specific areas of text, extract text using OCR, and automatically match items from your inventory.

## Features

- **Receipt Preview Modal** - Full-screen preview with zoom in/out capability
- **Zoom Controls** - Mouse wheel zoom, +/- buttons, fit-to-screen
- **Area Selection** - Draw rectangles to select specific text areas
- **Pan Mode** - Drag to move around the image when zoomed in
- **OCR Text Extraction** - Send selected area to OCR API for text extraction
- **Automatic Item Matching** - Match extracted text against your inventory items
- **Quick Item Addition** - Select matched items to add them to the sale transaction

## Workflow

1. **Scan/Upload Receipt** - Use the scanner or upload a receipt image
2. **Click on Receipt Thumbnail** - Opens the OCR preview modal
3. **Zoom to Text Area** - Use mouse wheel or buttons to zoom
4. **Select Text Area** - Draw a rectangle around the text you want to extract
5. **Extract Text** - Click "Extract Text" button to send to OCR
6. **Review Matched Items** - Check the items that match the extracted text
7. **Add to Sale** - Click "Add Selected Items" to add them to your sale

## Configuration

### OCR.space API (Recommended - Free Tier)

1. Get a free API key from [https://ocr.space/ocrapi](https://ocr.space/ocrapi)
2. Add to your `.env` file:

```env
SPACE_OCR_URL=https://api.ocr.space/parse/image
SPACE_OCR_KEY=your_api_key_here
```

**Free Tier Limits:**
- 500 requests per day
- 1 MB max file size per request
- No credit card required

### Google Cloud Vision (Alternative)

1. Enable the Cloud Vision API in Google Cloud Console
2. Create an API key
3. Add to your `.env` file:

```env
GOOGLE_VISION_KEY=your_google_api_key_here
```

### Local Tesseract OCR (Offline Option)

For offline OCR capability, install Tesseract:

**Windows:**
```bash
# Download installer from: https://github.com/UB-Mannheim/tesseract/wiki
# Add tesseract to PATH after installation
```

**Linux:**
```bash
sudo apt-get install tesseract-ocr
```

No API key required - the system will automatically detect and use Tesseract if available.

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/admin/api/ocr/extract` | POST | Extract text from image |
| `/admin/api/ocr/search-items` | POST | Search items by text |
| `/admin/api/ocr/status` | GET | Check OCR service status |

### Extract Text Request

```json
{
    "image": "base64_encoded_image_data",
    "selection": {
        "x": 100,
        "y": 50,
        "width": 300,
        "height": 200
    }
}
```

### Search Items Request

```json
{
    "search_terms": ["Paracetamol 500mg", "Aspirin", "Vitamin C"],
    "limit": 20
}
```

## Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Escape` | Close modal |
| `+` or `=` | Zoom in |
| `-` | Zoom out |
| `0` | Fit to screen |

## Troubleshooting

### OCR Not Working

1. Check if API key is configured in `.env`
2. Clear config cache: `php artisan config:clear`
3. Check browser console for errors
4. Verify API quota hasn't been exceeded

### Poor Text Recognition

1. Ensure receipt is properly oriented
2. Select a smaller, focused area
3. Try higher scanner DPI (300+ recommended)
4. Ensure good lighting/contrast

### Items Not Matching

1. Verify items exist in your inventory
2. Check for typos in item names
3. Try selecting only the item name portion
4. Ensure items are not marked as deleted

## Files Structure

```
bill-software/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Api/
│               └── OCRController.php       # Backend OCR controller
├── config/
│   └── services.php                        # OCR service configuration
├── public/
│   └── js/
│       └── receipt-ocr-preview.js          # Frontend OCR module
├── resources/
│   └── views/
│       └── admin/
│           └── sale/
│               └── transaction.blade.php   # Integrated preview functionality
└── routes/
    └── web.php                             # OCR API routes
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+

## Mobile Support

The OCR preview modal is responsive and supports touch gestures:
- Pinch to zoom (on touch devices)
- Touch and drag to pan
- Touch and drag to select area

## Security Notes

- OCR API calls are proxied through your server
- Image data is not permanently stored
- CSRF protection enabled on all endpoints
- Only authenticated users can access OCR features
