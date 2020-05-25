<?php
/*
Plugin Name: rutery
Plugin URI: https://google.com
Description: Plugin para la administración de rutas: Rutery
Version: 1.0
Author: Snayder Acero - helacer3@yahoo.es
License: GPL2
*/
defined('ABSPATH') or die("Bye bye");
//define('MAPS_API', "AIzaSyCKVGEf80o4Qlrje04Iq6XmxR5f04WIiwE");
//define('MAPS_API', "AIzaSyDKq054RhEGHRPCnVIBBqEDmY1S27jgh1M");
define('MAPS_API', "AIzaSyAM6JHTir9agfylPbPeMeOpXl4xq_SdD3Q");
define('PLG_RUTA', plugin_dir_path(__FILE__));

/**
 * crud Routes
*/
function crudRoutesTables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	/* table Routes */
	$tableRoutes = $wpdb->prefix . 'routes';
	$sql = "CREATE TABLE IF NOT EXISTS `$tableRoutes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(220) DEFAULT NULL,
	`description` text DEFAULT NULL,
	`status` boolean DEFAULT NULL,
	PRIMARY KEY(id)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

	// validate Table Exist
	if ($wpdb->get_var("SHOW TABLES LIKE '$tableRoutes'") != $tableRoutes) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

/**
 * crud Station Routes
*/
function crudStationsRoutesTables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	/* table Routes Station */
	$tableRoutesStation = $wpdb->prefix . 'routes_station';
	$sql = "CREATE TABLE  IF NOT EXISTS `$tableRoutesStation` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_routes` smallint(4) DEFAULT NULL,
	`name` varchar(220) DEFAULT NULL,
	`address` varchar(250) DEFAULT NULL,
	`latitude` varchar(250) DEFAULT NULL,
	`longitude` varchar(250) DEFAULT NULL,
	`status` boolean DEFAULT NULL,
	PRIMARY KEY(id)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

	// validate Table Exist
	if ($wpdb->get_var("SHOW TABLES LIKE '$tableRoutesStation'") != $tableRoutesStation) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

/**
 * Station Request
*/
function crudStationsRequestTables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	/* table Routes Station */
	$tableRoutesRequest = $wpdb->prefix . 'routes_request';
	$sql = "CREATE TABLE  IF NOT EXISTS `$tableRoutesRequest` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_routes` smallint(4) DEFAULT NULL,
	`name` varchar(50) DEFAULT NULL,
	`phone` varchar(20) DEFAULT NULL,
	`request_date` datetime DEFAULT NOW(),
	`status` boolean DEFAULT NULL,
	PRIMARY KEY(id)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

	// validate Table Exist
	if ($wpdb->get_var("SHOW TABLES LIKE '$tableRoutesRequest'") != $tableRoutesRequest) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

/**
* register Activation Hooks
*/
register_activation_hook( __FILE__, 'crudRoutesTables');
register_activation_hook( __FILE__, 'crudStationsRoutesTables');
register_activation_hook( __FILE__, 'crudStationsRequestTables');


/**
Add Menu Itemd
*/
add_action('admin_menu', 'addAdminRoute');
function addAdminRoute() {
  add_menu_page('RUTERY', 'Rutas', 'manage_options' , 'adminRoutes', 'crudRoutesPage', 'dashicons-location');
  add_submenu_page('adminRoutes', "Paradas", "Paradas", 'manage_options', 'adminRoutesStations', 'crudRoutesStationPage', 2); 
}


/**
 * load Route
*/
function loadSingleRoute($namTable, $id) {
  	global $wpdb;
  	$objRoute = null;
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
  	if ($id > 0) {
		$objRequest = $wpdb->get_results("SELECT * FROM $tableRoutesStation WHERE id_routes = ".(int)$id);
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

/**
* ajax Request Service
*/
function ajaxRequestService() {
	// global Connection
	global $wpdb;
	// set Table Name
	$tableRoutesRequest = $wpdb->prefix . 'routes_request';
	// request Post
	$solName  = $_POST['solName'];
	$solPhone = $_POST['solPhone'];
	$solRoute = $_POST['solRoute'];
	// create Request
	$wpdb->query("INSERT INTO $tableRoutesRequest (id_routes, name, phone, request_date, status) 
		VALUES (".(int)$solRoute.", '$solName', '$solPhone', NOW(), 1)");
	// print Response
	echo "OK";
	// die
	wp_die(); 
}

/**
* ajax Request List
*/
function ajaxRequestList() {
	// post Vars
	$id = $_POST['solRoute'];
	// load Request List
	$lstRequest = loadRequestListByRoute($id);
	// include View
	include_once('includes/listRequest.php');
	// die
	wp_die(); 
}

// add Ajax Request
add_action( 'wp_ajax_ajaxRequestService', 'ajaxRequestService' );
add_action( 'wp_ajax_ajaxRequestList', 'ajaxRequestList' );

/**
* Crud Routes
*/
function crudRoutesPage() {
	// CSS
	wp_enqueue_style( 'cssRutery', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
	// include View
	include_once('includes/adminRoutes.php');
}

/**
* Crud Routes Station
*/
function crudRoutesStationPage() {
	// CSS
	wp_enqueue_style( 'cssRutery', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
	// include View
  	include_once('includes/adminRoutesStations.php');
}

/**
* Show Route Map
*/
function showRouteMap() {
  include_once('includes/showRouteMap.php');
}

// load Scrips JS
// add_action( 'wp_enqueue_scripts', 'scriptsMaps' );

// add Front Map
add_shortcode( 'routesMapShortCode', function ($atts, $content, $tag) {
	// get Route ID
	$id = (array_key_exists('id', $atts)) ? $atts['id'] : 0;
	//echo "<pre>";var_dump(arraySingleRoutesStation($id));echo "</pre>";die;
	// load Scripts
	scriptsMaps($id);
	// load Routes
	ob_start();
	showRouteMap($id);
	return ob_get_clean();
});

function scriptsMaps($id) {
	// CSS
	wp_enqueue_style( 'cssRutery', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
	// JS
	wp_enqueue_script( 'scriptRoute', plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js', array( 'jquery' ) );
	
	wp_localize_script('scriptRoute','jsVars', array(
			'idRoute'    => $id,
			'plgRuta'    => plugin_dir_url( __FILE__ ),
			'ajaxUrl'    => admin_url('admin-ajax.php'),
			'jsonRoutes' => arraySingleRoutesStation($id)
		)
	);

	wp_enqueue_script( 'scriptGMaps', 'http://maps.googleapis.com/maps/api/js?key='.MAPS_API);
}