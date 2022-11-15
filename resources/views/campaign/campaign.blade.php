@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Campaign') }}</h1>
    </header>

    <form method="post" action="{{ url('campaign/save') }}">
    @csrf
    <input type="hidden" name="id" value="{{ (!empty($campaign->id)) ? $campaign->id : '' }}">
    <div class="row">
        <div class="col">
            <label for="subject">{{ __('Subject') }}</label>
            <input type="text" name="subject" id="subject" value="{{ $campaign->subject }}" class="form-control form-control-lg">
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="col mt-2">
            <a href="{{ url('campaign') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div><!--./row-->
    </form>
@endsection
