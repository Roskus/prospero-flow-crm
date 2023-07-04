<div id="map" class="h-100 rounded border" style="min-height: 240px"></div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.js" integrity="sha512-Io0KK/1GsMMQ8Vpa7kIJjgvOcDSwIqYuigJEYxrrObhsV4j+VTOQvxImACNJT5r9O4n+u9/58h7WjSnT5eC4hA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    "use strict";

    var latitude = "{{ $attributes['latitude'] }}";
    var longitude = "{{ $attributes['longitude'] }}";

    if (latitude && longitude) {
        var map = L.map("map").setView([latitude, longitude], 12);

        var tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19
            , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([latitude, longitude]).addTo(map);
    } else {
        document.getElementById('map').innerHTML = "<p class='p-3'>{{ __('It is not possible to show the map of the location because the geolocation is not established.') }}</p>";
    }
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
