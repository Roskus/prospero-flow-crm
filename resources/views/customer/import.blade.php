@extends('layouts.app')

@section('content')

    <header>
        <h1>{{ __('Customer import') }}
    </header>


    <div class="card">
        <div class="card-body">
        <div class="mt-2 mb-2">
            <a href="{{ asset('/asset/upload/example/pflow_customer_example_20230414.csv') }}" rel="noopener"
               target="_blank" class="btn btn-outline-success">
                {{ __('Download example file') }} <i class="las la-file-csv"></i>
            </a>
        </div>
        <form method="POST" action="{{ url('/customer/import/save') }}" enctype="multipart/form-data" class="form">
            @csrf
            <div class="row">
                <div class="col">
                    <label for="upload">{{ __('File') }}</label>
                    <input type="file" name="upload" id="upload" accept="text/csv" required class="form-control-file">
                </div>
                <div class="col">
                    <label for="separator">{{ __('Separator') }}</label>
                    <input type="text" name="separator" id="separator" maxlength="1" value="" placeholder=";">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <a href="{{ url('/customer') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
        </div>
    </div>
@endsection
