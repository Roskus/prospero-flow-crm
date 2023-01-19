<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

/**
 *  @OA\Schema(
 *    schema="User",
 *    type="object",
 *    required={"first_name", "email", "password"},
 *    @OA\Property(
 *        property="first_name",
 *        description="First Name of the user",
 *        type="string",
 *        example="John"
 *    ),
 *    @OA\Property(
 *        property="last_name",
 *        description="Last Name of the user",
 *        type="string",
 *        example="Smith"
 *    ),
 *    @OA\Property(
 *        property="email",
 *        description="Email of the user",
 *        type="string",
 *        format="email",
 *        example="john.smith@company.com"
 *    ),
 *    @OA\Property(
 *        property="phone",
 *        description="Phone of the user",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="password",
 *        description="Password of the user",
 *        type="string",
 *        format="password",
 *        example=""
 *    )
 *  )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password', 'photo', 'lang', 'last_login_at', 'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function company()
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function getAll()
    {
        return User::paginate(10);
    }

    public function getFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return User::where('company_id', $company_id)->paginate(10);
    }

    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? config('app.timezone'),
        );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
