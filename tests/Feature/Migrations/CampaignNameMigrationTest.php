<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Rolling the migration back and forth alters the table, which implicitly commits on MySQL,
 * so this test runs without the RefreshDatabase transaction and the next test migrates a fresh database.
 */
class CampaignNameMigrationTest extends TestCase
{
    /**
     * @return list<string|null>
     */
    protected function connectionsToTransact(): array
    {
        return [];
    }

    protected function tearDown(): void
    {
        RefreshDatabaseState::$migrated = false;

        parent::tearDown();
    }

    #[Test]
    public function existing_campaigns_take_their_subject_as_name(): void
    {
        $migration = require database_path('migrations/2026_10_28_085620_add_name_to_campaign_table.php');
        $migration->down();

        $campaignId = DB::table('campaign')->insertGetId([
            'company_id' => $this->user->company_id,
            'subject' => 'Autumn offers',
            'from' => 'shop@example.com',
        ]);

        $migration->up();

        $this->assertSame('Autumn offers', DB::table('campaign')->where('id', $campaignId)->value('name'));
    }
}
