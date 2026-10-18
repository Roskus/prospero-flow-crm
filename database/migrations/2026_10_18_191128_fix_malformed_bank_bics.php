<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Malformed BICs shipped by older versions of BankSeeder, mapped to the real ones.
     *
     * @var array<string, string>
     */
    private const array CORRECTED_BICS = [
        'SCOTIPL' => 'BSUDPEPLXXX',
        'BANCL22' => 'CREDCLRMXXX',
        'ALPHARO22' => 'BUCUROBUXXX',
        'SCOTIUYUU' => 'COMEUYMMXXX',
        'C6BRBRSP' => 'CSIXBRSPXXX',
    ];

    /**
     * Fixes the seeded banks in place, keeping their ids so bank accounts are untouched,
     * and removes the non-existent Inbursa Perú when no bank account uses it.
     * The previous BICs are not restored on rollback: they were never valid.
     */
    public function up(): void
    {
        foreach (self::CORRECTED_BICS as $oldBic => $newBic) {
            if (DB::table('bank')->where('bic', $newBic)->exists()) {
                continue;
            }

            DB::table('bank')->where('bic', $oldBic)->update([
                'bic' => $newBic,
                'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, $newBic)->toString(),
            ]);
        }

        DB::table('bank')
            ->where('bic', 'INBUPERPL')
            ->whereNotExists(fn ($query) => $query->select(DB::raw(1))->from('bank_account')->whereColumn('bank_account.bank_id', 'bank.id'))
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
