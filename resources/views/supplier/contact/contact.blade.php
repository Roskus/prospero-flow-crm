@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Contact')])

@include('supplier.contact.contact_form')

@endsection
