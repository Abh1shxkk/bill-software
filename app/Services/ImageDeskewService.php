<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * ImageDeskewService - Handles image straightening and deskewing
 * 
 * Used to automatically straighten tilted/crooked receipts in scanned images
 * and fill the background with white color.
 */
class ImageDeskewService
{
    protected $geminiService;

    public function __construct(GeminiOCRService $geminiService = null)
    {
        $this->geminiService = $geminiService ?? new GeminiOCRService();
    }

    /**
     * Process a receipt image - detect tilt, straighten, and fill with white background
     * 
     * @param string $imageContent Raw image binary content
     * @param array $options Optional settings
     * @return array ['success' => bool, 'image' => base64, 'tilt_angle' => float, 'message' => string]
     */
    public function processReceiptImage($imageContent, array $options = []): array
    {
        try {
            Log::info('ImageDeskewService: Starting receipt processing (crop + manual rotation)', [
                'image_size_kb' => strlen($imageContent) / 1024
            ]);

            // Get manual angle if provided
            $manualAngle = $options['angle'] ?? 0;

            // Step 1: Extract receipt (remove white space)
            $processedImage = $this->straightenImage($imageContent, $manualAngle, null, false);

            if (!$processedImage) {
                Log::error('ImageDeskewService: Failed to process image');
                return [
                    'success' => true,
                    'image' => base64_encode($imageContent),
                    'tilt_angle' => 0,
                    'was_processed' => false,
                    'message' => 'Processing failed, returning original'
                ];
            }

            Log::info('ImageDeskewService: Image processed successfully', [
                'original_size_kb' => strlen($imageContent) / 1024,
                'processed_size_kb' => strlen($processedImage) / 1024,
                'angle_applied' => $manualAngle
            ]);

            return [
                'success' => true,
                'image' => base64_encode($processedImage),
                'tilt_angle' => $manualAngle,
                'was_processed' => true,
                'message' => $manualAngle != 0 ? "Receipt extracted and rotated by {$manualAngle}°" : "Receipt extracted from background"
            ];

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'image' => base64_encode($imageContent),
                'tilt_angle' => 0,
                'was_processed' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Try auto-detection of tilt using GD
     */
    protected function tryAutoDetection($imageContent): float
    {
        try {
            if (!function_exists('imagecreatefromstring')) {
                Log::debug('ImageDeskewService: GD not available for auto-detection');
                return 0;
            }

            $image = @imagecreatefromstring($imageContent);
            if (!$image) {
                Log::warning('ImageDeskewService: Could not create image for auto-detection');
                return 0;
            }

            // Clone the image for edge detection (so we don't modify original)
            $width = imagesx($image);
            $height = imagesy($image);
            $clone = imagecreatetruecolor($width, $height);
            imagecopy($clone, $image, 0, 0, 0, 0, $width, $height);
            imagedestroy($image);

            // Detect angle on the clone
            $angle = $this->detectRotationAngleGD($clone);
            imagedestroy($clone);

            return $angle;

        } catch (\Exception $e) {
            Log::warning('ImageDeskewService: Auto-detection exception', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Analyze image for deskewing using Gemini AI
     * 
     * @param string $imageContent Raw image binary
     * @return array Analysis results
     */
    protected function analyzeImageForDeskew($imageContent): array
    {
        try {
            if (!$this->geminiService->isAvailable()) {
                Log::warning('ImageDeskewService: Gemini not available');
                return [
                    'success' => false,
                    'message' => 'Gemini API not available'
                ];
            }

            // Use the detect_boundaries mode to get detailed info
            $result = $this->geminiService->analyzeReceipt($imageContent, ['mode' => 'detect_boundaries']);

            // Parse the response
            $tiltAngle = $result['tilt_angle_degrees'] ?? $result['tilt_angle'] ?? 0;
            $boundaries = $result['corners'] ?? $result['boundaries'] ?? null;
            $hasDarkBackground = $result['has_dark_background'] ?? false;
            $confidence = $result['confidence'] ?? 0;

            Log::info('ImageDeskewService: detect_boundaries result', [
                'tilt_angle' => $tiltAngle,
                'confidence' => $confidence
            ]);

            // If tilt is 0 or very low confidence, try quality_check as fallback
            // Sometimes quality_check gives better tilt detection
            if (abs($tiltAngle) < 1 && $confidence < 80) {
                Log::info('ImageDeskewService: Trying quality_check fallback for better tilt detection');
                try {
                    $qualityResult = $this->geminiService->analyzeImageQuality($imageContent);
                    $qualityTilt = floatval($qualityResult['tilt_angle_degrees'] ?? 0);
                    
                    Log::info('ImageDeskewService: quality_check tilt result', ['tilt_angle' => $qualityTilt]);
                    
                    // Use quality_check tilt if it's non-zero
                    if (abs($qualityTilt) >= 1) {
                        $tiltAngle = $qualityTilt;
                    }
                } catch (\Exception $e) {
                    Log::warning('ImageDeskewService: quality_check fallback failed', ['error' => $e->getMessage()]);
                }
            }

            return [
                'success' => true,
                'tilt_angle' => floatval($tiltAngle),
                'boundaries' => $boundaries,
                'has_dark_background' => $hasDarkBackground,
                'confidence' => $confidence
            ];

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: Gemini analysis failed', [
                'error' => $e->getMessage()
            ]);

            // Fallback to quality check
            try {
                $qualityResult = $this->geminiService->analyzeImageQuality($imageContent);
                return [
                    'success' => true,
                    'tilt_angle' => floatval($qualityResult['tilt_angle_degrees'] ?? 0),
                    'boundaries' => null,
                    'has_dark_background' => false,
                    'confidence' => 50
                ];
            } catch (\Exception $fallbackError) {
                return [
                    'success' => false,
                    'message' => 'Analysis failed: ' . $fallbackError->getMessage()
                ];
            }
        }
    }

    /**
     * Straighten (rotate) an image by the specified angle with symmetry correction
     * 
     * @param string $imageContent Raw image binary
     * @param float $angle Rotation angle in degrees (positive = counter-clockwise)
     * @param array|null $boundaries Receipt corner coordinates (for cropping)
     * @param bool $fillWhiteBackground Whether to replace dark scanner background with white
     * @return string|null Processed image binary or null on failure
     */
    protected function straightenImage($imageContent, float $angle, ?array $boundaries, bool $fillWhiteBackground): ?string
    {
        // Use GD for simple rotation and cropping
        return $this->straightenWithGD($imageContent, $angle, $boundaries, $fillWhiteBackground);
    }

    /**
     * Advanced straightening with perspective correction and symmetry alignment
     * Uses edge detection and Hough transform for better accuracy
     */
    protected function straightenWithPerspectiveCorrection($imageContent, ?array $boundaries): ?string
    {
        try {
            if (!extension_loaded('imagick')) {
                Log::warning('ImageDeskewService: Imagick required for perspective correction');
                return null;
            }

            $imagick = new \Imagick();
            $imagick->readImageBlob($imageContent);

            // Convert to grayscale for edge detection
            $clone = clone $imagick;
            $clone->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
            
            // Apply edge detection to find receipt boundaries
            $clone->edgeImage(1);
            $clone->thresholdImage(0.5 * \Imagick::getQuantum());

            // If boundaries provided, use 4-point perspective transform
            if ($boundaries && $this->validateBoundaries($boundaries)) {
                $width = $imagick->getImageWidth();
                $height = $imagick->getImageHeight();

                // Convert percentage to pixels
                $srcPoints = [
                    $boundaries['top_left']['x'] * $width / 100,
                    $boundaries['top_left']['y'] * $height / 100,
                    $boundaries['top_right']['x'] * $width / 100,
                    $boundaries['top_right']['y'] * $height / 100,
                    $boundaries['bottom_right']['x'] * $width / 100,
                    $boundaries['bottom_right']['y'] * $height / 100,
                    $boundaries['bottom_left']['x'] * $width / 100,
                    $boundaries['bottom_left']['y'] * $height / 100,
                ];

                // Calculate destination rectangle (perfectly aligned)
                $minX = min($srcPoints[0], $srcPoints[6]);
                $maxX = max($srcPoints[2], $srcPoints[4]);
                $minY = min($srcPoints[1], $srcPoints[3]);
                $maxY = max($srcPoints[5], $srcPoints[7]);
                
                $rectWidth = $maxX - $minX;
                $rectHeight = $maxY - $minY;

                $dstPoints = [
                    0, 0,                    // top-left
                    $rectWidth, 0,           // top-right
                    $rectWidth, $rectHeight, // bottom-right
                    0, $rectHeight           // bottom-left
                ];

                // Apply perspective distortion
                $imagick->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_WHITE);
                $imagick->distortImage(\Imagick::DISTORTION_PERSPECTIVE, $srcPoints + $dstPoints, true);
            }

            $clone->clear();
            $clone->destroy();

            // Final cleanup
            $imagick->setImageBackgroundColor(new \ImagickPixel('#FFFFFF'));
            $imagick->trimImage(0);
            $imagick->borderImage(new \ImagickPixel('#FFFFFF'), 20, 20);
            
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(92);

            $result = $imagick->getImageBlob();
            $imagick->clear();
            $imagick->destroy();

            return $result;

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: Perspective correction failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Straighten image using Imagick extension with improved symmetry
     */
    protected function straightenWithImagick($imageContent, float $angle, ?array $boundaries, bool $fillWhiteBackground): ?string
    {
        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($imageContent);

            // Set white background color
            $imagick->setImageBackgroundColor(new \ImagickPixel('#FFFFFF'));

            // If we need to fill dark background with white
            if ($fillWhiteBackground) {
                // Replace near-black colors with white (typical scanner background)
                $imagick->whiteThresholdImage(new \ImagickPixel('rgb(30,30,30)'));
            }

            // Try perspective correction first if boundaries available
            if ($boundaries && $this->validateBoundaries($boundaries)) {
                Log::info('ImageDeskewService: Attempting perspective correction');
                $perspectiveCorrected = $this->applyPerspectiveCorrection($imagick, $boundaries);
                if ($perspectiveCorrected) {
                    Log::info('ImageDeskewService: Perspective correction successful');
                    return $perspectiveCorrected;
                }
            }

            // Fallback to rotation if perspective correction not available
            if (abs($angle) >= 0.5) {
                // Rotate image (negative angle because Imagick rotates clockwise for positive)
                $imagick->rotateImage(new \ImagickPixel('#FFFFFF'), -$angle);
                
                // Apply deskew to fine-tune alignment
                try {
                    $imagick->deskewImage(40); // 40% threshold
                } catch (\Exception $e) {
                    Log::debug('ImageDeskewService: Deskew not available or failed');
                }
            }

            // Auto-crop white borders to tighten the image
            $imagick->trimImage(0);
            
            // Add small white padding for symmetry
            $imagick->borderImage(new \ImagickPixel('#FFFFFF'), 20, 20);

            // Convert back to JPEG
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(92);

            $result = $imagick->getImageBlob();
            $imagick->clear();
            $imagick->destroy();

            return $result;

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: Imagick processing failed', [
                'error' => $e->getMessage()
            ]);
            // Fallback to GD
            return $this->straightenWithGD($imageContent, $angle, $boundaries, $fillWhiteBackground);
        }
    }

    /**
     * Apply perspective correction using 4-point transform
     */
    protected function applyPerspectiveCorrection(\Imagick $imagick, array $boundaries): ?string
    {
        try {
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();

            // Convert percentage coordinates to pixels
            $topLeft = [
                'x' => ($boundaries['top_left']['x'] / 100) * $width,
                'y' => ($boundaries['top_left']['y'] / 100) * $height
            ];
            $topRight = [
                'x' => ($boundaries['top_right']['x'] / 100) * $width,
                'y' => ($boundaries['top_right']['y'] / 100) * $height
            ];
            $bottomRight = [
                'x' => ($boundaries['bottom_right']['x'] / 100) * $width,
                'y' => ($boundaries['bottom_right']['y'] / 100) * $height
            ];
            $bottomLeft = [
                'x' => ($boundaries['bottom_left']['x'] / 100) * $width,
                'y' => ($boundaries['bottom_left']['y'] / 100) * $height
            ];

            // Calculate the dimensions of the straightened receipt
            $widthTop = sqrt(pow($topRight['x'] - $topLeft['x'], 2) + pow($topRight['y'] - $topLeft['y'], 2));
            $widthBottom = sqrt(pow($bottomRight['x'] - $bottomLeft['x'], 2) + pow($bottomRight['y'] - $bottomLeft['y'], 2));
            $heightLeft = sqrt(pow($bottomLeft['x'] - $topLeft['x'], 2) + pow($bottomLeft['y'] - $topLeft['y'], 2));
            $heightRight = sqrt(pow($bottomRight['x'] - $topRight['x'], 2) + pow($bottomRight['y'] - $topRight['y'], 2));

            $newWidth = max($widthTop, $widthBottom);
            $newHeight = max($heightLeft, $heightRight);

            // Source points (current corners)
            $srcPoints = [
                $topLeft['x'], $topLeft['y'],
                $topRight['x'], $topRight['y'],
                $bottomRight['x'], $bottomRight['y'],
                $bottomLeft['x'], $bottomLeft['y']
            ];

            // Destination points (perfect rectangle)
            $dstPoints = [
                0, 0,
                $newWidth, 0,
                $newWidth, $newHeight,
                0, $newHeight
            ];

            // Apply perspective distortion
            $imagick->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_WHITE);
            $imagick->distortImage(\Imagick::DISTORTION_PERSPECTIVE, array_merge($srcPoints, $dstPoints), true);

            // Trim and add padding
            $imagick->trimImage(0);
            $imagick->borderImage(new \ImagickPixel('#FFFFFF'), 20, 20);

            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(92);

            return $imagick->getImageBlob();

        } catch (\Exception $e) {
            Log::warning('ImageDeskewService: Perspective correction failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Straighten image using GD extension (fallback) with improved symmetry
     * Includes auto-rotation detection and correction
     */
    protected function straightenWithGD($imageContent, float $angle, ?array $boundaries, bool $fillWhiteBackground): ?string
    {
        try {
            // Check if GD is available
            if (!function_exists('imagecreatefromstring')) {
                Log::error('ImageDeskewService: GD library not available');
                return null;
            }

            // Create image from content
            $image = @imagecreatefromstring($imageContent);
            if (!$image) {
                Log::error('ImageDeskewService: Could not create image from content');
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            Log::info('ImageDeskewService: Processing with GD (simple crop & rotate)', [
                'width' => $width,
                'height' => $height,
                'angle' => $angle
            ]);

            // Step 1: Auto-crop white borders (extract receipt from scanner background)
            $image = $this->autoCropReceipt($image);

            // Step 2: Rotate if angle provided
            if (abs($angle) >= 0.5) {
                $white = imagecolorallocate($image, 255, 255, 255);
                $rotated = imagerotate($image, $angle, $white);
                
                if ($rotated) {
                    imagedestroy($image);
                    $image = $rotated;
                    
                    // Crop again after rotation
                    $image = $this->autoCropReceipt($image);
                }
            }

            // Step 3: Add symmetric padding
            $image = $this->addSymmetricPaddingGD($image, 20);

            // Output as JPEG - high quality
            ob_start();
            imagejpeg($image, null, 95);
            $result = ob_get_clean();
            
            imagedestroy($image);

            return $result;

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: GD processing failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Auto-crop receipt from white background
     * Removes scanner white space around receipt
     * Uses aggressive detection to find receipt edges
     */
    protected function autoCropReceipt($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        Log::info('ImageDeskewService: Starting auto-crop', [
            'original_size' => "{$width}x{$height}"
        ]);
        
        // Convert to grayscale for better detection
        $gray = imagecreatetruecolor($width, $height);
        imagecopy($gray, $image, 0, 0, 0, 0, $width, $height);
        imagefilter($gray, IMG_FILTER_GRAYSCALE);
        imagefilter($gray, IMG_FILTER_CONTRAST, -20); // Increase contrast
        
        // More aggressive threshold - anything lighter than this is "background"
        $threshold = 250; // Very light gray/white

        // Find content boundaries with sampling
        $top = 0;
        $bottom = $height - 1;
        $left = 0;
        $right = $width - 1;

        // Find top boundary - scan every 10 pixels for speed
        for ($y = 0; $y < $height; $y++) {
            $darkPixels = 0;
            for ($x = 0; $x < $width; $x += 10) {
                $rgb = imagecolorat($gray, $x, $y);
                $brightness = $rgb & 0xFF;
                if ($brightness < $threshold) {
                    $darkPixels++;
                }
            }
            // If we found enough dark pixels (receipt content), this is the top
            if ($darkPixels > 3) {
                $top = max(0, $y - 10); // Add margin
                break;
            }
        }

        // Find bottom boundary
        for ($y = $height - 1; $y >= 0; $y--) {
            $darkPixels = 0;
            for ($x = 0; $x < $width; $x += 10) {
                $rgb = imagecolorat($gray, $x, $y);
                $brightness = $rgb & 0xFF;
                if ($brightness < $threshold) {
                    $darkPixels++;
                }
            }
            if ($darkPixels > 3) {
                $bottom = min($height - 1, $y + 10); // Add margin
                break;
            }
        }

        // Find left boundary
        for ($x = 0; $x < $width; $x++) {
            $darkPixels = 0;
            for ($y = 0; $y < $height; $y += 10) {
                $rgb = imagecolorat($gray, $x, $y);
                $brightness = $rgb & 0xFF;
                if ($brightness < $threshold) {
                    $darkPixels++;
                }
            }
            if ($darkPixels > 3) {
                $left = max(0, $x - 10); // Add margin
                break;
            }
        }

        // Find right boundary
        for ($x = $width - 1; $x >= 0; $x--) {
            $darkPixels = 0;
            for ($y = 0; $y < $height; $y += 10) {
                $rgb = imagecolorat($gray, $x, $y);
                $brightness = $rgb & 0xFF;
                if ($brightness < $threshold) {
                    $darkPixels++;
                }
            }
            if ($darkPixels > 3) {
                $right = min($width - 1, $x + 10); // Add margin
                break;
            }
        }

        imagedestroy($gray);

        // Calculate crop dimensions
        $cropWidth = $right - $left + 1;
        $cropHeight = $bottom - $top + 1;

        // Calculate how much we're removing
        $removedPercent = 100 - (($cropWidth * $cropHeight) / ($width * $height) * 100);

        Log::info('ImageDeskewService: Crop boundaries detected', [
            'top' => $top,
            'bottom' => $bottom,
            'left' => $left,
            'right' => $right,
            'crop_size' => "{$cropWidth}x{$cropHeight}",
            'removed_percent' => round($removedPercent, 1) . '%'
        ]);

        // Minimum size check - if crop is too small, return original
        if ($cropWidth < 100 || $cropHeight < 100) {
            Log::warning('ImageDeskewService: Crop area too small, returning original');
            return $image;
        }

        // If we're not removing much (< 5%), probably no background to remove
        if ($removedPercent < 5) {
            Log::info('ImageDeskewService: Minimal background detected, returning original');
            return $image;
        }

        // Create cropped image
        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        $white = imagecolorallocate($cropped, 255, 255, 255);
        imagefill($cropped, 0, 0, $white);
        
        imagecopy($cropped, $image, 0, 0, $left, $top, $cropWidth, $cropHeight);
        imagedestroy($image);

        return $cropped;
    }

    /**
     * Auto-detect rotation angle using Radon transform approximation (GD)
     * Fast and accurate method for document deskewing
     */
    protected function detectRotationAngleGD($image): float
    {
        try {
            $width = imagesx($image);
            $height = imagesy($image);

            Log::info('ImageDeskewService: GD auto-detection starting (Radon method)', [
                'width' => $width,
                'height' => $height
            ]);

            // Resize for faster processing
            $scale = 0.3;
            $smallWidth = max(200, (int)($width * $scale));
            $smallHeight = max(200, (int)($height * $scale));
            $small = imagecreatetruecolor($smallWidth, $smallHeight);
            imagecopyresampled($small, $image, 0, 0, 0, 0, $smallWidth, $smallHeight, $width, $height);

            // Convert to grayscale and apply threshold
            imagefilter($small, IMG_FILTER_GRAYSCALE);
            imagefilter($small, IMG_FILTER_CONTRAST, -50); // Increase contrast
            
            // Binarize image
            for ($y = 0; $y < $smallHeight; $y++) {
                for ($x = 0; $x < $smallWidth; $x++) {
                    $rgb = imagecolorat($small, $x, $y);
                    $gray = $rgb & 0xFF;
                    $color = ($gray < 128) ? 0 : 16777215; // Black or white
                    imagesetpixel($small, $x, $y, $color);
                }
            }

            // Test angles from -30 to +30 degrees
            $angles = [];
            $scores = [];
            
            // Coarse search: every 2 degrees
            for ($angle = -30; $angle <= 30; $angle += 2) {
                $score = $this->calculateAlignmentScore($small, $angle);
                $angles[] = $angle;
                $scores[] = $score;
            }

            // Find best angle from coarse search
            $maxScore = max($scores);
            $bestIndex = array_search($maxScore, $scores);
            $bestAngle = $angles[$bestIndex];

            // Fine search: around best angle in 0.5 degree steps
            $fineAngles = [];
            $fineScores = [];
            
            for ($angle = $bestAngle - 2; $angle <= $bestAngle + 2; $angle += 0.5) {
                $score = $this->calculateAlignmentScore($small, $angle);
                $fineAngles[] = $angle;
                $fineScores[] = $score;
            }

            $maxScore = max($fineScores);
            $bestIndex = array_search($maxScore, $fineScores);
            $finalAngle = $fineAngles[$bestIndex];

            imagedestroy($small);

            Log::info('ImageDeskewService: GD angle detection complete (Radon)', [
                'detected_angle' => round($finalAngle, 2),
                'score' => round($maxScore, 2)
            ]);

            // Return negative angle to correct the tilt
            return abs($finalAngle) > 0.3 ? -$finalAngle : 0;

        } catch (\Exception $e) {
            Log::error('ImageDeskewService: GD auto-detection error', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Calculate alignment score for a given angle
     * Higher score = better horizontal alignment of text lines
     */
    protected function calculateAlignmentScore($image, $angle): float
    {
        $width = imagesx($image);
        $height = imagesy($image);

        // Rotate image
        $white = imagecolorallocate($image, 255, 255, 255);
        $rotated = imagerotate($image, $angle, $white);
        
        if (!$rotated) return 0;

        $rotWidth = imagesx($rotated);
        $rotHeight = imagesy($rotated);

        // Calculate horizontal projection (sum of black pixels per row)
        $projection = [];
        for ($y = 0; $y < $rotHeight; $y++) {
            $blackPixels = 0;
            for ($x = 0; $x < $rotWidth; $x++) {
                $rgb = imagecolorat($rotated, $x, $y);
                if ($rgb == 0) { // Black pixel
                    $blackPixels++;
                }
            }
            $projection[] = $blackPixels;
        }

        imagedestroy($rotated);

        // Calculate variance of projection
        // Higher variance = text lines are more distinct = better alignment
        if (count($projection) == 0) return 0;
        
        $mean = array_sum($projection) / count($projection);
        $variance = 0;
        
        foreach ($projection as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        return sqrt($variance / count($projection)); // Return standard deviation
    }

    /**
     * Auto-trim white borders from image for perfect symmetry
     */
    protected function autoTrimImageGD($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $threshold = 250; // Near-white threshold

        // Find content boundaries
        $top = 0;
        $bottom = $height - 1;
        $left = 0;
        $right = $width - 1;

        // Find top boundary
        for ($y = 0; $y < $height; $y++) {
            $hasContent = false;
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $hasContent = true;
                    break;
                }
            }
            if ($hasContent) {
                $top = $y;
                break;
            }
        }

        // Find bottom boundary
        for ($y = $height - 1; $y >= 0; $y--) {
            $hasContent = false;
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $hasContent = true;
                    break;
                }
            }
            if ($hasContent) {
                $bottom = $y;
                break;
            }
        }

        // Find left boundary
        for ($x = 0; $x < $width; $x++) {
            $hasContent = false;
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $hasContent = true;
                    break;
                }
            }
            if ($hasContent) {
                $left = $x;
                break;
            }
        }

        // Find right boundary
        for ($x = $width - 1; $x >= 0; $x--) {
            $hasContent = false;
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $hasContent = true;
                    break;
                }
            }
            if ($hasContent) {
                $right = $x;
                break;
            }
        }

        // Calculate crop dimensions
        $cropWidth = $right - $left + 1;
        $cropHeight = $bottom - $top + 1;

        // Minimum size check
        if ($cropWidth < 100 || $cropHeight < 100) {
            return $image; // Don't crop if result would be too small
        }

        // Create cropped image
        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        $white = imagecolorallocate($cropped, 255, 255, 255);
        imagefill($cropped, 0, 0, $white);
        
        imagecopy($cropped, $image, 0, 0, $left, $top, $cropWidth, $cropHeight);
        imagedestroy($image);

        return $cropped;
    }

    /**
     * Add symmetric white padding around image
     */
    protected function addSymmetricPaddingGD($image, $padding)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = $width + ($padding * 2);
        $newHeight = $height + ($padding * 2);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // Center the image
        imagecopy($newImage, $image, $padding, $padding, 0, 0, $width, $height);
        imagedestroy($image);

        return $newImage;
    }

    /**
     * Replace dark/black scanner background with white
     */
    protected function replaceDarkBackgroundWithWhite($image, $width, $height)
    {
        // Create new white background image
        $newImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // Copy with transparency handling
        imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);

        // Replace very dark pixels (scanner background) with white
        $threshold = 40; // RGB values below this are considered "black"
        
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // If all RGB values are below threshold, it's a dark pixel
                if ($r < $threshold && $g < $threshold && $b < $threshold) {
                    imagesetpixel($newImage, $x, $y, $white);
                } else {
                    imagesetpixel($newImage, $x, $y, $rgb);
                }
            }
        }

        imagedestroy($image);
        return $newImage;
    }

    /**
     * Trim white borders from image (GD implementation)
     */
    protected function trimWhiteBordersGD($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        
        // Find content boundaries
        $top = 0;
        $bottom = $height - 1;
        $left = 0;
        $right = $width - 1;

        $threshold = 250; // Near-white threshold

        // Find top boundary
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $top = $y;
                    break 2;
                }
            }
        }

        // Find bottom boundary
        for ($y = $height - 1; $y >= 0; $y--) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $bottom = $y;
                    break 2;
                }
            }
        }

        // Find left boundary
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $left = $x;
                    break 2;
                }
            }
        }

        // Find right boundary
        for ($x = $width - 1; $x >= 0; $x--) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    $right = $x;
                    break 2;
                }
            }
        }

        // Calculate crop dimensions
        $cropWidth = $right - $left + 1;
        $cropHeight = $bottom - $top + 1;

        // Minimum size check
        if ($cropWidth < 100 || $cropHeight < 100) {
            return $image; // Don't crop if result would be too small
        }

        // Create cropped image
        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        $whiteColor = imagecolorallocate($cropped, 255, 255, 255);
        imagefill($cropped, 0, 0, $whiteColor);
        
        imagecopy($cropped, $image, 0, 0, $left, $top, $cropWidth, $cropHeight);
        imagedestroy($image);

        return $cropped;
    }

    /**
     * Add white padding around image (GD implementation)
     */
    protected function addWhitePaddingGD($image, $padding)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = $width + ($padding * 2);
        $newHeight = $height + ($padding * 2);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        imagecopy($newImage, $image, $padding, $padding, 0, 0, $width, $height);
        imagedestroy($image);

        return $newImage;
    }

    /**
     * Validate boundary coordinates
     */
    protected function validateBoundaries(?array $boundaries): bool
    {
        if (!$boundaries) return false;

        $required = ['top_left', 'top_right', 'bottom_right', 'bottom_left'];
        foreach ($required as $corner) {
            if (!isset($boundaries[$corner]) || 
                !isset($boundaries[$corner]['x']) || 
                !isset($boundaries[$corner]['y'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Crop image to receipt boundaries (Imagick)
     */
    protected function cropToReceiptBoundaries(\Imagick $imagick, array $boundaries): void
    {
        try {
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();

            // Convert percentage coordinates to pixels
            $topLeft = [
                'x' => ($boundaries['top_left']['x'] / 100) * $width,
                'y' => ($boundaries['top_left']['y'] / 100) * $height
            ];
            $bottomRight = [
                'x' => ($boundaries['bottom_right']['x'] / 100) * $width,
                'y' => ($boundaries['bottom_right']['y'] / 100) * $height
            ];

            $cropWidth = $bottomRight['x'] - $topLeft['x'];
            $cropHeight = $bottomRight['y'] - $topLeft['y'];

            if ($cropWidth > 100 && $cropHeight > 100) {
                $imagick->cropImage(
                    (int)$cropWidth,
                    (int)$cropHeight,
                    (int)$topLeft['x'],
                    (int)$topLeft['y']
                );
            }
        } catch (\Exception $e) {
            Log::warning('ImageDeskewService: Crop failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Crop image to receipt boundaries (GD)
     */
    protected function cropToReceiptBoundariesGD($image, array $boundaries, int $origWidth, int $origHeight)
    {
        try {
            $width = imagesx($image);
            $height = imagesy($image);

            // Convert percentage coordinates to pixels
            $topLeft = [
                'x' => ($boundaries['top_left']['x'] / 100) * $width,
                'y' => ($boundaries['top_left']['y'] / 100) * $height
            ];
            $bottomRight = [
                'x' => ($boundaries['bottom_right']['x'] / 100) * $width,
                'y' => ($boundaries['bottom_right']['y'] / 100) * $height
            ];

            $cropWidth = $bottomRight['x'] - $topLeft['x'];
            $cropHeight = $bottomRight['y'] - $topLeft['y'];

            if ($cropWidth > 100 && $cropHeight > 100) {
                $cropped = imagecreatetruecolor((int)$cropWidth, (int)$cropHeight);
                $white = imagecolorallocate($cropped, 255, 255, 255);
                imagefill($cropped, 0, 0, $white);
                
                imagecopy($cropped, $image, 0, 0, (int)$topLeft['x'], (int)$topLeft['y'], (int)$cropWidth, (int)$cropHeight);
                imagedestroy($image);
                return $cropped;
            }
        } catch (\Exception $e) {
            Log::warning('ImageDeskewService: GD crop failed', ['error' => $e->getMessage()]);
        }

        return $image;
    }

    /**
     * Check if image processing extensions are available
     */
    public function getAvailableProcessors(): array
    {
        return [
            'imagick' => extension_loaded('imagick'),
            'gd' => function_exists('imagecreatefromstring'),
            'gemini' => $this->geminiService->isAvailable()
        ];
    }
}
