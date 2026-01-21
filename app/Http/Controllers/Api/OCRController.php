<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\GeminiOCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OCRController extends Controller
{
    /**
     * Space OCR API URL - Update this with your actual API endpoint
     */
    protected $ocrApiUrl;
    protected $ocrApiKey;
    protected $geminiService;

    public function __construct(GeminiOCRService $geminiService)
    {
        // These should be set in your .env file
        $this->ocrApiUrl = config('services.space_ocr.url', 'https://api.ocr.space/parse/image');
        $this->ocrApiKey = config('services.space_ocr.key', '');
        $this->geminiService = $geminiService;
    }

    /**
     * Extract text from selected area of receipt image
     */
    public function extractText(Request $request)
    {
        try {
            // Increase execution time for OCR processing
            set_time_limit(180);
            
            $request->validate([
                'image' => 'required|string', // Base64 image data
                'selection' => 'nullable|array',
            ]);

            $imageData = $request->input('image');
            
            // Remove data URL prefix if present
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }

            // Decode base64
            $imageContent = base64_decode($imageData);
            
            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }
            
            // Check image size early - reject very large images
            $imageSizeMB = strlen($imageContent) / (1024 * 1024);
            if ($imageSizeMB > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image too large (' . round($imageSizeMB, 2) . 'MB). Please select a smaller area.'
                ], 400);
            }

            // Check if Gemini should be used (priority: Gemini > OCR.space > Tesseract > Google Vision)
            $useGemini = $request->input('use_gemini', true); // Default to Gemini if available
            
            if ($useGemini && $this->geminiService->isAvailable()) {
                Log::info('Using Gemini for text extraction');
                $extractedText = $this->geminiService->extractText($imageContent);
            } else {
                // Call OCR API (fallback to existing services)
                $extractedText = $this->callOCRApi($imageContent);
            }

            if ($extractedText === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'OCR processing failed'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'text' => $extractedText,
                'message' => 'Text extracted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('OCR extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error extracting text: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Call Space OCR API or alternative OCR service
     */
    protected function callOCRApi($imageContent)
    {
        try {
            // Option 1: OCR.space API (free tier available)
            if (!empty($this->ocrApiKey)) {
                Log::info('Using OCR.space API for text extraction');
                return $this->callOCRSpaceApi($imageContent);
            }

            // Option 2: Local Tesseract OCR (if installed)
            if ($this->isTesseractAvailable()) {
                Log::info('Using local Tesseract for text extraction');
                return $this->callTesseractOCR($imageContent);
            }

            // Option 3: Google Cloud Vision (if configured)
            if (config('services.google_vision.key')) {
                Log::info('Using Google Cloud Vision for text extraction');
                return $this->callGoogleVisionApi($imageContent);
            }

            // No OCR service configured - return helpful error
            Log::warning('No OCR service configured. Please set SPACE_OCR_KEY in .env file.');
            
            // Return a helpful message instead of just false
            throw new \Exception('No OCR service configured. Please add SPACE_OCR_KEY to your .env file. Get a free API key from https://ocr.space/ocrapi');

        } catch (\Exception $e) {
            Log::error('OCR API call failed: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by extractText method
        }
    }

    /**
     * Call OCR.space API
     * Free tier: 500 requests/day, 1MB max file size
     */
    protected function callOCRSpaceApi($imageContent)
    {
        $maxRetries = 2;
        $lastException = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Optimize image to reduce size for API (max 1MB for free tier)
                $optimizedImage = $this->optimizeImageForOCR($imageContent);
                
                $base64Image = 'data:image/jpeg;base64,' . base64_encode($optimizedImage);
                
                // Check image size (OCR.space free tier limit is 1MB)
                $imageSizeKB = strlen($optimizedImage) / 1024;
                Log::info("OCR Image size: {$imageSizeKB}KB (Attempt {$attempt}/{$maxRetries})");
                
                if ($imageSizeKB > 1024) {
                    Log::warning('Image size exceeds 1MB limit, may fail on free tier');
                }
                
                Log::info('Calling OCR.space API with key: ' . substr($this->ocrApiKey, 0, 5) . '...');

                $response = Http::timeout(120) // Increased timeout to 120 seconds
                    ->retry(2, 5000) // Retry up to 2 times with 5 second delay
                    ->asForm()
                    ->post($this->ocrApiUrl, [
                        'apikey' => $this->ocrApiKey,
                        'base64Image' => $base64Image,
                        'language' => 'eng',
                        'isOverlayRequired' => 'false',
                        'detectOrientation' => 'true',
                        'scale' => 'true',
                        'OCREngine' => '2', // Engine 2 is more accurate
                    ]);

                Log::info('OCR.space response status: ' . $response->status());
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::info('OCR.space response data: ' . json_encode($data));
                    
                    if (isset($data['ParsedResults'][0]['ParsedText'])) {
                        return $data['ParsedResults'][0]['ParsedText'];
                    }

                    if (isset($data['ErrorMessage'])) {
                        $errorMsg = is_array($data['ErrorMessage']) 
                            ? implode(', ', $data['ErrorMessage']) 
                            : $data['ErrorMessage'];
                        Log::error('OCR.space error: ' . $errorMsg);
                        throw new \Exception('OCR.space API error: ' . $errorMsg);
                    }
                    
                    if (isset($data['IsErroredOnProcessing']) && $data['IsErroredOnProcessing']) {
                        $errorDetail = $data['ParsedResults'][0]['ErrorMessage'] ?? 'Unknown processing error';
                        Log::error('OCR.space processing error: ' . $errorDetail);
                        throw new \Exception('OCR processing error: ' . $errorDetail);
                    }
                } else {
                    Log::error('OCR.space HTTP error: ' . $response->status() . ' - ' . $response->body());
                    throw new \Exception('OCR.space API HTTP error: ' . $response->status());
                }

                return false;
                
            } catch (\Exception $e) {
                $lastException = $e;
                Log::error("OCR.space attempt {$attempt} failed: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    Log::info("Retrying OCR request in 1 second...");
                    sleep(1);
                }
            }
        }
        
        throw $lastException ?? new \Exception('OCR request failed after multiple attempts');
    }
    
    /**
     * Optimize image for OCR API (reduce size while maintaining quality)
     */
    protected function optimizeImageForOCR($imageContent)
    {
        try {
            // Check if GD is available
            if (!function_exists('imagecreatefromstring')) {
                Log::warning('GD library not available, using original image');
                return $imageContent;
            }
            
            // Create image from content
            $image = @imagecreatefromstring($imageContent);
            
            if (!$image) {
                Log::warning('Could not create image from content, using original');
                return $imageContent;
            }
            
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            
            // Reduce max dimension for faster processing
            $maxDimension = 1500; // Reduced from 2000 for faster processing
            
            if ($originalWidth > $maxDimension || $originalHeight > $maxDimension) {
                // Calculate new dimensions
                $ratio = min($maxDimension / $originalWidth, $maxDimension / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
                
                // Create resized image - use faster method
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                
                // Use faster resize for speed
                imagecopyresized($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                imagedestroy($image);
                $image = $resized;
                
                Log::info("Image resized from {$originalWidth}x{$originalHeight} to {$newWidth}x{$newHeight}");
            }
            
            // Output as JPEG with quality 75 (faster, smaller file)
            ob_start();
            imagejpeg($image, null, 75);
            $optimized = ob_get_clean();
            imagedestroy($image);
            
            $originalSize = strlen($imageContent) / 1024;
            $newSize = strlen($optimized) / 1024;
            Log::info("Image optimized: {$originalSize}KB -> {$newSize}KB");
            
            return $optimized;
            
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage());
            return $imageContent; // Return original if optimization fails
        }
    }

    /**
     * Call local Tesseract OCR
     */
    protected function callTesseractOCR($imageContent)
    {
        // Save temporary image file
        $tempFile = sys_get_temp_dir() . '/ocr_' . uniqid() . '.jpg';
        file_put_contents($tempFile, $imageContent);

        try {
            // Run Tesseract command
            $command = sprintf(
                'tesseract "%s" stdout -l eng --oem 3 --psm 6 2>/dev/null',
                $tempFile
            );

            $output = shell_exec($command);
            
            return $output ?: false;

        } finally {
            // Clean up temp file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Check if Tesseract is available
     */
    protected function isTesseractAvailable()
    {
        $output = shell_exec('tesseract --version 2>&1');
        return strpos($output, 'tesseract') !== false;
    }

    /**
     * Call Google Cloud Vision API
     */
    protected function callGoogleVisionApi($imageContent)
    {
        $apiKey = config('services.google_vision.key');
        $url = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";

        $response = Http::timeout(30)->post($url, [
            'requests' => [
                [
                    'image' => [
                        'content' => base64_encode($imageContent)
                    ],
                    'features' => [
                        ['type' => 'TEXT_DETECTION']
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
                return $data['responses'][0]['textAnnotations'][0]['description'];
            }
        }

        return false;
    }

    /**
     * Search items based on extracted text with SMART SCORING
     * Returns top 30 items with highest match probability
     */
    public function searchItems(Request $request)
    {
        try {
            $request->validate([
                'search_terms' => 'required|array',
                'limit' => 'nullable|integer|max:100'
            ]);

            $searchTerms = $request->input('search_terms', []);
            $limit = $request->input('limit', 30); // Default 30 best matches
            
            // Get organization_id from authenticated user
            $organizationId = auth()->user()->organization_id ?? null;

            Log::info('OCR Smart Search - Terms: ' . json_encode($searchTerms) . ', Org: ' . $organizationId);

            // Clean search terms (4+ characters only)
            $cleanedTerms = [];
            foreach ($searchTerms as $term) {
                $term = trim($term);
                if (strlen($term) < 4) continue;
                $cleanTerm = preg_replace('/[^a-zA-Z0-9\s]/', '', $term);
                $cleanTerm = preg_replace('/\s+/', ' ', trim($cleanTerm));
                if (strlen($cleanTerm) >= 4) {
                    $cleanedTerms[] = strtolower($cleanTerm);
                }
            }
            $cleanedTerms = array_unique($cleanedTerms);

            if (empty($cleanedTerms)) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'count' => 0
                ]);
            }

            // Get all items for this organization
            $allItems = Item::where('organization_id', $organizationId)
                ->where('is_deleted', 0)
                ->select([
                    'id', 'name', 'packing', 'company_id', 'company_short_name',
                    'mrp', 's_rate', 'ws_rate', 'hsn_code', 'cgst_percent', 
                    'sgst_percent', 'igst_percent', 'bar_code', 'unit'
                ])
                ->with(['company:id,short_name,name'])
                ->get();

            // Calculate match score for each item
            $scoredItems = [];
            
            foreach ($allItems as $item) {
                $itemName = strtolower($item->name);
                $maxScore = 0;
                $matchedTerm = '';
                
                foreach ($cleanedTerms as $term) {
                    $score = $this->calculateMatchScore($itemName, $term, $item->bar_code ?? '');
                    
                    if ($score > $maxScore) {
                        $maxScore = $score;
                        $matchedTerm = $term;
                    }
                }
                
                // Only include items with score > 0
                if ($maxScore > 0) {
                    $itemArray = $item->toArray();
                    $itemArray['match_score'] = $maxScore;
                    $itemArray['matched_term'] = $matchedTerm;
                    $scoredItems[] = $itemArray;
                }
            }

            // Sort by score (highest first) and take top 30
            usort($scoredItems, function($a, $b) {
                return $b['match_score'] <=> $a['match_score'];
            });

            $topItems = array_slice($scoredItems, 0, $limit);
            
            Log::info('OCR Smart Search - Found ' . count($scoredItems) . ' matches, returning top ' . count($topItems));

            return response()->json([
                'success' => true,
                'items' => $topItems,
                'count' => count($topItems),
                'total_matches' => count($scoredItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Item search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate match score between item name and search term
     * Higher score = better match probability
     * 
     * Scoring:
     * - Exact match: 100 points
     * - Item name starts with term: 90 points
     * - Item name contains term: 70 points
     * - First 4 characters match: 60 points
     * - Word in item matches term: 50 points
     * - Barcode match: 80 points
     * - Partial match (40%+ overlap): 30-50 points
     */
    protected function calculateMatchScore(string $itemName, string $searchTerm, string $barcode = ''): int
    {
        $score = 0;
        $itemName = strtolower(trim($itemName));
        $searchTerm = strtolower(trim($searchTerm));
        
        // Exact match
        if ($itemName === $searchTerm) {
            return 100;
        }
        
        // Barcode exact match
        if (!empty($barcode) && strtolower($barcode) === $searchTerm) {
            return 95;
        }
        
        // Item name starts with search term
        if (strpos($itemName, $searchTerm) === 0) {
            return 90;
        }
        
        // Barcode contains search term
        if (!empty($barcode) && strpos(strtolower($barcode), $searchTerm) !== false) {
            return 80;
        }
        
        // Item name contains search term
        if (strpos($itemName, $searchTerm) !== false) {
            return 70;
        }
        
        // First 4 characters match (important for medicine names)
        $termFirst4 = substr($searchTerm, 0, 4);
        if (strlen($termFirst4) >= 4 && strpos($itemName, $termFirst4) === 0) {
            return 60;
        }
        
        // Any word in item name starts with first 4 characters
        $itemWords = explode(' ', $itemName);
        foreach ($itemWords as $word) {
            if (strlen($word) >= 4) {
                $wordFirst4 = substr($word, 0, 4);
                if ($wordFirst4 === $termFirst4) {
                    return 55;
                }
            }
        }
        
        // Check if search term matches start of any word in item name
        foreach ($itemWords as $word) {
            if (strpos($word, $searchTerm) === 0) {
                return 50;
            }
        }
        
        // Check reverse - item word at start of search term
        foreach ($itemWords as $word) {
            if (strlen($word) >= 4 && strpos($searchTerm, $word) === 0) {
                return 45;
            }
        }
        
        // Partial match using similar_text percentage
        similar_text($itemName, $searchTerm, $percent);
        if ($percent >= 60) {
            return 40;
        } elseif ($percent >= 40) {
            return 30;
        }
        
        // Levenshtein distance for close matches (only for similar length strings)
        $lenDiff = abs(strlen($itemName) - strlen($searchTerm));
        if ($lenDiff <= 5) {
            $distance = levenshtein($searchTerm, substr($itemName, 0, strlen($searchTerm) + 5));
            if ($distance <= 2) {
                return 35;
            } elseif ($distance <= 4) {
                return 25;
            }
        }
        
        return 0;
    }

    /**
     * Get OCR service status
     */
    public function status()
    {
        $services = [];

        // Check OCR.space API
        if (!empty($this->ocrApiKey)) {
            $services['ocr_space'] = [
                'available' => true,
                'name' => 'OCR.space API'
            ];
        }

        // Check Tesseract
        $services['tesseract'] = [
            'available' => $this->isTesseractAvailable(),
            'name' => 'Local Tesseract OCR'
        ];

        // Check Google Vision
        $services['google_vision'] = [
            'available' => !empty(config('services.google_vision.key')),
            'name' => 'Google Cloud Vision'
        ];

        // Check Gemini
        $services['gemini'] = [
            'available' => $this->geminiService->isAvailable(),
            'name' => 'Google Gemini AI',
            'features' => ['multi_receipt', 'auto_align', 'smart_matching']
        ];

        return response()->json([
            'success' => true,
            'services' => $services,
            'active' => collect($services)->first(fn($s) => $s['available'])['name'] ?? null
        ]);
    }

    /**
     * Analyze receipt using Gemini AI (full analysis)
     */
    public function analyzeWithGemini(Request $request)
    {
        try {
            set_time_limit(180);
            
            if (!$this->geminiService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API is not configured. Please add GEMINI_API_KEY to your .env file.'
                ], 400);
            }

            $request->validate([
                'image' => 'required|string',
            ]);

            $imageData = $request->input('image');
            
            // Remove data URL prefix if present
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }

            $imageContent = base64_decode($imageData);
            
            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            // Perform full analysis with Gemini
            $analysis = $this->geminiService->analyzeReceipt($imageContent, ['mode' => 'full_analysis']);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'Receipt analyzed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect and extract multiple receipts from single scan
     */
    public function detectMultipleReceipts(Request $request)
    {
        try {
            set_time_limit(180);
            
            if (!$this->geminiService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API is not configured.'
                ], 400);
            }

            $request->validate([
                'image' => 'required|string',
            ]);

            $imageData = $request->input('image');
            
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }

            $imageContent = base64_decode($imageData);
            
            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            $result = $this->geminiService->analyzeReceipt($imageContent, ['mode' => 'multi_receipt']);

            return response()->json([
                'success' => true,
                'multiple_receipts_detected' => $result['multiple_receipts_detected'] ?? false,
                'receipt_count' => $result['receipt_count'] ?? 0,
                'receipts' => $result['receipts'] ?? [],
                'message' => 'Multi-receipt detection completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Multi-receipt detection error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error detecting receipts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check image quality and get alignment suggestions
     */
    public function checkImageQuality(Request $request)
    {
        try {
            set_time_limit(180);
            
            if (!$this->geminiService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API is not configured.'
                ], 400);
            }

            $request->validate([
                'image' => 'required|string',
            ]);

            $imageData = $request->input('image');
            
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }

            $imageContent = base64_decode($imageData);
            
            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            $quality = $this->geminiService->analyzeImageQuality($imageContent);

            return response()->json([
                'success' => true,
                'quality' => $quality,
                'message' => 'Image quality analyzed'
            ]);

        } catch (\Exception $e) {
            Log::error('Image quality check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking image quality: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract structured items from receipt using Gemini
     */
    public function extractItemsWithGemini(Request $request)
    {
        try {
            set_time_limit(180);
            
            if (!$this->geminiService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API is not configured.'
                ], 400);
            }

            $request->validate([
                'image' => 'required|string',
            ]);

            $imageData = $request->input('image');
            
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }

            $imageContent = base64_decode($imageData);
            
            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            // Extract items
            $result = $this->geminiService->extractItems($imageContent);
            
            // Match with database items if organization_id provided
            $organizationId = auth()->user()->organization_id ?? null;
            $matchedItems = [];
            
            if ($organizationId && isset($result['items'])) {
                $matchedItems = $this->geminiService->matchWithDatabaseItems($result['items'], $organizationId);
            }

            return response()->json([
                'success' => true,
                'extracted' => $result,
                'matched_items' => $matchedItems,
                'message' => 'Items extracted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini item extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error extracting items: ' . $e->getMessage()
            ], 500);
        }
    }
}
