(function($) {
	



	function initMap() {
        var directionsService = new google.maps.DirectionsService();
        var directionsRenderer = new google.maps.DirectionsRenderer();
        var map = new google.maps.Map(document.getElementById('googleMapRoutes'), {
          zoom: 7,
          center: {lat: 4.736519, lng: -74.049400}
        });
        directionsRenderer.setMap(map);


        calculateAndDisplayRoute(directionsService, directionsRenderer);

        /*var onChangeHandler = function() {
          calculateAndDisplayRoute(directionsService, directionsRenderer);
        };
        document.getElementById('start').addEventListener('change', onChangeHandler);
        document.getElementById('end').addEventListener('change', onChangeHandler);*/
      }

      function calculateAndDisplayRoute(directionsService, directionsRenderer) {
        	directionsService.route(
            {
              origin: new google.maps.LatLng(4.756361, -74.046959),
              destination: new google.maps.LatLng(4.749549, -74.047640),
              travelMode: 'DRIVING'
            },
            function(response, status) {
              if (status === 'OK') {
                directionsRenderer.setDirections(response);
              } else {
                window.alert('Directions request failed due to ' + status);
              }
            });
      }



 	$(document).ready(initMap);
})(jQuery)
