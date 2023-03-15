@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Categories') }}</h1>
    </header>

    <div>
        <a href=" {{ url('/category/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <table class="table table-bordered table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($categories as $category)
            <tr>
                <td>
                    <a href="{{ url('/category/update/'.$category->id) }}">{{ $category->name }}</a>
                </td>
                <td>
                    <a href="{{ url('/category/update/'.$category->id) }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    <a onclick="Category.delete({{ $category->id }}, '{{ $category->name }}');" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
@endsection
