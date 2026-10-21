{{--
    Full address built by App\ValueObjects\Address, skipping empty parts. Plain HTML so it also renders in PDFs.
    Params: address (App\ValueObjects\Address), format ('inline' or 'lines').
--}}
@if(! $address->isEmpty())
    @if(($format ?? 'inline') === 'lines')
        @foreach($address->lines() as $line)
            <div>{{ $line }}</div>
        @endforeach
    @else
        {{ $address->oneLine() }}
    @endif
@endif
