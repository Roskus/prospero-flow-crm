{!! $body !!}

@isset($signature)
    {!! $signature !!}
@endisset

@isset($tracking_url)
    <img src="{{ $tracking_url }}" width="1" height="1" alt="" style="display:block;border:0;width:1px;height:1px;">
@endisset
