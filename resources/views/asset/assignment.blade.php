@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Edit assignment').' · '.$asset->code])

    <form method="post" action="{{ url('/inventory/assignment/save') }}" class="form">
        @csrf
        <input type="hidden" name="id" value="{{ $assignment->id }}">

        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group mb-2">
                        <label for="user_id" class="label-control">{{ __('User') }} <span class="text-danger">*</span></label>
                        @php($selectedUser = (int) old('user_id', $assignment->user_id))
                        <select name="user_id" id="user_id" required class="form-select @error('user_id') is-invalid @enderror">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected($selectedUser === $user->id)>{{ $user->getFullName() }}</option>
                            @endforeach
                            @if($assignment->user && ! $users->contains('id', $assignment->user_id))
                                <option value="{{ $assignment->user_id }}" @selected($selectedUser === $assignment->user_id)>{{ $assignment->user->getFullName() }} ({{ __('inactive') }})</option>
                            @endif
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('asset._field', ['name' => 'start_date', 'label' => __('Start date'), 'type' => 'date', 'value' => $assignment->start_date->toDateString(), 'required' => true, 'col' => 'col-md-4'])
                    @if($assignment->isOpen())
                        <div class="col-md-4 form-group mb-2">
                            <label class="label-control">{{ __('Return date') }}</label>
                            <p class="form-control-plaintext text-muted">{{ __('Still assigned. Return the asset to close it.') }}</p>
                        </div>
                    @else
                        @include('asset._field', ['name' => 'end_date', 'label' => __('Return date'), 'type' => 'date', 'value' => $assignment->end_date?->toDateString(), 'required' => true, 'col' => 'col-md-4'])
                    @endif
                </div>
                <div class="form-group">
                    <label for="notes" class="label-control">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="3" maxlength="2000" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $assignment->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('asset.show', $asset->id) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
