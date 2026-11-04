<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactRepository
{
    /**
     * Business rule: any user of the company can add contacts to any of its leads, whoever
     * generated it or is assigned to it, so sales can close every lead. Keep the lead lookup
     * scoped only by company, never by seller.
     */
    public function save(array $data): ?Contact
    {
        if (empty($data['id'])) {
            $contact = new Contact;
            $contact->company_id = Auth::user()->company_id;
            $contact->created_at = now();
        } else {
            $contact = Contact::where('id', $data['id'])
                ->where('company_id', Auth::user()->company_id)
                ->firstOrFail();
        }

        $contact->lead_id = ! empty($data['lead_id'])
            ? Lead::query()->where('company_id', Auth::user()->company_id)->findOrFail($data['lead_id'])->id
            : null;
        $contact->customer_id = ! empty($data['customer_id'])
            ? Customer::query()->where('company_id', Auth::user()->company_id)->findOrFail($data['customer_id'])->id
            : null;
        $contact->first_name = ! empty($data['contact_first_name']) ? $data['contact_first_name'] : null;
        $contact->last_name = ! empty($data['contact_last_name']) ? $data['contact_last_name'] : null;
        $contact->phone = ! empty($data['contact_phone']) ? $data['contact_phone'] : null;
        $contact->extension = $data['contact_extension'] ?? null;
        $contact->mobile = ! empty($data['contact_mobile']) ? $data['contact_mobile'] : null;
        $contact->email = ! empty($data['contact_email']) ? strtolower(trim($data['contact_email'])) : null;
        $contact->job_title = ! empty($data['contact_job_title']) ? $data['contact_job_title'] : null;
        $contact->linkedin = ! empty($data['contact_linkedin']) ? $data['contact_linkedin'] : null;
        $contact->twitter = ! empty($data['contact_twitter']) ? $data['contact_twitter'] : null;
        $contact->address = $contact->address->merge($data);
        $contact->notes = ! empty($data['contact_notes']) ? $data['contact_notes'] : null;
        $contact->updated_at = now();
        try {
            $contact->save();
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }

        return $contact;
    }
}
