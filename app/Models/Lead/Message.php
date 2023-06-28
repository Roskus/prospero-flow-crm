<?php

declare(strict_types=1);

namespace App\Models\Lead;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'lead_message';

    protected $fillable = [
        'lead_id',
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
