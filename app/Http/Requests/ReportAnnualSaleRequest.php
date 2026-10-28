<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Same permission and seller rules as the period sales report, for a calendar year.
 */
class ReportAnnualSaleRequest extends ReportSaleRequest
{
    public const int FIRST_YEAR = 2000;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'year' => ['nullable', 'integer', 'min:'.self::FIRST_YEAR, 'max:'.(now()->year + 1)],
            'seller_id' => ['nullable', 'integer', Rule::exists('user', 'id')->where('company_id', Auth::user()->company_id)],
        ];
    }

    public function year(): int
    {
        return $this->filled('year') ? (int) $this->validated('year') : (int) now()->year;
    }

    /**
     * @return array<string, mixed>
     */
    public function annualReportData(): array
    {
        $companyId = (int) Auth::user()->company_id;
        $sellerId = $this->sellerId();
        $canSeeAllSellers = $this->canSeeAllSellers();

        return [
            'report' => (new Order)->getAnnualSalesReport($companyId, $this->year(), $sellerId),
            'year' => $this->year(),
            'seller_id' => $sellerId,
            'seller' => $sellerId ? User::where('company_id', $companyId)->find($sellerId) : null,
            'can_see_all_sellers' => $canSeeAllSellers,
            'sellers' => $canSeeAllSellers ? (new User)->getAllActiveByCompany($companyId) : collect(),
        ];
    }
}
