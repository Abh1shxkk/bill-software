# Fix the updateAdjustmentBalance function
$filePath = "c:\xampp\htdocs\bill-software\resources\views\admin\sale-return\modification.blade.php"
$content = Get-Content $filePath

# Fix line 3019 (index 3018) - change data-balance to data-original-amount and originalBalance to originalAmount
$content[3018] = $content[3018] -replace 'data-balance', 'data-original-amount'
$content[3018] = $content[3018] -replace 'originalBalance', 'originalAmount'

# Fix line 3024 (index 3023) - change originalBalance to originalAmount  
$content[3023] = $content[3023] -replace 'originalBalance', 'originalAmount'

# Add validation before line 3020 (after line 3019)
$validationCode = @"
        
        // Validate: adjusted amount cannot exceed original bill amount
        if (adjusted > originalAmount) {
            input.value = originalAmount.toFixed(2);
            adjusted = originalAmount;
        }
"@

# Insert validation after line 3019
$newContent = @()
for ($i = 0; $i -lt $content.Length; $i++) {
    $newContent += $content[$i]
    if ($i -eq 3018) {
        $newContent += $validationCode
    }
}

# Save the file
$newContent | Set-Content $filePath

Write-Host "✅ Fixed updateAdjustmentBalance function successfully!"
