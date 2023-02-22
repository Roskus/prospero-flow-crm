<label for="seller_id">{{ __('Seller') }}</label>
<input name="seller_id" id="seller_id" value="{{ !empty($seller_id) ? $seller_id : null }}" list="seller-list" class="form-control">
<datalist id="seller-list">
    @foreach($sellers as $seller)
        <option value="{{ $seller->id }}">
            {{ $seller->first_name . ' ' . $seller->last_name }}
        </option>
    @endforeach
</datalist>
