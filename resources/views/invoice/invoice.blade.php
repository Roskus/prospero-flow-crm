@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Create invoice')])

<div class="card">
    <div class="card-body">
    <form method="post" action="{{ route('invoice.save') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">

    @error('order')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <p class="lead">
        {{ __('Order') }} #{{ $order->orderNumber() }}@if($order->order_date) · {{ __('Order date') }}: {{ $order->order_date->format('d/m/Y') }}@endif · {{ $order->customer?->displayName() }} · {{ $order->currency }}
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
            @if($order->order_date)
                <button type="button" class="btn btn-link btn-sm px-0" id="use-order-date" data-date="{{ $order->order_date->toDateString() }}"
                        onclick="document.getElementById('issue_date').value = this.dataset.date">{{ __('Use the order date') }} ({{ $order->order_date->format('d/m/Y') }})</button>
            @endif
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
                <th>SKU/EAN</th>
                <th>{{ __('Product') }}</th>
                <th class="text-end">{{ __('Quantity') }}</th>
                <th class="text-end">{{ __('Price (taxes included)') }}</th>
                <th class="text-end">{{ __('Discount') }}</th>
                <th class="text-end">{{ __('Subtotal') }}</th>
                <th class="text-end">{{ __('Tax') }}</th>
                <th class="text-end">{{ __('Taxes') }}</th>
                <th class="text-end">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                @php($line = $lines[$item->id])
                <tr>
                    <td>{!! collect($item->product?->identifierCodes() ?? [])->map(fn (string $code) => e($code))->implode('<br>') !!}</td>
                    <td>{{ $item->product?->name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($item->unit_price, $order->currency) }}</td>
                    <td class="text-end">{{ (float) $item->discount }}%</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($line['subtotal'], $order->currency) }}</td>
                    <td class="text-end">{{ (float) $item->tax }}%</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($line['tax_amount'], $order->currency) }}</td>
                    <td class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($line['total'], $order->currency) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="8" class="text-end">{{ __('Subtotal (without taxes)') }}</th>
                <th class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($invoiceSubtotal, $order->currency) }}</th>
            </tr>
            <tr>
                <th colspan="8" class="text-end">{{ __('Taxes included') }}</th>
                <th class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($invoiceTax, $order->currency) }}</th>
            </tr>
            <tr>
                <th colspan="8" class="text-end">{{ __('Total') }}</th>
                <th class="text-end">{{ \App\Helpers\CurrencyHelper::formatIn($invoiceTotal, $order->currency) }}</th>
            </tr>
            </tfoot>
        </table>
    </div>

    @can('create payment')
    <section class="border rounded p-3 mb-3" id="payment-section">
        <div class="form-check mb-2">
            <input type="hidden" name="register_payment" value="0">
            <input type="checkbox" name="register_payment" id="register_payment" value="1" class="form-check-input" @checked(old('register_payment'))
                   onchange="document.getElementById('payment-fields').classList.toggle('d-none', ! this.checked)">
            <label for="register_payment" class="form-check-label fw-semibold">{{ __('Register a payment now') }}</label>
        </div>
        <div id="payment-fields" @class(['d-none' => ! old('register_payment')])>
            @include('invoice.partial._payment_fields', ['prefix' => 'payment', 'currency' => $order->currency, 'amount' => $invoiceTotal, 'accounts' => $accounts, 'currencies' => $currencies, 'orderDate' => $order->order_date])
        </div>
    </section>
    @endcan

    <div>
        <a href="{{ url('/order') }}" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-lg btn-primary">{{ __('Create invoice') }}</button>
    </div>
    </form>
    </div>
</div>
@endsection
