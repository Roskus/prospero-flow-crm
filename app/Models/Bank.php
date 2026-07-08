<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(
    schema: 'Bank',
    required: ['name', 'country_id'],
    properties: [
        new OAT\Property(property: 'uuid', description: 'Bank UUID', type: 'string', example: 'UUID'),
        new OAT\Property(property: 'name', description: 'Bank name', type: 'string', example: 'Bank of America'),
        new OAT\Property(property: 'country_id', description: 'Country code ISO-2', type: 'string', example: 'FR'),
        new OAT\Property(property: 'bic', description: 'BIC of the bank', type: 'string'),
        new OAT\Property(property: 'phone', description: 'Phone of the bank', type: 'string', example: '+3400000000'),
        new OAT\Property(property: 'email', description: 'Email of the bank', type: 'string', format: 'email', example: 'bank@bank.com'),
        new OAT\Property(property: 'website', description: 'Website of the bank', type: 'string', format: 'url', example: 'https://bank.com'),
    ],
    type: 'object'
)]
class Bank extends Model
{
    use HasFactory, HasUuids;

    const int ACTIVE = 1;

    protected $table = 'bank';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getAll()
    {
        return Bank::orderBy('country_id')->orderBy('name')->get();
    }

    public function getAllPaginated(array $filters, int $limit = 10)
    {
        // Iniciar la consulta base
        $query = Bank::orderBy('name', 'asc');

        if (! empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'LIKE', '%'.$filters['name'].'%');
        }

        return $query->paginate($limit);
    }

    public function getAllByCountry(string $country_code)
    {
        return Bank::where('country', $country_code)->orderBy('name', 'asc')->get();
    }
}
