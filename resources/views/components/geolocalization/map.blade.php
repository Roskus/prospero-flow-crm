<div id="map" class="h-100 rounded border" style="min-height: 340px"></div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.js" integrity="sha512-Io0KK/1GsMMQ8Vpa7kIJjgvOcDSwIqYuigJEYxrrObhsV4j+VTOQvxImACNJT5r9O4n+u9/58h7WjSnT5eC4hA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    "use strict";

    var latitude = "{{ $attributes['latitude'] }}";
    var longitude = "{{ $attributes['longitude'] }}";

    if (latitude && longitude) {
        var map = L.map("map").setView([latitude, longitude], 18);

        var tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 20
            , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([latitude, longitude]).addTo(map);
    } else {
        document.getElementById('map').innerHTML = "<p class='p-3'>{{ __('It is not possible to show the map of the location because the geolocation is not established.') }}</p>";
    }

    function getCoordinates() {
        const addressInput = document.getElementById('search-address');
        const address = addressInput.value;

        if (address.trim() === '') {
            return;
        }

        const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const latitude = parseFloat(data[0].lat);
                    const longitude = parseFloat(data[0].lon);
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;

                    // Actualizar el mapa con las nuevas coordenadas
                    map.setView([latitude, longitude], 18);
                    marker.setLatLng([latitude, longitude]);

                    console.log(`Coordenadas de la dirección '${address}': Latitud ${latitude}, Longitud ${longitude}`);
                } else {
                    console.log('No se pudieron obtener las coordenadas para la dirección especificada.');
                }
            })
            .catch(error => {
                console.error('Error al obtener las coordenadas:', error);
            });
    }
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css" integrity="sha512-KJRB1wUfcipHY35z9dEE+Jqd+pGCuQ2JMZmQPAjwPjXuzz9oL1pZm2cd79vyUgHQxvb9sFQ6f05DIz0IqcG1Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
