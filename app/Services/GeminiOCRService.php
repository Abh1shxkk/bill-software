<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * GeminiOCRService - AI-powered OCR using Google Gemini
 * 
 * Handles:
 * - Multi-receipt detection and extraction
 * - Automatic image alignment/deskewing
 * - Intelligent text-to-item matching
 */
class GeminiOCRService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Check if Gemini service is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Analyze receipt image using Gemini Vision
     * 
     * @param string $imageContent Raw image binary content
     * @param array $options Optional settings
     * @return array Analysis results
     */
    public function analyzeReceipt($imageContent, array $options = []): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Gemini API key not configured. Please add GEMINI_API_KEY to your .env file.');
        }

        try {
            // Convert image to base64
            $base64Image = base64_encode($imageContent);
            
            // Detect image mime type
            $mimeType = $this->detectMimeType($imageContent);
            
            // Build the prompt based on options
            $prompt = $this->buildAnalysisPrompt($options);
            
            Log::info('Gemini OCR: Sending image for analysis', [
                'image_size_kb' => strlen($imageContent) / 1024,
                'mime_type' => $mimeType,
                'options' => $options
            ]);

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Image
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 32,
                        'topP' => 1,
                        'maxOutputTokens' => 4096,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                Log::info('Gemini OCR: Response received', [
                    'response_length' => strlen($text)
                ]);
                
                return $this->parseGeminiResponse($text, $options);
            } else {
                Log::error('Gemini OCR: API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Gemini API error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Gemini OCR: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Extract text from receipt image (simple OCR)
     */
    public function extractText($imageContent): string
    {
        $result = $this->analyzeReceipt($imageContent, ['mode' => 'text_only']);
        return $result['extracted_text'] ?? '';
    }

    /**
     * Detect and extract multiple receipts from a single scan
     */
    public function detectMultipleReceipts($imageContent): array
    {
        $result = $this->analyzeReceipt($imageContent, ['mode' => 'multi_receipt']);
        return $result['receipts'] ?? [];
    }

    /**
     * Analyze image quality and suggest corrections
     */
    public function analyzeImageQuality($imageContent): array
    {
        $result = $this->analyzeReceipt($imageContent, ['mode' => 'quality_check']);
        
        // The response from quality_check mode should already be the quality object
        // But handle different response structures
        if (isset($result['is_tilted'])) {
            return $result; // Direct quality object
        }
        
        // Try to get from nested quality key
        return $result['quality'] ?? $result ?? [];
    }

    /**
     * Extract structured item data from receipt
     */
    public function extractItems($imageContent): array
    {
        $result = $this->analyzeReceipt($imageContent, ['mode' => 'extract_items']);
        return $result['items'] ?? [];
    }

    /**
     * Build the analysis prompt based on options
     */
    protected function buildAnalysisPrompt(array $options): string
    {
        $mode = $options['mode'] ?? 'full_analysis';

        switch ($mode) {
            case 'text_only':
                return <<<PROMPT
You are an OCR assistant. Extract ALL text visible in this receipt image.

Return the extracted text exactly as it appears, preserving line breaks.
Focus on accuracy - extract every piece of text you can see.
If the image is tilted or skewed, still extract the text correctly.
PROMPT;

            case 'multi_receipt':
                return <<<PROMPT
Analyze this scanned image for multiple receipts.

Instructions:
1. Determine if there are multiple receipts in this single image
2. If YES, identify each separate receipt and its boundaries
3. For each receipt found, extract:
   - Receipt number/position (1, 2, 3, etc.)
   - Approximate location in image (top-left, center, etc.)
   - Store/company name if visible
   - All item names and prices
   - Total amount

Return your response in this exact JSON format:
{
  "multiple_receipts_detected": true/false,
  "receipt_count": number,
  "receipts": [
    {
      "position": 1,
      "location": "description of location in image",
      "store_name": "store name or null",
      "items": [
        {"name": "item name", "price": "price as string"}
      ],
      "total": "total amount or null",
      "raw_text": "all text from this receipt"
    }
  ]
}
PROMPT;

            case 'quality_check':
                return <<<PROMPT
Analyze this scanned image containing a receipt/bill for OCR processing.

IMPORTANT: Look at the RECEIPT/BILL PAPER inside the image - is the paper itself placed at an angle/rotated?
Even if the overall scan is straight, the receipt paper might be tilted.

Check for:
1. Is the RECEIPT PAPER tilted/rotated within the image? 
   - Look at the edges of the receipt paper
   - Look at the text lines - are they horizontal or at an angle?
   - Estimate the rotation angle needed to make the text perfectly horizontal
2. Is there blur or poor focus?
3. Is the lighting adequate?
4. Are there multiple receipts in one image?
5. Is the text readable?

VERY IMPORTANT for tilt_angle_degrees:
- If text lines are perfectly horizontal: return 0
- If text slants upward to the right: return a POSITIVE angle (e.g., 15)
- If text slants upward to the left: return a NEGATIVE angle (e.g., -15)
- Estimate the angle in degrees that you would need to ROTATE the receipt to make text horizontal

Return your response in this exact JSON format:
{
  "is_tilted": true/false,
  "tilt_angle_degrees": number (positive or negative) or 0 if straight,
  "is_blurry": true/false,
  "lighting_quality": "good/fair/poor",
  "multiple_receipts": true/false,
  "receipt_count": number,
  "text_readability": "excellent/good/fair/poor",
  "suggestions": ["list of improvement suggestions"]
}
PROMPT;

            case 'detect_boundaries':
                return <<<PROMPT
CRITICAL TASK: Analyze this scanned document image and ACCURATELY detect the tilt/rotation angle of the receipt paper.

Look carefully at the image:
1. Find where the actual receipt/bill PAPER is located
2. Look at the TEXT LINES on the receipt - are they horizontal or at an angle?
3. Look at the EDGES of the receipt paper - are they parallel to the image edges or tilted?

MEASURING THE TILT ANGLE:
- Draw an imaginary line along the TOP EDGE of the receipt paper
- Measure the angle between this line and the horizontal
- If the RIGHT side of the receipt is HIGHER than the left: the angle is POSITIVE (clockwise tilt)
- If the LEFT side of the receipt is HIGHER than the right: the angle is NEGATIVE (counter-clockwise tilt)

EXAMPLES:
- Receipt perfectly straight: tilt_angle_degrees = 0
- Receipt rotated 15 degrees clockwise: tilt_angle_degrees = 15
- Receipt rotated 20 degrees counter-clockwise: tilt_angle_degrees = -20

For the corners, provide coordinates as PERCENTAGES of the image dimensions (0-100).

BE VERY CAREFUL: Even a small tilt of 5-10 degrees is significant and MUST be detected.

Return your response in this exact JSON format:
{
  "is_receipt_detected": true/false,
  "corners": {
    "top_left": { "x": number (0-100), "y": number (0-100) },
    "top_right": { "x": number (0-100), "y": number (0-100) },
    "bottom_right": { "x": number (0-100), "y": number (0-100) },
    "bottom_left": { "x": number (0-100), "y": number (0-100) }
  },
  "tilt_angle_degrees": number (MUST be accurate - positive, negative, or 0),
  "has_dark_background": true/false,
  "confidence": number (0-100)
}
PROMPT;

            case 'extract_items':

                return <<<PROMPT
Extract all product/item information from this receipt image.

For each item found, extract:
- Item name (medicine name, product name, etc.)
- Quantity purchased
- Unit price
- Total price for that item
- Any batch number or expiry date if visible

Also extract:
- Store/pharmacy name
- Invoice/bill number
- Date
- Grand total

Return your response in this exact JSON format:
{
  "store_name": "store name or null",
  "invoice_number": "invoice number or null",
  "date": "date or null",
  "items": [
    {
      "name": "item name",
      "quantity": number or null,
      "unit_price": number or null,
      "total_price": number or null,
      "batch_no": "batch number or null",
      "expiry": "expiry date or null"
    }
  ],
  "subtotal": number or null,
  "tax": number or null,
  "grand_total": number or null,
  "raw_text": "all extracted text from receipt"
}
PROMPT;

            case 'full_analysis':
            default:
                return <<<PROMPT
You are an intelligent receipt OCR assistant. Analyze this receipt image comprehensively.

Perform the following analysis:
1. Check if image contains multiple receipts - if yes, process each separately
2. Check if image is tilted/skewed and estimate the angle
3. Extract all text visible on the receipt(s)
4. Identify individual items with their names, quantities, and prices
5. Identify totals, taxes, and other summary information

Return your response in this exact JSON format:
{
  "image_quality": {
    "is_tilted": true/false,
    "tilt_angle_degrees": number or null,
    "readability": "excellent/good/fair/poor"
  },
  "multiple_receipts": true/false,
  "receipt_count": number,
  "receipts": [
    {
      "position": 1,
      "store_name": "store name or null",
      "invoice_number": "invoice number or null",
      "date": "date or null",
      "items": [
        {
          "name": "item name",
          "quantity": number or null,
          "unit_price": number or null,
          "total_price": number or null,
          "batch_no": "batch number or null",
          "expiry": "expiry date or null"
        }
      ],
      "subtotal": number or null,
      "tax": number or null,
      "grand_total": number or null,
      "raw_text": "all text from this receipt"
    }
  ],
  "extracted_text": "complete extracted text from entire image"
}
PROMPT;
        }
    }

    /**
     * Parse the Gemini response based on mode
     */
    protected function parseGeminiResponse(string $text, array $options): array
    {
        $mode = $options['mode'] ?? 'full_analysis';
        
        // Try to extract JSON from the response
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/m', $text, $jsonMatch)) {
            try {
                $parsed = json_decode($jsonMatch[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Add raw text for debugging
                    $parsed['_raw_response'] = $text;
                    return $parsed;
                }
            } catch (\Exception $e) {
                Log::warning('Gemini OCR: Failed to parse JSON response', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // If JSON parsing fails, return raw text
        return [
            'extracted_text' => $text,
            'parse_error' => 'Could not parse structured response',
            '_raw_response' => $text
        ];
    }

    /**
     * Detect MIME type from image content
     */
    protected function detectMimeType($imageContent): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageContent);
        
        // Default to JPEG if detection fails
        if (!$mimeType || $mimeType === 'application/octet-stream') {
            // Check magic bytes
            $header = substr($imageContent, 0, 3);
            if ($header === "\xFF\xD8\xFF") {
                return 'image/jpeg';
            } elseif (substr($imageContent, 0, 8) === "\x89PNG\r\n\x1a\n") {
                return 'image/png';
            } elseif (substr($imageContent, 0, 4) === "GIF8") {
                return 'image/gif';
            } elseif (substr($imageContent, 0, 4) === "RIFF") {
                return 'image/webp';
            }
            return 'image/jpeg';
        }
        
        return $mimeType;
    }

    /**
     * Match extracted items with database items using fuzzy matching
     * 
     * @param array $extractedItems Items extracted from receipt
     * @param int $organizationId Organization ID for filtering
     * @return array Matched items with confidence scores
     */
    public function matchWithDatabaseItems(array $extractedItems, int $organizationId): array
    {
        $matchedItems = [];
        
        foreach ($extractedItems as $extracted) {
            $itemName = $extracted['name'] ?? '';
            if (empty($itemName)) continue;
            
            // Search in database with fuzzy matching
            $matches = $this->fuzzySearchItems($itemName, $organizationId);
            
            $matchedItems[] = [
                'extracted' => $extracted,
                'matches' => $matches,
                'best_match' => !empty($matches) ? $matches[0] : null
            ];
        }
        
        return $matchedItems;
    }

    /**
     * Fuzzy search items in database
     */
    protected function fuzzySearchItems(string $searchTerm, int $organizationId): array
    {
        $items = \App\Models\Item::where('organization_id', $organizationId)
            ->where('is_deleted', 0)
            ->select(['id', 'name', 'packing', 'company_short_name', 'mrp', 's_rate', 'bar_code'])
            ->get();

        $matches = [];
        $searchLower = strtolower($searchTerm);
        $searchWords = explode(' ', $searchLower);

        foreach ($items as $item) {
            $itemName = strtolower($item->name);
            
            // Calculate similarity score
            $score = 0;
            
            // Exact match
            if ($itemName === $searchLower) {
                $score = 100;
            }
            // Contains full search term
            elseif (strpos($itemName, $searchLower) !== false) {
                $score = 85;
            }
            // Search term contains item name
            elseif (strpos($searchLower, $itemName) !== false) {
                $score = 80;
            }
            else {
                // Word-by-word matching
                $matchedWords = 0;
                foreach ($searchWords as $word) {
                    if (strlen($word) >= 3 && strpos($itemName, $word) !== false) {
                        $matchedWords++;
                    }
                }
                if ($matchedWords > 0) {
                    $score = min(75, ($matchedWords / count($searchWords)) * 75);
                }
                
                // Levenshtein distance for similar names
                if ($score < 50) {
                    $distance = levenshtein($searchLower, $itemName);
                    $maxLen = max(strlen($searchLower), strlen($itemName));
                    if ($maxLen > 0) {
                        $similarity = (1 - ($distance / $maxLen)) * 100;
                        if ($similarity > $score) {
                            $score = $similarity;
                        }
                    }
                }
                
                // Soundex matching for phonetic similarity
                if ($score < 40) {
                    $searchSoundex = soundex($searchLower);
                    $itemSoundex = soundex($itemName);
                    if ($searchSoundex === $itemSoundex) {
                        $score = max($score, 40);
                    }
                }
            }
            
            if ($score >= 30) { // Minimum threshold
                $matches[] = [
                    'item' => $item->toArray(),
                    'confidence' => round($score, 2),
                    'match_type' => $this->getMatchType($score)
                ];
            }
        }

        // Sort by confidence descending
        usort($matches, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        // Return top 5 matches
        return array_slice($matches, 0, 5);
    }

    /**
     * Get match type description based on confidence score
     */
    protected function getMatchType(float $score): string
    {
        if ($score >= 90) return 'exact';
        if ($score >= 75) return 'strong';
        if ($score >= 50) return 'partial';
        if ($score >= 30) return 'weak';
        return 'none';
    }
}
