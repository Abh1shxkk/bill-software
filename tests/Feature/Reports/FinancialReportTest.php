<?php

namespace Tests\Feature\Reports;

/**
 * Financial Report Tests (P1 - Priority)
 */
class FinancialReportTest extends ReportsTestCase
{
    public function test_trial_balance_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/trial-balance');
        $response->assertStatus(200);
    }

    public function test_profit_loss_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/profit-loss');
        $response->assertStatus(200);
    }

    public function test_balance_sheet_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/balance-sheet');
        $response->assertStatus(200);
    }

    public function test_cash_book_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/cash-book');
        $response->assertStatus(200);
    }

    public function test_bank_book_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/bank-book');
        $response->assertStatus(200);
    }

    public function test_ledger_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/financial/ledger');
        $response->assertStatus(200);
    }
}
