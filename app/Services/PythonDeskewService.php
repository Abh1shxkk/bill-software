<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Python-based image deskewing service
 * Uses OpenCV for accurate skew detection and correction
 * 
 * Requirements:
 * - Python 3 installed
 * - pip install opencv-python numpy
 */
class PythonDeskewService
{
    protected $pythonPath;
    protected $scriptPath;

    public function __construct()
    {
        // Try to find Python
        $this->pythonPath = $this->findPython();
        $this->scriptPath = base_path('straighten_image.py');
    }

    /**
     * Check if Python deskewing is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->pythonPath) && file_exists($this->scriptPath);
    }

    /**
     * Straighten image using Python/OpenCV
     */
    public function straightenImage($imageContent): ?string
    {
        if (!$this->isAvailable()) {
            Log::warning('PythonDeskewService: Python or script not available');
            return null;
        }

        try {
            // Save temp input file
            $tempInput = tempnam(sys_get_temp_dir(), 'img_in_') . '.jpg';
            $tempOutput = tempnam(sys_get_temp_dir(), 'img_out_') . '.jpg';
            
            file_put_contents($tempInput, $imageContent);

            // Run Python script
            $command = sprintf(
                '%s %s %s %s 2>&1',
                escapeshellarg($this->pythonPath),
                escapeshellarg($this->scriptPath),
                escapeshellarg($tempInput),
                escapeshellarg($tempOutput)
            );

            Log::info('PythonDeskewService: Running command', ['command' => $command]);

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            Log::info('PythonDeskewService: Command output', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);

            // Read output file
            if ($returnCode === 0 && file_exists($tempOutput)) {
                $result = file_get_contents($tempOutput);
                
                // Cleanup
                @unlink($tempInput);
                @unlink($tempOutput);
                
                return $result;
            } else {
                Log::error('PythonDeskewService: Python script failed', [
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                
                // Cleanup
                @unlink($tempInput);
                @unlink($tempOutput);
                
                return null;
            }

        } catch (\Exception $e) {
            Log::error('PythonDeskewService: Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find Python executable
     */
    protected function findPython(): ?string
    {
        $possiblePaths = [
            'python',
            'python3',
            'C:\\Python312\\python.exe',
            'C:\\Python311\\python.exe',
            'C:\\Python310\\python.exe',
            'C:\\Python39\\python.exe',
            'C:\\Python38\\python.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Local\\Programs\\Python\\Python312\\python.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
        ];

        foreach ($possiblePaths as $path) {
            $output = [];
            $returnCode = 0;
            @exec("$path --version 2>&1", $output, $returnCode);
            
            if ($returnCode === 0) {
                Log::info('PythonDeskewService: Found Python', [
                    'path' => $path,
                    'version' => implode(' ', $output)
                ]);
                return $path;
            }
        }

        Log::warning('PythonDeskewService: Python not found');
        return null;
    }
}
