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

    function initializeMap(latitude, longitude) {
        // Verifica si hay coordenadas disponibles y establece el centro del mapa y el marcador si es así
        if (latitude && longitude) {
            // Establece la vista del mapa en la posición inicial y un nivel de zoom determinado
            map.setView([latitude, longitude], 18);

            // Agrega capas de azulejos al mapa para mostrar el mapa base
            var tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Agrega un marcador a la posición inicial del mapa
            var marker = L.marker([latitude, longitude]).addTo(map);
        }
    }


    // Manage success get current user position
    function showPosition(position) {
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;

        // Llama a la función para mostrar el mapa y establecer la posición del marcador
        initializeMap(latitude, longitude);
    }

    // Función para manejar el caso de error al obtener la ubicación
    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                console.log("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                console.log("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                console.log("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                console.log("An unknown error occurred.");
                break;
        }
    }


    // Get current user location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }

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
                    if (map) {
                        setMarker(latitude, longitude);
                    }

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
    } else {
        getLocation();
    }
</script>
@endpush
