<?php

declare(strict_types=1);

namespace App\Repositories\Lead;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class LeadSaveRepository
{
    public function save(array $data): Lead
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
        $lead->name = $data['name'];
        $lead->business_name = $data['business_name'];
        $lead->dob = $data['dob'];
        $lead->vat = $data['vat'];
        $lead->phone = $data['phone'];
        $lead->phone2 = $data['phone2'];
        $lead->mobile = $data['mobile'];
        $lead->email = $data['email'];
        $lead->email2 = $data['email2'];
        $lead->website = ($data['website']) ? rtrim($data['website'], '/') : null;
        $lead->notes = $data['notes'];

        $lead->linkedin = ($data['linkedin']) ? rtrim($data['linkedin'], '/') : null;
        $lead->facebook = ($data['facebook']) ? rtrim($data['facebook'], '/') : null;
        $lead->instagram = ($data['instagram']) ? rtrim($data['instagram'], '/') : null;
        $lead->twitter = ($data['twitter']) ? rtrim($data['twitter'], '/') : null;
        $lead->youtube = ($data['youtube']) ? rtrim($data['youtube'], '/') : null;
        $lead->tiktok = ($data['tiktok']) ? rtrim($data['tiktok'], '/') : null;

        $lead->country_id = ($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;
        $lead->province = $data['province'];
        $lead->city = $data['city'];
        $lead->locality = $data['locality'];
        $lead->street = $data['street'];
        $lead->zipcode = $data['zipcode'];

        $lead->industry_id = $data['industry_id'];
        $lead->schedule_contact = $data['schedule_contact'];

        $lead->tags = ($data['tags']) ? explode(',', $data['tags']) : null;

        if ($data['status']) {
            $lead->status = $data['status'];
        }

        $lead->updated_at = now();

        $lead->save();

        return $lead;
    }
}
