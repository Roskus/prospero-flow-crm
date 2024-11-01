<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Company;
use App\Models\OrderNumber;

class CompanyRepository
{
    public function save(array $data): ?Company
    {
        if (empty($data['id'])) {
            $company = new Company;
            $company->created_at = now();
        } else {
            $company = Company::find($data['id']);
        }
        $company->name = $data['name'];
        $company->business_name = ! empty($data['business_name']) ? $data['business_name'] : null;
        $company->vat = ! empty($data['vat']) ? $data['vat'] : null;
        $company->signature_html = ! empty($data['signature_html']) ? $data['signature_html'] : null;
        $company->phone = ! empty($data['phone']) ? $data['phone'] : null;
        $company->email = ! empty($data['email']) ? $data['email'] : null;
        $company->country_id = ! empty($data['country_id']) ? $data['country_id'] : null;
        $company->province = ! empty($data['province']) ? $data['province'] : null;
        $company->city = ! empty($data['city']) ? $data['city'] : null;
        $company->street = ! empty($data['street']) ? $data['street'] : null;
        $company->zipcode = ! empty($data['zipcode']) ? $data['zipcode'] : null;
        $company->currency = ! empty($data['currency']) ? $data['currency'] : null;
        $company->website = ! empty($data['website']) ? $data['website'] : null;
        $company->status = ! empty($data['status']) ? $data['status'] : Company::ACTIVE;
        $company->updated_at = now();
        $company->save();

        return $company;
    }
}
