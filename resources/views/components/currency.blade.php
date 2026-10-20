@php
    $selectedCurrency = strtoupper((string) ($currency ?? ''));
@endphp
<label for="currency">
    {{ __('Currency') }}
    @if(!empty($required))
        <span class="text-danger">*</span>
    @endif
</label>
<select name="currency" id="currency" class="form-select {{ $class ?? '' }}" data-tom-select placeholder="{{ __('Search') }}..." @if(!empty($required)) required="required" @endif>
    <option value=""></option>
    @foreach ($currencies as $currencyOption)
        <option value="{{ strtoupper($currencyOption->id) }}" @selected(strtoupper($currencyOption->id) === $selectedCurrency)>{{ strtoupper($currencyOption->id) }} - {{ $currencyOption->name }} ({{ $currencyOption->symbol }})</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
@endpush
