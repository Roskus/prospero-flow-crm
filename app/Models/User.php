<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OAT;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

#[OAT\Schema(schema: 'User', required: ['first_name', 'email', 'password'])]
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use HasRoles;

    protected $table = 'user';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password', 'photo', 'lang', 'timezone', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'company_id',
        'password',
        'remember_token',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $with = ['company'];

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $id = null;

    private ?int $company_id = null;

    #[OAT\Property(description: 'First Name of the user', type: 'string', example: 'John')]
    protected ?string $first_name = null;

    #[OAT\Property(description: 'Last Name of the user', type: 'string', example: 'Smith')]
    protected ?string $last_name = null;

    #[OAT\Property(description: 'Email of the user', type: 'string', format: 'email', example: 'john.smith@company.com')]
    protected ?string $email = null;

    #[OAT\Property(description: 'Phone of the user', type: 'string', example: '+3464500000')]
    protected ?string $phone = null;

    //#[OAT\Property(description: 'Password of the user', type: 'string', format: 'password', example: 'qwerty')]
    //protected ?string $password = null;

    #[OAT\Property(description: 'Profile photo', type: 'string', example: 'profile.jpg')]
    protected ?string $photo = null;

    #[OAT\Property(description: 'Language ISO code', type: 'string', example: 'es')]
    protected ?string $lang = null;

    #[OAT\Property(description: 'Date time zone', type: 'string', example: 'UTC')]
    protected ?string $timezone = null;

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function getAll()
    {
        return User::orderBy('company_id')->paginate(10);
    }

    public function getFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * A "name" property must exist in the model in order for the Mail class
     * to correctly display the name in addresses.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
        );
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
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
