@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Reports')])
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <a href="{{ url('/report/sale') }}">{{ __('Sales report') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
