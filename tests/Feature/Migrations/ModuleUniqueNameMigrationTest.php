<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Rolling the migration back and forth alters the table, which implicitly commits on MySQL,
 * so this test runs without the RefreshDatabase transaction and the next test migrates a fresh database.
 */
class ModuleUniqueNameMigrationTest extends TestCase
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
    public function duplicated_modules_are_merged_into_the_lowest_id_and_names_become_unique(): void
    {
        $migration = require database_path('migrations/2026_11_21_130404_add_unique_name_index_to_module_table.php');
        $migration->down();

        DB::table('module')->delete();
        $leadId = DB::table('module')->insertGetId(['name' => 'Lead']);
        $customerId = DB::table('module')->insertGetId(['name' => 'Customer']);
        $duplicatedLeadId = DB::table('module')->insertGetId(['name' => 'Lead']);
        $permissionId = DB::table('permission')->insertGetId([
            'name' => 'read duplicated lead',
            'guard_name' => 'web',
            'module_id' => $duplicatedLeadId,
        ]);

        $migration->up();

        $this->assertEqualsCanonicalizing([$leadId, $customerId], DB::table('module')->pluck('id')->all());
        $this->assertSame($leadId, (int) DB::table('permission')->where('id', $permissionId)->value('module_id'));

        $this->expectException(QueryException::class);
        DB::table('module')->insert(['name' => 'Customer']);
    }
}
