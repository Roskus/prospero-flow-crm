<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    public const string SEPARATOR = ';';

    /**
     * Leading characters that spreadsheet applications interpret as the start of a formula.
     */
    private const array FORMULA_TRIGGERS = ['=', '+', '-', '@', "\t", "\r"];

    /**
     * @param  array<int, string>  $columns
     * @param  iterable<array<string, mixed>>  $rows
     */
    public function download(string $fileName, array $columns, iterable $rows, string $separator = self::SEPARATOR): StreamedResponse
    {
        $this->assertValidSeparator($separator);

        return response()->streamDownload(function () use ($columns, $rows, $separator): void {
            echo $this->build($columns, $rows, $separator);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     * @param  iterable<array<string, mixed>>  $rows
     */
    public function build(array $columns, iterable $rows, string $separator = self::SEPARATOR): string
    {
        $this->assertValidSeparator($separator);

        $stream = fopen('php://temp', 'r+');

        $this->writeLine($stream, $columns, $separator);

        foreach ($rows as $row) {
            $this->writeLine($stream, array_map(fn (string $column): string => $this->sanitizeCell($row[$column] ?? ''), $columns), $separator);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        return $content;
    }

    /**
     * Neutralize CSV/formula injection (CWE-1236) by prefixing formula-like values with an apostrophe.
     */
    public function sanitizeCell(mixed $value): string
    {
        $value = (string) $value;

        if ($value === '' || is_numeric($value)) {
            return $value;
        }

        if (in_array($value[0], self::FORMULA_TRIGGERS, true)) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * @param  resource  $stream
     * @param  array<int, string>  $fields
     */
    private function writeLine($stream, array $fields, string $separator): void
    {
        fputcsv($stream, $fields, $separator, '"', '', "\n");
    }

    private function assertValidSeparator(string $separator): void
    {
        if (strlen($separator) !== 1 || in_array($separator, ['"', "\n", "\r"], true)) {
            throw new InvalidArgumentException('The CSV separator must be a single character other than a double quote or a line break.');
        }
    }
}
