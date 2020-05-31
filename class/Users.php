<?php
/**
* validate User Role
*/
function validateUserRole($rolName) {
	// create Boolean Var
	$booValidate  = false;
	// get Actual User
	$current_user = wp_get_current_user();	
	// validate User Roles
	if ($current_user != null && in_array($rolName, $current_user->roles)) {
		$booValidate = true;
	}
	// default Return
	return $booValidate;
}

/**
* load Drivers Users
*/
function loadDriversUsers() {
	$args = array(
    	'role'    => 'conductor',
    	'orderby' => 'display_name',
    	'order'   => 'ASC'
	);
	// print_r(get_users($args));die;
	return get_users($args);
}