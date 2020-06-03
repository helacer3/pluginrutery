<?php

/**
* get User Purshased Products
*/
function getUserPurshasedProducts() {
 	// global Wpdb
	global $wpdb;
	// create Query
	echo $query = "SELECT woim.meta_value
		FROM wld_posts AS p
		INNER JOIN wld_postmeta AS pm ON p.ID = pm.post_id
		INNER JOIN wld_woocommerce_order_items AS woi ON p.ID = woi.order_id
		INNER JOIN wld_woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
		WHERE woim.meta_key = '_product_id' 
		AND woi.order_item_type = 'line_item'
		AND (pm.meta_key = '_customer_user' AND pm.meta_value = ".get_current_user_id().")";
	// query Products
	$usrProducts =  $wpdb->get_results($query);
	// default Return
	return $usrProducts;
}

/**
* get Purchased Products
*/
function getPurchasedProducts($lstProducts) {
 	// global Wpdb
	global $wpdb;
	// create Query
	$query = "SELECT id, post_title, post_name, guid 
	FROM wld_posts WHERE id IN (".$lstProducts.")";
	// query Products
	$usrProducts =  $wpdb->get_results($query);
	// default Return
	return $usrProducts;
}

/**
* get User Products
*/
function getUserProducts() {
	// default Var
	$arrIds      = array();
	$arrProducts = array();
	// get Products List Ids
	$lstProducts = getUserPurshasedProducts();
	// validate Result
	if (count($lstProducts) > 0) {
		// iterate Result
		foreach ($lstProducts as $product) {
			$arrIds[] = $product->meta_value;
		}
	}
	// validate Products List
	if (count($lstProducts) > 0) {
		// get Purchased Products
		$arrProducts = getPurchasedProducts(implode(",", $arrIds));
	}
	// echo "<pre>"; print_r($arrProducts);	echo "<pre>";die;
	// default Return
	return $arrProducts;
}

/**
* get User Orders
*/
function getUserOrders() {
	// default Var
	$orders = array();
	// get Current User
	global $current_user;
	// get User Email
	$email = (string)$current_user->user_email;
	// validate Email
	if ($email != "") {
		// query Orders
		$query = new WC_Order_Query();
		// set Email
		$query->set('customer', $email);
		// set Status
		$query->set('status', ['processing','completed']);
		// get Orders
		$orders = $query->get_orders();
	}
	// default Return
	return $orders;
}