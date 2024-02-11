@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Payrolls')])
    <div class="card">
        <div class="card-body">
            <form method="post" action="" class="form form-inline">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="year" class="">{{ __('Year') }}</label>
                        <select name="year" id="year" class="form-control">
                            <option></option>
                            @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('Go') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table class="table table-striped table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
