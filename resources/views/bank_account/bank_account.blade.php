@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Bank')])
    <div class="card">
        <div class="card-body">
            <form action="{{ url('bank/account/save') }}" method="post">
            @csrf
            <div class="row">
                <div class="col">
                    @include('components.country', ['country_id' => $bank_account->country_id])
                </div>
                <div class="col">
                    <label for="bank_id" class="">{{ __('Bank') }}</label>
                    <select name="bank_id" id="bank_id"  required class="form-select">
                        <option value="">{{ __('Choose') }}</option>
                        @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="iban" class="">{{ __('IBAN') }}</label>
                    <input type="text" name="iban" id="iban" placeholder="ESXX XXXX XXXX XXXX XXXX XXXX" value="{{ $bank_account->iban }}" required maxlength="34" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $bank_account->id ?? null }}">
            </form>
        </div>
    </div>
@endsection
