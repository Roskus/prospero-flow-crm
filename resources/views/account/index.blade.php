@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Accounting') }}</h1>
    </header>

    <div>
        <form id="accountCreate" method="post" action="/account/save" class="form-horizontal">
        <div class="row">
            @csrf
            <div class="col">
                <label>Account name</label>
                <input type="text" name="name" id="name" value="" required="required" class="form-control">
            </div>
            <div class="col">
                <label>{{ __('Ammount') }}</label>
                <input type="number" name="ammount" id="ammount" required="required" step="0.01" autocomplete="false" class="form-control">
            </div>
            <div class="col">
                <label for=""></label>
                <button type="submit" class="btn btn-primary">{{ __('New') }}</a>
            </div>
        </div>
        </form>
    </div>

    <div class="mt-2">
        <table class="table table-bordered table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Ammount') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($accounts as $account)
        <tr>
            <td>
                <a href="/account/edit/{{ $account->id }}">{{ $account->name }}</a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
