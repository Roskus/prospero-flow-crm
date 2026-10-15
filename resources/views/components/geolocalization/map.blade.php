<div id="map" class="h-100 rounded border" style="min-height: 340px"></div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="anonymous">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="anonymous"></script>
<script>
    "use strict";

    var latitude = "{{ $attributes['latitude'] }}";
    var longitude = "{{ $attributes['longitude'] }}";
    var map = L.map("map");
    var marker;
    var tiles;

    function initializeMap(latitude, longitude) {
        setMarker(latitude, longitude);
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

    function addTiles() {
        if (tiles) {
            return;
        }

        tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    }

    function setMarker(latitude, longitude) {
        if (!latitude || !longitude) {
            return;
        }

        map.setView([latitude, longitude], 18);
        addTiles();

        if (!marker || !map.hasLayer(marker)) {
            marker = L.marker([latitude, longitude]).addTo(map);
        } else {
            marker.setLatLng([latitude, longitude]);
        }
    }

    function setAddressFeedback(message) {
        const addressInput = document.getElementById('search-address');
        const feedback = document.getElementById('search-address-feedback');

        addressInput.classList.toggle('is-invalid', message !== null);
        feedback.textContent = message ?? '';
    }

    function getCoordinates() {
        const addressInput = document.getElementById('search-address');
        const searchButton = document.getElementById('search-address-button');
        const address = addressInput.value.trim();

        if (address === '') {
            setAddressFeedback(@json(__('Enter an address to search')));
            return;
        }

        const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`;

        setAddressFeedback(null);
        searchButton.disabled = true;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            })
            .then(data => {
                if (data.length === 0) {
                    setAddressFeedback(@json(__('Address not found')));
                    return;
                }

                const latitude = Number.parseFloat(data[0].lat);
                const longitude = Number.parseFloat(data[0].lon);
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;

                setMarker(latitude, longitude);
            })
            .catch(error => {
                console.error(error);
                setAddressFeedback(@json(__('An error occurred while searching for the address')));
            })
            .finally(() => {
                searchButton.disabled = false;
            });
    }

    document.getElementById('search-address')?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            getCoordinates();
        }
    });

    document.getElementById('search-address')?.addEventListener('input', function () {
        setAddressFeedback(null);
    });

    if (latitude && longitude) {
        setMarker(latitude, longitude);
    } else {
        getLocation();
    }
</script>
@endpush
