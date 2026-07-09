@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('New Time Off Request')])

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ url('/rrhh/time-off/save') }}" enctype="multipart/form-data" class="form">
            @csrf
            <div class="alert alert-info">
                <div><strong>{{ __('Vacation') }}:</strong> {{ $available_vacation }} / {{ $annual_vacation }} {{ __('days available') }}</div>
                <div><strong>{{ __('Personal') }}:</strong> {{ $available_personal }} / {{ $annual_personal }} {{ __('days available') }}</div>
            </div>

            <div class="row form-group mb-2">
                <div class="col-md-4">
                    <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select name="type" id="type" required class="form-select">
                        <option value="vacation">{{ __('Vacation') }}</option>
                        <option value="personal">{{ __('Personal') }}</option>
                        <option value="sick">{{ __('Sick leave') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="start_date">{{ __('Start date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="end_date">{{ __('End date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="form-control">
                </div>
            </div>
            <div class="row form-group mb-2">
                <div class="col">
                    <label for="reason">{{ __('Reason') }}</label>
                    <textarea name="reason" id="reason" rows="3" class="form-control">{{ old('reason') }}</textarea>
                </div>
            </div>
            <div class="row form-group mb-2" id="attachmentField">
                <div class="col">
                    <label for="attachment">{{ __('Attachment') }}</label>
                    <input type="file" name="attachment" id="attachment" accept="application/pdf,image/jpeg,image/png" class="form-control">
                    <small class="text-muted">{{ __('PDF, JPG or PNG. Max 5MB.') }} <span id="attachmentNote" class="text-info">{{ __('Recommended for sick leave') }}</span></small>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <a href="{{ url('/rrhh/time-off') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('type').addEventListener('change', function() {
    document.getElementById('attachmentField').style.display = this.value === 'sick' ? '' : 'none';
});
</script>
@endpush
