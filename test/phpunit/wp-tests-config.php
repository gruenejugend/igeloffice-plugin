<?php

// change the next line to points to your wordpress dir
define( 'ABSPATH', '../../../' );
//define( 'ABSPATH', '../../../../' );

define( 'WP_DEBUG', false );

// WARNING WARNING WARNING!
// tests DROPS ALL TABLES in the database. DO NOT use a production database

define( 'DB_NAME', 'wp-dev' );
define( 'DB_USER', 'wp-dev' );
define( 'DB_PASSWORD', 'ZF2evW7SyFZSf3b9' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wp_'; // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'localhost/dev' );
define( 'WP_TESTS_EMAIL', 'mail@kay-wilhelm.de' );
define( 'WP_TESTS_TITLE', 'KWM::DEV' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );