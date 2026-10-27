@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Orders')])

<div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
    <a href="{{ url('order/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    @if($branches->isNotEmpty())
    <form method="get" action="{{ url('order') }}" class="d-flex gap-2 align-items-center">
        <label for="branch_id" class="visually-hidden">{{ __('Branch') }}</label>
        <select name="branch_id" id="branch_id" class="form-select" onchange="this.form.submit()">
            <option value="">{{ __('All branches') }}</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected($branchId === $branch->id)>{{ $branch->code }} - {{ $branch->name }}</option>
            @endforeach
        </select>
    </form>
    @endif
</div>

<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th>{{ __('Number') }}</th>
                @if($branches->isNotEmpty())
                <th>{{ __('Branch') }}</th>
                @endif
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Item count') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td>
                    @if(!empty($order->order_number))
                    <a href="{{ url('order/show/'.$order->order_number) }}">{{ $order->orderNumber() }}</a>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                @if($branches->isNotEmpty())
                <td class="text-nowrap">{{ $order->branch?->name }}</td>
                @endif
                <td>{{ $order->customer?->displayName() }}</td>
                <td class="text-center">
                    {{ (!empty($order->items)) ? $order->items->count() : 0 }}
                </td>
                <td>
                    {{ $order->getAmountFormated() }}
                </td>
                <td class="text-nowrap">{{ $order->order_date?->format('d/m/Y') }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                <td><span class="badge bg-{{ $order->getStatusBadgeClass() }}">{{ __($order->getStatusLabel()) }}</span></td>
                <td class="text-nowrap">
                    @if(!empty($order->order_number))
                    <a href="{{ url('order/show/'.$order->order_number) }}" title="{{ __('View') }}"
                       class="btn btn-xs btn-primary text-white">
                        <i class="las la-eye"></i>
                    </a>

                    @if($order->status == App\Models\Order::PENDING)
                    <a href="{{ url('/order/update/'.$order->order_number) }}" title="{{ __('Edit') }}"
                       class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    <form method="POST" action="{{ route('order.confirm', $order->order_number) }}" style="display:inline;">
                        @csrf
                        <button type="submit" title="{{ __('Confirm') }}" class="btn btn-xs btn-success text-white" onclick="return confirm('{{ __('Are you sure you want to confirm the order: :id?', ['id' => $order->order_number]) }}')">
                            <i class="las la-check-circle"></i>
                        </button>
                    </form>
                    @endif

                    @if($order->status == App\Models\Order::CONFIRMED)
                    <form method="POST" action="{{ route('order.pending', $order->order_number) }}" style="display:inline;">
                        @csrf
                        <button type="submit" title="{{ __('Back to pending') }}" class="btn btn-xs btn-warning text-white" onclick="return confirm('{{ __('Are you sure you want to set the order :id back to pending?', ['id' => $order->order_number]) }}')">
                            <i class="las la-undo"></i>
                        </button>
                    </form>
                    @endif

                    @if(in_array($order->status, [App\Models\Order::PENDING, App\Models\Order::CONFIRMED]))
                    <form method="POST" action="{{ route('order.cancel', $order->order_number) }}" style="display:inline;">
                        @csrf
                        <button type="submit" title="{{ __('Cancel') }}" class="btn btn-xs btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to cancel the order: :id?', ['id' => $order->order_number]) }}')">
                            <i class="las la-ban"></i>
                        </button>
                    </form>
                    @endif

                    <a href="{{ url('/order/download/'.$order->order_number) }}" title="{{ __('Download') }}"
                       class="btn btn-xs btn-secondary">
                        <i class="las la-file-pdf"></i>
                    </a>

                    @can('delete order')
                    <a href="#" onclick="Order.delete({{ $order->order_number }},'{{ __('Are you sure you want to delete the order: :id?', ['id' => $order->order_number]) }}')" title="{{ __('Delete') }}"
                       class="btn bt-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                    @endcan
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
@push('scripts')
    <script src="{{ asset('/asset/js/Order.js') }}"></script>
@endpush
@endsection

