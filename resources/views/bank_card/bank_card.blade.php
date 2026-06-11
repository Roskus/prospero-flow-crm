@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', [
        'title' => $card->id ? __('Edit card') : __('Add card')
    ])

    <form method="POST" action="{{ url('/bank-card/save') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $card->id ?? '' }}">
        <input type="hidden" name="bank_account_id" value="{{ $bank_account->id }}">

        <div class="card mt-2">
            <div class="card-body">
                <div class="text-muted small mb-3">
                    <i class="las la-piggy-bank"></i>
                    {{ $bank_account->account_name ?: $bank_account->bank?->name }}
                    ({{ strtoupper($bank_account->currency) }})
                </div>

                <div class="row">
                    <div class="col-12 col-md-3">
                        <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" required class="form-select form-select-lg">
                            <option value="debit" @selected(old('type', $card->type) === 'debit')>{{ __('Debit') }}</option>
                            <option value="credit" @selected(old('type', $card->type) === 'credit')>{{ __('Credit') }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="network">{{ __('Network') }} <span class="text-danger">*</span></label>
                        <select name="network" id="network" required class="form-select form-select-lg">
                            @foreach(\App\Models\BankCard::NETWORKS as $value => $label)
                                <option value="{{ $value }}" @selected(old('network', $card->network) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="cardholder_name">{{ __('Cardholder name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="cardholder_name" id="cardholder_name"
                               value="{{ old('cardholder_name', $card->cardholder_name) }}"
                               maxlength="80" required
                               class="form-control form-control-lg text-uppercase"
                               placeholder="JOHN DOE">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-5">
                        <label for="number">
                            {{ __('Card number') }}
                            @if(!$card->id) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="text" name="number" id="number"
                               maxlength="19" class="form-control form-control-lg font-monospace"
                               value="{{ $card->id ? $card->number : old('number') }}"
                               placeholder="**** **** **** ****"
                               inputmode="numeric"
                               @if(!$card->id) required @endif>
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="cvv">CVV</label>
                        <input type="text" name="cvv" id="cvv"
                               maxlength="4" class="form-control form-control-lg font-monospace"
                               value="{{ $card->id ? $card->cvv : old('cvv') }}"
                               placeholder="***"
                               inputmode="numeric">
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="expires_month">{{ __('Exp. month') }} <span class="text-danger">*</span></label>
                        <select name="expires_month" id="expires_month" required class="form-select form-select-lg">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected(old('expires_month', $card->expires_month) == $m)>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="expires_year">{{ __('Exp. year') }} <span class="text-danger">*</span></label>
                        <select name="expires_year" id="expires_year" required class="form-select form-select-lg">
                            @for($y = date('Y'); $y <= date('Y') + 15; $y++)
                                <option value="{{ $y }}" @selected(old('expires_year', $card->expires_year) == $y)>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <label for="notes">{{ __('Notes') }}</label>
                        <input type="text" name="notes" id="notes" maxlength="255"
                               value="{{ old('notes', $card->notes) }}"
                               class="form-control form-control-lg">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/bank-account/show/' . $bank_account->id) }}" class="btn btn-secondary btn-lg">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary btn-lg">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
