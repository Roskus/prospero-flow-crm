@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Ticket') }}</h1>
    </header>

    <form method="post" action="{{ url('/ticket/save') }}">
    @csrf
    <div class="row">
        <div class="col">
            <label>{{ __('Title') }}</label>
            <input type="text" name="title" id="title" value="{{ $ticket->title }}" maxlength="80" required="required" class="form-control">
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="col">
            <label>{{ __('Description') }}</label>
            <textarea name="description" required="required" class="form-control">{{ $ticket->description }}</textarea>
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="col mt-2">
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div><!--./row-->
    <input type="hidden" name="id" value="{{ $ticket->id }}">
    </form>
@endsection
