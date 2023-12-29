<div id="map" class="h-100 rounded border" style="min-height: 340px"></div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    "use strict";

    var latitude = "{{ $attributes['latitude'] }}";
    var longitude = "{{ $attributes['longitude'] }}";
    var map = L.map("map");

    function setMarker(latitude, longitude) {
        if (!latitude || !longitude) {
            document.getElementById('map').innerHTML = "<p class='p-3'>{{ __('It is not possible to show the map of the location because the geolocation is not established.') }}</p>";
            return; // No hagas nada si no hay coordenadas válidas
        }

        if (latitude && longitude) {

            if (!map.hasLayer(marker)) {
                // Si el marcador aún no está agregado al mapa, agrégalo
                marker = L.marker([latitude, longitude]).addTo(map);
            } else {
                // Si el marcador ya está en el mapa, actualiza su posición
                marker.setLatLng([latitude, longitude]);
            }

            // Centrar el mapa en la nueva posición del marcador
            map.setView([latitude, longitude], 18);

            /*
            var tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 20
                , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);*/
        }
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
                    setMarker(latitude, longitude);

                    console.log(`Coordenadas de la dirección '${address}': Latitud ${latitude}, Longitud ${longitude}`);
                } else {
                    console.log('No se pudieron obtener las coordenadas para la dirección especificada.');
                }
            })
            .catch(error => {
                console.error('Error al obtener las coordenadas:', error);
            });
    }

    if (latitude && longitude) {
        map.setView([latitude, longitude], 18);

        var tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([latitude, longitude]).addTo(map);
    }
</script>
@endpush
