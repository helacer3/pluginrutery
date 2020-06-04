(function($) {
	// vars
  var globalMap     = null;
  var markerDrivers = [];
  var mcrTime       = 10000;

  /**
  * general Init
  */
  function generalInit() {
    // register Position Automatized
    if (jsVars.isDriver == true) {
      console.log("El usuario actual, es un conductor");
      // register Position
      autRegister = setInterval(function () { getLocation(); }, mcrTime);
    } else {
      console.log("El usuario actual, no es un conductor");
      // load Drivers Position
      shwDrivers  = setInterval(function () { findDriversPositions(); }, mcrTime);
    }
    // init Map Directions
    initMapDirections();
  }

  /**
  * calculate Center
  */
  function calculateCenter(objAddress) {
    var bounds = new google.maps.LatLngBounds();
    // iterate Adressess
    for (var i = 0; i < objAddress.length; i++) {
      bounds.extend(coordinates[i]);
    }
    return bounds.getCenter();
  }

  /**
  * add Marker Map
  */
  function addMarkerMap(cooLatitude, cooLongitude, cooTitle) {
    console.log(cooLatitude+" - "+cooLongitude+" - "+cooTitle);
    // coordinates
    var myLatLng  = { lat: parseFloat(cooLatitude), lng: parseFloat(cooLongitude) };

    var image     = jsVars.plgRuta+'assets/images/markers/busstop.png';
    // config Marker
    markerDrivers.push(
      new google.maps.Marker({
        position:  myLatLng,
        map:       globalMap,
        icon:      image,
        //animation: google.maps.Animation.DROP,
        title:     cooTitle
      })
    );
    // set Map
    //marker.setMap(globalMap);
  }

  /**
  * init Map Directions
  */
  function initMapDirections() {
    // Configuración del mapa
    var mapProp = {
      zoom: 13,
      //center: {lat: 4.736519, lng: -74.049400},
      mapTypeId: google.maps.MapTypeId.terrain
    };
    // Agregando el mapa al tag de id googleMap
    globalMap = new google.maps.Map(document.getElementById("googleMapRoutes"), mapProp);

    var directionsService = new google.maps.DirectionsService();

    var renderOptions = { draggable: true };
    var directionDisplay = new google.maps.DirectionsRenderer(renderOptions);

    //set the directions display service to the map
    directionDisplay.setMap(globalMap);
    //set the directions display panel
    //panel is usually just and empty div.  
    //This is where the turn by turn directions appear.
    //directionDisplay.setPanel(document.getElementById("googleMapRoutesAddressess")); 

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
            globalMap.fitBounds(bounds);
            globalMap.setCenter(bounds.getCenter());
        }
        else {
        }
    });
  }

  /**
  * request Service
  */
  $('#solSubmit').on('click', function(e) {
    // get Data
    var solName  = $('#solName').val();
    var solPhone = $('#solPhone').val();
    // alert("nombre: "+solName+" celular: "+solPhone+" ruta: "+jsVars.ajaxUrl);
    // validate Info
    if (solName.length < 3) {
      // show Message
      $('#solErroMessage').html('Favor diligencie su nombre completo').show();
      // hide Messsage
      setTimeout( function () { $('#solErroMessage').html('').hide(); }, 4000);
    } else if (solPhone.length < 10) {
      // show Message
      $('#solErroMessage').html('Favor verifique su número de celular').show();
      // hide Messsage
      setTimeout( function () { $('#solErroMessage').html('').hide(); }, 4000);
    } else {
      $.ajax({
        url: jsVars.ajaxUrl,
        type: "POST",
        data: {
          action:   'ajaxRequestService',
          solRoute: jsVars.idRoute, // global Var
          solName:  solName,
          solPhone: solPhone
        },
        success: function(respuesta) {
          // show Message
          $('#solSuccessMessage').html('En pocos minutos nos pondremos en contacto. Muchas gracias!').show();
        },
        error: function() {
          console.log("No se ha podido obtener la información");
        }
      });
    }
  });


  /**
  * request Service
  */
  $('#genRequest').on('click', function(e) {
    $.ajax({
      url: jsVars.ajaxUrl,
      type: "POST",
      data: {
        action:   'ajaxRequestList',
        solRoute: jsVars.idRoute, // global Vars
      },
      success: function(response) {
        $('#drivers-reqlist-content').html(response);
      },
      error: function() {
        console.log("No se ha podido obtener la información");
      }
    });
  });


  /**
  * request Service
  */
  $(document).on('click', '.reqContacted', function(e) {
    // contacted Confirmation
    if (confirm('Confirma que ya contactó al usuario?')) {
      // get Request ID
      var idRequest = $(this).attr('data-reqid');
      // request Ajax
      $.ajax({
        url: jsVars.ajaxUrl,
        type: "POST",
        data: {
          idRequest: idRequest,
          action:    'ajaxRequestContacted',
          solRoute:  jsVars.idRoute, // global Vars
        },
        success: function(response) {
          $('#genRequest').trigger('click');
        },
        error: function() {
          console.log("No se ha podido obtener la información");
        }
      });
    }
  });

  /**
  * driver Route Finish
  */
  $(document).on('click', '#routeFinish', function(e) {
    clearInterval(autRegister);
  });

  /**
  * register Driver Position
  */
  function registerDriverPosition(actCoordinates) {
    // request Ajax
    $.ajax({
      url: jsVars.ajaxUrl,
      type: "POST",
      data: {
        action:       'ajaxRegisterDriverPosition',
        actRoute:     jsVars.idRoute, // global Vars
        actUser:      jsVars.idUser,
        actLatitude:  actCoordinates.coords.latitude,
        actLongitude: actCoordinates.coords.longitude
      },
      success: function(response) {
      },
      error: function() {
        console.log("No se ha podido obtener la información");
      }
    });
  }

  /**
  * find Drivers Positions
  */
  function findDriversPositions() {
    // request Ajax
    $.ajax({
      url: jsVars.ajaxUrl,
      type: "POST",
      dataType:'json',
      data: {
        action:   'ajaxFindDriversPositions',
        actRoute: jsVars.idRoute // global Vars
      },
      success: function(response) {
        // validate Response
        if (response.status == "OK") {

          if ( markerDrivers.length > 0) {
            for (var i = 0; i < markerDrivers.length; i++) {
              console.log("Ingresa a eliminar el marcador");
              markerDrivers[i].setMap(null);
            }
            markerDrivers = [];
          }

          // iterate Data
          $.each(response.data, function(index, item) {
            //console.log(item);
            addMarkerMap(item.latitude, item.longitude, item.display_name);
          });
        }
      },
      error: function() {
        console.log("No se ha podido obtener la información");
      }
    });
  }

  /**
  * get Location
  */
  function getLocation() {
    if (navigator.geolocation) {
      console.log("Ingresa a Registrar la ubicación");
      navigator.geolocation.getCurrentPosition(registerDriverPosition);
    } else {
      console.log("Geolocation is not supported by this browser.");
    }
  }

 	$(document).ready(generalInit);
})(jQuery)