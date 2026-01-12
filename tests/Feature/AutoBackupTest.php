<?php

namespace Tests\Feature;

use App\Models\AutoBackupLog;
use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationBackupService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class AutoBackupTest extends TestCase
{
    use DatabaseTransactions;

    protected $backupService;
    protected $testBackupPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupService = new OrganizationBackupService();
        $this->testBackupPath = storage_path('app/backups/organizations');
        
        // Ensure backup directory exists
        if (!File::exists($this->testBackupPath)) {
            File::makeDirectory($this->testBackupPath, 0755, true);
        }
    }

    /** @test */
    public function it_can_create_a_full_backup_zip_with_database_and_code()
    {
        // 1. Setup Test Data
        $organization = Organization::create([
            'name' => 'Test Backup Org',
            'code' => 'TEST_ORG',
            'auto_backup_enabled' => true,
        ]);

        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        // 2. Trigger Backup via Service
        $result = $this->backupService->createBackup($organization, $user, true); // true = include code

        // 3. Assert Backup Creation Success
        $this->assertTrue($result['success'], 'Backup creation failed: ' . ($result['message'] ?? ''));
        $this->assertFileExists($result['filename']);
        
        $filePath = $this->testBackupPath . '/' . $result['filename'];
        $this->assertTrue(File::exists($filePath), "Backup file not found at: $filePath");

        // 4. Verify ZIP Contents
        $zip = new ZipArchive();
        $res = $zip->open($filePath);
        $this->assertTrue($res === true, "Could not open ZIP file");

        // Check for Metadata
        $this->assertNotFalse($zip->locateName('_backup_metadata.json'), 'Metadata file missing in ZIP');
        
        // Check for Database Backup
        $this->assertNotFalse($zip->locateName('database/database_backup.json'), 'Database backup file missing in ZIP');

        // Check for Code Directories (at least one key file/folder)
        $this->assertNotFalse($zip->locateName('code/composer.json'), 'composer.json missing in code backup');
        $this->assertNotFalse($zip->locateName('code/app/Models/User.php'), 'App code missing in backup');

        $zip->close();

        // 5. Verify Database Log Entry
        $this->assertDatabaseHas('auto_backup_logs', [
            'organization_id' => $organization->id,
            'backup_filename' => $result['filename'],
            'status' => 'success',
        ]);

        // Cleanup: Delete the test backup file
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    /** @test */
    public function only_tables_with_organization_id_are_included_in_backup()
    {
        // 1. Create a dummy table WITH organization_id
        // Validating logic by checking the service's discovered tables
        
        // We can't easily create tables on the fly in a test without migrations, 
        // preventing pollution. Instead, we'll check the service's excluded logic.
        
        $organization = Organization::create([
            'name' => 'Data Check Org',
            'code' => 'DATA_ORG',
        ]);

        $user = User::factory()->create(['organization_id' => $organization->id]);

        // Perform backup (Database only for speed)
        $result = $this->backupService->createBackup($organization, $user, false); 
        
        $filePath = $this->testBackupPath . '/' . $result['filename'];
        $zip = new ZipArchive();
        $zip->open($filePath);
        
        $jsonContent = $zip->getFromName('database/database_backup.json');
        $data = json_decode($jsonContent, true);
        $zip->close();

        // Validate that we have tables
        $this->assertArrayHasKey('tables', $data);
        $this->assertNotEmpty($data['tables']);

        // Check that 'users' table is included (it has organization_id)
        $this->assertArrayHasKey('users', $data['tables']);
        
        // Check that 'migrations' table is NOT included (system table)
        $this->assertArrayNotHasKey('migrations', $data['tables']);
        
        // Check that 'organizations' table is NOT included (master table)
        $this->assertArrayNotHasKey('organizations', $data['tables']);

        // Cleanup
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
