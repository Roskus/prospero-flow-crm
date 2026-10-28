{{--
    Payment fields shared by the invoice payment form and the payment section of the invoice form.
    Params: currency (invoice currency), amount (default amount), accounts, currencies, prefix ('' or 'payment').
--}}
@php
    $prefix = $prefix ?? '';
    $field = fn (string $name): string => $prefix === '' ? $name : $prefix.'['.$name.']';
    $key = fn (string $name): string => $prefix === '' ? $name : $prefix.'.'.$name;
    $id = fn (string $name): string => ($prefix === '' ? 'payment_' : $prefix.'_').$name;
@endphp
<div class="row g-2">
    <div class="col-12 col-md-3">
        <label for="{{ $id('payment_method') }}">{{ __('Payment method') }} <span class="text-danger">*</span></label>
        <select name="{{ $field('payment_method') }}" id="{{ $id('payment_method') }}" class="form-select @error($key('payment_method')) is-invalid @enderror">
            @foreach(\App\Models\Transaction::PAYMENT_METHODS as $method => $label)
                <option value="{{ $method }}" @selected(old($key('payment_method'), \App\Models\Transaction::METHOD_CASH) === $method)>{{ __($label) }}</option>
            @endforeach
        </select>
        @error($key('payment_method')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-3">
        <label for="{{ $id('amount') }}">{{ __('Amount') }} <span class="text-danger">*</span></label>
        <input type="number" name="{{ $field('amount') }}" id="{{ $id('amount') }}" step="0.01" min="0.01"
               value="{{ old($key('amount'), $amount) }}"
               class="form-control @error($key('amount')) is-invalid @enderror">
        @error($key('amount')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-3">
        <label for="{{ $id('currency') }}">{{ __('Currency') }}</label>
        <select name="{{ $field('currency') }}" id="{{ $id('currency') }}" class="form-select @error($key('currency')) is-invalid @enderror">
            @foreach($currencies as $currencyOption)
                <option value="{{ strtoupper($currencyOption->id) }}" @selected(strtoupper($currencyOption->id) === strtoupper((string) old($key('currency'), $currency)))>{{ strtoupper($currencyOption->id) }}</option>
            @endforeach
        </select>
        @error($key('currency')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-3">
        <label for="{{ $id('exchange_rate') }}">{{ __('Exchange rate') }}</label>
        <input type="number" name="{{ $field('exchange_rate') }}" id="{{ $id('exchange_rate') }}" step="0.000001" min="0.000001"
               value="{{ old($key('exchange_rate')) }}" placeholder="{{ __('Current rate') }}"
               class="form-control @error($key('exchange_rate')) is-invalid @enderror">
        @error($key('exchange_rate')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-4">
        <label for="{{ $id('bank_account_id') }}">{{ __('Account') }}</label>
        <select name="{{ $field('bank_account_id') }}" id="{{ $id('bank_account_id') }}" class="form-select @error($key('bank_account_id')) is-invalid @enderror">
            <option value="">{{ __('Cash account of the branch') }}</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" @selected((string) old($key('bank_account_id')) === (string) $account->id)>{{ $account->account_name }} ({{ $account->currency }})</option>
            @endforeach
        </select>
        <small class="form-text text-muted">{{ __('Required for transfers and Bizum.') }}</small>
        @error($key('bank_account_id')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-2">
        <label for="{{ $id('payment_date') }}">{{ __('Payment date') }}</label>
        <input type="date" name="{{ $field('payment_date') }}" id="{{ $id('payment_date') }}"
               value="{{ old($key('payment_date'), today()->toDateString()) }}"
               class="form-control @error($key('payment_date')) is-invalid @enderror">
        @error($key('payment_date')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-3">
        <label for="{{ $id('reference') }}">{{ __('Reference') }}</label>
        <input type="text" name="{{ $field('reference') }}" id="{{ $id('reference') }}" maxlength="80"
               value="{{ old($key('reference')) }}"
               class="form-control @error($key('reference')) is-invalid @enderror">
        @error($key('reference')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 col-md-3">
        <label for="{{ $id('attachment') }}">{{ __('Receipt') }}</label>
        <input type="file" name="{{ $field('attachment') }}" id="{{ $id('attachment') }}" accept=".pdf,.jpg,.jpeg,.png,.webp"
               class="form-control @error($key('attachment')) is-invalid @enderror">
        @error($key('attachment')) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
