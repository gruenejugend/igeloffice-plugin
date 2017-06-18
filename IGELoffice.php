<?php

/*
	Plugin Name: IGELoffice
	Plugin URI: http://www.kay-wilhelm.de
	Description: Membership Management System der GRÜNEN JUGEND
	Author: Kay Wilhelm Mähler
	Author URI: http://www.kay-wilhelm.de
	Version: 1.0.0
*/


//TODO Change Serialize

	ini_set('display_errors', '1');
	
	defined('ABSPATH') or die( "Access denied !" );
	define('IGELOFFICE_PATH', plugin_dir_path(__FILE__));
	define('IO_NAME','igeloffice');
	define('IO_URL', trailingslashit(plugin_dir_url(__FILE__)));
	
	wp_register_script('jqueryIO', 'https://code.jquery.com/jquery-1.11.3.min.js');

require_once 'control/Log_Control.php';
	
	require_once 'services/util/User_Util.php';
	require_once 'services/util/Group_Util.php';
	require_once 'services/util/Permission_Util.php';
	require_once 'services/util/Request_Util.php';
	require_once 'services/util/Remember_Util.php';
	require_once 'services/util/Newsletter_Util.php';
	require_once 'services/util/Domain_Util.php';
	
	require_once 'control/ldap.php';
	require_once 'control/ldapConnector.php';
	
	require_once 'functions.php';
	
	require_once 'model/Group.php';
	require_once 'model/Permission.php';
	require_once 'model/Request.php';
	require_once 'model/User.php';
	require_once 'model/Domain.php';
	
	require_once 'control/Group_Control.php';
	require_once 'control/LDAP_Proxy.php';
	require_once 'control/MySQL_Proxy.php';
	require_once 'control/Permission_Control.php';
	require_once 'control/User_Control.php';
	require_once 'control/Remember_Control.php';
	require_once 'control/Domain_Control.php';
	require_once 'control/request/Request_Strategy.php';
	require_once 'control/request/Request_Control.php';
	require_once 'control/request/Request_Factory.php';
	require_once 'control/request/Request_Group.php';
	require_once 'control/request/Request_Permission.php';
	require_once 'control/request/Request_User.php';
    require_once 'control/request/Request_Domain.php';
    require_once 'control/request/Request_WordPress.php';
    require_once 'control/request/Request_Cloud.php';
	
	require_once 'view/Permission_Backend_View.php';
	require_once 'view/Groups_Backend_View.php';
	require_once 'view/Groups_Frontend_View.php';
	require_once 'view/Register_Backend_View.php';
	require_once 'view/Profile_Backend_View.php';
    require_once 'view/Profile_Frontend_View.php';
	require_once 'view/Auth_Backend_View.php';
	require_once 'view/Register_Frontend_View.php';
	require_once 'view/viewHelper.php';
	require_once 'view/Request_Backend_View.php';
	require_once 'view/Newsletter_Frontend_View.php';
	require_once 'view/Domain_Backend_View.php';
	
	add_action('user_new_form',														array('Register_Backend_View', 'maskHandler'));
	add_action('register_form',														array('Register_Frontend_View', 'maskHandler'));
    add_filter('registration_errors',												array('Register_Frontend_View', 'errorHandler'), 10, 3);
    add_action('io_user_register',                                                  array('User_Control', 'inLDAP'), 10, 1);
    add_action('io_user_register',                                                  array('User_Control', 'inSherpa'), 20, 1);
    add_action('io_user_register',                                                  array('Remember_Control', 'register'), 30, 1);
    add_action('io_user_activate',                                                  array('Group_Control', 'standardZuweisung'), 10, 1);
	add_action('user_register',														array('User_Control', 'createMeta'));
	add_action('user_register',														array('Register_Backend_View', 'maskExecution'));
	add_filter('wp_login_errors',													array('Register_Backend_View', 'registerMsg'), 10, 2);
	add_filter('login_message',														'io_toLoginMsg', 5, 2);
	add_action('login_footer',														array('Register_Frontend_View', 'loginLabel'));
	
	add_filter('manage_users_columns',												array('Profile_Backend_View', 'column'), 10, 2);
	add_action('manage_users_custom_column',										array('Profile_Backend_View', 'maskColumn'), 10, 3);
	add_filter('manage_users_sortable_columns',										array('Profile_Backend_View', 'column'), 10, 2);
	add_action('pre_user_query',													array('Profile_Backend_View', 'orderby'));
	add_filter('user_row_actions',													array('Profile_Backend_View', 'row'), 10, 2);
	add_action('admin_menu',														array('Profile_Backend_View', 'menu'));
	add_action('password_reset',													array('LDAP_Proxy', 'changePW'), 10, 2);
	add_action('admin_notices',														array('Profile_Backend_View', 'userActive'));
	add_action('admin_notices',														array('Profile_Backend_View', 'msg_request_permission_fail'));
	add_action('admin_notices',														array('Profile_Backend_View', 'msg_request_permission_start'));
	add_action('admin_notices',														array('Profile_Backend_View', 'msg_request_group_fail'));
	add_action('admin_notices',														array('Profile_Backend_View', 'msg_request_group_start'));
    add_action('admin_notices',														array('Remember_Control', 'unremember'));
    add_action('admin_notices',														array('Remember_Control', 'unremember_final'));
    add_action('admin_notices',														array('Remember_Control', 'msg_remember_profil'));
    add_action('admin_notices',														array('Remember_Control', 'msg_remember_profil_final'));
    add_shortcode('profilUpdate',                                                   array('Profile_Frontend_View', 'maskHandler'));
    add_filter('if_menu_conditions',												array('Profile_Frontend_View', 'menu'));

	add_action('show_user_profile',													array('Profile_Backend_View', 'maskHandler'));
	add_action('edit_user_profile',													array('Profile_Backend_View', 'maskHandler'));
	add_action('profile_update',													array('Profile_Backend_View', 'maskExecution'), 10, 2);
	add_action('user_profile_update_errors',										'io_mailErrorMsg', 10, 3);
    add_action('show_user_profile',                                                 array('Profile_Backend_View', 'maskContact'), 9);
    add_action('edit_user_profile',                                                 array('Profile_Backend_View', 'maskContact'), 9);
    add_action('profile_update',													array('Profile_Backend_View', 'maskContactSave'), 10, 2);

	add_filter('authenticate',														array('Auth_Backend_View', 'authentifizierung'), 10, 3);
	remove_action('authenticate',													'wp_authenticate_username_password', 20);
	add_filter('login_message',														function($message) {
		global $errors;
		$errors->remove('authentication_failed');
		
		return $message;
	}, 5, 1);
	
	add_action('add_meta_boxes',													array('Groups_Backend_View', 'maskHandler'), 10, 2);
	add_action('save_post',															array('Groups_Backend_View', 'maskSave'));
	add_action('delete_post',														array('Groups_Backend_View', 'maskDelete'));
	add_filter('manage_' .Group_Util::POST_TYPE. '_posts_columns',					array('Groups_Backend_View', 'column'), 10, 2);
	add_filter('manage_' .Group_Util::POST_TYPE. '_posts_custom_column',			array('Groups_Backend_View', 'maskColumn'), 10, 2);
	add_filter('manage_edit-' .Group_Util::POST_TYPE. '_sortable_columns',			array('Groups_Backend_View', 'column'), 10, 2);
	add_filter('request',															array('Groups_Backend_View', 'orderby'), 10, 2);
	add_action('restrict_manage_posts',												array('Groups_Backend_View', 'maskFiltering'));
	add_filter('parse_query',														array('Groups_Backend_View', 'filtering'));
	add_filter('parse_query',														array('Groups_Backend_View', 'leadingFilter'));
    add_action("admin_notices", array("Groups_Backend_View", "userSizeMsg"));
	add_action("admin_notices",														array("Groups_Backend_View", "userAddedLeaderUserMsg"));
	add_action("admin_notices",														array("Groups_Backend_View", "userFailedLeaderUserMsg"));
    add_action("admin_notices",                                                     array("Groups_Backend_View", "rememberUserMsg"));
	add_shortcode('group_dialog',													array("Groups_Frontend_View", "maskHandler"));
	
	add_action('add_meta_boxes',													array('Permission_Backend_View', 'maskHandler'));
	add_action('save_post',															array('Permission_Backend_View', 'maskSave'));
	add_action('delete_post',														array('Permission_Backend_View', 'maskDelete'));
	add_filter('manage_' .Permission_Util::POST_TYPE. '_posts_columns',				array('Permission_Backend_View', 'column'), 10, 2);
	add_filter('manage_' .Permission_Util::POST_TYPE. '_posts_custom_column',		array('Permission_Backend_View', 'maskColumn'), 10, 2);
	add_filter('manage_edit-' .Permission_Util::POST_TYPE. '_sortable_columns',		array('Permission_Backend_View', 'column'), 10, 2);
	add_filter('request',															array('Permission_Backend_View', 'orderby'), 10, 2);
	add_action('restrict_manage_posts',												array('Permission_Backend_View', 'maskFiltering'));
	add_filter('parse_query',														array('Permission_Backend_View', 'filtering'));
    add_action("admin_notices",                                                     array("Permission_Backend_View", "rememberUserMsg"));
	
	add_action('add_meta_boxes',													array('Request_Backend_View', 'maskHandler'), 10, 2);
	add_action('save_post',															array('Request_Backend_View', 'maskSave'));
	add_filter('manage_' .Request_Util::POST_TYPE. '_posts_columns',				array('Request_Backend_View', 'column'), 10, 2);
	add_filter('manage_' .Request_Util::POST_TYPE. '_posts_custom_column',			array('Request_Backend_View', 'maskColumn'), 10, 2);
	add_filter('manage_edit-' .Request_Util::POST_TYPE. '_sortable_columns',		array('Request_Backend_View', 'column'), 10, 2);
	add_filter('request',															array('Request_Backend_View', 'orderby'), 10, 2);
	add_action('restrict_manage_posts',												array('Request_Backend_View', 'maskFiltering'));
	add_filter('parse_query',														array('Request_Backend_View', 'filtering'));
	add_filter('parse_query',														array('Request_Backend_View', 'leadingFilter'));
	add_action('admin_menu',														array('Request_Backend_View', 'menu'));
	
	add_shortcode('newsletter_dialog',												array('Newsletter_Frontend_View', 'maskHandler'));
    add_action("init",                                                              array('Remember_Control', 'schedule'));
    add_action("rememberSchedule",                                                  array('Remember_Control', 'schedule_exec'));
	
	add_action('add_meta_boxes',													array('Domain_Backend_View', 'maskHandler'));
	add_action('save_post',															array('Domain_Backend_View', 'maskSave'));
	add_filter('manage_' .Domain_Util::POST_TYPE. '_posts_columns',					array('Domain_Backend_View', 'column'), 10, 2);
	add_filter('manage_' .Domain_Util::POST_TYPE. '_posts_custom_column',			array('Domain_Backend_View', 'maskColumn'), 10, 2);
	add_action('before_delete_post',							    				array('Domain_Backend_View', 'maskDelete'));
	
	if (!function_exists('wp_new_user_notification')) {
		function wp_new_user_notification($user_id, $notify = '') {
			return;
		}
	}
	
	/*
	 * Permissions
	 */
	require_once 'permission/mailStandard/mailStandard_model.php';
	require_once 'permission/mailStandard/mailStandard_Control.php';
	require_once 'permission/mailStandard/mailStandard_view.php';
	
	add_shortcode("io_mailStandard", array("mailStandard_view", "maskHandler"));
	
	require_once 'permission/cloud/cloud_model.php';
	require_once 'permission/cloud/cloud_control.php';
	require_once 'permission/cloud/cloud_view.php';
	
	add_shortcode("io_cloud", array("cloud_view", "maskHandler"));
	
	require_once 'permission/mailinglisten/mailinglisten_model.php';
	require_once 'permission/mailinglisten/mailinglisten_control.php';
	require_once 'permission/mailinglisten/mailinglisten_view.php';
	
	add_shortcode("io_mailinglisten", array("mailinglisten_view", "maskHandler"));
	
	require_once 'permission/menu_dependencies.php';
	add_filter('if_menu_conditions',												array('menu_dependencies', 'cloudMenu'));
	add_filter('if_menu_conditions',												array('menu_dependencies', 'mailMenu'));
	add_filter('if_menu_conditions',												array('menu_dependencies', 'listMenu'));
	add_filter('if_menu_conditions',												array('menu_dependencies', 'diensteMenu'));
    add_filter('if_menu_conditions',												array('menu_dependencies', 'domainMenu'));

    require_once 'permission/domain/Domain_Front_View.php';
    require_once 'permission/domain/Domain_Front_Control.php';
    require_once 'permission/domain/Domain_Front_Model.php';

    add_shortcode("io_domain", array("Domain_Front_View", "maskHandler"));