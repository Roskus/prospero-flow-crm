<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerRepository
{
    public function save(array $data): ?Customer
    {
        if (empty($data['id'])) {
            $customer = new Customer();
            $customer->created_at = now();
        } else {
            $customer = Customer::find($data['id']);
        }

        $customer->seller_id = ($data['seller_id']) ? $data['seller_id'] : Auth::user()->id;
        $customer->company_id = Auth::user()->company_id;
        $customer->external_id = $data['external_id'] ?? null;
        $customer->name = $data['name'];
        $customer->business_name = $data['business_name'] ?? null;
        $customer->dob = $data['dob'] ?? null;
        $customer->vat = (isset($data['vat'])) ? strtoupper($data['vat']) : null; //Identity Number
        $customer->phone = $data['phone'] ?? null;
        $customer->extension = $data['extension'] ?? null;
        $customer->phone2 = $data['phone2'] ?? null;
        $customer->mobile = $data['mobile'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->email2 = $data['email2'] ?? null;
        $customer->website = ($data['website']) ? rtrim($data['website'], '/') : null;
        $customer->notes = $data['notes'] ?? null;

        $customer->linkedin = ($data['linkedin']) ? rtrim($data['linkedin'], '/') : null;
        $customer->facebook = ($data['facebook']) ? rtrim($data['facebook'], '/') : null;
        $customer->instagram = ($data['instagram']) ? rtrim($data['instagram'], '/') : null;
        $customer->twitter = ($data['twitter']) ? rtrim($data['twitter'], '/') : null;
        $customer->youtube = ($data['youtube']) ? rtrim($data['youtube'], '/') : null;
        $customer->tiktok = ($data['tiktok']) ? rtrim($data['tiktok'], '/') : null;

        $customer->country_id = ($data['country_id']) ? strtolower($data['country_id']) : Auth::user()->company->country_id;
        $customer->province = $data['province'] ?? null;
        $customer->city = $data['city'] ?? null;
        $customer->locality = $data['locality'] ?? null;
        $customer->street = $data['street'] ?? null;
        $customer->zipcode = $data['zipcode'] ?? null;
        $customer->industry_id = $data['industry_id'] ?? null;
        $customer->latitude = ! empty($data['latitude']) ? $data['latitude'] : null;
        $customer->longitude = ! empty($data['longitude']) ? $data['longitude'] : null;
        $customer->schedule_contact = $data['schedule_contact'] ?? null;

        $customer->tags = ($data['tags']) ? explode(',', $data['tags']) : null;

        if ($data['status']) {
            $customer->status = $data['status'];
        }
        $customer->updated_at = now();
        $customer->save();

        return $customer;
    }
}
