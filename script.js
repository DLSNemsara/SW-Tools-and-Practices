document.addEventListener('DOMContentLoaded', function () {
    initMap();
});

async function initMap() {
    const response = await fetch('fetch_locations.php'); // Assuming your server endpoint is 'fetch_locations.php'
    const markers = await response.json();

    const map = L.map('map').setView([6.8235313, 80.0367716], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const bounds = [];

    // Loop through all markers
    markers.forEach(markerInfo => {
        const marker = L.marker([parseFloat(markerInfo.lat), parseFloat(markerInfo.lng)]).addTo(map);
        bounds.push([parseFloat(markerInfo.lat), parseFloat(markerInfo.lng)]);

        marker.bindPopup(`
            <div class="feh-content">
                <h3>${markerInfo.locationName}</h3>
                <address>
                    <p>${markerInfo.address}</p>
                </address>
            </div>
        `);
    });

    map.fitBounds(bounds);
}
