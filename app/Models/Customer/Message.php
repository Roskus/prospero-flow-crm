<?php

declare(strict_types=1);

namespace App\Models\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $with = ['author'];

    /**
     * Adding a message counts as an interaction with the customer.
     *
     * @var list<string>
     */
    protected $touches = ['customer'];

    protected static function boot()
    {
        parent::boot();

        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
