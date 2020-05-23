<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 
delete_option( 'plugin_option_name' );
 
// For site options in Multisite
delete_site_option( $option_name );  
 
// Drop a custom db table
global $wpdb;
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}clientes" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}obras" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}remision" );