@php($size = $size ?? 48)
@if($asset->hasPhoto())
    <img src="{{ route('asset.photo', $asset->id) }}?v={{ $asset->photoVersion() }}" alt="{{ $asset->name }}" loading="lazy"
         class="rounded object-fit-cover border" style="width: {{ $size }}px; height: {{ $size }}px;">
@else
    <span class="d-inline-flex align-items-center justify-content-center rounded bg-light border text-secondary asset-photo-placeholder"
          style="width: {{ $size }}px; height: {{ $size }}px;" title="{{ __('No photo') }}">
        <i class="{{ $asset->type?->icon() ?? $asset->category->icon() }}" style="font-size: {{ (int) ($size / 2) }}px;"></i>
    </span>
@endif
