@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Create invoice')])

<div class="card">
    <div class="card-body">
    <form method="post" action="{{ route('invoice.save') }}">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">

    @error('order')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <p class="lead">
        {{ __('Order') }} #{{ $order->orderNumber() }} · {{ $order->customer?->displayName() }} · {{ $order->currency }}
    </p>

    <div class="row">
        <div class="col-md-3">
            <label for="issue_date">{{ __('Issue date') }}</label>
            <input type="date" name="issue_date" id="issue_date" required="required"
                   value="{{ old('issue_date', $issueDate) }}"
                   class="form-control form-control-lg @error('issue_date') is-invalid @enderror">
            @error('issue_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3">
            <label for="due_date">{{ __('Due date') }}</label>
            <input type="date" name="due_date" id="due_date" required="required"
                   value="{{ old('due_date', $dueDate) }}"
                   class="form-control form-control-lg @error('due_date') is-invalid @enderror">
            @error('due_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @if(strtoupper((string) $order->currency) !== $companyCurrency)
        <div class="col-md-3">
            <label for="exchange_rate">{{ __('Exchange rate') }} (1 {{ $order->currency }} = ___ {{ $companyCurrency }})</label>
            <input type="number" name="exchange_rate" id="exchange_rate" required="required" step="0.000001" min="0.000001"
                   value="{{ old('exchange_rate', $exchangeRate) }}"
                   class="form-control form-control-lg @error('exchange_rate') is-invalid @enderror">
            @error('exchange_rate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @endif
    </div>

    <div class="row mt-2">
        <div class="col">
            <label for="notes">{{ __('Notes') }}</label>
            <textarea name="notes" id="notes" rows="3" maxlength="2000" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-sm">
            <thead>
            <tr>
                <th>{{ __('Product') }}</th>
                <th class="text-end">{{ __('Quantity') }}</th>
                <th class="text-end">{{ __('Price') }}</th>
                <th class="text-end">{{ __('Discount') }}</th>
                <th class="text-end">{{ __('Tax') }}</th>
                <th class="text-end">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                @php($line = \App\Services\InvoiceService::lineTotals((string) $item->quantity, (string) $item->unit_price, (string) ($item->discount ?? 0), (string) ($item->tax ?? 0)))
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($item->unit_price, $order->currency) }}</td>
                    <td class="text-end">{{ (float) $item->discount }}%</td>
                    <td class="text-end">{{ (float) $item->tax }}%</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($line['total'], $order->currency) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <a href="{{ url('/order') }}" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-lg btn-primary">{{ __('Create invoice') }}</button>
    </div>
    </form>
    </div>
</div>
@endsection
