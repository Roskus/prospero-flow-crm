<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Bank\Account;
use Database\Seeders\BankSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class BankSeederTest extends TestCase
{
    #[Test]
    public function it_seeds_the_banks_of_every_country_file_including_italy(): void
    {
        $this->seed(BankSeeder::class);

        $this->assertDatabaseHas('bank', [
            'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, 'UNCRITM1XXX')->toString(),
            'country_id' => 'it',
            'name' => 'UniCredit',
            'phone' => '+390288621',
        ]);
        $this->assertSame(9, DB::table('bank')->where('country_id', 'it')->count());
        $this->assertGreaterThan(0, DB::table('bank')->where('country_id', 'es')->count());
    }

    #[Test]
    public function every_bank_file_has_valid_and_unique_bics(): void
    {
        $bics = [];

        foreach (glob(database_path('seeders/Bank/bank_*.php')) as $filePath) {
            foreach (require $filePath as $bank) {
                $this->assertTrue(
                    Validator::make($bank, ['bic' => 'required|bic'])->passes(),
                    basename($filePath).': '.$bank['name'].' has an invalid BIC "'.$bank['bic'].'"'
                );
                $bics[] = $bank['bic'];
            }
        }

        $this->assertSame([], array_keys(array_filter(array_count_values($bics), fn (int $count) => $count > 1)));
    }

    #[Test]
    public function running_it_again_keeps_bank_ids_and_the_accounts_that_reference_them(): void
    {
        $this->seed(BankSeeder::class);
        $bankId = DB::table('bank')->where('bic', 'UNCRITM1XXX')->value('id');
        $account = Account::factory()->create(['bank_id' => $bankId]);
        DB::table('bank')->where('id', $bankId)->update(['name' => 'Outdated name']);
        $bankCount = DB::table('bank')->count();

        $this->seed(BankSeeder::class);

        $this->assertSame($bankCount, DB::table('bank')->count());
        $this->assertSame('UniCredit', DB::table('bank')->where('id', $bankId)->value('name'));
        $this->assertSame($bankId, $account->fresh()->bank_id);
    }
}
