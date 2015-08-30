<?php

define('LDAP_HOST', 'localhost');
define('LDAP_PORT', 389);
define('LDAP_DN_BASE', 'dc=example,dc=com');
define('LDAP_USER_BASE', 'ou=users,'.LDAP_DN_BASE);
define('LDAP_GROUP_BASE', 'ou=groups,'.LDAP_DN_BASE);
define('LDAP_PERMISSION_BASE', 'ou=permissions,'.LDAP_DN_BASE);
define('IGELOFFICE_PATH', plugin_dir_path(__FILE__));