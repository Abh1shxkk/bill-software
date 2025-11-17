<?php

namespace Database\Seeders;

use App\Models\GeneralReminder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reminders = [
            ['name' => 'Annual Meeting', 'code' => 'AM001', 'due_date' => '2025-01-15', 'status' => 'Pending'],
            ['name' => 'Tax Filing', 'code' => 'TF002', 'due_date' => '2025-01-31', 'status' => 'Urgent'],
            ['name' => 'Insurance Renewal', 'code' => 'IR003', 'due_date' => '2025-02-10', 'status' => 'Pending'],
            ['name' => 'Audit Preparation', 'code' => 'AP004', 'due_date' => '2025-02-20', 'status' => 'In Progress'],
            ['name' => 'License Renewal', 'code' => 'LR005', 'due_date' => '2025-03-05', 'status' => 'Pending'],
            ['name' => 'Contract Review', 'code' => 'CR006', 'due_date' => '2025-03-15', 'status' => 'Completed'],
            ['name' => 'Quarterly Report', 'code' => 'QR007', 'due_date' => '2025-03-31', 'status' => 'Pending'],
            ['name' => 'Staff Appraisal', 'code' => 'SA008', 'due_date' => '2025-04-10', 'status' => 'In Progress'],
            ['name' => 'Budget Planning', 'code' => 'BP009', 'due_date' => '2025-04-20', 'status' => 'Pending'],
            ['name' => 'Vendor Payment', 'code' => 'VP010', 'due_date' => '2025-05-01', 'status' => 'Urgent'],
            
            ['name' => 'Equipment Maintenance', 'code' => 'EM011', 'due_date' => '2025-05-15', 'status' => 'Pending'],
            ['name' => 'Safety Inspection', 'code' => 'SI012', 'due_date' => '2025-05-25', 'status' => 'Scheduled'],
            ['name' => 'Training Session', 'code' => 'TS013', 'due_date' => '2025-06-05', 'status' => 'Pending'],
            ['name' => 'Product Launch', 'code' => 'PL014', 'due_date' => '2025-06-15', 'status' => 'In Progress'],
            ['name' => 'Marketing Campaign', 'code' => 'MC015', 'due_date' => '2025-06-30', 'status' => 'Pending'],
            ['name' => 'Client Meeting', 'code' => 'CM016', 'due_date' => '2025-07-10', 'status' => 'Confirmed'],
            ['name' => 'Inventory Check', 'code' => 'IC017', 'due_date' => '2025-07-20', 'status' => 'Pending'],
            ['name' => 'System Upgrade', 'code' => 'SU018', 'due_date' => '2025-08-01', 'status' => 'Planned'],
            ['name' => 'Compliance Review', 'code' => 'CR019', 'due_date' => '2025-08-15', 'status' => 'Pending'],
            ['name' => 'Lease Renewal', 'code' => 'LR020', 'due_date' => '2025-08-31', 'status' => 'Urgent'],
            
            ['name' => 'Performance Review', 'code' => 'PR021', 'due_date' => '2025-09-10', 'status' => 'Pending'],
            ['name' => 'Salary Revision', 'code' => 'SR022', 'due_date' => '2025-09-20', 'status' => 'In Progress'],
            ['name' => 'Board Meeting', 'code' => 'BM023', 'due_date' => '2025-10-05', 'status' => 'Scheduled'],
            ['name' => 'Financial Audit', 'code' => 'FA024', 'due_date' => '2025-10-15', 'status' => 'Pending'],
            ['name' => 'Year End Closing', 'code' => 'YE025', 'due_date' => '2025-12-31', 'status' => 'Pending'],
            ['name' => 'GST Filing', 'code' => 'GF026', 'due_date' => '2025-01-20', 'status' => 'Urgent'],
            ['name' => 'PF Submission', 'code' => 'PF027', 'due_date' => '2025-02-15', 'status' => 'Pending'],
            ['name' => 'ESI Payment', 'code' => 'EP028', 'due_date' => '2025-03-10', 'status' => 'Pending'],
            ['name' => 'TDS Return', 'code' => 'TR029', 'due_date' => '2025-04-30', 'status' => 'Urgent'],
            ['name' => 'Stock Verification', 'code' => 'SV030', 'due_date' => '2025-05-31', 'status' => 'Pending'],
        ];

        foreach ($reminders as $reminder) {
            GeneralReminder::create($reminder);
        }
    }
}
