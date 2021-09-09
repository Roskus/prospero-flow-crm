@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Accounting') }}</h1>
    </header>

    <div>
        <form id="accountCreate" method="post" action="/account/save" class="form-horizontal">
        <div class="row mb-3">
            @csrf
            <div class="col">
                <label>{{ __('Issue date')}} <span class="text-danger">*</span></label>
                <input type="date" name="issue_date" id="issue_date" value="{{ date('Y-m-d') }}" class="form-control form-control-lg" required="required">
            </div>
            <div class="col">
                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="" required="required" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Amount') }}  <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="amount" id="amount" required="required" step="0.01" autocomplete="false" class="form-control form-control-lg">
                    <span class="input-group-text">€</span>
                </div>
            </div>
            <div class="col">
                <label for="">&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-lg form-control">{{ __('Save') }}</button>
            </div>
        </div>
        </form>
    </div>

    <div class="mt-2">
        <table class="table table-bordered table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Issue date') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($accounts as $account)
        <tr>
            <td>{{ $account->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $account->issue_date->format('d/m/Y H:i') }}</td>
            <td>
                <a href="/account/edit/{{ $account->id }}">{{ $account->name }}</a>
            </td>
            <td>{{ $account->amount }}</td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
