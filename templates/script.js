function initMap() {
    const markers = [
        {
            locationName: 'NSBM Green University',
            lat: 6.821456927850701,
            lng: 80.04243120284237,
            address: 'Pitipana - Thalagala Rd,<br> Homagama'
        },
        {
            locationName: 'Bodima (බෝඩිම)',
            lat: 6.82278853698427, 
            lng: 80.03850444904397,
            address: 'R2FQ+363'
        },
        {
            locationName: 'Green Village Hostel',
            lat: 6.826814038982563, 
            lng: 80.03407649309094,
            address: 'No. 63/4, <br> Homagama'
        },
    ];

    const centerMap = {lat: 6.8235313, lng: 80.0367716}

    const mapOptions = {
        center: centerMap,
        zoom: 10,
        disableDefaultUI: true
    }

    const map = new google.maps.Map(document.getElementById('google-map'), mapOptions);

    const infoWindow = new google.maps.InfoWindow({
        minWidth: 200,
        maxWidth: 200
    });

    const bounds = new google.maps.LatLngBounds();

    // Loop through all markers
    for (let i = 0; i < markers.length; i++) {
        const marker = new google.maps.Marker({
            position: { lat: markers[i]['lat'], lng: markers[i]['lng']},
            map: map
        });

        // Call createInfoWindows function for each marker
        createInfoWindows(marker, infoWindow, markers[i], map, bounds);
    }

    // Extend bounds for each marker
    for (let i = 0; i < markers.length; i++) {
        bounds.extend(new google.maps.LatLng(markers[i]['lat'], markers[i]['lng']));
    }

    // Fit map to bounds after all markers are added
    map.fitBounds(bounds);
}

// Function to create info windows
function createInfoWindows(marker, infoWindow, markerInfo, map, bounds) {
    const infoWindowContent = `
        <div class="feh-content"> 
            <h3>${markerInfo['locationName']}</h3>
            <address>
                <p>${markerInfo['address']}</p>
            </address>
        </div>
    `;

    google.maps.event.addListener(marker, 'click', function () {
        infoWindow.setContent(infoWindowContent);
        infoWindow.open(map, marker);
    });

    // Close info window when close button is clicked
    infoWindow.addListener('closeclick', function(){
        map.fitBounds(bounds);
    });
}
