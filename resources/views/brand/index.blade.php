@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Brands') }}</h1>
    </header>

    <div>
        <a href="/brand/add" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="mt-2">
        <table class="table table-bordered table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($brands as $brand)
        <tr>
            <td>
                <a href="/brand/edit/{{ $brand->id }}">{{ $brand->name }}</a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
