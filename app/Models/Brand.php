<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Brand', required: ['name'], type: 'object')]
class Brand extends Model
{
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

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $id;

    private ?int $company_id = null;

    #[OAT\Property(type: 'string', example: 'My brand')]
    protected string $name;

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
        return Brand::where('company_id', $company_id)->get();
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
