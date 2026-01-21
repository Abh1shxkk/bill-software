# Gemini Model Selection Guide

## Available Models (January 2026)

### 🌟 **gemini-2.0-flash-exp** (RECOMMENDED for OCR)
- **Status**: Latest experimental model
- **Speed**: ⚡⚡⚡ Fastest
- **Cost**: 💰 Free tier available
- **Best for**: Vision tasks, OCR, image analysis
- **Why use it**: 
  - Cutting-edge vision capabilities
  - Optimized for receipt/document scanning
  - Best accuracy for text extraction
  - Handles tilted/blurry images better

### ✅ **gemini-2.0-flash** (Stable alternative)
- **Status**: Stable production model
- **Speed**: ⚡⚡⚡ Fastest
- **Cost**: 💰 Free tier available
- **Best for**: Production environments where stability is critical
- **Why use it**: Same as experimental but more stable

### 🧠 **gemini-2.0-flash-thinking**
- **Status**: Latest with reasoning
- **Speed**: ⚡⚡ Medium
- **Cost**: 💰💰 Medium
- **Best for**: Complex reasoning tasks
- **Why NOT for OCR**: Slower, more expensive, overkill for OCR

### 💪 **gemini-1.5-pro**
- **Status**: Previous generation, very powerful
- **Speed**: ⚡ Slower
- **Cost**: 💰💰💰 Most expensive
- **Best for**: Long documents, complex analysis
- **Why NOT for OCR**: Too slow and expensive for simple OCR tasks

### ⚡ **gemini-1.5-flash**
- **Status**: Previous generation
- **Speed**: ⚡⚡ Fast
- **Cost**: 💰 Cheap
- **Best for**: General tasks
- **Why NOT for OCR**: Older vision capabilities than 2.0

---

## Model Comparison for OCR Tasks

| Feature | 2.0-flash-exp | 2.0-flash | 1.5-pro | 1.5-flash |
|---------|---------------|-----------|---------|-----------|
| **Vision Quality** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Speed** | 0.5-1s | 0.5-1s | 2-3s | 1-2s |
| **Cost (per 1K images)** | $0.075 | $0.075 | $0.25 | $0.075 |
| **Tilt Handling** | Excellent | Excellent | Good | Fair |
| **Multi-Receipt** | Excellent | Excellent | Good | Fair |
| **Fuzzy Matching** | Best | Best | Good | Fair |

---

## How to Change Models

### Option 1: Update .env file (Recommended)

```env
# Use latest experimental (best for OCR)
GEMINI_MODEL=gemini-2.0-flash-exp

# OR use stable version
GEMINI_MODEL=gemini-2.0-flash

# OR use pro version (if you need it)
GEMINI_MODEL=gemini-1.5-pro
```

### Option 2: Update config/services.php

```php
'gemini' => [
    'key' => env('GEMINI_API_KEY', ''),
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash-exp'),
],
```

After changing, run:
```bash
php artisan config:clear
```

---

## Pricing (as of January 2026)

### Free Tier (All models)
- **15 requests per minute**
- **1,500 requests per day**
- **1 million tokens per month**

This is MORE than enough for most small-medium businesses!

### Paid Tier (if you exceed free tier)

| Model | Input (per 1M tokens) | Output (per 1M tokens) |
|-------|----------------------|------------------------|
| gemini-2.0-flash-exp | $0.075 | $0.30 |
| gemini-2.0-flash | $0.075 | $0.30 |
| gemini-1.5-pro | $1.25 | $5.00 |
| gemini-1.5-flash | $0.075 | $0.30 |

**For OCR**: An average receipt image = ~1,000 tokens
- Free tier = 1,500 receipts/day
- Paid tier = $0.075 per 1,000 receipts

---

## When to Use Each Model

### Use `gemini-2.0-flash-exp` when:
✅ You want the absolute best OCR accuracy
✅ You're okay with experimental features
✅ You want the latest vision capabilities
✅ Speed is important
✅ **This is the DEFAULT and RECOMMENDED choice**

### Use `gemini-2.0-flash` when:
✅ You need production stability
✅ You can't risk experimental model changes
✅ You want same speed as experimental

### Use `gemini-1.5-pro` when:
✅ You have very complex receipts with lots of text
✅ You need maximum accuracy at any cost
✅ Speed is not critical
✅ Budget is not a concern

### Use `gemini-1.5-flash` when:
✅ You're on a very tight budget
✅ You don't need the latest features
✅ Your receipts are simple and clear

---

## Testing Different Models

You can test different models without changing code:

```bash
# Test with experimental
curl -X POST http://localhost/bill-software/admin/api/ocr/gemini/analyze \
  -H "Content-Type: application/json" \
  -d '{"image": "base64_image_data"}'

# Then change GEMINI_MODEL in .env and test again
```

---

## Monitoring Usage

Check your usage at:
- **Google AI Studio**: https://makersuite.google.com/
- **API Console**: https://console.cloud.google.com/

---

## Recommendation Summary

**For 99% of use cases, use `gemini-2.0-flash-exp`**

It's:
- ✅ Latest and greatest
- ✅ Fastest
- ✅ Cheapest
- ✅ Best for OCR
- ✅ Best for vision tasks
- ✅ Best for handling tilted/blurry images
- ✅ Best for multi-receipt detection

Only switch to `gemini-1.5-pro` if you have very specific needs that require it.

---

## Future-Proofing

When Google releases newer models (e.g., `gemini-3.0-flash`), you can simply:

1. Update `.env`:
   ```env
   GEMINI_MODEL=gemini-3.0-flash
   ```

2. Clear config:
   ```bash
   php artisan config:clear
   ```

3. Done! No code changes needed.

---

**Current Configuration**: `gemini-2.0-flash-exp` ⭐

This is the best choice for your receipt OCR needs!
