@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Products'), 'count' => $product_count])
@include('layouts.partials._search_buttons_bar', [
    'action_search' => url("/product"), 
    'buttons' => [
        ['url' => url('/product/create'), 'class' => 'btn btn-primary', 'text' => __('New')],
        ['url' => url('/product/import'), 'class' => 'btn btn-success', 'text' => __('Import'), 'icon_class' => 'las la-file-csv']
    ]
])

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th>#ID</th>
                <th>{{ __('Photo') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Name') }}</th>
                <th>SKU</th>
                <th class="d-none d-sm-table-cell">{{ __('Brand') }}</th>
                <th class="text-center">{{ __('Quantity') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>
                @if($product->photo)
                  <img src="{{ asset("/asset/upload/product/$product->id/$product->photo")}}" alt="" width="100" class="img-fluid img-thumbnail">
                @endif
                </td>
                <td>{{ (!empty($product->category)) ? $product->category->name : '' }}</td>
                <td><a href="{{ url("/product/update/$product->id") }}">{{ $product->name }}</a></td>
                <td class="text-nowrap">{{ $product->sku }}</td>
                <td class="text-nowrap d-none d-sm-table-cell">{{ (!empty($product->brand)) ? $product->brand->name : '' }}</td>
                <td class="text-center">
                <span class="@if($product->quantity < $product->min_stock_quantity) text-danger @else text-success @endif">
                    {{ $product->quantity }}
                </span>
                </td>
                <td class="text-nowrap">
                    {{ number_format($product->price, 2, ',', '.') }}
                    {{ Auth::user()->company->country->currency?->symbol }}
                </td><!--Money::format(-->
                <td class="text-nowrap">{{ $product->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/product/update/$product->id") }}" class="btn bt-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    <a href="{{ url("/product/delete/$product->id") }}" class="btn bt-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
@endsection
