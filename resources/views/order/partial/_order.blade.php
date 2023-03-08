<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                @if(empty(Auth::user()->company->logo))
                    {{ Auth::user()->company->name }}
                @else
                    <img src="{{ App\Helpers\ImageHelper::render(public_path('/asset/upload/company/'.\Illuminate\Support\Str::slug(Auth::user()->company->name, '_').'/'.Auth::user()->company->logo)) }}" alt="{{ env('APP_NAME') }}" class="logo">
                @endif
                <div>
                    <h1>{{ __('Order') }}</h1>
                </div>
            </div>
            <div class="col">
                <div class="mt-4">
                    <label>{{ __('Date') }}:</label> {{ ($order->created_at) ? $order->created_at->format('d/m/Y') : '' }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div>
                    <label>{{ 'Number' }}:</label> {{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}
                </div>
                <div>
                    <label>{{ __('Business name') }}:</label> {{ Auth::user()->company->business_name }}
                </div>
                <div>
                    <label>{{ __('Vat') }}:</label> {{ Auth::user()->company->vat }}
                </div>
                <div>
                    <label>{{ __('Phone') }}:</label> {{ \App\Helpers\PhoneHelper::format(Auth::user()->company->phone) }}
                </div>
                <div class="">
                    <label>E-mail:</label> {{ Auth::user()->company->email }}
                </div>
            </div>
            <div class="col">
                <div>
                    <label>{{ __('Address') }}:</label> {{ Auth::user()->company->street }}
                </div>
                <div>
                    <label>{{ __('Province') }}:</label> {{ Auth::user()->company->province }}
                </div>
                <div>
                    <label>{{ __('City') }}:</label> {{ Auth::user()->company->city }}
                </div>
                <div>
                    <label>{{ __('Zipcode') }}:</label> {{ Auth::user()->company->zipcode }}
                </div>
                <div>
                    <label>{{ __('Seller') }}:</label>
                    {{ !empty($order->seller) ? $order->seller->first_name.' '.$order->seller->last_name : '' }}
                </div>
            </div>
        </div>
    </div>
</div><!--./card-->
<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <label>{{ __('Customer') }}:</label> {{ $order->customer->name }}
            </div>
            <div class="col">
                <label>{{ __('Business name') }}:</label> {{ $order->customer->business_name }}
            </div>
            <div class="col">
                <label>{{ __('Vat') }}:</label> {{ $order->customer->vat }}
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label>{{ __('Phone') }}:</label> {{ $order->customer->phone }}
            </div>
            <div class="col">
                <label>{{ __('Mobile') }}:</label> {{ $order->customer->mobile }}
            </div>
            <div class="col">
                <label>E-mail:</label> {{ $order->customer->email }}
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label>{{ __('Province') }}:</label> {{ $order->customer->province }}
            </div>
            <div class="col">
                <label>{{ __('City') }}:</label> {{ $order->customer->city }}
            </div>
            <div class="col">
                <label>{{ __('Zipcode') }}:</label> {{ $order->customer->zipcode }}
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Street') }}:</label> {{ $order->customer->street }}
            </div>
        </div>
    </div>
</div><!--./card-->
<div class="card mt-2">
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th width="60%">{{ __('Product') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Subtotal') }}</th>
            </tr>
            </thead>
            <tbody id="order-items">
            @if($order->items()->count() == 0)
                <tr id="row-no-data">
                    <td colspan="4">{{ __('No items') }}</td>
                </tr>
            @else
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ (!empty($item->product)) ? $item->product->name : '' }}
                            @if(!empty($item->product->photo))
                                <img src="{{ App\Helpers\ImageHelper::render(public_path('/asset/upload/product/'.$item->product->id.'/'.$item->product->photo)) }}"
                                     alt="" width="100" class="img-fluid img-thumbnail">
                            @endif
                        </td>
                        <td>
                            {{ number_format($item->unit_price, 2, ',', '.') }}
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format(($item->unit_price * $item->quantity), 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
            <tfoot>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th class="text-right">{{ __('Total') }}</th>
                <th>
                    {{ number_format($order->getTotal(), 2, ',', '.') }}
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
</div><!--./card-->

