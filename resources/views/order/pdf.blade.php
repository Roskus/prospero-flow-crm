<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 10mm; }
        table { width: 100%; border-collapse: collapse; }
        .layout-table td { vertical-align: top; padding: 4px 6px; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .order-label { font-size: 20px; font-weight: bold; margin-top: 6px; }
        .info-label { font-weight: bold; }
        .items-table th { background: #f0f0f0; border: 1px solid #ccc; padding: 5px 7px; text-align: left; }
        .items-table td { border: 1px solid #ccc; padding: 5px 7px; vertical-align: top; }
        .items-table tfoot th { border: 1px solid #ccc; padding: 5px 7px; }
        .text-right { text-align: right; }
        .divider { border-top: 1px solid #ddd; margin: 10px 0; }
        .logo { max-width: 200px; max-height: 80px; }
        .barcode-cell { text-align: right; }
    </style>
</head>
<body>

@php
    $company  = Auth::user()->company;
    $symbol   = App\Helpers\CurrencyHelper::symbol();
    $logoPath = storage_path('app/public/company/'.Illuminate\Support\Str::slug($company->name, '_').'/'.$company->logo);

    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodePng = base64_encode($generator->getBarcode($order->orderNumber(), $generator::TYPE_CODE_128, 2, 60));
@endphp

{{-- Header: logo/name + date/barcode --}}
<table class="layout-table">
    <tr>
        <td width="50%">
            @if(!empty($company->logo) && file_exists($logoPath))
                @php
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoMime = mime_content_type($logoPath);
                @endphp
                <img src="data:{{ $logoMime }};base64,{{ $logoData }}" class="logo" alt="">
            @else
                <span class="company-name">{{ $company->name }}</span>
            @endif
            <div class="order-label">{{ __('Order') }}</div>
        </td>
        <td width="50%" class="barcode-cell">
            <div><span class="info-label">{{ __('Date') }}:</span> {{ $order->created_at?->format('d/m/Y') }}</div>
            <div><span class="info-label">{{ __('Number') }}:</span> {{ $order->orderNumber() }}</div>
            <img src="data:image/png;base64,{{ $barcodePng }}" style="max-width:200px;" alt="{{ $order->orderNumber() }}">
        </td>
    </tr>
</table>

<div class="divider"></div>

{{-- Company info --}}
<table class="layout-table">
    <tr>
        <td width="50%">
            <div><span class="info-label">{{ __('Business name') }}:</span> {{ $company->business_name }}</div>
            <div><span class="info-label">{{ __('Identity number') }}:</span> {{ $company->vat }}</div>
            <div><span class="info-label">{{ __('Phone') }}:</span> {{ $company->phone }}</div>
            <div><span class="info-label">E-mail:</span> {{ $company->email }}</div>
            <div><span class="info-label">Web:</span> {{ $company->website }}</div>
        </td>
        <td width="50%">
            <div><span class="info-label">{{ __('Address') }}:</span> {{ $company->street }}</div>
            <div><span class="info-label">{{ __('Province') }}:</span> {{ $company->province }}</div>
            <div><span class="info-label">{{ __('City') }}:</span> {{ $company->city }}</div>
            <div><span class="info-label">{{ __('Zipcode') }}:</span> {{ $company->zipcode }}</div>
            <div><span class="info-label">{{ __('Seller') }}:</span> {{ !empty($order->seller) ? $order->seller->first_name.' '.$order->seller->last_name : '' }}</div>
        </td>
    </tr>
</table>

<div class="divider"></div>

{{-- Customer info --}}
<table class="layout-table">
    <tr>
        <td width="33%">
            <div><span class="info-label">{{ __('Customer') }}:</span> {{ $order->customer->name }}</div>
            <div><span class="info-label">{{ __('Phone') }}:</span> {{ $order->customer->phone }}</div>
            <div><span class="info-label">{{ __('Province') }}:</span> {{ $order->customer->province }}</div>
            <div><span class="info-label">{{ __('Street') }}:</span> {{ $order->customer->street }}</div>
        </td>
        <td width="33%">
            <div><span class="info-label">{{ __('Business name') }}:</span> {{ $order->customer->business_name }}</div>
            <div><span class="info-label">{{ __('Mobile') }}:</span> {{ $order->customer->mobile }}</div>
            <div><span class="info-label">{{ __('City') }}:</span> {{ $order->customer->city }}</div>
        </td>
        <td width="33%">
            <div><span class="info-label">{{ __('Identity number') }}:</span> {{ $order->customer->vat }}</div>
            <div><span class="info-label">E-mail:</span> {{ $order->customer->email }}</div>
            <div><span class="info-label">{{ __('Zipcode') }}:</span> {{ $order->customer->zipcode }}</div>
        </td>
    </tr>
</table>

<div class="divider"></div>

{{-- Order items --}}
<table class="items-table">
    <thead>
        <tr>
            <th>SKU</th>
            <th>{{ __('Product') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Tax') }}</th>
            <th>{{ __('Qty') }}</th>
            <th>{{ __('Discount') }}</th>
            <th>{{ __('Subtotal') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($order->items as $item)
        <tr>
            <td>{{ $item->product->sku ?? '' }}</td>
            <td>{{ $item->product->name ?? '' }}</td>
            <td>{{ $symbol }} {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td>{{ $item->tax }}%</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->discount }}%</td>
            <td>{{ $symbol }} {{ number_format($item->getSubtotal(), 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="7">{{ __('No items') }}</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">&nbsp;</th>
            <th class="text-right">{{ __('Taxes') }}</th>
            <th>{{ $symbol }} {{ number_format($order->getTax(), 2, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="5">&nbsp;</th>
            <th class="text-right">{{ __('Total') }}</th>
            <th>{{ $symbol }} {{ number_format($order->getTotal(), 2, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
