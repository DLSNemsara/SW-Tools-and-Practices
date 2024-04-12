// Fetch properties from the backend API
function fetchProperties() {
    // Make an AJAX request to fetch properties
    // Example using fetch API
    fetch('/api/properties')
        .then(response => response.json())
        .then(properties => {
            // Call a function to populate the advertisement list
            populateAdvertisementList(properties);
        })
        .catch(error => console.error('Error fetching properties:', error));
}

// Function to populate the advertisement list with property details
function populateAdvertisementList(properties) {
    const advertisementList = document.getElementById('advertisementList');
    
    // Clear previous content
    advertisementList.innerHTML = '';

    // Iterate through each property and create advertisement div
    properties.forEach(property => {
        const advertisementDiv = document.createElement('div');
        advertisementDiv.classList.add('advertisement');
        advertisementDiv.innerHTML = `
            <h2>${property.title}</h2>
            <p>Location: ${property.location}</p>
            <p>Rent: $${property.rent}/month</p>
            <button onclick="viewDetails(${property.id})">View Details</button>
            <button onclick="approve(${property.id})">Approve</button>
            <button onclick="reject(${property.id})">Reject</button>
        `;
        advertisementList.appendChild(advertisementDiv);
    });
}

// Function to view details of a property
function viewDetails(propertyId) {
    // Code to display details of the selected property in the popup window
}

// Function to approve a property
function approve(propertyId) {
    // Code to send an AJAX request to approve the property
}

// Function to reject a property
function reject(propertyId) {
    // Code to send an AJAX request to reject the property
}

// Fetch properties when the page loads
window.onload = fetchProperties;
