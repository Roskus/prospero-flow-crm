@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Categories') }}</h1>
    </header>

    <div>
        <a href="/category/add" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="mt-2">
        <table class="table table-bordered table-striped table-bordered">
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
                <a href="/category/edit/{{ $category->id }}">{{ $category->name }}</a>
            </td>
            <td>
                <a href="/category/edit/{{ $category->id }}" class="btn btn-xs btn-warning text-white">
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
@endsection
