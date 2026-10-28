{{--
    Invoice document shared by the invoice page and its PDF. Plain HTML so it renders in dompdf.
    Params: invoice (App\Models\Invoice).
--}}
@php
    $company = $invoice->issuer_data['company'] ?? [];
    $branch = $invoice->issuer_data['branch'] ?? [];
    $customer = $invoice->customer_data ?? [];
    $badge = $invoice->paymentBadge();
    $logo = $invoice->company?->logoDataUri();
@endphp
<table class="table table-borderless" style="width: 100%">
    <tr>
        <td style="vertical-align: top; width: 50%">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $company['name'] ?? '' }}" style="max-width: 200px; max-height: 80px; margin-bottom: 6px">
                <div><strong>{{ $company['business_name'] ?: ($company['name'] ?? '') }}</strong></div>
            @else
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 4px">{{ $company['business_name'] ?: ($company['name'] ?? '') }}</div>
            @endif
            @if(!empty($company['vat']))<div>{{ __('VAT') }}: {{ $company['vat'] }}</div>@endif
            @if(!empty($company['registration_number']))<div>{{ __('Company registration number') }}: {{ $company['registration_number'] }}</div>@endif
            @include('components.address.display', ['address' => $invoice->branchAddress(), 'format' => 'lines'])
            @if(!empty($company['phone']))<div>{{ $company['phone'] }}</div>@endif
            @if(!empty($company['email']))<div>{{ $company['email'] }}</div>@endif
            @if(!empty($company['website']))<div>{{ $company['website'] }}</div>@endif
            <div>{{ __('Branch') }}: {{ $branch['code'] ?? '' }} - {{ $branch['name'] ?? '' }}</div>
        </td>
        <td style="vertical-align: top; width: 50%; text-align: right">
            <div style="font-size: 20px; font-weight: bold">{{ __('Invoice') }}</div>
            <div style="font-size: 16px; font-weight: bold">{{ $invoice->fullNumber() }}</div>
            <div>{{ __('Issue date') }}: {{ $invoice->issue_date->format('d/m/Y') }}</div>
            <div>{{ __('Due date') }}: {{ $invoice->due_date->format('d/m/Y') }}</div>
            @if($invoice->order)
                <div>{{ __('Order') }}: #{{ $invoice->order->orderNumber() }}@if($invoice->order->order_date) · {{ $invoice->order->order_date->format('d/m/Y') }}@endif</div>
            @endif
            <div style="margin: 6px 0">
                <span class="invoice-payment-badge" style="display: inline-block; border: 2px solid {{ $badge['color'] }}; color: {{ $badge['color'] }}; border-radius: 4px; padding: 3px 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px">{{ __($badge['label']) }}</span>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top">
            <strong>{{ __('Customer') }}</strong>
            <div>{{ trim(($customer['name'] ?? '').' '.($customer['business_name'] ?? '')) }}</div>
            @if(!empty($customer['vat']))<div>{{ __('VAT') }}: {{ $customer['vat'] }}</div>@endif
            @include('components.address.display', ['address' => $invoice->customerAddress(), 'format' => 'lines'])
        </td>
    </tr>
</table>

<table class="table table-sm table-bordered invoice-items" style="width: 100%; border-collapse: collapse">
    <thead>
    <tr>
        <th>SKU/EAN</th>
        <th>{{ __('Description') }}</th>
        <th style="text-align: right">{{ __('Quantity') }}</th>
        <th style="text-align: right">{{ __('Price (taxes included)') }}</th>
        <th style="text-align: right">{{ __('Discount') }}</th>
        <th style="text-align: right">{{ __('Subtotal') }}</th>
        <th style="text-align: right">{{ __('Tax') }}</th>
        <th style="text-align: right">{{ __('Taxes') }}</th>
        <th style="text-align: right">{{ __('Total') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td>{!! collect($item->identifierCodes())->map(fn (string $code) => e($code))->implode('<br>') !!}</td>
            <td>{{ $item->description }}</td>
            <td style="text-align: right">{{ (float) $item->quantity }}</td>
            <td style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($item->unit_price, $invoice->currency) }}</td>
            <td style="text-align: right">{{ (float) $item->discount }}%</td>
            <td style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($item->subtotal, $invoice->currency) }}</td>
            <td style="text-align: right">{{ (float) $item->tax }}%</td>
            <td style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($item->tax_amount, $invoice->currency) }}</td>
            <td style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($item->total, $invoice->currency) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="8" style="text-align: right">{{ __('Subtotal (without taxes)') }}</th>
        <th style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($invoice->subtotal, $invoice->currency) }}</th>
    </tr>
    <tr>
        <th colspan="8" style="text-align: right">{{ __('Taxes included') }}</th>
        <th style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($invoice->tax, $invoice->currency) }}</th>
    </tr>
    <tr>
        <th colspan="8" style="text-align: right">{{ __('Total') }}</th>
        <th style="text-align: right">{{ \App\Helpers\CurrencyHelper::formatIn($invoice->total, $invoice->currency) }}</th>
    </tr>
    </tfoot>
</table>

@if($equivalent = $invoice->equivalentText())
    <p style="text-align: right">{{ $equivalent }}</p>
@endif

@if($invoice->notes)
    <p><strong>{{ __('Notes') }}</strong><br>{!! nl2br(e($invoice->notes)) !!}</p>
@endif
