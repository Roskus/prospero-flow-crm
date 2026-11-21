@if($state === \App\Models\Asset::EXPIRED)
    <span class="badge text-bg-danger">{{ $label ?? __('Expired') }}</span>
@elseif($state === \App\Models\Asset::EXPIRING)
    <span class="badge text-bg-warning">{{ $label ?? __('Expiring soon') }}</span>
@endif
