@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Currencies')])

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <strong>{{ __('Current') }} {{ __('Currency') }}:</strong> {{ $baseCurrency }}
            </div>

            <form method="POST" action="{{ route('currency.save') }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Symbol') }}</th>
                                <th>{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $currency)
                                @php
                                    $currencyCode = strtoupper($currency->id);
                                    $isBaseCurrency = $currencyCode === $baseCurrency;
                                    $savedRate = $currencyRates[$currencyCode] ?? null;
                                    $displayRate = old("conversion_rates.$currencyCode", $savedRate ?? ($isBaseCurrency ? '1.000000' : ''));
                                @endphp
                                <tr @class(['table-active' => $isBaseCurrency])>
                                    <td class="text-nowrap">{{ $currencyCode }}</td>
                                    <td>{{ $currency->name }}</td>
                                    <td class="text-nowrap">{{ $currency->symbol ?: '—' }}</td>
                                    <td style="min-width: 180px;">
                                        <input
                                            type="number"
                                            step="0.000001"
                                            min="0.000001"
                                            name="conversion_rates[{{ $currencyCode }}]"
                                            value="{{ $displayRate }}"
                                            class="form-control @error("conversion_rates.$currencyCode") is-invalid @enderror"
                                            @readonly($isBaseCurrency)
                                        >
                                        @error("conversion_rates.$currencyCode")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ url('/setting') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
