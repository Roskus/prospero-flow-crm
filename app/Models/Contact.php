<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\VCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Contact', required: ['first_name'])]
class Contact extends Model
{
    use SoftDeletes;
    use HasFactory;
    use VCard;

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
        'deleted_at',
    ];

    #[OAT\Property(description: 'First name of the contact', type: 'string', example: 'John')]
    protected ?string $first_name = null;

    #[OAT\Property(description: 'Lastname of the contact', type: 'string', example: 'Smith')]
    protected ?string $last_name = null;

    #[OAT\Property(description: 'Phone of the contact', type: 'string', example: '3400000000')]
    protected ?string $phone = null;

    #[OAT\Property(description: 'Email of the contact', type: 'string', format: 'email', example: 'john.smith@gmail.com')]
    protected ?string $email = null;

    #[OAT\Property(description: 'LinkedIn of the contact', type: 'string', example: 'https://linkedin.com/john.smith')]
    protected ?string $linkedin = null;

    #[OAT\Property(description: 'Country of the contact', type: 'string', example: 'uk')]
    protected ?string $country = null;

    #[OAT\Property(description: 'Notes of the contact', type: 'string', example: 'Test comment')]
    protected ?string $notes = null;

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
