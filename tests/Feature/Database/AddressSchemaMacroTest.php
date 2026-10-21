<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\ValueObjects\Address;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressSchemaMacroTest extends TestCase
{
    #[Test]
    public function it_defines_the_nullable_address_columns(): void
    {
        $connection = DB::connection();
        $connection->useDefaultSchemaGrammar();
        $blueprint = new Blueprint($connection, 'address_macro_test');
        $blueprint->address();

        $columns = collect($blueprint->getColumns())->keyBy('name');

        $this->assertSame(Address::COLUMNS, $columns->keys()->all());
        $this->assertTrue($columns->every(fn ($column): bool => $column->nullable === true));
        $this->assertSame(2, $columns['country_id']->length);
        $this->assertSame(80, $columns['province']->length);
        $this->assertSame(50, $columns['city']->length);
        $this->assertSame(80, $columns['address_extra']->length);
        $this->assertSame(10, $columns['zipcode']->length);
        $this->assertSame([10, 8], [$columns['latitude']->total, $columns['latitude']->places]);
        $this->assertSame([11, 8], [$columns['longitude']->total, $columns['longitude']->places]);
    }
}
