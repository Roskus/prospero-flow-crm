<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceIndexController extends MainController
{
    public function index(Request $request): View
    {
        $company = $request->user()->company;
        $branches = $company->hasSeveralBranches()
            ? Branch::ofCompany($company->id)->withTrashed()->orderByDesc('is_main')->orderBy('code')->get()
            : collect();

        $filters = [
            'branch_id' => $branches->isNotEmpty() ? ($request->integer('branch_id') ?: null) : null,
            'status' => $request->string('status')->toString() ?: null,
            'customer_id' => $request->integer('customer_id') ?: null,
        ];

        return view('invoice.index', [
            'invoices' => Invoice::searchByCompany($company->id, $filters)
                ->with('branch')
                ->paginate(20)
                ->withQueryString(),
            'branches' => $branches,
            'customers' => Customer::withTrashed()
                ->where('company_id', $company->id)
                ->whereIn('id', Invoice::query()->ofCompany($company->id)->select('customer_id'))
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }
}
