<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get the admin user (organization owner)
        $admin = DB::table('users')->where('user_id', 1)->first();
        
        if ($admin) {
            // Update organization with admin's actual data
            DB::table('organizations')->where('id', 1)->update([
                'name' => $admin->licensed_to ?: $admin->full_name,
                'email' => $admin->email,
                'gst_no' => $admin->gst_no,
                'dl_no' => $admin->dl_no,
                'dl_no_1' => $admin->dl_no_1,
                'updated_at' => now(),
            ]);
            
            // Check if license exists for org 1
            $license = DB::table('licenses')->where('organization_id', 1)->first();
            
            if (!$license) {
                // Create a license for org 1
                DB::table('licenses')->insert([
                    'organization_id' => 1,
                    'license_key' => 'LIC-' . strtoupper(substr(md5(uniqid()), 0, 16)),
                    'plan_type' => 'premium',
                    'max_users' => 10,
                    'max_items' => 10000,
                    'is_active' => true,
                    'activated_at' => now(),
                    'expires_at' => now()->addYear(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed
    }
};
