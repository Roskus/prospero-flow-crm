<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OAT;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

#[OAT\Schema(
    schema: 'User',
    required: ['first_name', 'email', 'password'],
    properties: [
        new OAT\Property(property: 'id', description: 'User ID', type: 'integer', example: 1),
        new OAT\Property(property: 'first_name', description: 'First name of the user', type: 'string', example: 'John'),
        new OAT\Property(property: 'last_name', description: 'Last name of the user', type: 'string', example: 'Smith'),
        new OAT\Property(property: 'email', description: 'Email of the user', type: 'string', format: 'email', example: 'john.smith@company.com'),
        new OAT\Property(property: 'phone', description: 'Phone of the user', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'photo', description: 'Profile photo filename', type: 'string', example: 'profile.jpg'),
        new OAT\Property(property: 'lang', description: 'Language ISO code', type: 'string', example: 'es'),
        new OAT\Property(property: 'timezone', description: 'Timezone of the user', type: 'string', example: 'UTC'),
        new OAT\Property(property: 'last_login_at', description: 'Last login date and time', type: 'string', format: 'date-time'),
        new OAT\Property(property: 'last_login_ip', description: 'Last login IP address', type: 'string'),
    ],
    type: 'object'
)]
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'user';

    protected $fillable = [
        'company_id',
        'manager_id',
        'first_name',
        'last_name',
        'email', 'phone',
        'password',
        'photo',
        'lang',
        'timezone',
        'employee_number',
        'is_employee',
        'hire_date',
        'vacation_days_override',
        'weekly_hours_override',
        'iban',
        'last_login_at',
        'last_login_ip',
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
        'is_employee' => 'boolean',
        'hire_date' => 'date:Y-m-d',
    ];

    protected $with = ['company'];

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
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
        return User::where('company_id', $company_id)->get();
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
