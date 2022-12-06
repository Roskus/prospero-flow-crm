<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category';

    protected $fillable = [
        'company_id',
        'name',
    ];

    public function getAll()
    {
        return Category::orderBy('name', 'asc')->get();
    }

    /**
     * @param  int  $company_id
     * @return mixed
     */
    public function getAllActiveByCompany(int $company_id)
    {
        return Category::where('company_id', $company_id)
                        ->orderBy('name', 'asc')
                        ->get();
    }

    /**
     * @param  int  $company_id
     * @return array
     */
    public static function getAllActiveAsArrayByCompany(int $company_id): array
    {
        return Category::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * @param  string  $category_name
     * @return int|null
     */
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
