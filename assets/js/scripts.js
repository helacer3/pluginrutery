(function($) {
	
  function calculateCenter(objAddress) {
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0; i < objAddress.length; i++) {
      bounds.extend(coordinates[i]);
    }
    return bounds.getCenter();
  }

  function initMapDirections() {
    // Configuración del mapa
    var mapProp = {
      zoom: 12,
      //center: {lat: 4.736519, lng: -74.049400},
      mapTypeId: google.maps.MapTypeId.terrain
    };
    // Agregando el mapa al tag de id googleMap
    var map = new google.maps.Map(document.getElementById("googleMapRoutes"), mapProp);


    var directionsService = new google.maps.DirectionsService();

    var renderOptions = { draggable: true };
    var directionDisplay = new google.maps.DirectionsRenderer(renderOptions);

    //set the directions display service to the map
    directionDisplay.setMap(map);
    //set the directions display panel
    //panel is usually just and empty div.  
    //This is where the turn by turn directions appear.
    directionDisplay.setPanel(document.getElementById("googleMapRoutesAddressess")); 

    //build the waypoints
    //free api allows a max of 9 total stops including the start and end address
    //premier allows a total of 25 stopsAddressess. 
    
    var waypoints          = [];
    var items              = jsVars.jsonRoutes.intermediate;     
    //set the starting address and destination address
    var originAddress      = jsVars.jsonRoutes.origin.address;
    var destinationAddress = jsVars.jsonRoutes.destination.address;

    // iterate Intermediate
    Object.entries(items).forEach(([key, item]) => {
      if (item.address !== "") {
        waypoints.push({
            location: item.address,
            stopover: true
        });
      }
    });

    //build directions request
    var request = {
      origin: originAddress,
      destination: destinationAddress,
      waypoints: waypoints, //an array of waypoints
      optimizeWaypoints: true, //set to true if you want google to determine the shortest route or false to use the order specified.
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    //get the route from the directions service
    directionsService.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionDisplay.setDirections(response);

            var bounds = response.routes[0].bounds;
            map.fitBounds(bounds);
            map.setCenter(bounds.getCenter());
        }
        else {
        }
    });
  }

 	$(document).ready(initMapDirections);
})(jQuery)
