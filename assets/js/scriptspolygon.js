(function($) {
	function initialize() {
		// position
		var pos = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
		// Configuración del mapa
		var mapProp = {
			zoom: 12,
			center: {lat: 4.736519, lng: -74.049400},
			mapTypeId: google.maps.MapTypeId.terrain
		};
		// Agregando el mapa al tag de id googleMap
		var map = new google.maps.Map(document.getElementById("googleMapRoutes"), mapProp);

		// Coordenada de la ruta
		//var flightPlanCoordinates = [
		//	{"lat": 4.756361, "lng": -74.046959},
		//	{"lat": 4.749549, "lng": -74.047640},
		//	{"lat": 4.702783, "lng": -74.041421},
		//	{"lat": 4.683675, "lng": -74.035714},
		//	{"lat": 4.694954, "lng": -74.086220}
		//];

		// create Array Coordinates
		let polItems = [];
		let actItems = jsVars.jsonRoutes;
		// iterate Actual Items
		for (var i=0; i < actItems.length; i++) {
			// Latitude - Longitude
			polItems[i] = new google.maps.LatLng(actItems[i].lat, actItems[i].lng);
			markerItem  = createMarker(polItems[i], actItems[i].name, pos[i]);
			markerItem.setMap(map);
		}    

		// Información de la ruta (coordenadas, color de línea, etc...)
		var flightPath = new google.maps.Polyline({
			path: polItems,
			geodesic: true,
			strokeColor: '#1A3E6A',
			strokeOpacity: 1.0,
			strokeWeight: 4
		});

		// Creando la ruta en el mapa
		flightPath.setMap(map);
	}

	function createMarker(LatLngItem, nameItem, charItem) {
		var image = jsVars.plgRuta+'assets/images/markers/busstop.png';

		return new google.maps.Marker({
			position:  LatLngItem,
			animation: google.maps.Animation.DROP,
			title:     nameItem,
			label:     { text: charItem, color: "white", fontSize: "12px" },
			icon:      {				
				path: google.maps.SymbolPath.CIRCLE,
				fillColor: '#1A3E6A',
				fillOpacity: 1,
				scale: 5,
				strokeColor: '#1A3E6A',
				strokeWeight: 14
          	},
		});
	}

 	$(document).ready(initialize);
})(jQuery)