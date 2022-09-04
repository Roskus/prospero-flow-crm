@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Supplier') }}</h1>
    </header>
    <form method="post" action="{{ url('/supplier/save') }}">
        @csrf
        <div class="row">
            <div class="col">
                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ $supplier->name }}" required="required" class="form-control">
            </div>
            <div class="col">
                <label>{{ __('Phone') }}</label>
                <input type="tel" name="phone" value="{{ $supplier->phone }}" maxlength="15" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>E-mail</label>
                <input type="email" name="email" value="{{ $supplier->email }}" maxlength="254" class="form-control">
            </div>
            <div class="col">
                <label>{{ __('Website') }}</label>
                <input type="url" name="website" value="{{ $supplier->website }}" maxlength="255" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ $supplier->id }}">
    </form>
@endsection
