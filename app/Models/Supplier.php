<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Supplier\SupplierContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(
    schema: 'Supplier',
    required: ['name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Supplier ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Supplier name', type: 'string', example: 'Supplier name'),
        new OAT\Property(property: 'business_name', description: 'Business name of the supplier', type: 'string', example: 'Supplier name S.A'),
        new OAT\Property(property: 'vat', description: 'VAT / TAX Identification Number', type: 'string', example: 'L12345678A'),
        new OAT\Property(property: 'phone', description: 'Phone of the supplier', type: 'string', example: '+34000000000'),
        new OAT\Property(property: 'email', description: 'Email of the supplier', type: 'string', format: 'email', example: 'info@suppliername.com'),
        new OAT\Property(property: 'website', description: 'Website of the supplier', type: 'string', format: 'url', example: 'https://suppliername.com'),
        new OAT\Property(property: 'country_id', description: 'Country ISO Code 2', type: 'string', example: 'UK'),
        new OAT\Property(property: 'province', description: 'Province of the supplier', type: 'string', example: 'London'),
        new OAT\Property(property: 'city', description: 'City of the supplier', type: 'string', example: 'London'),
        new OAT\Property(property: 'street', description: 'Street address of the supplier', type: 'string', example: 'Street name, 5'),
        new OAT\Property(property: 'zipcode', description: 'Zipcode of the supplier', type: 'string', example: 'EC4N 1SA'),
        new OAT\Property(property: 'notes', description: 'Notes about the supplier', type: 'string', example: 'This is a note'),
        new OAT\Property(property: 'account_number', description: 'Account number of the supplier', type: 'string', example: '1234567890'),
        new OAT\Property(property: 'order_url', description: 'URL for ordering from supplier', type: 'string', format: 'url', example: 'https://supplier.com/order'),
        new OAT\Property(property: 'order_user', description: 'Username for supplier ordering system', type: 'string', example: 'username123'),
        new OAT\Property(property: 'order_password', description: 'Password for supplier ordering system', type: 'string', example: 'password123'),
    ],
    type: 'object'
)]
class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'notes',
        'account_number',
        'order_url',
        'order_user',
        'order_password',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function contacts()
    {
        return $this->hasMany(SupplierContact::class, 'supplier_id', 'id');
    }

    public function getAllByCompany(int $company_id)
    {
        return Supplier::with('country')->where('company_id', $company_id)->get();
    }
}
