<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    const ACTIVE = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'bank';
  
    public function getAll()
    {
        return Bank::orderBy('name', 'asc')->get();
    }
}