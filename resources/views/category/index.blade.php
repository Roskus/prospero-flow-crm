@extends('layout.app')

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
            </tr>
        </thead>
        <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>
                <a href="/category/edit/{{ $category->id }}">{{ $category->name }}</a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
