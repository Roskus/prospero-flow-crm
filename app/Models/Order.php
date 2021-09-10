<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const CANCELED = 0;
    const PENDING = 1;
    const CONFIRMED = 2;
    const COMPLETED = 3;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'order';

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function getAll()
    {
        return Order::all();
    }

    public function getPendingCount()
    {
        return Order::where('status', self::PENDING)->count();
    }
}
