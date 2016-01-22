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
	define('IO_NAME','igeloffice');
	define('IO_URL', trailingslashit(plugin_dir_url(__FILE__)));
	
	require_once 'functions.php';
	
	require_once 'model/Group.php';
	require_once 'model/Permission.php';
	require_once 'model/User.php';
	
	require_once 'control/Group_Control.php';
	require_once 'control/LDAP_Proxy.php';
	require_once 'control/Permission_Control.php';
	require_once 'control/User_Control.php';
	require_once 'control/ldap.php';
	require_once 'control/ldapConnector.php';
