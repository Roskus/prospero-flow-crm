{{--
    Monthly table and rankings of the annual sales report, shared by the page and its PDF. Plain HTML for dompdf.
    Params: report (Order::getAnnualSalesReport()), year.
--}}
@php
    $money = fn (float $value): string => \App\Helpers\CurrencyHelper::format($value);
    $change = fn (?float $value): string => $value === null ? '—' : ($value > 0 ? '+' : '').number_format($value, 1).'%';
    $changeClass = fn (?float $value): string => $value === null ? 'text-muted' : ($value >= 0 ? 'text-success' : 'text-danger');
@endphp

<table class="table table-sm table-striped annual-months">
    <thead>
    <tr>
        <th>{{ __('Month') }}</th>
        <th class="text-end">{{ $year }}</th>
        <th class="text-end">{{ __('Orders') }}</th>
        <th class="text-end">{{ $year - 1 }}</th>
        <th class="text-end">{{ __('Orders') }}</th>
        <th class="text-end">{{ __('Change') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($report['months'] as $month => $sales)
        <tr>
            <td>{{ ucfirst(\Illuminate\Support\Carbon::create($year, $month, 1)->translatedFormat('F')) }}</td>
            <td class="text-end">{{ $money($sales['total']) }}</td>
            <td class="text-end">{{ $sales['orders'] }}</td>
            <td class="text-end">{{ $money($sales['previous_total']) }}</td>
            <td class="text-end">{{ $sales['previous_orders'] }}</td>
            <td class="text-end {{ $changeClass($sales['change']) }}">{{ $change($sales['change']) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="row">
    <div class="col-12 col-lg-6">
        <h3 class="h6 mt-3">{{ __('Top products') }}</h3>
        <table class="table table-sm annual-products">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Product') }}</th>
                <th class="text-end">{{ __('Quantity') }}</th>
                <th class="text-end">{{ __('Sales') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($report['top_products'] as $position => $product)
                <tr>
                    <td>{{ $position + 1 }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td class="text-end">{{ $product['quantity'] }}</td>
                    <td class="text-end">{{ $money($product['total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">{{ __('No sales') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="col-12 col-lg-6">
        <h3 class="h6 mt-3">{{ __('Top customers') }}</h3>
        <table class="table table-sm annual-customers">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Customer') }}</th>
                <th class="text-end">{{ __('Orders') }}</th>
                <th class="text-end">{{ __('Sales') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($report['top_customers'] as $position => $customer)
                <tr>
                    <td>{{ $position + 1 }}</td>
                    <td>{{ $customer['name'] }}</td>
                    <td class="text-end">{{ $customer['orders'] }}</td>
                    <td class="text-end">{{ $money($customer['total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">{{ __('No sales') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<h3 class="h6 mt-3">{{ __('Sales by seller') }}</h3>
<table class="table table-sm annual-sellers">
    <thead>
    <tr>
        <th>{{ __('Seller') }}</th>
        <th class="text-end">{{ __('Orders') }}</th>
        <th class="text-end">{{ __('Sales') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['sellers'] as $seller)
        <tr>
            <td>{{ $seller['name'] }}</td>
            <td class="text-end">{{ $seller['orders'] }}</td>
            <td class="text-end">{{ $money($seller['total']) }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-muted">{{ __('No sales') }}</td></tr>
    @endforelse
    </tbody>
</table>
