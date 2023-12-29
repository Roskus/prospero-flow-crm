<label for="country_id">{{ __('Country') }}</label>
<input name="country_id" id="country_id" value="{{ !empty($country_id) ? $country_id : null }}" list="country-list"
    class="form-control">
<datalist id="country-list">
    @foreach ($countries as $country)
        <option value="{{ $country->code_2 }}" @if(request()->query('country_id') == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
    @endforeach
</datalist>
