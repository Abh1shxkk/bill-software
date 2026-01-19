<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
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

    public function __construct()
    {
        // These should be set in your .env file
        $this->ocrApiUrl = config('services.space_ocr.url', 'https://api.ocr.space/parse/image');
        $this->ocrApiKey = config('services.space_ocr.key', '');
    }

    /**
     * Extract text from selected area of receipt image
     */
    public function extractText(Request $request)
    {
        try {
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

            // Call OCR API
            $extractedText = $this->callOCRApi($imageContent);

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
        try {
            $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageContent);
            
            Log::info('Calling OCR.space API with key: ' . substr($this->ocrApiKey, 0, 5) . '...');

            $response = Http::timeout(60)
                ->asForm()
                ->post($this->ocrApiUrl, [
                    'apikey' => $this->ocrApiKey,
                    'base64Image' => $base64Image,
                    'language' => 'eng',
                    'isOverlayRequired' => 'false',
                    'detectOrientation' => 'true',
                    'scale' => 'true',
                    'OCREngine' => '2',
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
            Log::error('OCR.space exception: ' . $e->getMessage());
            throw $e;
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
     * Search items based on extracted text
     */
    public function searchItems(Request $request)
    {
        try {
            $request->validate([
                'search_terms' => 'required|array',
                'limit' => 'nullable|integer|max:50'
            ]);

            $searchTerms = $request->input('search_terms', []);
            $limit = $request->input('limit', 20);
            
            // Get organization_id from authenticated user
            $organizationId = auth()->user()->organization_id ?? null;

            Log::info('OCR Item Search - Terms: ' . json_encode($searchTerms) . ', Org: ' . $organizationId);

            $items = collect();

            foreach ($searchTerms as $term) {
                // Clean and prepare search term
                $term = trim($term);
                if (strlen($term) < 2) continue;
                
                // Remove extra spaces and special characters for search
                $cleanTerm = preg_replace('/[^a-zA-Z0-9\s]/', '', $term);
                $cleanTerm = preg_replace('/\s+/', ' ', trim($cleanTerm));
                
                Log::info('OCR Item Search - Searching for term: ' . $cleanTerm);

                // Search items by name with flexible matching
                $results = Item::where('organization_id', $organizationId)
                    ->where('is_deleted', 0)
                    ->where(function($query) use ($cleanTerm, $term) {
                        // Match anywhere in name (case-insensitive)
                        $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cleanTerm) . '%'])
                            // Or match start of name
                            ->orWhereRaw('LOWER(name) LIKE ?', [strtolower($cleanTerm) . '%'])
                            // Or match by bar code
                            ->orWhere('bar_code', 'LIKE', "%{$term}%");
                        
                        // Also try each word separately for multi-word searches
                        $words = explode(' ', $cleanTerm);
                        if (count($words) > 1) {
                            foreach ($words as $word) {
                                if (strlen($word) >= 3) {
                                    $query->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($word) . '%']);
                                }
                            }
                        }
                    })
                    ->select([
                        'id', 'name', 'packing', 'company_id', 'company_short_name',
                        'mrp', 's_rate', 'ws_rate', 'hsn_code', 'cgst_percent', 
                        'sgst_percent', 'igst_percent', 'bar_code', 'unit'
                    ])
                    ->with(['company:id,short_name,name'])
                    ->limit(15)
                    ->get();

                Log::info('OCR Item Search - Found ' . $results->count() . ' items for term: ' . $cleanTerm);

                $items = $items->merge($results);
            }

            // Remove duplicates and limit results
            $uniqueItems = $items->unique('id')->take($limit)->values();
            
            Log::info('OCR Item Search - Total unique items: ' . $uniqueItems->count());

            return response()->json([
                'success' => true,
                'items' => $uniqueItems,
                'count' => $uniqueItems->count()
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

        return response()->json([
            'success' => true,
            'services' => $services,
            'active' => collect($services)->first(fn($s) => $s['available'])['name'] ?? null
        ]);
    }
}
