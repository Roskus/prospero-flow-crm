<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderIndexController extends MainController
{
    public function index(Request $request)
    {
        $order = new Order;
        $company = Auth::user()->company;
        $data['branches'] = $company->hasSeveralBranches()
            ? Branch::ofCompany($company->id)->withTrashed()->orderByDesc('is_main')->orderBy('code')->get()
            : collect();
        $data['branchId'] = $data['branches']->isNotEmpty() ? ($request->integer('branch_id') ?: null) : null;
        $data['orders'] = $order->getAllActiveByCompany($company->id, $data['branchId']);

        return view('order/index', $data);
    }
}
