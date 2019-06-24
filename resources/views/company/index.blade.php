@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Companies') }}</h1>
</header>

<table class="table table-bordered">
<thead>
<tr>
    <th>{{ __('Name') }}</th>
</tr>
</thead>
<tbody>
@foreach($companies as $company)
<tr>
    <td>
        <a href="/company/edit/{{ $company->id }}">
        {{ $company->name }}
        </a>
    </td>
</tr>
@endforeach
</tbody>
</table>
@endsection
