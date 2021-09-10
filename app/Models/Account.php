<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const ACTIVE = 1;

    protected $table = 'account';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'datetime:Y-m-d',
    ];

    public function getAll()
    {
        return Account::orderBy('created_at', 'desc')->get();
    }

    /**
     * @param int $company_id
     * @return mixed
     */
    public function getAllActiveByCompany(int $company_id)
    {
        return Account::where('status', self::ACTIVE)
                        ->where('company_id', $company_id)
                        ->orderBy('created_at', 'desc')->get();
    }
}
