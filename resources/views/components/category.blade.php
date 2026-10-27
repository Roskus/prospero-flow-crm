<label for="category_id" class="label-control mb-2">
    {{ __('Category') }} @if(!empty($required))<span class="text-danger">*</span>@endif
    <a href="{{ url('/category') }}" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
</label>
<select name="category_id" id="category_id" class="form-select {{ $class ?? '' }}" data-tom-select placeholder="{{ __('Search') }}..." @if(!empty($required)) required="required" @endif>
    <option value=""></option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected((string) ($category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
@endpush
