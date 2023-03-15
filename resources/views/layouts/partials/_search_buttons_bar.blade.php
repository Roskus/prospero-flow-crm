<div class="row">
    <div class="col">
        <form method="GET" action="{{ $action_search }}" class="form-inline mb-2">
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit" id="btn-search">
                    <i class="las la-search"></i>
                </button>
            </div>
        </form>
    </div>
    @foreach ($buttons as $button)
    <div class="col-2">
        <a href="{{ $button['url'] }}" class="{{ $button['class'] }} w-100">
            {{ $button['text'] }} <i class="{{ $button['icon_class'] ?? '' }}"></i>
        </a>
    </div>
    @endforeach
</div>
