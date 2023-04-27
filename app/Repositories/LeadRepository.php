<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class LeadRepository
{
    public function save(array $data): ?Lead
    {
        if (empty($data['id'])) {
            $lead = new Lead();
            $lead->created_at = now();
            $lead->status = Lead::OPEN;
        } else {
            $lead = Lead::find($data['id']);
        }

        $lead->seller_id = ($data['seller_id']) ? $data['seller_id'] : Auth::user()->id;
        $lead->company_id = Auth::user()->company_id;
        $lead->external_id = ! empty($data['external_id']) ? $data['external_id'] : null;
        $lead->name = $data['name'];
        $lead->business_name = ! empty($data['business_name']) ? $data['business_name'] : null;
        $lead->dob = ! empty($data['dob']) ? $data['dob'] : null;
        $lead->vat = ! empty($data['vat']) ? strtoupper($data['vat']) : null;
        $lead->phone = $data['phone'];
        $lead->phone2 = $data['phone2'];
        $lead->mobile = $data['mobile'];
        $lead->email = ! empty($data['email']) ? $data['email'] : null;
        $lead->email2 = ! empty($data['email2']) ? $data['email2'] : null;
        $lead->website = ! empty($data['website']) ? rtrim($data['website'], '/') : null;
        $lead->linkedin = ! empty($data['linkedin']) ? rtrim($data['linkedin'], '/') : null;
        $lead->facebook = ! empty($data['facebook']) ? rtrim($data['facebook'], '/') : null;
        $lead->instagram = ! empty($data['instagram']) ? rtrim($data['instagram'], '/') : null;
        $lead->twitter = ! empty($data['twitter']) ? rtrim($data['twitter'], '/') : null;
        $lead->youtube = ! empty($data['youtube']) ? rtrim($data['youtube'], '/') : null;
        $lead->tiktok = ! empty($data['tiktok']) ? rtrim($data['tiktok'], '/') : null;
        $lead->notes = ! empty($data['notes']) ? $data['notes'] : null;
        $lead->country_id = ! empty($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;
        $lead->province = ! empty($data['province']) ? $data['province'] : null;
        $lead->city = ! empty($data['city']) ? $data['city'] : null;
        $lead->locality = $data['locality'] ?? null;
        $lead->street = $data['street'] ?? null;
        $lead->zipcode = $data['zipcode'] ?? null;
        $lead->schedule_contact = ! empty($data['schedule_contact']) ? $data['schedule_contact'] : null;
        $lead->industry_id = ! empty($data['industry_id']) ? $data['industry_id'] : null;
        $lead->latitude = ! empty($data['latitude']) ? $data['latitude'] : null;
        $lead->longitude = ! empty($data['longitude']) ? $data['longitude'] : null;
        $lead->opt_in = ! empty($data['opt_in']) ? $data['opt_in'] : null;
        $lead->tags = ($data['tags']) ? explode(',', $data['tags']) : null;

        if ($data['status']) {
            $lead->status = $data['status'];
        }

        $lead->updated_at = now();
        $lead->save();

        return $lead;
    }
}
