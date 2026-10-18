<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Money columns were decimal(10,2|3|5), capped between 99,999 and 99,999,999, which is not enough for
 * devalued currencies such as ARS. Unify them as decimal(18,5): 13 integer digits and 5 decimals.
 * Widening keeps every stored value unchanged.
 */
return new class extends Migration
{
    private const int PRECISION = 18;

    private const int SCALE = 5;

    /**
     * table => [column => [original precision, original scale, nullable, default]]
     *
     * @var array<string, array<string, array{int, int, bool, string|null}>>
     */
    private const array COLUMNS = [
        'transaction' => ['amount' => [10, 5, false, null]],
        'order' => ['amount' => [10, 3, false, null]],
        'order_item' => ['unit_price' => [10, 3, true, null]],
        'product' => ['cost' => [10, 3, false, null], 'price' => [10, 3, false, null]],
        'bank_account' => ['opening_balance' => [10, 3, false, '0']],
        'payroll' => ['gross_amount' => [10, 2, true, null], 'net_amount' => [10, 2, false, null]],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column => [, , $nullable, $default]) {
                    $this->define($table, $column, self::PRECISION, self::SCALE, $nullable, $default);
                }
            });
        }
    }

    /**
     * Reverting fails if any stored value no longer fits the original column.
     */
    public function down(): void
    {
        foreach (self::COLUMNS as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column => [$precision, $scale, $nullable, $default]) {
                    $this->define($table, $column, $precision, $scale, $nullable, $default);
                }
            });
        }
    }

    private function define(Blueprint $table, string $column, int $precision, int $scale, bool $nullable, ?string $default): void
    {
        $definition = $table->decimal($column, $precision, $scale)->nullable($nullable);

        if ($default !== null) {
            $definition->default($default);
        }

        $definition->change();
    }
};
