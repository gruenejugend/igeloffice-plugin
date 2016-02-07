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
	
	defined('ABSPATH') or die( "Access denied !" );
	define('IGELOFFICE_PATH', plugin_dir_path(__FILE__));
	define('IO_NAME','igeloffice');
	define('IO_URL', trailingslashit(plugin_dir_url(__FILE__)));
	
	wp_register_script('jqueryIO', 'https://code.jquery.com/jquery-1.11.3.min.js');
	
	require_once 'control/ldap.php';
	require_once 'control/ldapConnector.php';
	
	require_once 'functions.php';
	
	require_once 'model/Group.php';
	require_once 'model/Permission.php';
	require_once 'model/User.php';
	
	require_once 'control/Group_Control.php';
	require_once 'control/LDAP_Proxy.php';
	require_once 'control/Permission_Control.php';
	require_once 'control/User_Control.php';
	
	require_once 'view/backend_permission.php';
	require_once 'view/backend_groups.php';
	require_once 'view/backend_register.php';
	require_once 'view/backend_profile.php';
	require_once 'view/backend_auth.php';
	require_once 'view/frontend_register.php';
	require_once 'view/viewHelper.php';
	
	add_action('user_new_form',														array('backend_register', 'maskHandler'));
	add_action('register_form',														array('frontend_register', 'maskHandler'));
    add_filter('registration_errors',												array('frontend_register', 'errorHandler'), 10, 3);
	add_action('user_register',														array('User_Control', 'createMeta'));
	add_action('user_register',														array('backend_register', 'maskExecution'));
	add_filter('wp_login_errors',													array('backend_register', 'registerMsg'), 10, 2);
	add_filter('login_message',														'io_toLoginMsg', 5, 2);
	
	add_filter('manage_users_columns',												array('backend_profile', 'column'), 10, 2);
	add_action('manage_users_custom_column',										array('backend_profile', 'maskColumn'), 10, 3);
	add_filter('manage_users_sortable_columns',										array('backend_profile', 'column'), 10, 2);
	add_action('pre_user_query',													array('backend_profile', 'orderby'));
	add_filter('user_row_actions',													array('backend_profile', 'row'), 10, 2);
	add_action('admin_menu',														array('backend_profile', 'menu'));
	add_action('password_reset',													array('LDAP_Proxy', 'changePW'), 10, 2);
	
	add_action('show_user_profile',													array('backend_profile', 'maskHandler'));
	add_action('edit_user_profile',													array('backend_profile', 'maskHandler'));
	add_action('profile_update',													array('backend_profile', 'maskExecution'), 10, 2);
	add_action('user_profile_update_errors',										'io_mailErrorMsg', 10, 3);
	
	add_filter('authenticate',														array('backend_auth', 'authentifizierung'), 10, 3);
	remove_action('authenticate',													'wp_authenticate_username_password', 20);
	
	add_action('add_meta_boxes',													array('backend_groups', 'maskHandler'));
	add_action('save_post',															array('backend_groups', 'maskSave'));
	add_action('delete_post',														array('backend_groups', 'maskDelete'));
	add_filter('manage_' .Group_Control::POST_TYPE. '_posts_columns',				array('backend_groups', 'column'), 10, 2);
	add_filter('manage_' .Group_Control::POST_TYPE. '_posts_custom_column',			array('backend_groups', 'maskColumn'), 10, 2);
	add_filter('manage_edit-' .Group_Control::POST_TYPE. '_sortable_columns',		array('backend_groups', 'column'), 10, 2);
	add_filter('request',															array('backend_groups', 'orderby'), 10, 2);
	add_action('restrict_manage_posts',												array('backend_groups', 'maskFiltering'));
	add_filter('parse_query',														array('backend_groups', 'filtering'));
	
	add_action('add_meta_boxes',													array('backend_permission', 'maskHandler'));
	add_action('save_post',															array('backend_permission', 'maskSave'));
	add_action('delete_post',														array('backend_permission', 'maskDelete'));
	add_filter('manage_' .Permission_Control::POST_TYPE. '_posts_columns',			array('backend_permission', 'column'), 10, 2);
	add_filter('manage_' .Permission_Control::POST_TYPE. '_posts_custom_column',	array('backend_permission', 'maskColumn'), 10, 2);
	add_filter('manage_edit-' .Permission_Control::POST_TYPE. '_sortable_columns',	array('backend_permission', 'column'), 10, 2);
	add_filter('request',															array('backend_permission', 'orderby'), 10, 2);
	add_action('restrict_manage_posts',												array('backend_permission', 'maskFiltering'));
	add_filter('parse_query',														array('backend_permission', 'filtering'));
	
	if (!function_exists('wp_new_user_notification')) {
		function wp_new_user_notification($user_id, $notify = '') {
			return;
		}
	}