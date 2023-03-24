@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Contact')])

@include('contact.contact_form')

@endsection
