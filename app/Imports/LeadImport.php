<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        return new Lead([
            'company_id' => Auth::user()->company_id,
            'name' => $row['name'],
            'business_name' => $row['business_name'],
            'vat' => $row['vat'],
            'phone' => $row['phone'],
            'phone2' => $row['phone2'],
            'mobile' => $row['mobile'],
            'email' => $row['email'],
            'email2' => $row['email2'],
            'website' => $row['website'],
            'country_id' => $row['country_id'],
            'province' => $row['province'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:120',
            'business_name' => 'max:120',
            'vat' => 'nullable|max:20',
            'phone' => 'nullable|string|max_digits:15',
            'phone2' => 'nullable|string|max_digits:15',
            'mobile' => 'nullable|string|max_digits:15',
            'email' => 'nullable|email|max:254',
            'email2' => 'nullable|email|max:254',
            'website' => 'nullable|url|max:255',
            'country_id' => 'required|max:2',
            'province' => 'nullable|max:80',
        ];
    }
}
