<?php
function getUserPurshasedProducts() {
 	// global Wpdb
	global $wpdb;
	// query Products
	$usrProducts = $wpdb->get_col(
		$wpdb->prepare(
			"
			SELECT      itemmeta.meta_value
			FROM        " . $wpdb->prefix . "woocommerce_order_itemmeta itemmeta
			INNER JOIN  " . $wpdb->prefix . "woocommerce_order_items items
			            ON itemmeta.order_item_id = items.order_item_id
			INNER JOIN  $wpdb->posts orders
			            ON orders.ID = items.order_id
			INNER JOIN  $wpdb->postmeta ordermeta
			            ON orders.ID = ordermeta.post_id
			WHERE       itemmeta.meta_key = '_product_id'
			            AND ordermeta.meta_key = '_customer_user'
			            AND ordermeta.meta_value = %s
			ORDER BY    orders.post_date DESC
			",
		get_current_user_id()
		)
	);
 
	// some orders may contain the same product, but we do not need it twice
	$usrProducts = array_unique( $usrProducts );

	// default Return
	return $usrProducts;
}