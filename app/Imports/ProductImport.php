<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Product([
            'company_id' => $row['company_id'],
            'name' => $row['name'],
            'model' => $row['model'],
            'sku' => $row['sku'],
            'barcode' => $row['barcode'],
            'price' => $row['price'],
            'description' => $row['description'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}
