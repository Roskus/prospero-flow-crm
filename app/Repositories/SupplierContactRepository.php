<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Supplier\SupplierContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierContactRepository
{
    public function save(array $data): ?SupplierContact
    {
        if (empty($data['id'])) {
            $contact = new SupplierContact();
            $contact->company_id = Auth::user()->company_id;
            $contact->created_at = now();
        } else {
            $contact = SupplierContact::find($data['id']);
        }
        $contact->supplier_id = ! empty($data['supplier_id']) ? $data['supplier_id'] : null;
        $contact->first_name = ! empty($data['contact_first_name']) ? $data['contact_first_name'] : null;
        $contact->last_name = ! empty($data['contact_last_name']) ? $data['contact_last_name'] : null;
        $contact->phone = ! empty($data['contact_phone']) ? $data['contact_phone'] : null;
        $contact->extension = $data['contact_extension'] ?? null;
        $contact->mobile = ! empty($data['contact_mobile']) ? $data['contact_mobile'] : null;
        $contact->email = ($data['contact_email']) ? strtolower(trim($data['contact_email'])) : null;
        $contact->job_title = ! empty($data['contact_job_title']) ? $data['contact_job_title'] : null;
        $contact->linkedin = ! empty($data['contact_linkedin']) ? $data['contact_linkedin'] : null;
        $contact->twitter = ! empty($data['contact_twitter']) ? $data['contact_twitter'] : null;
        //$contact->country = $data['contact_country'];
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
