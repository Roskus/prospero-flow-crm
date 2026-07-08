<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Customer\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Customer',
    required: ['name', 'seller_id', 'country_id'],
    properties: [
        new OAT\Property(property: 'id', description: 'Customer ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Customer name', type: 'string', example: 'My Company'),
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
        new OAT\Property(property: 'notes', description: 'Notes about the customer', type: 'string', example: 'Some notes about the customer'),
        new OAT\Property(property: 'seller_id', description: 'Seller ID', type: 'integer', example: 1),
        new OAT\Property(property: 'country_id', description: 'Country ISO code', type: 'string', example: 'ES'),
        new OAT\Property(property: 'province', description: 'Province', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'city', description: 'City', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'locality', description: 'Locality', type: 'string', example: 'Palermo'),
        new OAT\Property(property: 'street', description: 'Street address', type: 'string', example: 'Av. Santa Fe 1234'),
        new OAT\Property(property: 'address_extra', description: 'Additional address info', type: 'string', example: 'Piso 3, Dpto B'),
        new OAT\Property(property: 'zipcode', description: 'Postal code', type: 'string', example: '1425'),
        new OAT\Property(property: 'industry_id', description: 'Industry ID', type: 'integer', example: 1),
        new OAT\Property(property: 'status', description: 'Customer status', type: 'string', example: 'open'),
    ],
    type: 'object'
)]
class Customer extends Prospect
{
    protected $table = 'customer';

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id', 'id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'customer_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getAllByCompanyId(
        int $company_id,
        ?string $search = null,
        ?array $filters = null,
        ?string $order_by = 'created_at',
        int $limit = 50): mixed
    {
        if (is_null($order_by) || ! in_array($order_by, self::SORTABLE_COLUMNS, true)) {
            $order_by = 'created_at';
        }

        $customers = static::where('company_id', $company_id);

        $supportsFulltext = $this->supportsFulltext();

        if ($supportsFulltext && ! empty($search)) {
            $customers = $this->applyFulltextSearch($customers, $search);
        } elseif (! empty($search)) {
            $customers = $this->applyBasicSearch($customers, $search);
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $customers->where($key, $filter);
            }
        }

        return $customers->orderBy($order_by, 'desc')->paginate($limit);
    }

    protected function isPgTrgmEnabled(): bool
    {
        $connection = DB::connection();
        $result = $connection->select("SELECT * FROM pg_extension WHERE extname = 'pg_trgm'");

        return ! empty($result);
    }

    protected function supportsFulltext(): bool
    {
        $driver = config('database.connections.'.config('database.default').'.driver');

        switch ($driver) {
            case 'mysql':
                return true;
            case 'pgsql':
                return $this->isPgTrgmEnabled();
            default:
                return false;
        }
    }

    protected function applyFulltextSearch(Builder $query, string $search): Builder
    {
        return $query->whereFullText(['name', 'business_name'], $search)
            ->orWhere('tags', 'LIKE', "%$search%");
    }

    protected function applyBasicSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            if (is_numeric($search)) {
                $query->orWhere('external_id', '=', $search)
                    ->orWhere('phone', 'LIKE', "%$search%");
            }

            if (is_string($search)) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%")
                        ->orWhere('tags', 'LIKE', "%$search%")
                        ->orWhere('external_id', 'LIKE', "%$search%")
                        ->orWhere('vat', 'LIKE', "%$search%");
                });
            }
        });
    }
}
