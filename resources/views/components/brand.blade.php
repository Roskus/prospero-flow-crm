<label for="brand_id" class="label-control mb-2">
    {{ __('Brand') }} @if(!empty($required))<span class="text-danger">*</span>@endif
    <a href="{{ url('/brand') }}" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
</label>
<select name="brand_id" id="brand_id" class="form-select {{ $class ?? '' }}" data-tom-select placeholder="{{ __('Search') }}..." @if(!empty($required)) required="required" @endif>
    <option value=""></option>
    @foreach ($brands as $brand)
        <option value="{{ $brand->id }}" @selected((string) ($brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
@endpush
