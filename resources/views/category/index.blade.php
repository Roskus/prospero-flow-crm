@extends('layout.app')

@section('content')
    <header>
        <h1>{{ __('Categories') }}</h1>
    </header>

    <div>
        <a href="/category/add" class="btn btn-primary">{{ __('hammer.New') }}</a>
    </div>

    <table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('hammer.Name') }}</th>
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
@endsection
