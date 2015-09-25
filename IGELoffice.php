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
	require_once('config.inc.php');
	require_once('class/ldap.php');
	require_once('class/ldapConnector.php');
	require_once('class/io_form.php');
	require_once('class/io_postlist.php');
	require_once('class/io_groups.php');
	require_once('class/io_permission.php');
	require_once('class/io_request.php');
	require_once('class/io_user.php');
	require_once('class/permissions/io_mailing.php');
	require_once('functions/register.php');
	require_once('functions/functions.php');
	
	wp_register_script('jqueryIO', 'https://code.jquery.com/jquery-1.11.3.min.js');
	
	//Registrierung und Login
	add_action('init',											'io_init');
	
	add_action('register_form',									array('io_user', 'register_form'));
	add_action('user_new_form',									array('io_user', 'new_user_form'));
    add_filter('registration_errors',							array('io_user', 'register_error'), 10, 3);
	add_filter('wp_login_errors',								array('io_user', 'user_register_msg'), 10, 2);
    add_action('user_register',									array('io_user', 'user_register'));
	
	add_filter('manage_users_columns',							array('io_user', 'user_column'));
	add_action('manage_users_custom_column',					array('io_user', 'user_column_value'), 10, 3);
	add_filter('manage_users_sortable_columns',					array('io_user', 'user_column'));
	add_action('pre_user_query',								array('io_user', 'user_order'));
	add_filter('user_row_actions',								array('io_user', 'user_options'), 10, 2);
	add_action('admin_menu',									array('io_user', 'user_menu'));
	
	add_action('show_user_profile',								array('io_user', 'user_profile'));
	add_action('edit_user_profile',								array('io_user', 'user_profile'));
	add_action('profile_update',								array('io_user', 'user_profile_save'));
	add_filter('authenticate',									array('io_user', 'authentifizierung'), 10, 3);
	
	add_action('add_meta_boxes',								array('io_groups', 'metabox'));
	add_filter('manage_io_groups_posts_columns',				array('io_groups', 'postlists'));
	add_filter('manage_io_groups_posts_custom_column',			array('io_groups', 'postlist_column'), 10, 2);
	add_filter('manage_edit-io_groups_sortable_columns',		array('io_groups', 'postlist_sorting'));
	add_filter('request',										array('io_groups', 'postlist_orderby'));
	add_action('restrict_manage_posts',							array('io_groups', 'postlist_filtering'));
	add_filter('parse_query',									array('io_groups', 'postlist_filtering_sort'));
	add_action('save_post',										array('io_groups', 'save'));
	add_action('delete_post',									array('io_groups', 'delete'));
	
	add_action('add_meta_boxes',								array('io_permission', 'metabox'));
	add_filter('manage_io_permission_posts_columns',			array('io_permission', 'postlists'));
	add_filter('manage_io_permission_posts_custom_column',		array('io_permission', 'postlist_column'), 10, 2);
	add_filter('manage_edit-io_permission_sortable_columns',	array('io_permission', 'postlist_sorting'));
	add_filter('request',										array('io_permission', 'postlist_orderby'));
	add_action('restrict_manage_posts',							array('io_permission', 'postlist_filtering'));
	add_filter('parse_query',									array('io_permission', 'postlist_filtering_sort'));
	add_action('save_post',										array('io_permission', 'save'));
	add_action('delete_post',									array('io_permission', 'delete'));
	
	add_action('add_meta_boxes',								array('io_request', 'metabox'));
	add_filter('manage_io_request_posts_columns',				array('io_request', 'postlists'));
	add_filter('manage_io_request_posts_custom_column',			array('io_request', 'postlist_column'), 10, 2);
	add_filter('manage_edit-io_request_sortable_columns',		array('io_request', 'postlist_sorting'));
	add_filter('request',										array('io_request', 'postlist_orderby'));
	add_action('restrict_manage_posts',							array('io_request', 'postlist_filtering'));
	add_filter('parse_query',									array('io_request', 'postlist_filtering_sort'));
	add_action('admin_menu',									array('io_request', 'menu'));
	
	//Permission Shortcodes
	add_shortcode('io_mail',									array('io_mailing', 'mask'));
	add_filter('sanitize_user',									'io_sanitize_user', 10, 3);
	
	if (!function_exists('wp_new_user_notification')) {
		function wp_new_user_notification($user_id, $notify = '') {
			return;
		}
	}

?>
