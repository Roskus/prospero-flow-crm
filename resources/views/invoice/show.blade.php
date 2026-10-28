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
