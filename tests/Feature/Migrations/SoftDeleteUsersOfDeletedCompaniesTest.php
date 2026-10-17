<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SoftDeleteUsersOfDeletedCompaniesTest extends TestCase
{
    #[Test]
    public function it_soft_deletes_users_of_deleted_companies_and_reverts_them(): void
    {
        $deletedCompany = Company::factory()->create();
        $orphanUser = User::factory()->create(['company_id' => $deletedCompany->id]);
        $previouslyDeletedUser = User::factory()->create(['company_id' => $deletedCompany->id]);
        $this->travelTo(Carbon::now()->subDay(), fn () => $previouslyDeletedUser->delete());
        $deletedCompany->deleteQuietly();
        $activeUser = User::factory()->create();

        $migration = require database_path('migrations/2026_10_17_174644_soft_delete_users_of_deleted_companies.php');
        $migration->up();

        $this->assertSoftDeleted('user', ['id' => $orphanUser->id, 'deleted_at' => $deletedCompany->fresh()->deleted_at]);
        $this->assertNotSoftDeleted('user', ['id' => $activeUser->id]);

        $migration->down();

        $this->assertNotSoftDeleted('user', ['id' => $orphanUser->id]);
        $this->assertSoftDeleted('user', ['id' => $previouslyDeletedUser->id]);
    }
}
