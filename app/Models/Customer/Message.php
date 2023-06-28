<?php

declare(strict_types=1);

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    protected $table = 'customer_message';

    protected $fillable = [
        'customer_id',
        'body',
        'author_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $with = ['author',];

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
