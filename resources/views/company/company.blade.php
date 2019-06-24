@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Company') }}</h1>
</header>

<form method="post" action="/company/save" class="form">
    <div class="form-group">
        <label class="label-control">{{ trans('hammer.Name') }}</label>
        <input type="text" name="name" id="name" value="{{ $company->name }}" class="form-control" required="required">
    </div>
    <div class="row">
        <div class="col form-group">
            <label class="label-control">E-mail</label>
            <input type="email" name="email" id="email" value="{{ $company->email }}" class="form-control">
        </div>

        <div class="col form-group">
            <label class="label-control">{{ trans('hammer.Website') }}</label>
            <input type="url" name="website" id="website" value="{{ $company->website }}" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ trans('hammer.Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $company->id }}">
    {{ csrf_field() }}
</form>

@endsection
