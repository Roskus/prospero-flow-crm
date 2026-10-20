<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('read sales report') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'seller_id' => ['nullable', 'integer', Rule::exists('user', 'id')->where('company_id', Auth::user()->company_id)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from.date' => __('The start date is not valid.'),
            'to.date' => __('The end date is not valid.'),
            'to.after_or_equal' => __('The end date must be on or after the start date.'),
            'seller_id.exists' => __('The selected seller is not valid.'),
        ];
    }

    /**
     * Managers see the whole company; any other role only its own sales.
     */
    public function canSeeAllSellers(): bool
    {
        return Auth::user()->hasRole('SuperAdmin') || Auth::user()->isCompanyAdmin();
    }

    public function periodFrom(): Carbon
    {
        return $this->filled('from') ? Carbon::parse($this->validated('from')) : Carbon::now()->startOfMonth();
    }

    public function periodTo(): Carbon
    {
        return $this->filled('to') ? Carbon::parse($this->validated('to')) : Carbon::now();
    }

    public function sellerId(): ?int
    {
        if (! $this->canSeeAllSellers()) {
            return (int) Auth::id();
        }

        return $this->filled('seller_id') ? (int) $this->validated('seller_id') : null;
    }

    /**
     * Data shared by the sales report page and its PDF export.
     *
     * @return array<string, mixed>
     */
    public function reportData(): array
    {
        $companyId = (int) Auth::user()->company_id;
        $from = $this->periodFrom();
        $to = $this->periodTo();
        $sellerId = $this->sellerId();
        $canSeeAllSellers = $this->canSeeAllSellers();

        return [
            'report' => (new Order)->getSalesReport($companyId, $from, $to, $sellerId),
            'from' => $from,
            'to' => $to,
            'seller_id' => $sellerId,
            'seller' => $sellerId ? User::where('company_id', $companyId)->find($sellerId) : null,
            'can_see_all_sellers' => $canSeeAllSellers,
            'sellers' => $canSeeAllSellers ? (new User)->getAllActiveByCompany($companyId) : collect(),
            'kpis' => [
                'total' => ['label' => __('Total sales'), 'currency' => true, 'higher_is_better' => true],
                'orders' => ['label' => __('Orders'), 'currency' => false, 'higher_is_better' => true],
                'average_ticket' => ['label' => __('Average ticket'), 'currency' => true, 'higher_is_better' => true],
                'pending_orders' => ['label' => __('Pending orders'), 'currency' => false, 'higher_is_better' => false],
            ],
        ];
    }
}
