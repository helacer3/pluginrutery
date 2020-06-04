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

// include Entities Class
require_once('class/Entities.php');
// include User Class
require_once('class/Users.php');
// include User Class
require_once('class/Routes.php');
// include Products Class
require_once('class/Products.php');

// registerDriversPositionsByRoute(1, 1, "12", "13");

/**
Add Menu Itemd
*/
add_action('admin_menu', 'addAdminRoute');
function addAdminRoute() {
  add_menu_page('RUTERY', 'Rutas', 'manage_options' , 'adminRoutes', 'crudRoutesPage', 'dashicons-location');
  add_submenu_page('adminRoutes', "Paradas", "Paradas", 'manage_options', 'adminRoutesStations', 'crudRoutesStationPage', 2); 
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
	$wpdb->query("INSERT INTO $tableRoutesRequest (id_routes, name, phone, request_date, contacted, status) 
		VALUES (".(int)$solRoute.", '$solName', '$solPhone', NOW(), 0, 1)");
	// print Response
	echo "OK";
	// die
	wp_die(); 
}


function ajaxRequestContacted () {
	// global Connection
	global $wpdb;
	// set Table Name
	$tableRoutesRequest = $wpdb->prefix . 'routes_request';
	// request Post
	$idRequest  = $_POST['idRequest'];
	// create Request
	$wpdb->query("UPDATE $tableRoutesRequest SET contacted = 1 where id = ".(int)$idRequest);
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

/**
* ajax Register Driver Position 
*/
function ajaxRegisterDriverPosition() {
	// post Vars
	$idUser       = $_POST['actUser'];
	$idRoutes     = $_POST['actRoute'];
	$strLatitude  = $_POST['actLatitude'];
	$strLongitude = $_POST['actLongitude'];
	// register Drivers Positions By Route
	if (registerDriversPositionsByRoute($idUser, $idRoutes, $strLatitude, $strLongitude)) {
		echo "OK";
	} else {
		echo "ERROR";
	}
	// die
	die;
}

/**
* ajax Find Drivers Positions
*/
function ajaxFindDriversPositions() {
	// post Vars
	$idRoutes = $_POST['actRoute'];
	// register Drivers Positions By Route
	echo loadPositionsByRoute($idRoutes);
	// die
	wp_die(); 
}

// add Ajax Request Private
add_action( 'wp_ajax_ajaxRequestService', 'ajaxRequestService' );
add_action( 'wp_ajax_ajaxRequestContacted', 'ajaxRequestContacted' );
add_action( 'wp_ajax_ajaxRequestList', 'ajaxRequestList' );
add_action( 'wp_ajax_ajaxRegisterDriverPosition', 'ajaxRegisterDriverPosition' );
add_action( 'wp_ajax_ajaxFindDriversPositions', 'ajaxFindDriversPositions' );

// add Ajax Request No Private
add_action( 'wp_ajax_nopriv_ajaxRequestService', 'ajaxRequestService' );
add_action( 'wp_ajax_nopriv_ajaxRequestContacted', 'ajaxRequestContacted' );
add_action( 'wp_ajax_nopriv_ajaxRequestList', 'ajaxRequestList' );
add_action( 'wp_ajax_nopriv_ajaxRegisterDriverPosition', 'ajaxRegisterDriverPosition' );
add_action( 'wp_ajax_nopriv_ajaxFindDriversPositions', 'ajaxFindDriversPositions' );

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
  include_once('includes/pages/showRouteMap.php');
}

/**
* Show User Routes
*/
function showUserRoutes() {
  include_once('includes/pages/showUserRoutes.php');
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

// add Front User Routes
add_shortcode( 'userRoutesShortCode', function ($atts, $content, $tag) {
	// CSS
	wp_enqueue_style( 'cssRutery', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
	// load Routes
	ob_start();
	showUserRoutes();
	return ob_get_clean();
});

/**
* scripts Maps
*/
function scriptsMaps($id) {
	// CSS
	wp_enqueue_style( 'cssRutery', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
	// JS
	wp_enqueue_script( 'scriptRoute', plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js', array( 'jquery' ) );
	
	wp_localize_script('scriptRoute','jsVars', array(
			'idRoute'    => $id,
			'idUser'     => get_current_user_id(),
			'isDriver'   => validateUserRole('conductor'),
			'plgRuta'    => plugin_dir_url( __FILE__ ),
			'ajaxUrl'    => admin_url('admin-ajax.php'),
			'jsonRoutes' => arraySingleRoutesStation($id)
		)
	);

	wp_enqueue_script( 'scriptGMaps', 'http://maps.googleapis.com/maps/api/js?key='.MAPS_API);
}