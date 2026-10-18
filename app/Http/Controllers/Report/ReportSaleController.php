<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportSaleRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportSaleController extends MainController
{
    public function index(ReportSaleRequest $request): View
    {
        $companyId = (int) Auth::user()->company_id;
        $canSeeAllSellers = Auth::user()->hasAnyRole(['SuperAdmin', 'CompanyAdmin']);

        $from = $request->filled('from') ? Carbon::parse($request->validated('from')) : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->validated('to')) : Carbon::now();
        $sellerId = $canSeeAllSellers
            ? ($request->filled('seller_id') ? (int) $request->validated('seller_id') : null)
            : (int) Auth::id();

        $data['report'] = (new Order)->getSalesReport($companyId, $from, $to, $sellerId);
        $data['from'] = $from;
        $data['to'] = $to;
        $data['seller_id'] = $sellerId;
        $data['can_see_all_sellers'] = $canSeeAllSellers;
        $data['sellers'] = $canSeeAllSellers ? (new User)->getAllActiveByCompany($companyId) : collect();

        return view('report.sale', $data);
    }
}
