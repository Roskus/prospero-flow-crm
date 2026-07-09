<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        return new Product([
            'company_id' => Auth::user()->company_id,
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
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|numeric|digits_between:1,48',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:5000',
        ];
    }
}
