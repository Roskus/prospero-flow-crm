<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Category',
    required: ['name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Category ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Category name', type: 'string', example: 'My category'),
        new OAT\Property(property: 'amount', description: 'Category amount', type: 'number', format: 'float', example: 100.75),
    ],
    type: 'object'
)]
class Category extends Model
{
    use HasFactory;

    const int ACTIVE = 1;

    protected $table = 'category';

    protected $fillable = [
        'company_id',
        'name',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    public function getAll()
    {
        return Category::orderBy('name', 'asc')->get();
    }

    public function getAllActiveByCompanyPaginated(int $company_id, int $limit = 10)
    {
        return Category::where('company_id', $company_id)->orderBy('name', 'asc')->paginate($limit);
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return Category::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function getAllActiveAsArrayByCompany(int $company_id): array
    {
        return Category::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    public static function getCategoryIdByName(array $categories, string $category_name): ?int
    {
        foreach ($categories as $category) {
            if ($category['name'] == $category_name) {
                return $category['id'];
            }
        }

        return null;
    }
}
