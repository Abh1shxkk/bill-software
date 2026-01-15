<?php
/**
 * Audit Script: Find all transaction number generation methods that need organization_id filtering
 * 
 * This script scans all controllers in app/Http/Controllers/Admin and identifies
 * methods that generate transaction numbers without considering organization_id.
 */

$controllersPath = __DIR__ . '/../app/Http/Controllers/Admin';
$results = [];
$issueCount = 0;
$fixedCount = 0;

// Patterns that indicate transaction number generation without org filtering
$patterns = [
    // Direct orderBy without organization filter
    [
        'pattern' => '/(\$\w+)\s*=\s*(\w+)::(orderBy\([\'"]id[\'"]\s*,\s*[\'"]desc[\'"]|orderByDesc\([\'"]id[\'"]\))\s*->\s*first\(\)/',
        'description' => 'Direct orderBy without organization_id filter',
        'severity' => 'HIGH'
    ],
    // Query without withoutGlobalScopes and organization filter
    [
        'pattern' => '/(\w+)::(where|orderBy).*?->first\(\)(?!.*withoutGlobalScopes)/',
        'description' => 'Query without withoutGlobalScopes for transaction number',
        'severity' => 'MEDIUM'
    ],
];

// Models that typically store transaction numbers
$transactionModels = [
    'PurchaseTransaction',
    'SaleTransaction',
    'SaleReturnTransaction',
    'PurchaseReturnTransaction',
    'SaleChallanTransaction',
    'PurchaseChallanTransaction',
    'StockAdjustment',
    'BreakageSupplierIssuedTransaction',
    'BreakageSupplierReceivedTransaction',
    'BreakageSupplierUnusedDumpTransaction',
    'BreakageExpiryTransaction',
    'CustomerReceipt',
    'SupplierPayment',
    'CashBankBook',
];

echo "=================================================\n";
echo "TRANSACTION NUMBER AUDIT - Organization ID Check\n";
echo "=================================================\n\n";

// Scan all controller files
$files = glob($controllersPath . '/*.php');

foreach ($files as $file) {
    $filename = basename($file);
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $fileIssues = [];

    // Check for each transaction model
    foreach ($transactionModels as $model) {
        // Pattern 1: Direct orderBy without organization filter
        if (preg_match_all(
            '/(\$\w+)\s*=\s*' . $model . '::(orderBy|orderByDesc)\([\'"]id[\'"].*?\)->first\(\)/s',
            $content,
            $matches,
            PREG_OFFSET_CAPTURE
        )) {
            foreach ($matches[0] as $match) {
                $lineNum = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $code = trim($match[0]);
                
                // Check if this line already has withoutGlobalScopes or organization_id
                $contextStart = max(0, $match[1] - 500);
                $contextEnd = min(strlen($content), $match[1] + 500);
                $context = substr($content, $contextStart, $contextEnd - $contextStart);
                
                if (stripos($context, 'withoutGlobalScopes') === false && 
                    stripos($context, 'organization_id') === false) {
                    
                    $fileIssues[] = [
                        'line' => $lineNum,
                        'model' => $model,
                        'code' => $code,
                        'severity' => 'HIGH',
                        'description' => "Transaction number generation without organization_id filter"
                    ];
                    $issueCount++;
                }
            }
        }

        // Pattern 2: Look for specific methods like generateTrnNo, generateInvoiceNo
        $methodPatterns = [
            'generateTrnNo',
            'generateInvoiceNo',
            'generateChallanNo',
            'generateBillNo',
            'generateReceiptNo',
            'generateVoucherNo',
            'getNext.*Number'
        ];

        foreach ($methodPatterns as $methodPattern) {
            if (preg_match_all(
                '/function\s+' . $methodPattern . '\s*\([^)]*\)\s*\{([^}]+)\}/s',
                $content,
                $methodMatches,
                PREG_OFFSET_CAPTURE
            )) {
                foreach ($methodMatches[0] as $methodMatch) {
                    $methodBody = $methodMatch[0];
                    $lineNum = substr_count(substr($content, 0, $methodMatch[1]), "\n") + 1;
                    
                    // Check if method body contains the model and lacks organization_id filtering
                    if (stripos($methodBody, $model) !== false) {
                        if (stripos($methodBody, 'withoutGlobalScopes') === false && 
                            stripos($methodBody, 'organization_id') === false &&
                            stripos($methodBody, 'orgId') === false) {
                            
                            $fileIssues[] = [
                                'line' => $lineNum,
                                'model' => $model,
                                'method' => $methodPattern,
                                'code' => substr($methodBody, 0, 100) . '...',
                                'severity' => 'CRITICAL',
                                'description' => "Method $methodPattern uses $model without organization filtering"
                            ];
                            $issueCount++;
                        } else {
                            $fixedCount++;
                        }
                    }
                }
            }
        }
    }

    if (!empty($fileIssues)) {
        $results[$filename] = $fileIssues;
    }
}

// Display results
if (empty($results)) {
    echo "✅ NO ISSUES FOUND! All transaction number generators are properly scoped.\n\n";
} else {
    echo "⚠️  ISSUES FOUND: $issueCount\n";
    echo "✅ ALREADY FIXED: $fixedCount\n\n";
    
    foreach ($results as $filename => $issues) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📄 FILE: $filename\n";
        echo "   Issues: " . count($issues) . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        foreach ($issues as $issue) {
            $severity = $issue['severity'];
            $icon = $severity === 'CRITICAL' ? '🔴' : ($severity === 'HIGH' ? '🟠' : '🟡');
            
            echo "  $icon [$severity] Line {$issue['line']}\n";
            echo "     Model: {$issue['model']}\n";
            if (isset($issue['method'])) {
                echo "     Method: {$issue['method']}\n";
            }
            echo "     Issue: {$issue['description']}\n";
            echo "     Code: " . substr($issue['code'], 0, 80) . "\n";
            echo "\n";
        }
        echo "\n";
    }
}

// Summary
echo "=================================================\n";
echo "SUMMARY\n";
echo "=================================================\n";
echo "Total files scanned: " . count($files) . "\n";
echo "Files with issues: " . count($results) . "\n";
echo "Total issues found: $issueCount\n";
echo "Already fixed: $fixedCount\n";
echo "=================================================\n\n";

// Generate fix recommendations
if (!empty($results)) {
    echo "📋 RECOMMENDED FIXES:\n\n";
    echo "For each identified issue, apply this pattern:\n\n";
    echo "BEFORE:\n";
    echo "  \$lastTransaction = ModelName::orderBy('id', 'desc')->first();\n\n";
    echo "AFTER:\n";
    echo "  \$orgId = auth()->user()->organization_id ?? 1;\n";
    echo "  \$lastTransaction = ModelName::withoutGlobalScopes()\n";
    echo "      ->where('organization_id', \$orgId)\n";
    echo "      ->orderBy('id', 'desc')\n";
    echo "      ->first();\n\n";
    echo "=================================================\n";
}

// Export results to JSON for automated processing
$jsonOutput = [
    'scan_date' => date('Y-m-d H:i:s'),
    'total_files' => count($files),
    'files_with_issues' => count($results),
    'total_issues' => $issueCount,
    'already_fixed' => $fixedCount,
    'results' => $results
];

file_put_contents(__DIR__ . '/audit-results.json', json_encode($jsonOutput, JSON_PRETTY_PRINT));
echo "💾 Results saved to: scripts/audit-results.json\n";
