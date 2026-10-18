<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CsvExportService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CsvExportServiceTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function formulaPayloads(): array
    {
        return [
            'equals dde' => ["=cmd|' /C calc'!A0", "'=cmd|' /C calc'!A0"],
            'plus' => ['+HYPERLINK("http://evil.test")', "'+HYPERLINK(\"http://evil.test\")"],
            'minus' => ['-2+3+cmd|\' /C calc\'!A0', "'-2+3+cmd|' /C calc'!A0"],
            'at' => ['@SUM(1+1)', "'@SUM(1+1)"],
            'tab' => ["\t=1+1", "'\t=1+1"],
            'carriage return' => ["\r=1+1", "'\r=1+1"],
        ];
    }

    #[Test]
    #[DataProvider('formulaPayloads')]
    public function it_neutralizes_formula_payloads(string $value, string $expected): void
    {
        $this->assertSame($expected, (new CsvExportService)->sanitizeCell($value));
    }

    #[Test]
    public function it_keeps_plain_and_numeric_values_unchanged(): void
    {
        $service = new CsvExportService;

        $this->assertSame('Acme S.L.', $service->sanitizeCell('Acme S.L.'));
        $this->assertSame('+34600000000', $service->sanitizeCell('+34600000000'));
        $this->assertSame('-15.5', $service->sanitizeCell(-15.5));
        $this->assertSame('42', $service->sanitizeCell(42));
        $this->assertSame('', $service->sanitizeCell(null));
    }

    #[Test]
    public function it_quotes_separators_quotes_and_line_breaks_so_columns_cannot_be_forged(): void
    {
        $csv = (new CsvExportService)->build(
            ['id', 'name', 'notes'],
            [['id' => 1, 'name' => 'Foo;Bar "Baz"', 'notes' => "line1\n2;evil;row"]],
        );

        $this->assertSame("id;name;notes\n1;\"Foo;Bar \"\"Baz\"\"\";\"line1\n2;evil;row\"\n", $csv);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $csv);
        rewind($stream);
        $parsedRows = [];
        while (($parsedRow = fgetcsv($stream, null, ';', '"', '')) !== false) {
            $parsedRows[] = $parsedRow;
        }
        fclose($stream);

        $this->assertSame([
            ['id', 'name', 'notes'],
            ['1', 'Foo;Bar "Baz"', "line1\n2;evil;row"],
        ], $parsedRows);
    }

    #[Test]
    public function it_fills_missing_columns_with_empty_values(): void
    {
        $csv = (new CsvExportService)->build(['id', 'name'], [['id' => 7]]);

        $this->assertSame("id;name\n7;\n", $csv);
    }

    #[Test]
    public function it_uses_semicolon_as_the_default_separator(): void
    {
        $csv = (new CsvExportService)->build(['id', 'name'], [['id' => 1, 'name' => 'Acme']]);

        $this->assertSame("id;name\n1;Acme\n", $csv);
    }

    #[Test]
    public function it_accepts_a_custom_separator_and_quotes_values_containing_it(): void
    {
        $csv = (new CsvExportService)->build(['id', 'name', 'city'], [['id' => 1, 'name' => 'Foo, Bar', 'city' => 'Madrid;Centro']], ',');

        $this->assertSame("id,name,city\n1,\"Foo, Bar\",Madrid;Centro\n", $csv);
    }

    #[Test]
    public function it_accepts_a_tab_separator(): void
    {
        $csv = (new CsvExportService)->build(['id', 'name'], [['id' => 1, 'name' => 'Acme']], "\t");

        $this->assertSame("id\tname\n1\tAcme\n", $csv);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidSeparators(): array
    {
        return [
            'empty' => [''],
            'multiple characters' => [';;'],
            'double quote' => ['"'],
            'line feed' => ["\n"],
            'carriage return' => ["\r"],
        ];
    }

    #[Test]
    #[DataProvider('invalidSeparators')]
    public function it_rejects_invalid_separators(string $separator): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CsvExportService)->build(['id'], [], $separator);
    }
}
