<?php
// Standalone Backup Verification Script
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationBackupService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

echo "\n=============================================\n";
echo "  AUTO BACKUP & RESTORE VERIFICATION TEST\n";
echo "=============================================\n\n";

try {
    DB::beginTransaction(); // Start transaction to rollback DB changes

    // 1. SETUP TEST DATA
    echo "[1/5] Creating Test Organization & User... ";
    
    $testOrgCode = 'TEST_' . time();
    $org = Organization::create([
        'name' => 'Test Auto Backup Org',
        'code' => $testOrgCode,
        'auto_backup_enabled' => true,
        // Add other required fields if any (based on migration, these seem nullable/default)
    ]);
    
    $user = new User();
    $user->full_name = 'Test Backup Admin';
    $user->username = 'test_backup_admin_' . time();
    $user->email = 'test_backup_' . time() . '@example.com';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->organization_id = $org->id;
    $user->save();

    echo "DONE (Org ID: {$org->id})\n";

    // 2. TRIGGER BACKUP
    echo "[2/5] Triggering Full Backup (Database + Code)...\n";
    echo "      (This may take a few seconds)...\n";
    
    $service = new OrganizationBackupService();
    $result = $service->createBackup($org, $user, true);

    if (!$result['success']) {
        throw new Exception("Backup failed: " . $result['message']);
    }

    echo "      -> Backup Created: {$result['filename']}\n";
    echo "      -> Size: {$result['size_formatted']}\n";
    echo "      -> Tables: {$result['tables_count']}\n";
    echo "      -> Code Files: {$result['code_files_count']}\n";
    echo "DONE\n";

    // 3. VERIFY FILE CONTENT
    echo "[3/6] Verifying ZIP Content... ";
    
    $zipPath = $result['path'] ?? (storage_path('app/backups/organizations/' . $result['filename']));
    
    if (!File::exists($zipPath)) {
        throw new Exception("Backup file not found on disk!");
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        throw new Exception("Could not open ZIP file!");
    }

    // Check critical files
    $filesToCheck = [
        '_backup_metadata.json',
        'database/database_backup.json',
        'code/composer.json',
        'code/app/Models/User.php'
    ];

    foreach ($filesToCheck as $file) {
        if ($zip->locateName($file) === false) {
            throw new Exception("Missing file in ZIP: $file");
        }
    }
    
    $zip->close();
    echo "DONE (All checks passed)\n";

    // 4. TEST RESTORE FUNCTIONALITY
    echo "[4/6] Testing Restore Functionality...\n";
    
    // a. Create a TEMPORARY table for testing to avoid FK/Constraint issues
    echo "      -> Creating temporary test table... ";
    $testTable = 'temp_restore_test';
    Schema::dropIfExists($testTable); // Pre-emptive cleanup
    Schema::create($testTable, function ($table) {
        $table->id();
        $table->unsignedBigInteger('organization_id');
        $table->string('data_value');
    });
    
    // Important: We need to tell the service about this new table since it was created AFTER service instantiation
    $service->addBackupTables([$testTable]);
    
    echo "DONE\n";

    // b. Insert Test Data
    echo "      -> Inserting test record... ";
    $testValue = "Important Data " . time();
    $recordId = DB::table($testTable)->insertGetId([
        'organization_id' => $org->id,
        'data_value' => $testValue
    ]);
    echo "DONE (ID: $recordId)\n";

    // c. Create a NEW backup (Full Backup for Code Restore Test)
    echo "      -> Creating NEW FULL backup with test data & code... ";
    
    // Create a dummy code file to test restore
    $dummyCodeFile = base_path('restore_test_code_file.txt');
    File::put($dummyCodeFile, 'This is a test code file ' . time());
    
    // Create Full Backup (True)
    $backupWithData = $service->createBackup($org, $user, true); 
    if (!$backupWithData['success']) {
        throw new Exception("Full backup failed");
    }
    echo "DONE\n";

    // d. Delete Data and Code File (Simulate Loss)
    echo "      -> Simulating data & code loss... ";
    DB::table($testTable)->where('id', $recordId)->delete();
    File::delete($dummyCodeFile);
    
    if (File::exists($dummyCodeFile)) {
        throw new Exception("Failed to delete dummy code file!");
    }
    echo "DONE\n";

    // e. Perform Restore
    echo "      -> Restoring from backup... ";
    $restoreResult = $service->restoreBackup($backupWithData['filename'], $org);
    
    if (!$restoreResult['success']) {
        throw new Exception("Restore failed: " . $restoreResult['message']);
    }
    echo "DONE\n";

    // f. Verify Data Recovery
    echo "      -> Verifying Data recovery... ";
    $restoredRecord = DB::table($testTable)->where('id', $recordId)->first();
    
    if (!$restoredRecord) {
        throw new Exception("Data verification failed!");
    }
    echo "Pass\n";
    
    // g. Verify Code Recovery
    echo "      -> Verifying Code recovery... ";
    if (!File::exists($dummyCodeFile)) {
        throw new Exception("Code verification failed! File was NOT restored.");
    }
    // Verify content
    if (strpos(File::get($dummyCodeFile), 'This is a test code file') === false) {
         throw new Exception("Restored code file content mismatch!");
    }
    echo "Pass (File recovered)\n";

    // Cleanup
    Schema::dropIfExists($testTable);
    if (File::exists($dummyCodeFile)) File::delete($dummyCodeFile);

    // 5. CLEANUP FILES
    echo "[5/6] Cleaning up Backup Files... ";
    if (File::exists($zipPath)) File::delete($zipPath);
    if (isset($backupWithData['filename'])) {
        $path2 = storage_path('app/backups/organizations/' . $backupWithData['filename']);
        if (File::exists($path2)) File::delete($path2);
    }
    // Clean up Pre-Restore backup created during restore
    $preRestorePattern = storage_path('app/backups/organizations/org_' . $org->code . '_pre_restore_*.zip');
    foreach (glob($preRestorePattern) as $preFile) {
        File::delete($preFile);
    }
    echo "DONE\n";

    // 6. ROLLBACK DB
    echo "[6/6] Rolling back Database changes... ";
    DB::rollBack();
    echo "DONE\n\n";

    echo "✅ FULL TEST (BACKUP + RESTORE) COMPLETED SUCCESSFULLY!\n";

} catch (Exception $e) {
    DB::rollBack();
    if (isset($zipPath) && File::exists($zipPath)) {
        File::delete($zipPath);
    }
    
    echo "\n❌ TEST FAILED: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
