@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Brands') }}</h1>
    </header>

    <div class="mb-2">
        <a href="{{ url('/brand/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($brands as $brand)
        <tr>
            <td>
                <a href="{{ url('/brand/update/'.$brand->id) }}">{{ $brand->name }}</a>
            </td>
            <td>
                <a href="{{ url('/brand/update/'.$brand->id) }}" class="btn btn-xs btn-warning text-white">
                    <i class="las la-pen"></i>
                </a>
                <a onclick="Brand.delete({{ $brand->id }}, '{{ $brand->name }}');" class="btn btn-xs btn-danger">
                    <i class="las la-trash-alt"></i>
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
