{{-- Generic form input: $name, $label, optional $type, $value, $required, $col, $attributes --}}
<div class="{{ $col ?? 'col-md-4' }} form-group mb-2">
    <label for="{{ $name }}" class="label-control">{{ $label }} @if(!empty($required))<span class="text-danger">*</span>@endif</label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value ?? null) }}" {!! $attributes ?? '' !!}
           @if(!empty($required)) required @endif
           class="form-control @error($name) is-invalid @enderror">
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
