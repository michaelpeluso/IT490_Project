function initAutocomplete() {
  var searchInput = document.getElementById('search-area');
  var autocomplete = new google.maps.places.Autocomplete(searchInput);

  var startingInput = document.getElementById('starting-location');
  var startingAutocomplete = new google.maps.places.Autocomplete(startingInput);
}

function searchArea() {
  var searchInput = document.getElementById('search-area').value;
  var startingLocation = document.getElementById('starting-location').value;

  // Geocode the search input to get the latitude and longitude
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: searchInput }, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      var location = results[0].geometry.location;
      var latitude = location.lat();
      var longitude = location.lng();

      // Search for nearby restaurants
      searchNearbyRestaurants(latitude, longitude);

      // Search for nearby flights
      searchFlights(startingLocation, searchInput);
    } else {
      console.error('Geocoding error:', status);
    }
  });
}


function searchNearbyRestaurants(latitude, longitude) {
  var service = new google.maps.places.PlacesService(document.createElement('div'));
  var request = {
    location: new google.maps.LatLng(latitude, longitude),
    radius: 5000, // Search radius in meters
    type: ['restaurant']
  };

  service.nearbySearch(request, function(results, status) {
    if (status === google.maps.places.PlacesServiceStatus.OK) {
      var restaurantsContainer = document.getElementById('restaurants-container');
      restaurantsContainer.innerHTML = ''; // Clear previous results

      if (results.length > 0) {
        restaurantsContainer.innerHTML = '<h2 class="nearby-restaurants-title text-center mb-4">Nearby Restaurants</h2>';
        var restaurantCardsContainer = document.createElement('div');
        restaurantCardsContainer.className = 'restaurant-cards';

        results.forEach(restaurant => {
          var restaurantInfo = `
            <div class="col-md-4 mb-4">
              <div class="card">
                <div class="card-img-container">
                  ${restaurant.photos && restaurant.photos.length > 0 ? `<img src="${restaurant.photos[0].getUrl({ maxWidth: 300, maxHeight: 200 })}" class="card-img-top" alt="${restaurant.name}">` : ''}
                </div>
                <div class="card-body">
                  <h5 class="card-title">${restaurant.name}</h5>
                  <p class="card-text">Rating: ${restaurant.rating}</p>
                  <p class="card-text">Address: <a href="https://www.google.com/maps/dir/Current+Location/${encodeURIComponent(restaurant.vicinity)}" target="_blank">${restaurant.vicinity}</a></p>
                  <i class="fas fa-plus-circle add-to-trip" data-restaurant='${JSON.stringify(restaurant).replace(/'/g, "&#39;")}'></i>
                </div>
              </div>
            </div>
          `;
          restaurantCardsContainer.innerHTML += restaurantInfo;
        });

        restaurantsContainer.appendChild(restaurantCardsContainer);
        // Attach event listener to "Add to Trip" buttons
        var addButtons = document.getElementsByClassName('add-to-trip');
        for (var i = 0; i < addButtons.length; i++) {
          addButtons[i].addEventListener('click', addRestaurantToTrip);
        }
      } else {
        restaurantsContainer.innerHTML = '<p class="text-center">No nearby restaurants found.</p>';
      }
    }
  });
}

function addRestaurantToTrip() {
  var restaurantData = JSON.parse(this.getAttribute('data-restaurant'));
  var restaurantName = restaurantData.name;
  var restaurantAddress = restaurantData.vicinity;
  var restaurantRating = restaurantData.rating;

  // Create a new trip item element
  var tripItem = document.createElement('div');
  tripItem.className = 'trip-item';
  tripItem.innerHTML = `
    <i class="fas fa-minus-circle remove-from-trip" data-restaurant='${JSON.stringify(restaurantData).replace(/'/g, "&#39;")}'></i>
    <span>${restaurantName}</span>
    `;

  // Append the trip item to the "My Trips" column
  document.getElementById('trip-items').appendChild(tripItem);

  // Attach event listener to the remove button
  var removeButton = tripItem.querySelector('.remove-from-trip');
  removeButton.addEventListener('click', removeRestaurantFromTrip);
}

