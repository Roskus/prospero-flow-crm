<label for="bank_id">{{ __('Bank') }}</label>
<select name="bank_id" id="bank_id" class="form-select {{ $class ?? '' }}" data-tom-select data-bank-country="{{ strtolower((string) ($country_id ?? '')) }}" placeholder="{{ __('Search bank...') }}" @if(!empty($required)) required="required" @endif>
    <option value=""></option>
    @foreach ($banks as $bank)
        <option value="{{ $bank->id }}" data-bic="{{ $bank->bic }}" data-country="{{ $bank->country_id }}" @selected((string) ($bank_id ?? '') === (string) $bank->id)>{{ $bank->country?->flag }} {{ $bank->name }}</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
    @once
    <script>
        /**
         * Show only the banks of the given country (all of them without country), keeping the selected bank.
         */
        function filterBanksByCountry(select, countryCode) {
            const tomSelect = select.tomselect;
            const country = (countryCode || '').toLowerCase();
            tomSelect.allBankOptions ??= Object.values(tomSelect.options);
            tomSelect.clearOptions();
            tomSelect.addOptions(tomSelect.allBankOptions.filter(option => country === '' || option.country === country));
            tomSelect.refreshOptions(false);
        }

        document.querySelectorAll('select[data-bank-country]').forEach(select => filterBanksByCountry(select, select.dataset.bankCountry));
    </script>
    @endonce
@endpush
