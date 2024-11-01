<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;
use Squire\Models\Country;

#[OA\Schema(
    schema: 'Company',
    required: ['name', 'country_id'],
    type: 'object'
)]
class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    const ACTIVE = 1;

    const DEFAULT_COMPANY = 1;

    protected $table = 'company';

    protected $fillable = [
        'name',
        'business_name',
        'vat',
        'logo',
        'signature_html',
        'phone',
        'email',
        'country_id',
        'province',
        'city',
        'street',
        'zipcode',
        'currency',
        'website',
        'updated_at',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $with = ['country'];

    #[OA\Property(property: 'name', description: 'Name of the company', type: 'string', example: 'My Company')]
    public string $name = '';

    #[OA\Property(property: 'business_name', description: 'Business Name of the company', type: 'string', example: 'My Company S.L')]
    public string $business_name;

    #[OA\Property(property: 'vat', description: 'VAT / TAX Identification Number of the company', type: 'string', example: '')]
    public string $vat;

    #[OA\Property(property: 'phone', description: 'Phone of the company', type: 'string', example: '+34645600000')]
    public string $phone;

    #[OA\Property(property: 'email', description: 'Email of the company', type: 'string', format: 'email', example: 'info@company.com')]
    public string $email;

    #[OA\Property(property: 'country_id', description: 'Country ISO code of the company', type: 'string', example: 'ES')]
    public ?string $country_id = null;

    #[OA\Property(property: 'province', description: 'Province of the company', type: 'string', example: 'Barcelona')]
    public string $province;

    #[OA\Property(property: 'city', description: 'City of the company', type: 'string', example: 'Barcelona')]
    public string $city;

    #[OA\Property(property: 'street', description: 'Street of the company', type: 'string', example: 'Av. Constitución 123')]
    public string $street;

    #[OA\Property(property: 'zipcode', description: 'Zipcode of the company', type: 'string', example: '08860')]
    public string $zipcode;

    #[OA\Property(property: 'currency', description: 'Default currency of the company', type: 'string', example: 'USD')]
    public string $currency;

    #[OA\Property(property: 'website', description: 'Website of the company', type: 'string', example: 'https://www.company.com')]
    public string $website;

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): ?string => $attributes['name'] ?? null,
        );
    }

    public function getAllPaginated(int $limit = 50)
    {
        return Company::orderBy('name', 'asc')->paginate($limit);
    }

    public function getAll()
    {
        return Company::orderBy('name', 'asc')->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
