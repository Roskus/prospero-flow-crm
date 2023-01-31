@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Ticket') }}</h1>
    </header>

    <form method="post" action="{{ url('/ticket/save') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col">
            <label for="title">{{ __('Title') }}</label>
            <input type="text" name="title" id="title" value="{{ $ticket->title }}" maxlength="80" required="required" class="form-control">
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="col">
            <label for="customer_id">{{ __('Customer') }}</label>
            <select name="customer_id" id="customer_id" class="form-select form-control-lg">
                <option value=""></option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div><!--./col-->
        <div class="col">
            <label for="assigned_to">{{ __('Assigned to') }}</label>
            <select name="assigned_to" id="assigned_to" required="required" class="form-select form-control-lg">
                <option value=""></option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if($ticket->assigned_to == $user->id) selected="selected" @endif>{{ $user->first_name.' '.$user->last_name }}</option>
                @endforeach
            </select>
        </div><!--./col-->
    </div><!--./row-->
    <div class="row">
        <div class="col">
            <label for="description">{{ __('Description') }}</label>
            <textarea name="description" id="description" required="required" class="form-control">{{ $ticket->description }}</textarea>
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="col">
            <label for="type">{{ __('Type') }}</label>
            <select name="type" id="type" class="form-select form-control-lg">
                <option value=""></option>
                @foreach($ticket->types() as $type)
                    <option value="{{ $type }}" @if(!empty($ticket->type) && $ticket->type == $type) selected="selected" @endif>{{ __($type) }}</option>
                @endforeach
            </select>
        </div><!--./col-->
        <div class="col">
            <label for="priority">{{ __('Priority') }}</label>
            <select name="priority" id="priority" class="form-select form-control-lg">
                <option value=""></option>
                @foreach($ticket->priorities() as $priority)
                    <option value="{{ $priority }}" @if(!empty($ticket->priority) && $ticket->priority == $priority) selected="selected" @endif>{{ __($priority) }}</option>
                @endforeach
            </select>
        </div><!--./col-->
    </div><!--./row-->
    <div class="row">
        <div class="col">
            <label for="attachment">{{ __('Attachments') }}</label>
            <input type="file" name="attachment[]" id="attachment">
        </div>
    </div>
    <div class="row">
        <div class="col mt-2">
            <a href="{{ url('/ticket') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div><!--./row-->
    <input type="hidden" name="id" value="{{ $ticket->id }}">
    </form>
@endsection
