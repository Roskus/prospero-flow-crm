<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *    schema="Contact",
 *    type="object",
 *    required={"first_name"},
 *    @OA\Property(
 *        property="first_name",
 *        description="Firstname of the contact",
 *        type="string",
 *        example="John"
 *    ),
 *    @OA\Property(
 *        property="last_name",
 *        description="Lastname of the contact",
 *        type="string",
 *        example="Smith"
 *    ),
 *    @OA\Property(
 *        property="phone",
 *        description="Phone of the contact",
 *        type="string",
 *        example="+34645000000"
 *    ),
 *    @OA\Property(
 *        property="email",
 *        description="Email of the contact",
 *        type="string",
 *        example="john.smith@company.com"
 *    ),
 *    @OA\Property(
 *        property="linkedin",
 *        description="Linkedin url of the contact",
 *        type="string",
 *        example="https://likedin.com/in/contactname"
 *    ),
 *    @OA\Property(
 *        property="country",
 *        description="Country ISO code of the contact",
 *        type="string",
 *        example="CO"
 *    ),
 *    @OA\Property(
 *        property="notes",
 *        description="Notes of the contact",
 *        type="string",
 *        example="We meet last time at the Coffe Shop inside the hotel."
 *    )
 *  )
 */
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

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
