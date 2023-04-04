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
        $customer->business_name = $data['business_name'];
        $customer->dob = $data['dob'];
        $customer->vat = (isset($data['vat'])) ? strtoupper($data['vat']) : null; //Identity Number
        $customer->phone = $data['phone'];
        $customer->phone2 = $data['phone2'];
        $customer->mobile = $data['mobile'];
        $customer->email = $data['email'];
        $customer->email2 = $data['email2'];
        $customer->website = ($data['website']) ? rtrim($data['website'], '/') : null;
        $customer->notes = $data['notes'] ?? null;

        $customer->linkedin = ($data['linkedin']) ? rtrim($data['linkedin'], '/') : null;
        $customer->facebook = ($data['facebook']) ? rtrim($data['facebook'], '/') : null;
        $customer->instagram = ($data['instagram']) ? rtrim($data['instagram'], '/') : null;
        $customer->twitter = ($data['twitter']) ? rtrim($data['twitter'], '/') : null;
        $customer->youtube = ($data['youtube']) ? rtrim($data['youtube'], '/') : null;
        $customer->tiktok = ($data['tiktok']) ? rtrim($data['tiktok'], '/') : null;

        $customer->country_id = ($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;
        $customer->province = $data['province'];
        $customer->city = $data['city'];
        $customer->locality = $data['locality'];
        $customer->street = $data['street'];
        $customer->zipcode = $data['zipcode'];
        $customer->industry_id = $data['industry_id'];
        $customer->schedule_contact = $data['schedule_contact'];

        $customer->tags = ($data['tags']) ? explode(',', $data['tags']) : null;

        if ($data['status']) {
            $customer->status = $data['status'];
        }
        $customer->updated_at = now();
        $customer->save();

        return  $customer;
    }
}
