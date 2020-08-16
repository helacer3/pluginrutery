<?php
/**
* load Route Driver Name
*/
function loadRouteDriverName($idUser) {
	// load User
	$user = get_user_by( 'id', $idUser );
	// return User Name
	return ($user != null) ? $user->data->display_name : '';
}

/**
 * load Route
*/
function loadSingleRoute($namTable, $id) {
  	global $wpdb;
  	$objRoute = null;
  	// validate ID
  	if ($id > 0) {
		$objRoute = $wpdb->get_row("SELECT * FROM $namTable WHERE id = ".(int)$id);
	}
	// default Return
	return $objRoute;
}

/*
 * load Request List By Route
*/
function loadRequestListByRoute($id) {
  	global $wpdb;
  	$objRequest = null;
  	$tableRoutesStation = $wpdb->prefix . 'routes_request';
  	// validate ID
  	if ($id > 0) {
  		// set Query
  		$query = "SELECT * 
			FROM $tableRoutesStation 
			WHERE DATE(request_date) = DATE(NOW()) 
			AND contacted = 0
			AND id_routes = ".(int)$id;
		// get Results
		$objRequest = $wpdb->get_results($query);
	}
	// default Return
	return $objRequest;
}

/**
* singleRouteStations
*/
function singleRouteStations($id = 0) {
	global $wpdb; // this is how you get access to the database
	$arrStations = array();
  	// validate ID
  	if ($id > 0) {
	  	$tableRoutesStation = $wpdb->prefix . 'routes_station';
		$objStations        = $wpdb->get_results("SELECT * FROM $tableRoutesStation 
			WHERE id_routes = ".(int)$id." AND status = 1 order by position ASC");
		// valdiate Object
		if (count($objStations) > 0) {
			// iterate Object
			foreach ($objStations as $station) {
				array_push($arrStations,
					array(
						//'lat'  => (float)$station->latitude,
						//'lng'  => (float)$station->longitude,
						'name'    => $station->name,
						'address' => $station->address,
					)
				);
			}
		}
	}
	//print_r($arrStations);die;
	// json Response	
	return $arrStations;
}

/**
* arraySingleRoutesStation
*/
function arraySingleRoutesStation($id) {
	// get Array Routes
	$arrRoutes = singleRouteStations($id);
	// count Items
	$numItems  = count($arrRoutes);
	// default Return
	return array (
		'origin'       => ($numItems > 0) ? $arrRoutes[0]: '',
		'intermediate' => call_user_func(function() use ($arrRoutes, $numItems) {
			$arrIntermediate = array();
			// validate Other Addresses
			if ($numItems > 2) {
				unset($arrRoutes[0]);
				unset($arrRoutes[$numItems-1]);
				// set Array Value
				$arrIntermediate = $arrRoutes;
			}
			// default Return
			return $arrIntermediate;
		}),
		'destination'  => ($numItems > 1) ? $arrRoutes[$numItems-1]: ''
	);
}

/*
 * load Positions By Route
*/
function loadPositionsByRoute($idRoutes) {
  	global $wpdb;
  	// create Default Vars
  	$objRequest  = null;
	// table Routes Station
	$tableUsers        = $wpdb->prefix . 'users';
	$tableRoutesDriver = $wpdb->prefix . 'routes_driver_position';
	// load Drivers Position  	
	$sqlQuery = "SELECT 
		u.display_name, rdp.id_user, rdp.latitude, 
		rdp.longitude, CONCAT(rdp.routes_date, ' ', rdp.routes_time) routes_date 
		FROM $tableRoutesDriver rdp
		LEFT JOIN $tableUsers u
		ON rdp.id_user = u.id
		WHERE routes_date = DATE(NOW())
		AND id_routes = ".(int)$idRoutes.
		" ORDER BY routes_time DESC LIMIT 1";
	// echo $sqlQuery;
	// get Results
	$objRequest = $wpdb->get_results($sqlQuery);
	// default Return
	return json_encode(
		array (
	  		'status' => 'OK',
	  		'data'   => $objRequest
	  	)
	);
}

/*
 * load Drivers Positions By Route
*/
function loadDriversPositionsByRoute($idUser, $idRoutes, $vldDate = false) {
	// global Wpdb
  	global $wpdb;
  	// default Object
  	$objRequest = null;
	// table Routes Station
	$tableRoutesDriver = $wpdb->prefix . 'routes_driver_position';
	// load Drivers Position  	
	$sqlQuery = "SELECT 
		latitude, longitude, CONCAT(routes_date, ' ', routes_time) routes_date   
		FROM $tableRoutesDriver 
		WHERE id_routes = ".(int)$idRoutes. 
		" AND id_user   = ".(int)$idUser;
	// validate Date
	if ($vldDate) {
		$sqlQuery .= " AND routes_date = AND DATE(NOW())";
	}
	// order and limit query
	$sqlQuery  .= " ORDER BY routes_time DESC LIMIT 1";
	// echo $sqlQuery;die;
	// get Results
	$objRequest = $wpdb->get_results($sqlQuery);
	// default Return
	return $objRequest;
}

/*
 * validate Drivers Positions By Coordinates
*/
function validateDriversPositionsByCoordinates($idUser, $idRoutes, $strLatitude, $strLongitude) {
  	global $wpdb;
	// table Routes Station
	$tableRoutesDriver = $wpdb->prefix . 'routes_driver_position';
	// load Drivers Position  	
	$sqlQuery = "SELECT 
		count(id)   
		FROM $tableRoutesDriver 
		WHERE id_routes = ".(int)$idRoutes. 
		" AND id_user   = ".(int)$idUser.
		" AND latitude  = '".$strLatitude."'".
		" AND longitude = '".$strLongitude."'";
	// get Results
	$qtyQuantity = $wpdb->get_var($sqlQuery);
	// echo $sqlQuery ." RESULTADO: ".$qtyQuantity;
	// default Return
	return $qtyQuantity;
}

/*
 * register Drivers Positions By Route
*/
function registerDriversPositionsByRoute($idUser, $idRoutes, $strLatitude, $strLongitude) {
  	global $wpdb;
	// table Routes Station
	$tableRoutesDriver = $wpdb->prefix . 'routes_driver_position';
	$strQuery = "INSERT INTO $tableRoutesDriver 
	(id_routes, id_user, latitude, longitude, routes_date, routes_time, status) 
	VALUES(".(int)$idRoutes.", ".(int)$idUser.", '".$strLatitude."', '".$strLongitude."', DATE(NOW()), TIME(NOW()), 1)";
  	// echo $strQuery;die;
  	// run Query
  	$wpdb->query($strQuery);
  	// default Return
  	return true;
}