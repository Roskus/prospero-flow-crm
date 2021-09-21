<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\Customer;

final class Order extends Model
{
    use SoftDeletes;

    # Class Constants
    const CANCELED = 0;
    const PENDING = 1;
    const CONFIRMED = 2;
    const COMPLETED = 3;

    # Class Properties
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'order';

    protected ?int $company_id = null;
    protected ?int $customer_id;
    protected ?int $status;

    /**
     * @var mixed
     */
    private ?float $amount;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('orderCreated', function(Builder $builder) {
            $builder->orderby('created_at', 'DESC');
        });
    }

    # Constructor
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->company_id = 0;
        $this->customer_id = 0;
        $this->setAttribute('status', Order::PENDING);
        $this->setAttribute('created_at', now());
        $this->amount = 0.0;
    }

    # Relationships
    /**
     * @return Company
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * @return Customer
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(App\Models\Order\Item::class, 'id', 'order_id');
    }

    # Accessors and Mutators
    public function getId() : int
    {
        return (int) $this->getAttribute('id');
    }

    /*
    * @return int
    */
    public function getCompanyId() : int
    {
        return (int) $this->getAttribute('company_id');
    }

    public function setCompanyId(int $company_id)
    {
        $this->setAttribute('company_id', $company_id);
    }

    /**
     * @return int
     */
    public function getCustomerId() : int
    {
        return (int) $this->getAttribute('customer_id');
    }

    /**
     * @param int $customer_id
     */
    public function setCustomerId(int $customer_id): void
    {
        $this->setAttribute('customer_id', $customer_id);
    }

    /**
     * @return float
     */
    public function getAmount() : float
    {
        return (float) $this->getAttribute('amount');
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->setAttribute('amount', $amount);
    }

    /**
     * Return amount - discounts + taxes
     *
     * @return float
     */
    public function getTotal() : float
    {
        return $this->getAmount();
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
