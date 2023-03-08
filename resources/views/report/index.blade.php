@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Reports')])
    <div class="row">
        <div class="col">
            <div class="card">
                <card-body>
                    <a class="btn btn-primary m-3" href="/report/sale">{{ __('Sales report') }}</a>
                </card-body>
            </div>            
        </div>
    </div>
@endsection
