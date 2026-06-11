@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', [
        'title' => $bank_account->id ? __('Edit bank account') . ' #' . $bank_account->id : __('New bank account')
    ])

    <form action="{{ url('bank-account/save') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ $bank_account->id ?? '' }}">

        <div class="card mt-2">
            <div class="card-body">

                {{-- Type + Name + Country + Currency --}}
                <div class="row">
                    <div class="col-12 col-md-3">
                        <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" required class="form-select form-select-lg">
                            @foreach(\App\Models\Bank\Account::TYPES as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $bank_account->type ?? 'bank') === $value)>
                                    {{ __($label) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="account_name">{{ __('Account name') }}</label>
                        <input type="text" name="account_name" id="account_name" maxlength="80"
                               value="{{ old('account_name', $bank_account->account_name) }}"
                               placeholder="{{ __('e.g. Main account') }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-3">
                        @include('components.country', ['country_id' => old('country_id', $bank_account->country_id)])
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="currency">{{ __('Currency') }} <span class="text-danger">*</span></label>
                        <input type="text" name="currency" id="currency" maxlength="3"
                               value="{{ old('currency', $bank_account->currency) }}"
                               placeholder="EUR" required class="form-control form-control-lg text-uppercase">
                    </div>
                </div>

                {{-- Bank selector + SWIFT (always for type=bank) --}}
                <div id="row-bank">
                    <div class="row mt-3">
                        <div class="col-12 col-md-8">
                            <label for="bank_id">{{ __('Bank') }}</label>
                            <input type="text" id="bank_search" placeholder="{{ __('Search bank...') }}"
                                   class="form-control form-control-lg mb-1" autocomplete="off">
                            <select name="bank_id" id="bank_id" class="form-select form-select-lg">
                                <option value="">{{ __('Choose') }}</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" @selected(old('bank_id', $bank_account->bank_id) == $bank->id)>
                                        {{ $bank->name }}@if($bank->country) ({{ $bank->country->flag }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="swift">SWIFT</label>
                            <input type="text" name="swift" id="swift" maxlength="11"
                                   value="{{ old('swift', $bank_account->swift) }}"
                                   placeholder="BSCHESMMXXX"
                                   class="form-control form-control-lg text-uppercase">
                        </div>
                    </div>

                    {{-- IBAN (Europe / default) --}}
                    <div class="row mt-3" id="row-iban">
                        <div class="col-12">
                            <label for="iban">{{ __('IBAN') }}</label>
                            <input type="text" name="iban" id="iban" maxlength="34"
                                   value="{{ old('iban', $bank_account->iban) }}"
                                   placeholder="ESXX XXXX XXXX XXXX XXXX XXXX"
                                   class="form-control form-control-lg font-monospace">
                        </div>
                    </div>

                    {{-- Sort Code + Account number (UK) --}}
                    <div class="row mt-3" id="row-sortcode">
                        <div class="col-12 col-md-4">
                            <label for="sort_code">Sort Code</label>
                            <input type="text" name="sort_code" id="sort_code" maxlength="8"
                                   value="{{ old('sort_code', $bank_account->sort_code) }}"
                                   placeholder="20-00-00"
                                   class="form-control form-control-lg font-monospace">
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="account_number">{{ __('Account number') }}</label>
                            <input type="text" name="account_number" id="account_number" maxlength="30"
                                   value="{{ old('account_number', $bank_account->account_number) }}"
                                   class="form-control form-control-lg font-monospace">
                        </div>
                    </div>

                    {{-- Bizum (Spain) --}}
                    <div class="row mt-3" id="row-bizum">
                        <div class="col-12 col-md-4">
                            <label for="bizum">Bizum <span class="text-muted small">({{ __('mobile') }})</span></label>
                            <input type="tel" name="bizum" id="bizum" maxlength="15"
                                   value="{{ old('bizum', $bank_account->bizum) }}"
                                   placeholder="+34 600 000 000"
                                   class="form-control form-control-lg">
                        </div>
                    </div>

                    {{-- CBU + Alias (Argentina) --}}
                    <div class="row mt-3" id="row-cbu">
                        <div class="col-12 col-md-6">
                            <label for="cbu">CBU <span class="text-muted small">(22 {{ __('digits') }})</span></label>
                            <input type="text" name="cbu" id="cbu" maxlength="22"
                                   value="{{ old('cbu', $bank_account->cbu) }}"
                                   placeholder="0110048200000004823002"
                                   inputmode="numeric"
                                   class="form-control form-control-lg font-monospace">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="alias">
                                Alias <span class="text-muted small">({{ __('no spaces') }})</span>
                            </label>
                            <input type="text" name="alias" id="alias" maxlength="60"
                                   value="{{ old('alias', $bank_account->alias) }}"
                                   placeholder="MI.CUENTA.BANCO"
                                   pattern="\S+"
                                   title="{{ __('No spaces allowed') }}"
                                   class="form-control form-control-lg">
                        </div>
                    </div>
                </div>

                {{-- Account ID / Email (digital providers) --}}
                <div class="row mt-3" id="row-provider">
                    <div class="col-12 col-md-6">
                        <label for="account_number_provider">{{ __('Account ID / Email') }}</label>
                        <input type="text" name="account_number" id="account_number_provider" maxlength="30"
                               value="{{ old('account_number', $bank_account->account_number) }}"
                               placeholder="acct_1A2B3C4D5E / user@email.com"
                               class="form-control form-control-lg">
                    </div>
                </div>

                {{-- Balance + Notes (always visible) --}}
                <div class="row mt-3">
                    <div class="col-12 col-md-3">
                        <label for="opening_balance">{{ __('Opening balance') }}</label>
                        <div class="input-group">
                            <input type="number" name="opening_balance" id="opening_balance" step="0.01"
                                   value="{{ old('opening_balance', $bank_account->opening_balance ?? 0) }}"
                                   class="form-control form-control-lg">
                            <span class="input-group-text" id="currency-suffix">
                                {{ old('currency', $bank_account->currency ?? '€') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-9">
                        <label for="notes">{{ __('Notes') }}</label>
                        <input type="text" name="notes" id="notes" maxlength="255"
                               value="{{ old('notes', $bank_account->notes) }}"
                               class="form-control form-control-lg">
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
            <a href="{{ url('/bank-account') }}" class="btn btn-secondary btn-lg">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary btn-lg">{{ __('Save') }}</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
const COUNTRY_UK = 'gb';
const COUNTRY_AR = 'ar';
const COUNTRY_ES = 'es';

function updateForm() {
    const type    = document.getElementById('type').value;
    const country = document.getElementById('country_id').value.toLowerCase();
    const isBank  = type === 'bank';
    const isUK    = isBank && country === COUNTRY_UK;
    const isAR    = isBank && country === COUNTRY_AR;
    const isES    = isBank && country === COUNTRY_ES;

    setVisible('row-bank',     isBank);
    setVisible('row-iban',     isBank && !isUK && !isAR);
    setVisible('row-sortcode', isUK);
    setVisible('row-cbu',      isAR);
    setVisible('row-bizum',    isES);
    setVisible('row-provider', !isBank);

    document.getElementById('currency-suffix').textContent =
        document.getElementById('currency').value || '€';
}

function setVisible(id, visible) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = visible ? '' : 'none';
    el.querySelectorAll('input, select').forEach(input => {
        input.disabled = !visible;
    });
}

document.getElementById('type').addEventListener('change', updateForm);
document.getElementById('country_id').addEventListener('input', updateForm);
document.getElementById('currency').addEventListener('input', () => {
    document.getElementById('currency-suffix').textContent =
        document.getElementById('currency').value || '€';
});

document.getElementById('bank_search').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    Array.from(document.getElementById('bank_id').options).forEach(opt => {
        opt.hidden = opt.value !== '' && !opt.text.toLowerCase().includes(search);
    });
});

updateForm();
</script>
@endpush
