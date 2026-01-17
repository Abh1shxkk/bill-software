<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use App\Traits\ReportHelperTrait;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for ReportHelperTrait
 */
class ReportHelperTraitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a mock class using the trait
     */
    protected function getTraitMock()
    {
        return new class {
            use ReportHelperTrait;
        };
    }

    /**
     * Test getOrganizationId returns user's organization
     */
    public function test_get_organization_id_returns_user_org(): void
    {
        $user = User::factory()->create(['organization_id' => 5]);
        $this->actingAs($user);

        $mock = $this->getTraitMock();
        $result = $mock->getOrganizationId();

        $this->assertEquals(5, $result);
    }

    /**
     * Test getOrganizationId returns default when no user
     */
    public function test_get_organization_id_returns_default(): void
    {
        $mock = $this->getTraitMock();
        $result = $mock->getOrganizationId();

        $this->assertEquals(1, $result);
    }
}
