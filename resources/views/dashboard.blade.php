@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Dashboard') }}</h1>
    </header>
    <div>
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <h2>{{ __('Leads') }}</h2>
                    <h3>{{ $lead_count }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
