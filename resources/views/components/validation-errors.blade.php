@props(['errors'])

@if ($errors->any())
    <div class="alert alert-danger mt-3" role="alert">
        Sorry, the validation is failed.
        <ul class="m-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif
