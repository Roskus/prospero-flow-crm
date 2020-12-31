@extends('layout.app')

@section('content')
    <form method="POST" action="/customer/save" class="form">
        @csrf
        <div class="row">
            <div class="col">

            </div>
            <div class="col">

            </div>
        </div>
        <div class="row">
            <div class="col">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ (!empty($customer)) ? $customer->id : '' }}">
    </form>
@endsection