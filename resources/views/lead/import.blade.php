@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Lead import') }}
    </header>

    <div class="row">
        <div class="col">
            <a href="{{ url('/asset/upload/example/pflow_lead_example_20221212.csv') }}" rel="noopener" target="_blank"
               class="btn btn-outline-success">{{ __('Download example file') }} <i class="las la-file-csv"></i></a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ url('/lead/import/save') }}" enctype="multipart/form-data" class="form">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="upload">{{ __('File') }}</label>
                        <input type="file" name="upload" id="upload" accept="text/csv" required>
                    </div>
                    <div class="col">
                        <label for="separator">{{ __('Separator') }}</label>
                        <input type="text" name="separator" id="separator" maxlength="1" value="" placeholder=";">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <a href="{{ url('/lead') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div><!--./card-body-->
    </div><!--./card-->
@endsection
