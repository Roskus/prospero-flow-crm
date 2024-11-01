<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierRepository
{
    public function save(array $data): ?Supplier
    {
        $supplier = (empty($data['id'])) ? new Supplier : Supplier::find($data['id']);
        $supplier->company_id = Auth::user()->company_id;
        $supplier->name = $data['name'];
        $supplier->business_name = (! empty($data['business_name'])) ? $data['business_name'] : null;
        $supplier->phone = (! empty($data['phone'])) ? $data['phone'] : null;
        $supplier->vat = (! empty($data['vat'])) ? $data['vat'] : null;
        $supplier->email = (! empty($data['email'])) ? $data['email'] : null;
        $supplier->website = (! empty($data['website'])) ? $data['website'] : null;
        $supplier->country_id = $data['country_id'];
        $supplier->province = (! empty($data['province'])) ? $data['province'] : null;
        $supplier->street = (! empty($data['street'])) ? $data['street'] : null;
        $supplier->zipcode = (! empty($data['zipcode'])) ? $data['zipcode'] : null;
        $supplier->notes = !empty($data['notes']) ? $data['notes'] : null;
        $supplier->account_number = !empty($data['account_number']) ? $data['account_number'] : null;
        $supplier->order_url = !empty($data['order_url']) ? $data['order_url'] : null;
        $supplier->order_user = !empty($data['order_user']) ? $data['order_user'] : null;
        $supplier->order_password = !empty($data['order_password']) ? $data['order_password'] : null;
        $supplier->updated_at = now();
        $supplier->save();

        return $supplier;
    }
}
