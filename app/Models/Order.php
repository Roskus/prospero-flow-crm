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
    /**
     * @var mixed
     */
    private int $customer_id;

    /**
     * @var mixed
     */
    private float $amount = 0.0;

    /**
     * @return App\Models\Company
     */
    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    /**
     * @return App\Models\Customer
     */
    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param mixed $customer_id
     */
    public function setCustomerId($customer_id): void
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return mixed
     */
    public function getAmount() : float
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return Order[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Order::all();
    }

    /**
     * @return int
     */
    public function getPendingCount()
    {
        return Order::where('status', self::PENDING)->count();
    }
}
