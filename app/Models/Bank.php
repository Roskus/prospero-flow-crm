<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(schema: 'Bank', required: ['name', 'country'])]
class Bank extends Model
{
    const ACTIVE = 1;

    protected $table = 'bank';

    protected $fillable = [
        'name',
        'country_id',
        'bic',
        'phone',
        'email',
        'website',
    ];

    protected $hidden = ['deletead_at'];

    protected $with = ['country'];

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id;

    #[OAT\Property(type: 'string', example: 'Bank of America')]
    protected string $name;

    #[OAT\Property(description: 'Country code ISO-2', type: 'string', example: 'FR')]
    protected string $country_id;

    #[OAT\Property(description: 'BIC of the bank', type: 'string', example: '')]
    protected string $bic;

    #[OAT\Property(description: 'Phone of the bank', type: 'string', example: '+3400000000')]
    protected string $phone;

    #[OAT\Property(description: 'Email of the bank', type: 'string', format: 'email', example: 'bank@bank.com')]
    protected string $email;

    #[OAT\Property(description: 'Website of the bank', type: 'string', format: 'url', example: 'https://bank.com')]
    protected string $website;

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getAll()
    {
        return Bank::orderBy('name', 'asc')->get();
    }

    public function getAllPaginated($limit = 10)
    {
        return Bank::orderBy('name', 'asc')->limit($limit)->paginate();
    }

    public function getAllByCountry(string $country_code)
    {
        return Bank::where('country', $country_code)->orderBy('name', 'asc')->get();
    }
}
