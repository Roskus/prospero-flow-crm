@extends('layouts.app')

@section('content')

    <header>
        <h1>{{ __('Customer import from Excel') }}
    </header>

    <div class="card">
        <div class="card-body">
            <div class="mt-2 mb-2">
                <a href="{{ asset('/asset/upload/example/pflow_customer_example_20230414.csv') }}" rel="noopener"
                   target="_blank" class="btn btn-outline-success">
                    {{ __('Download example file') }} <i class="las la-file-csv"></i>
                </a>
            </div>
            <form method="POST" action="{{ url('/customer/import/excel/save') }}" enctype="multipart/form-data" class="form">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="upload">{{ __('Excel File') }}</label>
                        <input type="file" name="upload" id="upload" accept=".xlsx,.xls" required class="form-control-file">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <a href="{{ url('/customer') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
