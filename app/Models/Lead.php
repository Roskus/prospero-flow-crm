<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Lead\Message;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Lead',
    required: ['name', 'seller_id'],
    properties: [
        new OAT\Property(property: 'id', description: 'Lead ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Lead name', type: 'string', example: 'My Company'),
        new OAT\Property(property: 'business_name', description: 'Business name', type: 'string', example: 'My Company S.A.'),
        new OAT\Property(property: 'dob', description: 'Date of birth', type: 'string', format: 'date', example: '1990-02-20'),
        new OAT\Property(property: 'vat', description: 'VAT/TAX ID', type: 'string', example: 'ESX1234567X'),
        new OAT\Property(property: 'phone', description: 'Phone number', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'extension', description: 'Phone extension', type: 'string', example: '4004'),
        new OAT\Property(property: 'phone2', description: 'Alternative phone', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'mobile', description: 'Mobile number', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'email', description: 'Email address', type: 'string', format: 'email', example: 'john.doe@email.com'),
        new OAT\Property(property: 'email2', description: 'Alternative email', type: 'string', format: 'email', example: 'john.doe@email.com'),
        new OAT\Property(property: 'website', description: 'Website URL', type: 'string', format: 'uri', example: 'https://www.company.com'),
        new OAT\Property(property: 'source_id', description: 'Source ID', type: 'integer', example: 1),
        new OAT\Property(property: 'linkedin', description: 'LinkedIn profile', type: 'string', format: 'uri', example: 'https://www.linkedin.com/in/profile'),
        new OAT\Property(property: 'facebook', description: 'Facebook profile', type: 'string', format: 'uri', example: 'https://www.facebook.com/mycompany'),
        new OAT\Property(property: 'instagram', description: 'Instagram profile', type: 'string', format: 'uri', example: 'https://www.instagram.com/mycompany'),
        new OAT\Property(property: 'twitter', description: 'Twitter/X profile', type: 'string', format: 'uri', example: 'https://www.twitter.com/mycompany'),
        new OAT\Property(property: 'youtube', description: 'YouTube profile', type: 'string', format: 'uri', example: 'https://www.youtube.com/@mycompany'),
        new OAT\Property(property: 'tiktok', description: 'TikTok profile', type: 'string', format: 'uri', example: 'https://www.tiktok.com/@mycompany'),
        new OAT\Property(property: 'notes', description: 'Notes about the lead', type: 'string', example: 'Some notes about the lead'),
        new OAT\Property(property: 'seller_id', description: 'Seller ID', type: 'integer', example: 1),
        new OAT\Property(property: 'country_id', description: 'Country ISO code', type: 'string', example: 'ES'),
        new OAT\Property(property: 'province', description: 'Province', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'city', description: 'City', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'locality', description: 'Locality', type: 'string', example: 'Palermo'),
        new OAT\Property(property: 'street', description: 'Street address', type: 'string', example: 'Av. Santa Fe 1234'),
        new OAT\Property(property: 'address_extra', description: 'Additional address info', type: 'string', example: 'Piso 3, Dpto B'),
        new OAT\Property(property: 'zipcode', description: 'Postal code', type: 'string', example: '1425'),
        new OAT\Property(property: 'industry_id', description: 'Industry ID', type: 'integer', example: 1),
        new OAT\Property(property: 'status', description: 'Lead status', type: 'string', example: 'open'),
    ],
    type: 'object'
)]
class Lead extends Prospect
{
    protected $table = 'lead';

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'lead_id', 'id');
    }

    public function getAllByCompanyId(int $company_id, ?string $search, ?array $filters, ?string $order_by = 'created_at'): mixed
    {
        if (is_null($order_by) || ! in_array($order_by, self::SORTABLE_COLUMNS, true)) {
            $order_by = 'created_at';
        }

        $leads = static::where('company_id', $company_id);

        if (! empty($search)) {
            $leads->where(function ($query) use ($search) {
                $words = explode(' ', $search);
                if (count($words) == 1) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%")
                        ->orWhere('tags', 'LIKE', "%$search%");
                } else {
                    $query->whereFullText(['name', 'business_name'], $search)
                        ->orWhere('tags', 'LIKE', "%$search%");
                }
            });
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $leads->where($key, $filter);
            }
        }

        return $leads->orderBy($order_by, 'desc')->paginate(10);
    }

    public function getScore(): int
    {
        $score = 0;

        if (! empty($this->email)) {
            $score = $this->email_verified ? $score + 10 : $score - 10;
        }

        if (! empty($this->mobile)) {
            $score = $this->mobile_verified ? $score + 10 : $score - 10;
        }

        if (! empty($this->phone)) {
            $score = $this->phone_verified ? $score + 10 : $score - 10;
        }

        if ($this->contacts()->count() > 0) {
            $score += 10;
        }

        return $score;
    }
}
