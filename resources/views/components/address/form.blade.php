{{--
    Address fields, search and map of an entity using App\Traits\HasAddress.
    Params: address (App\ValueObjects\Address), required (country), class (input size, e.g. form-control-lg).
    The search and map use fixed element ids, so only one address form fits in a page.
--}}
@php
    $inputClass = trim('form-control '.($class ?? ''));
    $selectClass = str_replace('form-control', 'form-select', $class ?? '');
@endphp
<div class="row mb-1">
    <div class="col">
        @include('components.geolocalization.search')
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-6">
        @include('components.country', [
            'country_id' => old('country_id', $address->countryId),
            'class' => $selectClass,
            'required' => ! empty($required),
        ])
    </div>
    <div class="col-12 col-md-6">
        <label for="province">{{ __('Province') }}</label>
        <input type="text" name="province" id="province" value="{{ old('province', $address->province) }}"
               class="{{ $inputClass }}" maxlength="80">
    </div>
    <div class="col-12 col-md-6">
        <label for="city">{{ __('City') }}</label>
        <input type="text" name="city" id="city" value="{{ old('city', $address->city) }}"
               class="{{ $inputClass }}" maxlength="50">
    </div>
    <div class="col-12 col-md-6">
        <label for="locality">{{ __('Locality') }}</label>
        <input type="text" name="locality" id="locality" value="{{ old('locality', $address->locality) }}"
               class="{{ $inputClass }}" maxlength="80">
    </div>
    <div class="col-12 col-md-6">
        <label for="street">{{ __('Street') }}</label>
        <input type="text" name="street" id="street" value="{{ old('street', $address->street) }}"
               class="{{ $inputClass }}" maxlength="80">
    </div>
    <div class="col-12 col-md-6">
        <label for="address_extra">{{ __('Address extra') }}</label>
        <input type="text" name="address_extra" id="address_extra" value="{{ old('address_extra', $address->extra) }}"
               placeholder="{{ __('Floor, office, suite...') }}" class="{{ $inputClass }}" maxlength="80">
    </div>
    <div class="col-12 col-md-6">
        <label for="zipcode">{{ __('Zipcode') }}</label>
        <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $address->zipcode) }}"
               class="{{ $inputClass }}" maxlength="10">
    </div>
</div>
<div class="row">
    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}">
    <div class="col mt-3">
        <x-geolocalization.map :latitude="$address->latitude" :longitude="$address->longitude" />
    </div>
</div>
