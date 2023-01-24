<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Category', required: ['name'])]
class Category extends Model
{
    const ACTIVE = 1;

    protected $table = 'category';

    protected $fillable = [
        'company_id',
        'name',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected int $company_id;

    #[OAT\Property(type: 'string', example: 'My category')]
    protected string $name;

    public function getAll()
    {
        return Category::orderBy('name', 'asc')->get();
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
