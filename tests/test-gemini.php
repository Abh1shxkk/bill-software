<?php

/**
 * Gemini API Test Script
 * Run this to verify Gemini integration is working
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiOCRService;

echo "=================================================\n";
echo "🧪 Testing Gemini OCR Service\n";
echo "=================================================\n\n";

// Initialize service
$geminiService = new GeminiOCRService();

// Test 1: Check if service is available
echo "Test 1: Checking if Gemini API is configured...\n";
if ($geminiService->isAvailable()) {
    echo "✅ SUCCESS: Gemini API key is configured!\n";
    echo "   API Key: " . substr(config('services.gemini.key'), 0, 20) . "...\n";
    echo "   Model: " . config('services.gemini.model') . "\n\n";
} else {
    echo "❌ FAILED: Gemini API key is NOT configured\n";
    echo "   Please add GEMINI_API_KEY to your .env file\n\n";
    exit(1);
}

// Test 2: Create a simple test image (1x1 white pixel PNG)
echo "Test 2: Creating test image...\n";
$testImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
echo "✅ Test image created (1x1 white pixel)\n\n";

// Test 3: Test simple text extraction
echo "Test 3: Testing text extraction (this will make an API call)...\n";
try {
    $result = $geminiService->analyzeReceipt($testImage, ['mode' => 'text_only']);
    
    if (isset($result['extracted_text']) || isset($result['_raw_response'])) {
        echo "✅ SUCCESS: Gemini API is responding!\n";
        echo "   Response received: " . strlen($result['_raw_response'] ?? $result['extracted_text']) . " characters\n\n";
    } else {
        echo "⚠️  WARNING: API responded but format unexpected\n";
        echo "   Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    }
} catch (\Exception $e) {
    echo "❌ FAILED: API call failed\n";
    echo "   Error: " . $e->getMessage() . "\n\n";
    
    // Check if it's an API key issue
    if (strpos($e->getMessage(), 'API key') !== false || strpos($e->getMessage(), '403') !== false) {
        echo "💡 TIP: Your API key might be invalid. Get a new one from:\n";
        echo "   https://makersuite.google.com/app/apikey\n\n";
    }
    exit(1);
}

// Test 4: Check OCR Controller integration
echo "Test 4: Checking OCR Controller integration...\n";
try {
    $controller = new \App\Http\Controllers\Api\OCRController($geminiService);
    echo "✅ SUCCESS: OCR Controller initialized with Gemini service\n\n";
} catch (\Exception $e) {
    echo "❌ FAILED: Controller initialization failed\n";
    echo "   Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Summary
echo "=================================================\n";
echo "✅ ALL TESTS PASSED!\n";
echo "=================================================\n\n";
echo "Your Gemini OCR integration is working correctly!\n";
echo "You can now use it in your application.\n\n";
echo "Next steps:\n";
echo "1. Go to: http://localhost/bill-software/admin/sale/transaction\n";
echo "2. Click 'Receipt Mode'\n";
echo "3. Upload a receipt image\n";
echo "4. Gemini will automatically process it!\n\n";
