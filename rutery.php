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
define('PLG_RUTA',plugin_dir_path(__FILE__));

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
* register Activation Hooks
*/
register_activation_hook( __FILE__, 'crudRoutesTables');
register_activation_hook( __FILE__, 'crudStationsRoutesTables');


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
function loadSingleRoute($id) {
  	global $wpdb;
  	$objRoute = null;
  	if ($id > 0) {
	  	$tableRoutes = $wpdb->prefix . 'routes';
		$objRoute    =  $wpdb->get_row("SELECT * FROM $tableRoutes WHERE id = ".(int)$id);
	}
	// default Return
	return $objRoute;
}

/**
* Crud Routes
*/
function crudRoutesPage() {
  include_once('includes/adminRoutes.php');
}


/**
* Crud Routes Station
*/
function crudRoutesStationPage() {
  include_once('includes/adminRoutesStations.php');
}