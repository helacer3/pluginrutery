<?php
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
	`drivers_list` varchar(50) DEFAULT NULL,
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
	`position` smallInt(1) DEFAULT NULL,
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
	`contacted` smallInt(1) DEFAULT 0,
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
 * Driver Router Position
*/
function crudDriverRoutePosition() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	/* table Routes Station */
	$tableRoutesDriver = $wpdb->prefix . 'routes_driver_position';
	$sql = "CREATE TABLE  IF NOT EXISTS `$tableRoutesDriver` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_routes` smallint(4) DEFAULT NULL,
	`id_user` smallint(4) DEFAULT NULL,
	`latitude` VARCHAR(10) DEFAULT NULL,
	`longitude` VARCHAR(10) DEFAULT NULL,
	`routes_date` datetime DEFAULT NOW(),
	`status` boolean DEFAULT NULL,
	PRIMARY KEY(id)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

	// validate Table Exist
	if ($wpdb->get_var("SHOW TABLES LIKE '$tableRoutesDriver'") != $tableRoutesDriver) {
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
register_activation_hook( __FILE__, 'crudDriverRoutePosition');