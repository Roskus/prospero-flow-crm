<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    public function save(array $data): ?Company
    {
        if (empty($data['id'])) {
            $company = new Company();
            $company->created_at = now();
        } else {
            $company = Company::find($data['id']);
        }
        $company->name = $data['name'];
        $company->business_name = ! empty($data['business_name']) ? $data['business_name'] : null;
        $company->phone = $data['phone'];
        $company->email = $data['email'];
        $company->website = $data['website'];
        $company->country_id = $data['country_id'];
        $company->vat = ! empty($data['vat']) ? $data['vat'] : null;
        $company->status = ! empty($data['status']) ? $data['status'] : Company::ACTIVE;
        $company->updated_at = now();
        $company->save();

        return $company;
    }
}
