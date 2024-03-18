
function initAutocomplete() {
  var searchInput = document.getElementById('search-area');
  var autocomplete = new google.maps.places.Autocomplete(searchInput);

  var startingInput = document.getElementById('starting-location');
  var startingAutocomplete = new google.maps.places.Autocomplete(startingInput);
}

const apiKey = 'qNoU2gO8Iq4gNGEDKX6yOJKWYFLS0JGX';
const apiSecret = 'E6cQBE7GBvHSeBAx';

async function fetchAccessToken() {
  const tokenResponse = await fetch('https://test.api.amadeus.com/v1/security/oauth2/token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `grant_type=client_credentials&client_id=${encodeURIComponent(apiKey)}&client_secret=${encodeURIComponent(apiSecret)}`
  });

  if (!tokenResponse.ok) {
    throw new Error('Failed to retrieve access token');
  }

  const { access_token } = await tokenResponse.json();
  return access_token;
}

function searchArea() {
  var searchInput = document.getElementById('search-area').value;
  var startingLocation = document.getElementById('starting-location').value;

  // Geocode the search input to get the latitude and longitude
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: searchInput }, async function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      var location = results[0].geometry.location;
      var latitude = location.lat();
      var longitude = location.lng();

      try {
        const accessToken = await fetchAccessToken();
        const nearbyAirports = await fetchNearbyAirports(accessToken, latitude, longitude);
        console.log('Nearby Airports:', nearbyAirports);

        // Use the first nearby airport as the destination
        const destinationCode = nearbyAirports[0];

        // Geocode the starting location to get the latitude and longitude
        geocoder.geocode({ address: startingLocation }, async function(startingResults, startingStatus) {
          if (startingStatus === google.maps.GeocoderStatus.OK) {
            var startingLatitude = startingResults[0].geometry.location.lat();
            var startingLongitude = startingResults[0].geometry.location.lng();

            const nearbyStartingAirports = await fetchNearbyAirports(accessToken, startingLatitude, startingLongitude);
            console.log('Nearby Starting Airports:', nearbyStartingAirports);

            // Use the first nearby starting airport as the origin
            const originCode = nearbyStartingAirports[0];

            // Search for flights using the origin and destination codes
            searchFlights(originCode, destinationCode);
          } else {
            console.error('Geocoding error for starting location:', startingStatus);
          }
        });
      } catch (error) {
        console.error('Error fetching nearby airports:', error);
      }

      // Search for nearby restaurants
      searchNearbyRestaurants(latitude, longitude);
      searchHotels(latitude, longitude);
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

function searchFlights(originCode, destinationCode) {
  var departureDate = document.getElementById('departure-date').value;

  // Validate the departure date
  var today = new Date().toISOString().split('T')[0];
  if (departureDate < today) {
    alert('Please select a future departure date.');
    return;
  }

  // Step 2: Request an Access Token
  var tokenUrl = 'https://test.api.amadeus.com/v1/security/oauth2/token';
  var tokenData = {
    grant_type: 'client_credentials',
    client_id: apiKey,
    client_secret: apiSecret
  };

  fetch(tokenUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams(tokenData)
  })
    .then(response => response.json())
    .then(data => {
      var accessToken = data.access_token;
      // Step 3: Use the Access Token and IATA codes to search for flights
      searchFlightOffers(accessToken, originCode, destinationCode, departureDate);
    })
    .catch(error => {
      console.error('Error getting access token:', error);
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = '<p class="text-center">An error occurred while getting the access token.</p>';
    });
}

async function fetchNearbyAirports(accessToken, latitude, longitude) {
  const radius = 100; // Specify the search radius in kilometers
  const airportsResponse = await fetch(`https://test.api.amadeus.com/v1/reference-data/locations/airports?latitude=${latitude}&longitude=${longitude}&radius=${radius}`, {
    headers: {
      'Authorization': `Bearer ${accessToken}`
    }
  });

  if (!airportsResponse.ok) {
    throw new Error('Failed to retrieve airports data');
  }

  const airportsData = await airportsResponse.json();
  return airportsData.data.map(airport => airport.iataCode);
}




function convertToIATACodes(accessToken, origin, destination) {
  var baseUrl = 'https://test.api.amadeus.com/v1';

  var getIATACode = (location) => {
    var url = `${baseUrl}/reference-data/locations?subType=CITY,AIRPORT&keyword=${encodeURIComponent(location)}`;

    return fetch(url, {
      headers: {
        'Authorization': `Bearer ${accessToken}`
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.data && data.data.length > 0) {
          return data.data[0].iataCode;
        } else {
          throw new Error(`No IATA code found for location: ${location}`);
        }
      });
  };

  return Promise.all([
    getIATACode(origin),
    getIATACode(destination)
  ])
    .then(([originCode, destinationCode]) => ({
      originCode,
      destinationCode
    }));
}

