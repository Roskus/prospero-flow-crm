<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const PENDING = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'order';
  
    public function getAll()
    {
        return Order::all();
    }
  
    public function getPendingCount()
    {
        return Order::where('status', self::PENDING)->count();
    }
}