<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(schema: 'Supplier', required: ['name'], type: 'object')]
class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'company_id',
        'name',
        'business_name',
        'vat',
        'phone',
        'email',
        'website',
        'country_id',
        'province',
        'city',
        'street',
        'zipcode',
    ];

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'Supplier name')]
    protected string $name;

    #[OAT\Property(type: 'string', example: 'Supplier name S.A')]
    protected ?string $business_name;

    #[OAT\Property(type: 'string', example: 'L12345678A')]
    protected string $vat;

    #[OAT\Property(type: 'string', example: '+34000000000')]
    protected ?string $phone;

    #[OAT\Property(type: 'string', format: 'email', example: 'info@suppliername.com')]
    protected ?string $email;

    #[OAT\Property(type: 'string', format: 'url', example: 'https://suppliername.com')]
    protected ?string $website;

    #[OAT\Property(description: 'ISO Code 2', type: 'string', example: 'UK')]
    protected ?string $country_id;

    #[OAT\Property(type: 'string', example: 'London')]
    protected ?string $province;

    #[OAT\Property(type: 'string', example: 'London')]
    protected ?string $city;

    #[OAT\Property(type: 'string', example: 'Street name, 5')]
    protected ?string $street;

    #[OAT\Property(type: 'string', example: 'EC4N 1SA')]
    protected ?string $zipcode;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getAllByCompany(int $company_id)
    {
        return Supplier::where('company_id', $company_id)->get();
    }
}
