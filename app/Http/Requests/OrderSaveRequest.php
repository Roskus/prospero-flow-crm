<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use App\Models\Order\Item;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create order') && Auth::user()?->can('update order');
    }

    public function rules(): array
    {
        return [
            'id' => ['sometimes', 'integer'],
            'customer_id' => ['required', 'integer'],
            'order_date' => ['sometimes', 'date_format:Y-m-d'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branch', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'seller_id' => ['sometimes', 'integer'],
            'currency' => ['sometimes', 'string', 'max:3'],
            'amount' => ['sometimes', 'numeric', 'min:0', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer', Rule::exists('product', 'id')
                ->where('company_id', Auth::user()->company_id)
                ->where(fn (Builder $query) => $query->whereNull('deleted_at')->orWhereIn('id', $this->productIdsOfTheOrder()))],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'items.*.discount' => ['sometimes', 'numeric', 'min:0'],
            'items.*.tax' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    /**
     * Products already on the edited order stay valid after being deleted, since saving resends every line.
     *
     * @return list<int>
     */
    private function productIdsOfTheOrder(): array
    {
        if (! $this->filled('id')) {
            return [];
        }

        return Item::query()
            ->whereHas('order', fn (Builder $query) => $query->where('company_id', Auth::user()->company_id))
            ->where('order_id', (int) $this->input('id'))
            ->pluck('product_id')
            ->all();
    }
}
