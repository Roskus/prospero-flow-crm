<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Contact', required: ['first_name'])]
class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'likedin',
        'country',
        'notes',
    ];

    protected $hidden = [
        'company_id',
    ];

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    private ?int $company_id;

    #[OAT\Property(description: 'Name of the contact', type: 'string', example: 'John')]
    protected string $first_name;

    #[OAT\Property(description: 'Lastname of the contact', type: 'string', example: 'Smith')]
    protected ?string $last_name;

    #[OAT\Property(description: 'Phone of the contact', type: 'string', example: '+34645000000')]
    protected ?string $phone;

    #[OAT\Property(description: 'Email of the contact', type: 'string', format: 'email', example: 'john.smith@company.com')]
    protected ?string $email;

    #[OAT\Property(description: 'Linkedin url of the contact', type: 'string', format: 'url', example: 'https://likedin.com/in/contactname')]
    protected ?string $linkedin;

    #[OAT\Property(description: 'Country ISO code of the contact', type: 'string', example: 'UK')]
    protected ?string $country;

    #[OAT\Property(description: 'Notes of the contact', type: 'string', example: 'We meet last time at the Coffee Shop inside the hotel.')]
    protected ?string $notes;

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
