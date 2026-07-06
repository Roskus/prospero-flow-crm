<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Brand',
    required: ['name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Brand ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Brand name', type: 'string', example: 'My brand'),
    ],
    type: 'object'
)]
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'brand';

    protected $fillable = [
        'name',
        'company_id',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    public function getAll()
    {
        return Brand::orderBy('name', 'asc')->get();
    }

    public function getAllByCompanyId(int $company_id)
    {
        return Brand::where('company_id', $company_id)->get();
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return $this->getAllByCompanyId($company_id);
    }

    public static function getAllActiveAsArrayByCompany(int $company_id): array
    {
        return Brand::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    public static function getBrandIdByName(array $brands, string $name): ?int
    {
        foreach ($brands as $brand) {
            if ($brand['name'] == $name) {
                return (int) $brand['id'];
            }
        }

        return null;
    }
}
