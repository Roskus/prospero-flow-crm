@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Customer import') }}
    </header>

    <div>
        <a href="/asset/upload/import/hammer_lead_example.csv" target="_blank" class="btn btn-outline-success">{{ __('Download example file') }} <i class="las la-file-csv"></i></a>
    </div>

    <form method="POST" action="{{ url('/customer/import/save') }}" enctype="multipart/form-data" class="form">
        @csrf
        <div class="row">
            <label for="upload">{{ __('File') }}</label>
            <input type="file" name="upload" accept="text/csv" required>
        </div>
        <div class="row mt-2">
            <div class="col">
                <a href="{{ url('/customer') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
    </form>
@endsection
