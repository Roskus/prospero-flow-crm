<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Customer;
use App\Rules\AddressRules;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        $model = new Customer([
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
        ]);
        $model->address = Address::fromArray($row);

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForValidation(array $data, int $index): array
    {
        foreach (['province', 'city', 'locality', 'street', 'address_extra', 'zipcode'] as $field) {
            if (is_int($data[$field] ?? null) || is_float($data[$field] ?? null)) {
                $data[$field] = (string) $data[$field];
            }
        }
        $data['country_id'] = is_string($data['country_id'] ?? null) ? strtolower(trim($data['country_id'])) : null;

        return $data;
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
            ...AddressRules::rules(true),
        ];
    }
}
