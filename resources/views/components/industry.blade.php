@php
    $selectedIndustry = (string) ($industry_id ?? request('industry_id'));
@endphp
<label for="industry_id">{{ __('Industry') }}</label>
<select name="industry_id" id="industry_id" class="form-select {{ $class ?? '' }}" data-tom-select placeholder="{{ __('Search') }}...">
    <option value=""></option>
    @foreach ($industries as $industry)
        <option value="{{ $industry->id }}" @if((string) $industry->id === $selectedIndustry) selected="selected" @endif>{{ __('industry.' . $industry->name) }}</option>
    @endforeach
</select>
@push('scripts')
    @include('components.tom_select')
@endpush
