<div class="card mt-2">
    <div class="card-header">{{ __('Advanced search') }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 col-md-2">
                @include('components.seller', ['seller_id' => !empty($seller_id) ? $seller_id : null])
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="status">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value=""></option>
                    @foreach($statuses as $key => $status)
                    <option value="{{ $key }}">{{ __($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="province">{{ __('Province') }}</label>
                <input type="text" class="form-control" id="province" name="province" value="{{ request()->query('province') }}">
            </div>
            <div class="col-sm-6 col-md-2">
                @include('components.country')
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="industry_id">{{ __('Industry') }}</label>
                <select name="industry_id" id="industry_id" class="form-select">
                    <option value=""></option>
                    @foreach($industries as $industry)
                    <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="phone">{{ __('Phone') }}</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ request()->query('phone') }}">
            </div>
        </div>
        <!--./row-->
    </div>
</div>
