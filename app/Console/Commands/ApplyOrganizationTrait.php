<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApplyOrganizationTrait extends Command
{
    protected $signature = 'multitenant:apply-trait {--dry-run : Show what would be changed without modifying files}';
    protected $description = 'Apply BelongsToOrganization trait to all tenant models';

    // Models that should NOT have the trait (system-level models)
    protected array $excludedModels = [
        'User.php',           // Already has organization_id handled differently
        'Organization.php',   // The organization itself
        'License.php',        // License management
        'LicenseLog.php',     // License audit
        'SubscriptionPlan.php', // Global plans
        'Permission.php',     // Global permissions
        'Hotkey.php',         // User-specific
        'IndexShortcut.php',  // Global shortcuts
        'BackupSchedule.php', // System-level
    ];

    public function handle()
    {
        $modelsPath = app_path('Models');
        $traitUse = 'use App\Traits\BelongsToOrganization;';
        $traitApply = 'use BelongsToOrganization;';
        
        $files = File::files($modelsPath);
        $modified = 0;
        $skipped = 0;
        $alreadyHas = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // Skip excluded models
            if (in_array($filename, $this->excludedModels)) {
                $this->line("<comment>Skipping (excluded):</comment> {$filename}");
                $skipped++;
                continue;
            }

            $content = File::get($file->getPathname());
            
            // Check if already has the trait
            if (str_contains($content, 'BelongsToOrganization')) {
                $this->line("<info>Already has trait:</info> {$filename}");
                $alreadyHas++;
                continue;
            }

            // Check if it's a valid model extending Eloquent
            if (!preg_match('/extends\s+Model/i', $content)) {
                $this->line("<comment>Skipping (not a Model):</comment> {$filename}");
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("<info>Would modify:</info> {$filename}");
                $modified++;
                continue;
            }

            // Add the trait import after other use statements
            $newContent = $content;
            
            // Find the namespace and add import after it
            if (preg_match('/^(namespace\s+[^;]+;)/m', $newContent, $matches)) {
                $namespaceEnd = strpos($newContent, $matches[1]) + strlen($matches[1]);
                
                // Check if our trait import already exists
                if (!str_contains($newContent, $traitUse)) {
                    // Find where to insert (after other use statements or after namespace)
                    if (preg_match('/^use\s+[^;]+;/m', $newContent, $useMatches, PREG_OFFSET_CAPTURE)) {
                        // Find the last 'use' statement in the import section
                        preg_match_all('/^use\s+[^;]+;/m', $newContent, $allUses, PREG_OFFSET_CAPTURE);
                        $lastUse = end($allUses[0]);
                        $insertPos = $lastUse[1] + strlen($lastUse[0]);
                        $newContent = substr($newContent, 0, $insertPos) . "\n" . $traitUse . substr($newContent, $insertPos);
                    } else {
                        // No use statements, add after namespace
                        $newContent = substr($newContent, 0, $namespaceEnd) . "\n\n" . $traitUse . substr($newContent, $namespaceEnd);
                    }
                }
            }

            // Add the trait usage inside the class
            if (preg_match('/class\s+\w+\s+extends\s+Model\s*\{/', $newContent, $classMatches, PREG_OFFSET_CAPTURE)) {
                $classOpenPos = $classMatches[0][1] + strlen($classMatches[0][0]);
                
                // Check if there's already a 'use' statement for traits
                $afterClass = substr($newContent, $classOpenPos, 500);
                
                if (preg_match('/^\s*use\s+[^;]+;/m', $afterClass, $existingTrait, PREG_OFFSET_CAPTURE)) {
                    // Add after existing trait use
                    $existingTraitEnd = $classOpenPos + $existingTrait[0][1] + strlen($existingTrait[0][0]);
                    
                    // Check if we need to modify existing use statement or add new one
                    if (str_contains($existingTrait[0][0], 'use ') && !str_contains($existingTrait[0][0], 'BelongsToOrganization')) {
                        // Add to existing use statement
                        $oldUse = $existingTrait[0][0];
                        $newUse = rtrim($oldUse, ';') . ', BelongsToOrganization;';
                        $newContent = str_replace($oldUse, $newUse, $newContent);
                    }
                } else {
                    // No existing trait, add new line
                    $newContent = substr($newContent, 0, $classOpenPos) . 
                                  "\n    " . $traitApply . "\n" . 
                                  substr($newContent, $classOpenPos);
                }
            }

            File::put($file->getPathname(), $newContent);
            $this->line("<info>Modified:</info> {$filename}");
            $modified++;
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Modified: {$modified}");
        $this->line("  Already has trait: {$alreadyHas}");
        $this->line("  Skipped: {$skipped}");
        
        if ($this->option('dry-run')) {
            $this->warn("This was a dry run. No files were modified.");
        }

        return Command::SUCCESS;
    }
}
