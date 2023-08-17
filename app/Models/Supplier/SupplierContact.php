<?php

declare(strict_types=1);

namespace App\Models\Supplier;

use App\Traits\VCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'SupplierContact', required: ['first_name'])]
class SupplierContact extends Model
{
    use SoftDeletes;
    use HasFactory;
    use VCard;

    protected $table = 'supplier_contact';

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

    #[OAT\Property(description: 'Twitter/X of the contact', type: 'string', example: 'https://x.com/john.smith')]
    protected ?string $twitter = null;

    #[OAT\Property(description: 'Country of the contact', type: 'string', example: 'uk')]
    protected ?string $country = null;

    #[OAT\Property(description: 'Notes of the contact', type: 'string', example: 'Test comment')]
    protected ?string $notes = null;

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
