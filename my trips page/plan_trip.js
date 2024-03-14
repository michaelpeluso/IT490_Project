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
      searchNearbyFlights(startingLocation, latitude, longitude);
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

function searchNearbyFlights(startingLocation, destinationLatitude, destinationLongitude) {
  // Geocode the starting location to get the nearest airport
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: startingLocation }, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      var startingLatitude = results[0].geometry.location.lat();
      var startingLongitude = results[0].geometry.location.lng();

      // Find the nearest airport to the starting location using the Tripadvisor API
      var nearestAirportUrl = `https://api.content.tripadvisor.com/api/v1/location/nearby_search?latLong=${startingLatitude},${startingLongitude}&category=airport&key=302D221339A3427FA7DA2DC8725BC77D`;

      fetch(nearestAirportUrl)
        .then(response => response.json())
        .then(data => {
          if (data.data && data.data.length > 0) {
            var nearestAirport = data.data[0];
            var nearestAirportId = nearestAirport.location_id;

            // Search for flights from the nearest airport to the destination using the Tripadvisor API
            var flightsUrl = `https://api.content.tripadvisor.com/api/v1/flights/search?sourceAirportId=${nearestAirportId}&destinationLatitude=${destinationLatitude}&destinationLongitude=${destinationLongitude}&key=302D221339A3427FA7DA2DC8725BC77D`;

            fetch(flightsUrl)
              .then(response => response.json())
              .then(flightData => {
                var flightsContainer = document.getElementById('flights-container');
                flightsContainer.innerHTML = ''; // Clear previous results

                if (flightData.data && flightData.data.length > 0) {
                  var flightsHTML = '<h2 class="section-title mb-4">Nearby Flights</h2>';

                  flightData.data.forEach(flight => {
                    flightsHTML += `
                      <div class="col-md-4 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">${flight.airlineName}</h5>
                            <p class="card-text">Departure: ${flight.departureTime}</p>
                            <p class="card-text">Arrival: ${flight.arrivalTime}</p>
                            <p class="card-text">Price: ${flight.price}</p>
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
                  flightsContainer.innerHTML = '<p class="text-center">No nearby flights found.</p>';
                }
              })
              .catch(error => {
                console.error('Error fetching flights:', error);
                var flightsContainer = document.getElementById('flights-container');
                flightsContainer.innerHTML = '<p class="text-center">An error occurred while searching for flights.</p>';
              });
          } else {
            console.error('No nearest airport found.');
            var flightsContainer = document.getElementById('flights-container');
            flightsContainer.innerHTML = '<p class="text-center">No nearest airport found.</p>';
          }
        })
        .catch(error => {
          console.error('Error fetching nearest airport:', error);
          var flightsContainer = document.getElementById('flights-container');
          flightsContainer.innerHTML = '<p class="text-center">An error occurred while searching for the nearest airport.</p>';
        });
    } else {
      console.error('Geocoding error:', status);
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = '<p class="text-center">An error occurred while geocoding the starting location.</p>';
    }
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