<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Bank\Account;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class BankBicTest extends TestCase
{
    #[Test]
    public function it_fixes_malformed_bics_in_place_keeping_bank_ids(): void
    {
        $bankId = $this->insertBank('Scotiabank Perú', 'pe', 'SCOTIPL');
        $account = Account::factory()->create(['bank_id' => $bankId]);

        $this->runFixMigration();

        $this->assertDatabaseHas('bank', [
            'id' => $bankId,
            'bic' => 'BSUDPEPLXXX',
            'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, 'BSUDPEPLXXX')->toString(),
        ]);
        $this->assertSame($bankId, $account->fresh()->bank_id);
    }

    #[Test]
    public function it_skips_a_fix_when_a_bank_with_the_correct_bic_already_exists(): void
    {
        $malformedId = $this->insertBank('Alpha Bank', 'ro', 'ALPHARO22');
        $this->insertBank('Alpha Bank', 'ro', 'BUCUROBUXXX');

        $this->runFixMigration();

        $this->assertDatabaseHas('bank', ['id' => $malformedId, 'bic' => 'ALPHARO22']);
        $this->assertSame(1, DB::table('bank')->where('bic', 'BUCUROBUXXX')->count());
    }

    #[Test]
    public function it_removes_inbursa_peru_only_when_no_bank_account_uses_it(): void
    {
        $this->insertBank('Banco Inbursa Perú', 'pe', 'INBUPERPL');

        $this->runFixMigration();

        $this->assertDatabaseMissing('bank', ['bic' => 'INBUPERPL']);

        $bankId = $this->insertBank('Banco Inbursa Perú', 'pe', 'INBUPERPL');
        Account::factory()->create(['bank_id' => $bankId]);

        $this->runFixMigration();

        $this->assertDatabaseHas('bank', ['id' => $bankId]);
    }

    #[Test]
    public function the_bic_is_unique_in_the_database(): void
    {
        $this->insertBank('First', 'es', 'CAIXESBBXXX');

        $this->expectException(QueryException::class);

        DB::table('bank')->insert([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'Second',
            'country_id' => 'es',
            'bic' => 'CAIXESBBXXX',
        ]);
    }

    private function insertBank(string $name, string $countryId, string $bic): int
    {
        return DB::table('bank')->insertGetId([
            'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, $bic)->toString(),
            'name' => $name,
            'country_id' => $countryId,
            'bic' => $bic,
        ]);
    }

    private function runFixMigration(): void
    {
        (require database_path('migrations/2026_10_18_191128_fix_malformed_bank_bics.php'))->up();
    }
}
