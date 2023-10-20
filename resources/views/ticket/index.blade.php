@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Tickets')])

    <div class="row mb-3">
        <div class="col">
            <a href="{{ url('ticket/create') }}" class="btn btn-primary">{{ __('New') }}</a>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            <form action="{{ url("/ticket") }}" class="form-inline mb-2">
                <div class="input-group">
                    <input type="search" name="search" placeholder="{{ __('You can search by title, by description and by status.') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                    <button class="btn btn-outline-primary" type="submit" id="btn-search"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('ticket.grid', ['tickets' => $tickets])
            </div>
        </div>
    </div>
@endsection
