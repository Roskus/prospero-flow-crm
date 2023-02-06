<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\VCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Contact', required: ['first_name'])]
class Contact extends Model
{
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