function removeRestaurantFromTrip() {
  var tripItem = this.closest('.trip-item');
  tripItem.remove();
}

function searchFlights(origin, destination) {
  var apiKey = 'qhyCpeOEeXXL7NAtF7PowMPR3HBP8IcJ';
  var apiSecret = '6SMREPe2c5oBPY9O';

  var url = 'https://api.amadeus.com/v1/security/oauth2/token';
  var data = {
    grant_type: 'client_credentials',
    client_id: apiKey,
    client_secret: apiSecret
  };

  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams(data)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error getting access token: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      var accessToken = data.access_token;
      searchFlightOffers(accessToken, origin, destination);
    })
    .catch(error => {
      console.error(error);
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = '<p class="text-center">An error occurred while getting the access token.</p>';
    });
}

function searchFlightOffers(accessToken, origin, destination) {
  var url = 'https://api.amadeus.com/v2/shopping/flight-offers';
  var data = {
    originLocationCode: origin,
    destinationLocationCode: destination,
    departureDate: '2023-06-01',
    adults: 1,
    max: 5
  };

  fetch(url, {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + accessToken
    },
    params: data
  })
    .then(response => response.json())
    .then(data => {
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = ''; // Clear previous results

      if (data && data.data && data.data.length > 0) {
        var flightsHTML = '<h2 class="section-title mb-4">Available Flights</h2>';

        data.data.forEach(flight => {
          flightsHTML += `
            <div class="col-md-4 mb-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">${flight.itineraries[0].segments[0].carrierCode}</h5>
                  <p class="card-text">Departure: ${flight.itineraries[0].segments[0].departure.at}</p>
                  <p class="card-text">Arrival: ${flight.itineraries[0].segments[flight.itineraries[0].segments.length - 1].arrival.at}</p>
                  <p class="card-text">Price: ${flight.price.total}</p>
                  <button class="btn btn-primary add-flight-to-trip" data-flight='${JSON.stringify(flight)}'>Add to Trip</button>
                </div>
              </div>
            </div>
          `;
        });

        flightsContainer.innerHTML = flightsHTML;

        // Attach event listeners to "Add to Trip" buttons for flights
        var addFlightButtons = document.getElementsByClassName('add-flight-to-trip');
        for (var i = 0; i < addFlightButtons.length; i++) {
          addFlightButtons[i].addEventListener('click', addFlightToTrip);
        }
      } else {
        flightsContainer.innerHTML = '<p class="text-center">No flights found.</p>';
      }
    })
    .catch(error => {
      console.error('Error fetching flights:', error);
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = '<p class="text-center">An error occurred while searching for flights.</p>';
    });
}



function addFlightToTrip() {
  var flightData = JSON.parse(this.getAttribute('data-flight'));
  var airline = flightData.airline;
  var departureTime = flightData.departure_time;
  var arrivalTime = flightData.arrival_time;
  var price = flightData.price;

  // Create a new trip item element for the flight
  var tripItem = document.createElement('div');
  tripItem.className = 'trip-item';
  tripItem.innerHTML = `
    <i class="fas fa-minus-circle remove-from-trip" data-flight='${JSON.stringify(flightData)}'></i>
    <span>${airline} - ${departureTime} to ${arrivalTime} (${price})</span>
  `;

  // Append the trip item to the "My Trips" column
  document.getElementById('trip-items').appendChild(tripItem);

  // Attach event listener to the remove button
  var removeButton = tripItem.querySelector('.remove-from-trip');
  removeButton.addEventListener('click', removeFlightFromTrip);
}

function removeFlightFromTrip() {
  var tripItem = this.closest('.trip-item');
  tripItem.remove();
}

// Initialize the autocomplete functionality when the page loads
google.maps.event.addDomListener(window, 'load', initAutocomplete);