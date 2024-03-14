function initAutocomplete() {
  var input = document.getElementById('search-area');
  var autocomplete = new google.maps.places.Autocomplete(input);
}

function searchArea() {
  var searchInput = document.getElementById('search-area').value;

  // Geocode the search input to get the latitude and longitude
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: searchInput }, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      var location = results[0].geometry.location;
      var latitude = location.lat();
      var longitude = location.lng();

      // Search for nearby restaurants
      searchNearbyRestaurants(latitude, longitude);
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

// Initialize the autocomplete functionality when the page loads
google.maps.event.addDomListener(window, 'load', initAutocomplete);