function searchFlightOffers(accessToken, originCode, destinationCode, departureDate) {
  var url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';
  var params = new URLSearchParams({
    originLocationCode: originCode,
    destinationLocationCode: destinationCode,
    departureDate: departureDate,
    adults: '2',
    max: '5'
  });

  fetch(url + '?' + params, {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + accessToken
    }
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error fetching flight offers: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      console.log('Flight Offers Response:', data);
      var flightsContainer = document.getElementById('flights-container');
      flightsContainer.innerHTML = ''; // Clear previous results

      if (data && data.data && data.data.length > 0) {
        data.data.forEach(flight => {
          var flightCard = document.createElement('div');
          flightCard.className = 'card mb-3';

          var flightCardBody = document.createElement('div');
          flightCardBody.className = 'card-body';

          var flightDetails = '';

          flight.itineraries[0].segments.forEach((segment, index) => {
            var departureDate = new Date(segment.departure.at);
            var arrivalDate = new Date(segment.arrival.at);

            flightDetails += `
              <h5 class="card-title">${segment.carrierCode} ${segment.number}</h5>
              <p class="card-text">Departure: ${departureDate.toLocaleString()}</p>
              <p class="card-text">Arrival: ${arrivalDate.toLocaleString()}</p>
              <p class="card-text">Terminal: ${segment.departure.terminal || 'N/A'}</p>
              <p class="card-text">Gate: ${segment.departure.gate || 'N/A'}</p>
              ${index < flight.itineraries[0].segments.length - 1 ? '<hr>' : ''}
            `;
          });

          flightDetails += `
            <p class="card-text">Price: ${flight.price.total}</p>
            <button class="btn btn-primary add-flight-to-trip" data-flight='${JSON.stringify(flight)}'>Add to Trip</button>
          `;

          flightCardBody.innerHTML = flightDetails;
          flightCard.appendChild(flightCardBody);
          flightsContainer.appendChild(flightCard);
        });

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
  var airline = flightData.itineraries[0].segments[0].carrierCode;
  var departureTime = flightData.itineraries[0].segments[0].departure.at;
  var arrivalTime = flightData.itineraries[0].segments[flightData.itineraries[0].segments.length - 1].arrival.at;
  var price = flightData.price.total;

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


let allHotels = []; // Store all the fetched hotels

async function searchHotels(latitude, longitude) {
  try {
    const accessToken = await fetchAccessToken();
    const radius = 20; // Search radius in miles

    const hotelListResponse = await fetch(`https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-geocode?latitude=${latitude}&longitude=${longitude}&radius=${radius}`, {
      headers: {
        'Authorization': `Bearer ${accessToken}`
      }
    });

    if (!hotelListResponse.ok) {
      throw new Error('Failed to retrieve hotel list');
    }

    const hotelListData = await hotelListResponse.json();
    const hotelIds = hotelListData.data.map(hotel => hotel.hotelId);
    console.log("HOTEL IDS ", hotelIds)

    searchHotelOffers(latitude, longitude, hotelIds);
  } catch (error) {
    console.error('Error searching hotels:', error);
  }
}

async function searchHotelOffers(latitude, longitude, hotelIds) {
  try {
    const accessToken = await fetchAccessToken();
    const guests = document.getElementById('guests-input').value || 2;
    const checkInDate = document.getElementById('check-in-date').value || getDefaultCheckInDate();
    const checkOutDate = document.getElementById('check-out-date').value || getDefaultCheckOutDate();
    const currency = 'USD';

    const hotelOffersResponse = await fetch(`https://test.api.amadeus.com/v3/shopping/hotel-offers?hotelIds=${hotelIds.join(',')}&adults=${guests}&checkInDate=${checkInDate}&checkOutDate=${checkOutDate}&currency=${currency}`, {
      headers: {
        'Authorization': `Bearer ${accessToken}`
      }
    });

    if (!hotelOffersResponse.ok) {
      throw new Error('Failed to retrieve hotel offers');
    }

    const hotelOffersData = await hotelOffersResponse.json();
    console.log("HOTEL OFFERS DATA: ", hotelOffersData);
    console.log("HOTEL OFFERS DATA: ", JSON.stringify(hotelOffersData, null, 2));

    allHotels = hotelOffersData.data; // Store all the fetched hotel offers
    displayHotels(allHotels);
  } catch (error) {
    console.error('Error searching hotel offers:', error);
  }
}


function displayHotels(hotelOffers) {
  const hotelsContainer = document.getElementById('hotels-container');
  hotelsContainer.innerHTML = '';

  if (hotelOffers.length === 0) {
    hotelsContainer.innerHTML = '<p class="text-center">No nearby hotels found.</p>';
    return;
  }

  const geocoder = new google.maps.Geocoder();

  hotelOffers.forEach(hotelOffer => {
    const hotel = hotelOffer.hotel;
    const offer = hotelOffer.offers[0];

    geocoder.geocode({ address: hotel.name + ', ' + hotel.cityCode }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const address = results[0].formatted_address;

        const hotelRow = document.createElement('div');
        hotelRow.className = 'hotel-row';
        hotelRow.innerHTML = `
          <div class="hotel-details">
            <div class="hotel-name">${hotel.name}</div>
            <div class="hotel-address"><a href="https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}" target="_blank">${address}</a></div>
            <div class="hotel-price">Price: ${offer.price.currency} ${offer.price.total}</div>
            <div class="hotel-checkin"><strong>Check-in:</strong> ${formatDate(offer.checkInDate)}</div>
            <div class="hotel-checkout"><strong>Check-out:</strong> ${formatDate(offer.checkOutDate)}</div>
            <div class="hotel-guests">Guests: ${offer.guests.adults}</div>
            <i class="fas fa-plus-circle add-to-favorites" data-hotel='${JSON.stringify(hotelOffer)}'></i>
            <button class="btn btn-primary book-now" data-hotel='${JSON.stringify(hotelOffer)}'>Book Now</button>
            </div>
        `;
        hotelsContainer.appendChild(hotelRow);
      }
    });
  });

  // Attach event listeners to "Add to Favorites" buttons
  const addToFavoritesButtons = document.getElementsByClassName('add-to-favorites');
  for (let i = 0; i < addToFavoritesButtons.length; i++) {
    addToFavoritesButtons[i].addEventListener('click', addHotelToFavorites);
  }

  const bookNowButtons = document.getElementsByClassName('book-now');
  for (let i = 0; i < bookNowButtons.length; i++) {
    bookNowButtons[i].addEventListener('click', handleBookNowClick);
  }
}

function handleBookNowClick() {
  const hotelOfferData = JSON.parse(this.getAttribute('data-hotel'));
  const hotel = hotelOfferData.hotel;
  const offer = hotelOfferData.offers[0];

  const bookingData = {
    hotelId: hotel.hotelId,
    hotelName: hotel.name,
    checkInDate: offer.checkInDate,
    checkOutDate: offer.checkOutDate,
    numGuests: offer.guests.adults,
    price: offer.price.total,
    currency: offer.price.currency
  };

  // Store the booking data in the browser's local storage
  localStorage.setItem('bookingData', JSON.stringify(bookingData));

  // Open the booking.html page in a new window
  window.open('./booking_page/booking.html', '_blank');
}

function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return date.toLocaleDateString('en-US', options);
}

