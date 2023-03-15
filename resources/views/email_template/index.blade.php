@extends('layouts.app')

@section('content')
@section('content')
@include('layouts.partials._header', ['title' => __('E-mail templates')])
@include('layouts.partials._search_buttons_bar', [
    'action_search' => url("/email-template"),
    'buttons' => [
        ['url' => url('/email-template/create'), 'class' => 'btn btn-primary', 'text' => __('New')]
    ]
])

<div class="card">
    <div class="card-body">
        @foreach($templates as $template)
        <div>
            <img src="{{ $template->screenshot }}" alt="">
        </div>
        @endforeach
    </div>
</div>
    
@endsection
