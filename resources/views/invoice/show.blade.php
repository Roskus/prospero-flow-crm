@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Invoice').' '.$invoice->fullNumber()])

    <div class="mb-2">
        @if($invoice->isOverdue())
            <span class="badge bg-danger">{{ __('Overdue') }}</span>
        @endif
        @unless($invoice->isCancelled())
            <span class="ms-2">{{ __('Balance') }}: {{ \App\Helpers\CurrencyHelper::formatIn($invoice->balance(), $invoice->currency) }}</span>
        @endunless
        @if($invoice->cancelled_at)
            <span class="ms-2 text-muted">{{ __('Cancelled at') }}: {{ $invoice->cancelled_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            @include('invoice.partial._invoice', ['invoice' => $invoice])
        </div>
    </div>

    <section class="card mt-3 d-print-none" id="payments">
        <h2 class="card-header bg-transparent h5 fw-semibold mb-0">
            <i class="las la-money-bill-wave"></i> {{ __('Payments') }}
        </h2>
        <div class="card-body">
            @if($payments->isEmpty())
                <p class="text-muted mb-0">{{ __('No payments yet') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>{{ __('Payment date') }}</th>
                            <th>{{ __('Payment method') }}</th>
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th class="text-end">{{ __('Settled') }}</th>
                            <th>{{ __('Receipt') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $payment)
                            <tr @class(['text-muted' => $payment->status !== \App\Models\Transaction::PAID])>
                                <td class="text-nowrap">{{ $payment->payment_date?->format('d/m/Y') }}</td>
                                <td>{{ __(\App\Models\Transaction::PAYMENT_METHODS[$payment->payment_method] ?? '—') }}</td>
                                <td>{{ $payment->bankAccount?->account_name }}</td>
                                <td>{{ $payment->reference }}</td>
                                <td class="text-end text-nowrap">{{ \App\Helpers\CurrencyHelper::formatIn($payment->amount, (string) $payment->currency) }}</td>
                                <td class="text-end text-nowrap">{{ \App\Helpers\CurrencyHelper::formatIn((string) $payment->invoice_amount, $invoice->currency) }}</td>
                                <td>
                                    @if($payment->attachment)
                                        <a href="{{ Storage::url($payment->attachment) }}" target="_blank" rel="noopener">
                                            <i class="las la-paperclip"></i> {{ basename($payment->attachment) }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @can('create payment')
                @if(! $invoice->isCancelled() && bccomp($invoice->balance(), '0', 2) > 0)
                    <form method="post" action="{{ route('invoice.payment', $invoice->id) }}" enctype="multipart/form-data" class="mt-3" id="payment-form">
                        @csrf
                        <h3 class="h6">{{ __('Register payment') }}</h3>
                        @error('invoice')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @include('invoice.partial._payment_fields', ['currency' => $invoice->currency, 'amount' => $invoice->balance(), 'accounts' => $accounts, 'currencies' => $currencies])
                        <button type="submit" class="btn btn-success mt-2">
                            <i class="las la-check"></i> {{ __('Register payment') }}
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </section>

    <div class="mt-2 text-center d-print-none">
        <a href="{{ route('invoice.index') }}" class="btn btn-lg btn-outline-secondary">{{ __('Back') }}</a>
        <a href="{{ route('invoice.download', $invoice->id) }}" class="btn btn-lg btn-success">
            <i class="las la-file-pdf"></i> {{ __('Download') }}
        </a>
        @can('delete invoice')
            @unless($invoice->isCancelled())
            <form method="POST" action="{{ route('invoice.cancel', $invoice->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-lg btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to cancel the invoice :number?', ['number' => $invoice->fullNumber()]) }}')">
                    <i class="las la-ban"></i> {{ __('Cancel invoice') }}
                </button>
            </form>
            @endunless
        @endcan
    </div>
@endsection