function addHotelToFavorites() {
  const hotelOfferData = JSON.parse(this.getAttribute('data-hotel'));
  const hotelName = hotelOfferData.hotel.name;
  const hotelPrice = hotelOfferData.offers[0].price.currency + ' ' + hotelOfferData.offers[0].price.total;

  // Create a new trip item element for the hotel
  const tripItem = document.createElement('div');
  tripItem.className = 'trip-item';
  tripItem.innerHTML = `
    <i class="fas fa-minus-circle remove-from-trip" data-hotel='${JSON.stringify(hotelOfferData)}'></i>
    <span>${hotelName} (${hotelPrice})</span>
  `;

  // Append the trip item to the "My Trips" column
  document.getElementById('trip-items').appendChild(tripItem);

  // Attach event listener to the remove button
  const removeButton = tripItem.querySelector('.remove-from-trip');
  removeButton.addEventListener('click', removeHotelFromTrip);
}

function removeHotelFromTrip() {
  const tripItem = this.closest('.trip-item');
  tripItem.remove();
}

function filterHotels() {
  const priceFilter = document.getElementById('price-filter').value;
  const ratingFilter = document.getElementById('rating-filter').value;

  const filteredHotels = allHotels.filter(hotelOffer => {
    const hotel = hotelOffer.hotel;
    const price = parseFloat(hotelOffer.offers[0].price.total);
    const rating = hotel.rating;

    if (priceFilter && !isPriceInRange(price, priceFilter)) {
      return false;
    }

    if (ratingFilter && rating < parseFloat(ratingFilter)) {
      return false;
    }

    return true;
  });

  displayHotels(filteredHotels);
}


function isPriceInRange(price, range) {
  const [min, max] = range.split('-').map(parseFloat);
  return price >= min && price <= max;
}

function getDefaultCheckInDate() {
  const today = new Date();
  return today.toISOString().split('T')[0];
}

function getDefaultCheckOutDate() {
  const checkOutDate = new Date();
  checkOutDate.setDate(checkOutDate.getDate() + 3);
  return checkOutDate.toISOString().split('T')[0];
}

// Initialize the autocomplete functionality when the page loads
google.maps.event.addDomListener(window, 'load', initAutocomplete);
