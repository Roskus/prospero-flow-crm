<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\VCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Contact',
    required: ['first_name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Contact ID', type: 'integer', example: 1),
        new OAT\Property(property: 'first_name', description: 'First name of the contact', type: 'string', example: 'John'),
        new OAT\Property(property: 'last_name', description: 'Last name of the contact', type: 'string', example: 'Smith'),
        new OAT\Property(property: 'phone', description: 'Phone of the contact', type: 'string', example: '3400000000'),
        new OAT\Property(property: 'extension', description: 'Extension of the contact', type: 'string'),
        new OAT\Property(property: 'email', description: 'Email of the contact', type: 'string', format: 'email', example: 'john.smith@gmail.com'),
        new OAT\Property(property: 'linkedin', description: 'LinkedIn profile of the contact', type: 'string', example: 'https://linkedin.com/john.smith'),
        new OAT\Property(property: 'twitter', description: 'Twitter / X profile of the contact', type: 'string', example: 'https://x.com/john.smith'),
        new OAT\Property(property: 'country', description: 'Country of the contact', type: 'string', example: 'uk'),
        new OAT\Property(property: 'notes', description: 'Notes about the contact', type: 'string', example: 'Test comment'),
    ],
    type: 'object'
)]
class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;
    use VCard;

    protected $table = 'contact';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'extension',
        'email',
        'likedin',
        'twitter',
        'country',
        'notes',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected $casts = [
        'updated_at' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
