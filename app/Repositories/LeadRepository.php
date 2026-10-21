<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Lead;
use App\ValueObjects\Address;
use Illuminate\Support\Facades\Auth;

class LeadRepository
{
    public function save(array $data): ?Lead
    {
        if (empty($data['id'])) {
            $lead = new Lead;
            $lead->created_at = now();
        } else {
            $lead = Lead::where('company_id', Auth::user()->company_id)->findOrFail($data['id']);
        }

        $lead->seller_id = (isset($data['seller_id'])) ? $data['seller_id'] : Auth::user()->id;
        $lead->company_id = Auth::user()->company_id;
        $lead->external_id = ! empty($data['external_id']) ? $data['external_id'] : null;
        $lead->name = $data['name'] ?? $lead->name;
        $lead->business_name = ! empty($data['business_name']) ? $data['business_name'] : null;
        $lead->dob = ! empty($data['dob']) ? $data['dob'] : null;
        $lead->vat = ! empty($data['vat']) ? strtoupper($data['vat']) : null;
        $lead->phone = $data['phone'];
        $lead->extension = $data['extension'] ?? null;
        $lead->phone2 = $data['phone2'] ?? null;
        $lead->mobile = $data['mobile'] ?? null;
        $lead->email = ! empty($data['email']) ? $data['email'] : null;
        $lead->email2 = ! empty($data['email2']) ? $data['email2'] : null;
        $lead->source_id = ! empty($data['source_id']) ? $data['source_id'] : null;
        $lead->website = ! empty($data['website']) ? rtrim($data['website'], '/') : null;
        $lead->notes = ! empty($data['notes']) ? $data['notes'] : null;

        $lead->linkedin = ! empty($data['linkedin']) ? rtrim($data['linkedin'], '/') : null;
        $lead->facebook = ! empty($data['facebook']) ? rtrim($data['facebook'], '/') : null;
        $lead->instagram = ! empty($data['instagram']) ? rtrim($data['instagram'], '/') : null;
        $lead->twitter = ! empty($data['twitter']) ? rtrim($data['twitter'], '/') : null;
        $lead->youtube = ! empty($data['youtube']) ? rtrim($data['youtube'], '/') : null;
        $lead->tiktok = ! empty($data['tiktok']) ? rtrim($data['tiktok'], '/') : null;

        $data['country_id'] = ! empty($data['country_id']) ? $data['country_id'] : Auth::user()->company->country_id;
        $lead->address = Address::fromArray($data);
        $lead->schedule_contact = ! empty($data['schedule_contact']) ? $data['schedule_contact'] : null;
        $lead->industry_id = ! empty($data['industry_id']) ? $data['industry_id'] : null;
        $lead->opt_in = ! empty($data['opt_in']) ? $data['opt_in'] : null;
        $lead->tags = ! empty($data['tags']) ? explode(',', $data['tags']) : null;

        $lead->status = ! empty($data['status']) ? $data['status'] : Lead::OPEN;

        $lead->updated_at = now();
        $lead->save();

        return $lead;
    }
}
