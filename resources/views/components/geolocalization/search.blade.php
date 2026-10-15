<label for="search-address">{{ __('Search for an address') }}</label>
<div class="input-group has-validation">
    <input type="text" id="search-address" placeholder="{{ __('Search for an address') }}" class="form-control form-control-lg">
    <button type="button" id="search-address-button" onclick="getCoordinates()" class="btn btn-secondary" title="{{ __('Search') }}">
        <i class="las la-search"></i>
    </button>
    <div id="search-address-feedback" class="invalid-feedback"></div>
</div>
