@php
    $selectedCountry = strtolower((string) ($country_id ?? request('country_id')));
@endphp
<label for="country_id">{{ __('Country') }}</label>
<select name="country_id" id="country_id" class="form-select {{ $class ?? '' }}" data-tom-select placeholder="{{ __('Search') }}..." @if(!empty($required)) required="required" @endif>
    <option value=""></option>
    @foreach ($countries as $country)
        <option value="{{ $country->code_2 }}" @if($country->code_2 === $selectedCountry) selected="selected" @endif>{{ $country->flag }} {{ $country->name }}</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
@endpush
