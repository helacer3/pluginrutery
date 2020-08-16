(function($) {
  // vars
  var globalMap       = null;
  var userMarker      = null;
  var markerDrivers   = [];
  var mcrTime         = 10000;
  var rfsTimeDriver   = 60000;
  var rfsTimeCustomer = 120000;
  // customer Coordinates
  var cstShowed       = [];
  var cstLatitude     = "";
  var cstLongitude    = "";

  /**
  * general Init
  */
  function generalInit() {
    // register Position Automatized
    if (jsVars.isDriver == true) {
      console.log("El usuario actual, es un conductor");
      // register Position
      autRegister  = setInterval(function () { getLocation("driver"); }, mcrTime);
      // load Customers Position
      shwCustomers = setInterval(function () { getCustomerRequestList(); }, mcrTime);
      // auto Refresh
      drvRefresh   = setInterval(function () { console.log("realiza Refresh"); location.reload(); }, rfsTimeDriver);
    } else {
      console.log("El usuario actual, no es un conductor");
      // load Drivers Position
      shwDrivers   = setInterval(function () { findDriversPositions(); }, mcrTime);
      // register User Position
      getLocation("user");
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
  * add Marker
  */
  function addMarker(cooLatLng, cooMap, cooImage, cooTitle, isUser = false) {
    // add Map Marker
    let actMarker = new google.maps.Marker({
      position:  cooLatLng,
      map:       cooMap,
      icon:      cooImage,
      title:     cooTitle
    });
    // validate Is User
    if (isUser) {
      // valdiate User Market
      if (userMarker != null) {
        userMarker.setMap(null);
      }
      userMarker = actMarker;
    }
  }

  /**
  * add Marker Map
  */
  function addMarkerMap(cooLatitude, cooLongitude, cooTitle, usrType = "driver", isUser = false) {
    console.log(cooLatitude+" - "+cooLongitude+" - "+cooTitle);
    // coordinates
    let myLatLng  = { lat: parseFloat(cooLatitude), lng: parseFloat(cooLongitude) };
    // validate User Type
    if (usrType == "driver") {
      // define Image
      let image = jsVars.plgRuta+'assets/images/markers/busstop.png';
      // config Marker
      markerDrivers.push(
        // add Map Marker
        addMarker(myLatLng, globalMap, image, cooTitle, isUser)
      );
    } else {
      // define Image Url
      let image = jsVars.plgRuta+'assets/images/markers/flagman.png';
      // add Map Marker
      addMarker(myLatLng, globalMap, image, cooTitle, isUser);
    }
    // set Map
    //marker.setMap(globalMap);
  }

  /**
  * add Marker Map By Address
  */
  function addMarkerMapByAddress(cooAddress, cooTitle, isPrincipal = false) {
    // instance Geocoder
    let geocoder = new google.maps.Geocoder();
    // geocoder Address
    geocoder.geocode({ 'address': cooAddress }, function (results, status) {
        // define Coordinates
        var latLng = {lat: results[0].geometry.location.lat (), lng: results[0].geometry.location.lng ()};
        console.log("Las coordenadas de la dirección son: ");
        console.log(latLng);
        // validate Status
        if (status == 'OK') {
          // define Image Url
          let image = (!isPrincipal) ? jsVars.plgRuta+'assets/images/markers/pedestriancrossing.png' : null;
          // add Map Marker
          addMarker(latLng, globalMap, image, cooTitle);
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
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

    var renderOptions = { 
      draggable: false,
      suppressMarkers: true
    };
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
    console.log("waypoints");
    console.log(waypoints);
    
    //build directions request
    var request = {
      origin: originAddress,
      destination: destinationAddress,
      waypoints: waypoints, //an array of waypoints
      optimizeWaypoints: true, //set to true if you want google to determine the shortest route or false to use the order specified.
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    // get the route from the directions service
    directionsService.route(request, function (response, status) {
        console.log(response);
        console.log(status);

        if (status == google.maps.DirectionsStatus.OK) {
            directionDisplay.setDirections(response);

            var bounds = response.routes[0].bounds;
            globalMap.fitBounds(bounds);
            globalMap.setCenter(bounds.getCenter());
        }
        else {
        }
    });
    // add Origin Marker
    addMarkerMapByAddress(originAddress, originAddress, true);
    // add Destination Marker
    addMarkerMapByAddress(destinationAddress, destinationAddress, true);


    /* ************************ */
    /* ** detect Event Click ** */
    /* ************************ */
    google.maps.event.addListener(globalMap, 'click', function(event) {
      //alert( "Latitude: "+event.latLng.lat()+" "+", longitude: "+event.latLng.lng() );
      // instance Geocoder
      let geocoder = new google.maps.Geocoder();
      // define latLong
      const latlng = {
        lat: parseFloat(event.latLng.lat()),
        lng: parseFloat(event.latLng.lng())
      };
      // define Geocoder
      geocoder.geocode({ location: latlng }, (results, status) => {
        // validate Status
        if (status === "OK") {
          //  validate Result
          if (results[0]) {
            let selAddress = results[0].formatted_address;
            $("#solAddressOrigin").val(selAddress);
            addMarkerMap(event.latLng.lat(), event.latLng.lng(), "", "user", true);
          }
        }
      });
    });
  }

  /**
  * request Service
  */
  $('#solSubmit').on('click', function(e) {
    // get Data
    var solName               = $('#solName').val();
    var solPhone              = $('#solPhone').val();
    var solAddressOrigin      = $('#solAddressOrigin').val();
    var solAddressDestination = $('#solAddressDestination').val();
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
    } else if (solAddressOrigin.length < 4) {
      // show Message
      $('#solErroMessage').html('Favor verifique su dirección de Origen').show();
      // hide Messsage
      setTimeout( function () { $('#solErroMessage').html('').hide(); }, 4000);
    } else if (solAddressDestination.length < 4) {
      // show Message
      $('#solErroMessage').html('Favor verifique su dirección de Destino').show();
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
          solPhone: solPhone,
          solLatitude: cstLatitude,
          solLongitude: cstLongitude,
          solAddressOrigin: solAddressOrigin,
          solAddressDestination: solAddressDestination
        },
        success: function(respuesta) {
          // show Message
          $('#solSuccessMessage').html('En pocos minutos nos pondremos en contacto. Muchas gracias!').show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log("No se ha podido obtener la información: "+textStatus);
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
      error: function(jqXHR, textStatus, errorThrown) {
        console.log("No se ha podido obtener la información: "+textStatus);
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
        error: function(jqXHR, textStatus, errorThrown) {
          console.log("No se ha podido obtener la información: "+textStatus);
        }
      });
    }
  });

  /**
  * driver Route Finish
  */
  $(document).on('click', '#routeFinish', function(e) {
    // confirm Finish
    if (confirm("¿Confirma que terminó su recorrido?")) {
      clearInterval(autRegister);
      clearInterval(drvRefresh);
    }
  });

  /**
  * register User Position
  */
  function registerUserPosition(actCoordinates) {
    // set Customer Latitude
    cstLatitude  = String(actCoordinates.coords.latitude);
    // set Customer Longitude 
    cstLongitude = String(actCoordinates.coords.longitude);
    console.log("Las coordeadas del usuario son: "+cstLatitude+" - "+cstLongitude);
  }

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
      error: function(jqXHR, textStatus, errorThrown) {
        console.log("No se ha podido obtener la información: "+textStatus);
      }
    });
  }

  /**
  *  get Customer Request List
  */
  function getCustomerRequestList() {
    // request Ajax
    $.ajax({
      url: jsVars.ajaxUrl,
      type: "POST",
      dataType: 'JSON',
      data: {
        action:   'ajaxCustomerList',
        actRoute: jsVars.idRoute, // global Vars
      },
      success: function(response) {
        // validate Response
        if (response.status == 'OK' && response.data.length > 0) {
          // iterate Customer List
          jQuery.each(response.data, function(request) {
            // customer Position
            let pos = cstShowed.indexOf(this.id);
            // validate Customer Position
            if (pos == -1) {
              // add Marker Actual Coordinates
              addMarkerMap(this.latitud, this.longitud, this.name, "customer");
              // add Marker by Address
              addMarkerMapByAddress(this.originAddress, this.name);
            }
          });
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log("No se ha podido obtener la información: "+textStatus);
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
  function getLocation(userType) {
    if (navigator.geolocation) {
      console.log("Ingresa a Registrar la ubicación");
      navigator.geolocation.watchPosition((userType == "driver") ? registerDriverPosition : registerUserPosition);
    } else {
      console.log("Geolocation is not supported by this browser.");
    }
  }

  $(document).ready(generalInit);
})(jQuery)