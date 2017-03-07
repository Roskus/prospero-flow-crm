<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    const ACTIVE = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'customer';
  
    public function getAll()
    {
        return Customer::orderBy('name','asc')->get();
    }
  
}