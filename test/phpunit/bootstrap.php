<?php

// path to test lib bootstrap.php
$test_lib_bootstrap_file = dirname( __FILE__ ) . '/includes/bootstrap.php';

if ( ! file_exists( $test_lib_bootstrap_file ) ) {
    echo PHP_EOL . "Error : unable to find " . $test_lib_bootstrap_file . PHP_EOL;
    exit( '' . PHP_EOL );
}

// set plugin and options for activation
$GLOBALS[ 'wp_tests_options' ] = array(
        'active_plugins' => array(
                'igeloffice/IGELoffice.php'
        )
);
// call test-lib's bootstrap.php
require_once $test_lib_bootstrap_file;

$current_user = new WP_User( 1 );
$current_user->set_role( 'administrator' );

echo PHP_EOL;
echo 'Using WordPress core : ' . ABSPATH . PHP_EOL;
echo PHP_EOL;

define('LDAP_HOST', 'localhost');
define('LDAP_PORT', 9999);
define('LDAP_DN_BASE', 'dc=gruene-jugend,dc=de');
define('LDAP_USER_BASE', 'ou=users,'.LDAP_DN_BASE);
define('LDAP_GROUP_BASE', 'ou=groups,'.LDAP_DN_BASE);
define('LDAP_PERMISSION_BASE', 'ou=permissions,'.LDAP_DN_BASE);
define('LDAP_DOMAIN_BASE', 'ou=domains'.LDAP_DN_BASE);
