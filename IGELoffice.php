<?php

/*
	Plugin Name: IGELoffice
	Plugin URI: http://www.kay-wilhelm.de
	Description: Membership Management System der GRÜNEN JUGEND
	Author: Kay Wilhelm Mähler
	Author URI: http://www.kay-wilhelm.de
	Version: 1.0.0
*/

	ini_set('display_errors', '1');
	require('functions/register.php');
	require('class/io_user.php');
	
	wp_register_script('jqueryIO', 'https://code.jquery.com/jquery-1.11.3.min.js');
	
	//Registrierung und Login
	add_action('register_form',						array('io_user', 'register_form'));
	add_action('user_new_form',						array('io_user', 'new_user_form'));
    add_filter('registration_errors',				array('io_user', 'register_error'), 10, 3);
    add_action('user_register',						array('io_user', 'user_register'));
	
	add_filter('manage_users_columns',				array('io_user', 'user_column'));
	add_action('manage_users_custom_column',		array('io_user', 'user_column_value'), 10, 3);
	add_filter('manage_users_sortable_columns',		array('io_user', 'user_column'));
	add_action('pre_user_query',					array('io_user', 'user_order'));
	add_filter('user_row_actions',					array('io_user', 'user_options'), 10, 2);
	add_action('admin_menu',						array('io_user', 'user_menu'));
	
	add_action('show_user_profile',					array('io_user', 'user_profile'));
	add_action('edit_user_profile',					array('io_user', 'user_profile'));
	add_filter('authenticate',						array('io_user', 'authentifizierung'), 30, 3);

?>
